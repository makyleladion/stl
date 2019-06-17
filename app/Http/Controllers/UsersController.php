<?php

namespace App\Http\Controllers;

use App\DefaultOutlet;
use App\Outlet;
use App\User;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditUserRequest;
use App\System\Data\User as UserData;
use App\System\Services\OutletService;
use App\System\Services\UserService;
use App\System\Utils\PaginationUtils;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Support\Facades\Auth;
use App\Hierarchy;
use App\System\Services\CachingService;

class UsersController extends Controller
{
    private $cache;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->cache = new CachingService();
    }

    /**
     * Show all users.
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
            $service = new UserService();

            $totalCount = $service->countUsers();
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $users = $service->getUsers($offsetLimit['offset'], $resultsPerPage);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);

            return view('admin.users', [
                'total_users' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'users' => $users,
                'total_pages' => $totalPages,
                'page' => $page,
                'prev' => PaginationUtils::getPreviousPageNumber($page, $totalCount, $resultsPerPage),
                'next' => PaginationUtils::getNextPageNumber($page, $totalCount, $resultsPerPage),
            ]);
        } catch (QueryException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            abort(500,$e->getMessage());
        }
    }

    /**
     * Show user creation form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }

        $outletCount = Outlet::count();
        $outletService = new OutletService();
        $outlets = $outletService->getOutlets(0, $outletCount, true);
        
        $userData = new UserData(auth()->user());
        
        $allowedIds = $this->getAllowedIds();
        $query = User::where(['is_admin' => true, 'status' => UserData::STATUS_ACTIVE]);
        if (!$userData->isSuperAdmin()) {
            $query->whereIn('id', $allowedIds);
        }
        $adminUsers = $query->get();

        return view('admin.user', [
            'outlets' => $outlets,
            'adminUser' => $userData,
            'adminUsers' => $adminUsers,
        ]);
    }
    
    /**
     * Show user update form.
     * 
     * @param int $user_id
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($user_id)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $user = User::where('id', $user_id)->firstOrFail();
        if ($user->status == UserData::STATUS_INACTIVE) {
            abort(404, 'Deactivated user cannot be shown');
        }
        
        $userData = new UserData($user);
        $allowedIds = $this->getAllowedIds();
        $query = User::where(['is_admin' => true, 'status' => UserData::STATUS_ACTIVE]);
        if (!auth()->user()->is_superadmin) {
            $query->whereIn('id', $allowedIds);
        }
        $adminUsers = $query->get();
        
        $outlets = [];
        $defaultOutlet = null;
        if ($userData->role() == UserData::ROLE_TELLER) {
            $outletCount = Outlet::count();
            $outletService = new OutletService();
            $outlets = $outletService->getOutlets(0, $outletCount);
            $defaultOutlet = DefaultOutlet::where('user_id', $user_id)->firstOrFail();
            $defaultOutlet = $defaultOutlet->outlet()->first();
        }
        
        $this->cache->clearUserCache($userData);
        
        return view('admin.user_edit', [
            'default_outlet' => $defaultOutlet,
            'outlets' => $outlets,
            'user' => $userData,
            'adminUsers' => $adminUsers,
        ]);
    }

    /**
     * Process user creation.
     *
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreate(CreateUserRequest $request)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            DB::beginTransaction();

            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');
            $outlet_id = $request->input('default_outlet');
            $superior_id = $request->input('user_superior');
            $is_read_only = $request->input('is_read_only', null);
            $is_usher = $request->input('is_usher', null);
            $is_admin = ($outlet_id <= 0) ? true : false;

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'is_admin' => $is_admin,
                'is_read_only' => $is_read_only ? true : false,
                'is_usher' => $is_read_only ? true : false,
            ]);

            if (!$is_admin) {
                DefaultOutlet::create([
                    'user_id' => $user->id,
                    'outlet_id' => $outlet_id,
                ]);
                $user->api_token = $user->id.str_random(60-strlen($user->id));
                $user->save();
            }
            
            if ($superior_id != 0) {
                Hierarchy::create([
                    'user_superior_id' => $superior_id,
                    'user_subordinate_id' => $user->id,
                ]);
            }

            DB::commit();
            
            $users = array_merge([$user],$this->cache->getUserAllSuperiors($user),$this->cache->getUserAllSubordinates($user));
            foreach ($users as $u) {
                $this->cache->clearUserCache($u);
            }

            Session::flash('user-success',
                sprintf("The user %s with email %s has been successfully created.",
                    $user->name, $user->email
                ));

            return redirect()->route('new-user');
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
    
    /**
     * Process user update.
     * 
     * @param EditUserRequest $request
     * @throws \Exception
     */
    public function postEdit(EditUserRequest $request)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            $user_id = $request->input('user_id', null);
            $name = $request->input('name', null);
            $email = $request->input('email', null);
            $password = $request->input('password', null);
            $superior_id = $request->input('user_superior', null);
            $is_read_only = $request->input('is_read_only', null);
            $is_usher = $request->input('is_usher', null);
            $outlet_id = $request->input('default_outlet', null);
            
            DB::beginTransaction();
            
            $user = User::where('id', $user_id)->firstOrFail();
            $user->name = $name;
            
            if ($user->email != $email) {
                $emailCount = User::where('email', $email)->count();
                if ($emailCount > 0) {
                    throw new \Exception('Email already exists.');
                } else {
                    $user->email = $email;
                }
            }
            
            if ($password && strlen($password) > 0) {
                $user->password = bcrypt($password);
            }
            
            $userData = new UserData($user);
            $outlet = null;
            if ($userData->role() == UserData::ROLE_TELLER) {
                if ($outlet_id && $outlet_id > 0) {
                    $defaultOutlet = DefaultOutlet::where('user_id', $user->id)->firstOrFail();
                    $defaultOutlet->outlet_id = $outlet_id;
                    $defaultOutlet->save();
                    $outlet = $defaultOutlet->outlet()->firstOrFail();
                } else if ($outlet_id && $outlet_id <= 0) {
                    $user->is_admin = true;
                } else {
                    throw new \Exception('Error on assigning outlet');
                }
                
                $savedIsUsher = (bool) $user->is_usher;
                $submittedIsUsher = $is_usher ? true : false;
                if ($savedIsUsher != $submittedIsUsher) {
                    $user->is_usher = $submittedIsUsher;
                }
                
            } else if ($userData->role() == UserData::ROLE_ADMIN) {
                $user->is_read_only = $is_read_only ? true : false;
            }
            
            if ($superior_id) {
                
                $hierarcy = $user->subordinateHierarchy()->first();
                if ($hierarcy) {
                    $hierarcy->user_superior_id = $superior_id;
                    $hierarcy->save();
                } else {
                    Hierarchy::create([
                        'user_superior_id' => $superior_id,
                        'user_subordinate_id' => $user->id,
                    ]);
                }
                
                if (!$outlet) {
                    $outlet = Outlet::where('id', $outlet_id)->firstOrFail();
                }
                if ($outlet->user_creator_id == 0) {
                    $outlet->user_creator_id = $superior_id;
                    $outlet->save();
                }
            }
            
            $user->save();            
            DB::commit();
            
            $users = array_merge([$user],$this->cache->getUserAllSuperiors($user),$this->cache->getUserAllSubordinates($user));
            foreach ($users as $u) {
                $this->cache->clearUserCache($u);
            }
            
            Session::flash('user-success',
                sprintf("The user %s with email %s has been successfully updated.",
                    $user->name, $user->email
                ));
            
            return redirect()->route('edit-user', [
                'user_id' => $user_id,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-user', [
                'user_id' => $user_id,
            ]);
        } catch(FatalThrowableError $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-user', [
                'user_id' => $user_id,
            ]);
        } catch(ModelNotFoundException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-user', [
                'user_id' => $user_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), 'edit-user', [
                'user_id' => $user_id,
            ]);
        }
    }
    
    /**
     * Set user to inactive.
     * 
     * @param int $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($user_id)
    {
        if (!auth()->user()->is_admin || (auth()->user()->is_admin && auth()->user()->is_read_only)) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        if (auth()->user()->id == $user_id) {
            abort(404, 'You cannot delete your own account.');
        }

        $user = User::where('id',$user_id)->firstOrFail();
        if ($user->status == UserData::STATUS_ACTIVE) {
            $user->status = UserData::STATUS_INACTIVE;
            $user->save();
        }
        
        Session::flash('user-success',
            sprintf("The user %s with email %s has been successfully deactivated.",
                $user->name, $user->email
            ));
        
        return redirect()->route('all-users');
    }
    
    /**
     * Logout user.
     * 
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function logoutUser($is_idle = false)
    {
        $service = new UserService();
        $service->recordUserLogout(auth()->user());
        Auth::logout();
        return redirect('/');
    }

    /**
     * Redirect to user craetion form with flash data.
     *
     * @param $message
     * @param $route
     * @param $params
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithErrorFlash($message, $route = null, array $params = [])
    {
        Session::flash('error-flash', $message);
        return redirect()->route(($route) ? $route : 'new-user', $params);
    }

    public function viewLogs($user_id) {
        $service = new UserService(); 
        $user = User::where('id', $user_id)->firstOrFail();
        $logs = $service->getUserTimeLog($user);
        
        return view('admin.user_logs', [
            'user' => $user,
            'logs' => $logs
        ]);
        
    }
    
    private function getAllowedIds()
    {
        $user = new UserData(auth()->user());
        
        $allowedId = [];
        $allowedId[] = $user->id();
        foreach ($this->cache->getUserAllSubordinates($user) as $sub) {
            $allowedId[] = $sub->id;
        }
        
        return $allowedId;
    }

    public function ajaxDisableUserBetting(){
        try {
            if (auth()->user()->is_read_only) {
                throw new \Exception('Read-only admins cannot disable or enable a user.');
            }
            
            $user_id = \request()->input('user_id');
            $to_enable = \request()->input('to_enable', false);
            $to_enable = (is_string($to_enable)) ? filter_var($to_enable, FILTER_VALIDATE_BOOLEAN) : $to_enable;
            
            $user = User::findOrFail($user_id);
            
            $service = new UserService();
            $service->setAllowBetting($to_enable, $user);
            
            return response()
                ->json(['success' => true])
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }
    }
}
