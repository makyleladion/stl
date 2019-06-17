<?php

namespace App\System\Services;

use App\Outlet as OutletModel;
use App\System\Data\Outlet;
use App\System\Data\Timeslot;
use App\DisableOutletRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\System\Utils\UserUtils;
use App\System\Data\User;

class OutletService
{
    const OUTLETS_SORT_TOTAL = 'total';
    
    private $outletsSummaryCache = [];
    private $cache;
    
    public function __construct()
    {
        $this->cache = new CachingService();
    }
    
    /**
     * Get outlets.
     *
     * Used for paginating outlets in pages.
     *
     * @param $skip
     * @param $take
     * @return array
     */
    public function getOutlets($skip, $take, $includeAllRelated = false)
    {
        $outlets = [];
        $query = OutletModel::with('tellers','tellers.user');
        $query->where('status', '<>', Outlet::STATUS_CLOSED);
        
        if (!auth()->user()->is_superadmin) {
            $user = new User(auth()->user());
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
        $query->orderBy('name', 'asc');
        $query->skip($skip);
        $query->take($take);
        $results = $query->get();
            
        foreach ($results as $result) {
            $outlets[] = new Outlet($result);
        }
        
        return $outlets;
    }
    
    public function getOutletsSummary($skip, $take, $draw_date, $sortByKey = self::OUTLETS_SORT_TOTAL, $origin = null, $ushersOnly = false)
    {
        $outlets = $this->getOutlets($skip, $take);
        $transactionService = new TransactionService(null, $origin);
        $outletsSummary = [];
        if ($origin) {
            $rawData = $transactionService->getDailySalesPerOutlet($draw_date, true, $ushersOnly);
        } else {
            $rawData = $transactionService->getDailySalesPerOutlet($draw_date);
        }
        
        foreach ($outlets as $outlet) {
            $summary = [];
            $total = 0;
            $summary['outlet'] = $outlet;
            foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
                $summary['sales'][$key] = 0;
                if (array_key_exists($outlet->id(), $rawData) && array_key_exists($key, $rawData[$outlet->id()])) {
                    $summary['sales'][$key] = $rawData[$outlet->id()][$key];
                }
                $total = bcadd($total, $summary['sales'][$key]);
            }
            
            $summary['sales'][self::OUTLETS_SORT_TOTAL] = $total;       
            $outletsSummary[] = $summary;
        }
        
        $this->outletsSummaryCache = $outletsSummary;
        $this->quicksortOutletsSummary(0, (count($this->outletsSummaryCache) - 1), $sortByKey);
        $outletsSummary = $this->outletsSummaryCache;
        $this->outletsSummaryCache = [];
        
        return $outletsSummary;
    }
    
    public function getOutletsSummaryDateRange($skip, $take, $draw_from, $date_to, $sortByKey = self::OUTLETS_SORT_TOTAL, $origin = null, $ushersOnly = false)
    {
        $outlets = $this->getOutlets($skip, $take);
        $transactionService = new TransactionService(null, $origin);
        $outletsSummary = [];
        if ($origin) {
            $rawData = $transactionService->getDailySalesPerOutletDateRange($draw_from, $date_to, true, $ushersOnly);
        } else {
            $rawData = $transactionService->getDailySalesPerOutletDateRange($draw_from, $date_to);
        }
        
        foreach ($outlets as $outlet) {
            $summary = [];
            $total = 0;
            $summary['outlet'] = $outlet;
            foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
                $summary['sales'][$key] = 0;
                if (array_key_exists($outlet->id(), $rawData) && array_key_exists($key, $rawData[$outlet->id()])) {
                    $summary['sales'][$key] = $rawData[$outlet->id()][$key];
                }
                $total = bcadd($total, $summary['sales'][$key]);
            }
            
            /* if ($total <= 0) {
                continue;
            } */
            
            $summary['sales'][self::OUTLETS_SORT_TOTAL] = $total;
            $outletsSummary[] = $summary;
        }
        
        $this->outletsSummaryCache = $outletsSummary;
        $this->quicksortOutletsSummary(0, (count($this->outletsSummaryCache) - 1), $sortByKey);
        $outletsSummary = $this->outletsSummaryCache;
        $this->outletsSummaryCache = [];
        
        return $outletsSummary;
    }
    
    public function disableOutlet($outlet_id)
    {
        $user = auth()->user();
        if ('3a8gaming@stl.ph' == $user->email) {
            return;
        }
        
        $this->disableOrClose($outlet_id, Outlet::STATUS_DISABLED);
    }
    
    public function closeOutlet($outlet_id)
    {
        $this->disableOrClose($outlet_id, Outlet::STATUS_CLOSED);
    }
    
    public function enableOutlet($outlet_id)
    {
        $outlet = OutletModel::findOrFail($outlet_id);
        
        DB::beginTransaction();
        
        try {
            $outlet->status = Outlet::STATUS_ACTIVE;
            $outlet->save();
            
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            report($e);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }
    }
    
    public function isDisabled($outlet_id)
    {
        if ($outlet_id instanceof OutletModel) {
            $outlet = $outlet_id;
        } else {
            $outlet = OutletModel::findOrFail($outlet_id);
        }
        
        return $outlet->status === Outlet::STATUS_DISABLED || $outlet->status === Outlet::STATUS_CLOSED;
    }
    
    private function quicksortOutletsSummary($lower, $upper, $sortByKey) {
        if ($lower >= $upper) {
            return;
        }
        $m = $lower;
        
        for ($i = $lower + 1; $i <= $upper; $i++) {
            if ((double) $this->outletsSummaryCache[$i]['sales'][$sortByKey] > (double) $this->outletsSummaryCache[$lower]['sales'][$sortByKey]) {
                $tmp = $this->outletsSummaryCache[++$m];
                $this->outletsSummaryCache[$m] = $this->outletsSummaryCache[$i];
                $this->outletsSummaryCache[$i] = $tmp;
            }
        }
        
        $tmp = $this->outletsSummaryCache[$m];
        $this->outletsSummaryCache[$m] = $this->outletsSummaryCache[$lower];
        $this->outletsSummaryCache[$lower] = $tmp;

        $this->quicksortOutletsSummary($lower, $m - 1, $sortByKey);
        $this->quicksortOutletsSummary($m + 1, $upper, $sortByKey);
    }
    
    private function disableOrClose($outlet_id, $status)
    {
        if ($status !== Outlet::STATUS_DISABLED && $status !== Outlet::STATUS_CLOSED) {
            throw new \Exception('Invalid status supplied.');
        }
        
        $outlet = OutletModel::findOrFail($outlet_id);
        
        DB::beginTransaction();
        
        try {
            $outlet->status = $status;
            $outlet->save();
            
            DisableOutletRecord::create([
                'outlet_id' => $outlet->id,
                'disable_timestamp' => Carbon::now(env('APP_TIMEZONE')),
            ]);
            
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            report($e);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }
    }
}
