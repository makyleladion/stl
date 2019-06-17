<?php

namespace App\Http\Controllers;

use App\Payout;
use App\System\Services\PayoutService;
use App\System\Utils\PaginationUtils;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\System\Utils\UserUtils;

class PayoutsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function all($page = '1')
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            $service = new PayoutService();

            $query = Payout::query();
            if (!auth()->user()->is_superadmin) {
                $allowedIds = UserUtils::currentAndSubordinatesIds(auth()->user());
                $totalCount = $query->whereIn('user_id', $allowedIds)->count();
            } else {
                $totalCount = $query->count();
            }
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $payouts = $service->getPayouts($offsetLimit['offset'], $resultsPerPage);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);
            
            return view('admin.payouts', [
                'total_payouts' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'payouts' => $payouts,
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
}
