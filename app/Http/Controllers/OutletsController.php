<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOutletRequest;
use App\Outlet;
use App\System\Services\OutletService;
use App\System\Utils\PaginationUtils;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Requests\EditOutletRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\System\Services\CachingService;
use App\System\Utils\UserUtils;

class OutletsController extends Controller
{
    private $cache;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->cache = new CachingService();
    }

    /**
     * Show all outlets.
     *
     * @param string $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function all($page = '1')
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            $service = new OutletService();

            $query = Outlet::where('status', '<>', \App\System\Data\Outlet::STATUS_CLOSED);
            $this->allowedOutletsOnly($query);
            
            $totalCount = $query->count();
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $outlets = $service->getOutlets($offsetLimit['offset'], $resultsPerPage);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);
            
            return view('admin.outlets', [
                'total_outlets' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'outlets' => $outlets,
                'total_pages' => $totalPages,
                'page' => $page,
                'prev' => PaginationUtils::getPreviousPageNumber($page, $totalCount, $resultsPerPage),
                'next' => PaginationUtils::getNextPageNumber($page, $totalCount, $resultsPerPage),
            ]);
        } catch (QueryException $e) {
            report($e);
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            abort(500,$e->getMessage());
        }
    }

    /**
     * Show outlet creation form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }

        return view('admin.outlet');
    }
    
    public function edit($outlet_id)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $outlet = Outlet::where('id',$outlet_id)->firstOrFail();
        
        return view('admin.outlet_edit', ['outlet' => $outlet]);
    }

    /**
     * Process outlet creation.
     *
     * @param CreateOutletRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreate(CreateOutletRequest $request)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            DB::beginTransaction();

            
            $email = $request->input('email');
            $outletName = $request->input('outlet-name');
            $address = $request->input('address');
            $isAffiliated = $request->input('is-affiliated');
            $isAffiliated = ($isAffiliated == 1) ? true : false;
            
            $userIsExist = $request->input('user-is-exist');
            $userIsExist = ($userIsExist == 1) ? true : false;
            
            if ($userIsExist) {
                $user = User::where('email', $email)->firstOrFail();
                $name = $user->name;
            } else {
                $name = $request->input('name');
                $password = $request->input('password');
                $checkIfExist = User::where('email', $email)->count();
                if ($checkIfExist <= 0) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => bcrypt($password),
                        'is_admin' => false,
                    ]);
                } else {
                    throw new \Exception('The user with email '.$email.' already exists.');
                }
            }

            Outlet::create([
                'user_id' => $user->id,
                'user_creator_id' => (!auth()->user()->is_superadmin) ? auth()->user()->id : 0,
                'name' => $outletName,
                'address' => $address,
                'status' => \App\System\Data\Outlet::STATUS_ACTIVE,
                'is_affiliated' => $isAffiliated,
            ]);

            DB::commit();

            Session::flash('outlet-success',
                sprintf("The outlet %s owned by %s has been successfully created.",
                    $outletName, $name
                ));

            return redirect()->route('new-outlet');
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch(FatalThrowableError $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage());
        }
    }
    
    public function postEdit(EditOutletRequest $request)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            DB::beginTransaction();
            
            $outletId = $request->input('outlet_id');
            $outletName = $request->input('outlet-name');
            $address = $request->input('address');
            $isAffiliated = $request->input('is-affiliated');
            $isAffiliated = ($isAffiliated == 1) ? true : false;
            
            $outlet = Outlet::where('id', $outletId)->firstOrFail();
            $name = $outlet->user()->first()->name;
            
            $outlet->name = $outletName;
            $outlet->address = $address;
            $outlet->is_affiliated = $isAffiliated;
            $outlet->save();
            
            DB::commit();
            
            Session::flash('outlet-success',
                sprintf("The outlet %s owned by %s has been successfully updated.",
                    $outletName, $name
                ));
            
            return redirect()->route('edit-outlet', ['outlet_id' => $outletId]);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-outlet', ['outlet_id' => $outletId]);
        } catch(ModelNotFoundException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-outlet', ['outlet_id' => $outletId]);
        } catch(FatalThrowableError $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-outlet', ['outlet_id' => $outletId]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-outlet', ['outlet_id' => $outletId]);
        }
    }
    
    public function removeOutlet($outlet_id)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            $service = new OutletService();
            $service->closeOutlet($outlet_id);

            Session::flash('outlet-success', 'Successfully removed an outlet.');
            return redirect()->route('all-outlets');
            
        } catch (QueryException $e) {
            return $this->redirectWithErrorFlash($e->getMessage(), 'all-outlets');
        } catch(ModelNotFoundException $e) {
            return $this->redirectWithErrorFlash($e->getMessage(), 'all-outlets');
        } catch(FatalThrowableError $e) {
            return $this->redirectWithErrorFlash($e->getMessage(), 'all-outlets');
        } catch (\Exception $e) {
            return $this->redirectWithErrorFlash($e->getMessage(), 'all-outlets');
        }
    }

    /**
     * Redirect to form with error flash data.
     *
     * @param $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithErrorFlash($message, $route = null, array $params = [])
    {
        Session::flash('error-flash', $message);
        return redirect()->route(($route) ? $route : 'new-outlet', $params);
    }
    
    private function allowedOutletsOnly($query)
    {
        if (!auth()->user()->is_superadmin) {
            $user = new \App\System\Data\User(auth()->user());
            $allowedIds = [];
            $relatedUsers = array_merge(
                    [auth()->user()],
                    $user->getSubordinates()
                );
            
            foreach ($relatedUsers as $sub) {
                $allowedIds[] = $sub->id;
            }
            $query->whereIn('user_creator_id', $allowedIds);
        }
        
        return $query;
    }
}
