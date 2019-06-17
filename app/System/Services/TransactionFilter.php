<?php
namespace App\System\Services;

use Carbon\Carbon;
use App\System\Utils\TimeslotUtils;
use App\System\Data\Timeslot;

class TransactionFilter
{
    private $dateRangeFrom;
    private $dateRangeTo;
    
    private $timeRangeFrom;
    private $timeRangeTo;
    
    private $amountRangeFrom;
    private $amountRangeTo;
    
    private $schedKey;
    
    private $specificOutlets;
    
    private $specificTellers;
    
    private $isCanceled;
    
    public function __construct()
    {
        $this->specificOutlets = [];
        $this->specificTellers = [];
        $this->isCanceled = false;
    }
    
    public function setDateRange($dateFrom, $dateTo = null)
    {
        if ($dateFrom instanceof Carbon) {
            $this->dateRangeFrom = $dateFrom;
            if (!$dateTo) {
                $this->dateRangeTo = $dateFrom;
            }
        } else if (is_string($dateFrom) && TimeslotUtils::validateDate($dateFrom)) {
            $this->dateRangeFrom = Carbon::createFromFormat('Y-m-d', $dateFrom, env('APP_TIMEZONE'));
            if (!$dateTo) {
                $this->dateRangeTo = $this->dateRangeFrom;
            }
        } else {
            throw new \Exception('Error in supplying dateFrom or dateTo.');
        }
        
        if ($dateTo) {
            if ($dateTo instanceof Carbon) {
                $this->dateRangeTo = $dateTo;
            } else if (is_string($dateTo) && TimeslotUtils::validateDate($dateTo)) {
                $this->dateRangeTo = Carbon::createFromFormat('Y-m-d', $dateTo, env('APP_TIMEZONE'));
            } else {
                throw new \Exception('Error in supplying dateTo.');
            }
        }
        
        if ($this->dateRangeTo->startOfDay()->lessThan($this->dateRangeFrom->startOfDay())) {
            throw new \Exception('dateRangeTo must be equal or greater than dateRangeFrom');
        }
    }
    
    public function setTimeRange($timeFrom, $timeTo = null)
    {
        if (is_string($timeFrom) && TimeslotUtils::validateDate($timeFrom, 'H:i:s')) {
            $this->timeRangeFrom = $timeFrom;
        }
        if ($timeTo) {
            if (is_string($timeTo) && TimeslotUtils::validateDate($timeTo, 'H:i:s')) {
                $this->timeRangeTo = $timeTo;
            }
        }
    }
    
    public function setAmountRange($amountFrom, $amountTo)
    {
        if (is_numeric($amountFrom) && is_numeric($amountTo)) {
            $this->amountRangeFrom = $amountFrom;
            $this->amountRangeTo = $amountTo;
        }
    }
    
    public function setSchedKey($schedKey)
    {
        if (in_array($schedKey, Timeslot::getTimeslotKeys())) {
            $this->schedKey = $schedKey;
        } else {
            throw new \Exception('Invalid Schedule Key.');
        }
    }
    
    public function setSpecificOutletById($id)
    {
        if (is_numeric($id)) {
            $this->specificOutlets[] = (int) $id;
        }
    }
    
    public function setSpecificTellerById($id)
    {
        if (is_numeric($id)) {
            $this->specificTellers[] = (int) $id;
        }
    }
    
    public function setIsCanceled($isCanceled)
    {
        if (is_bool($isCanceled)) {
            $this->isCanceled = $isCanceled;
        }
    }
    
    public function execute($model)
    {
        if ($this->dateRangeFrom && $this->dateRangeTo) {
            if ($this->dateRangeFrom->startOfDay()->eq($this->dateRangeTo->startOfDay())) {
                $model->whereDate('created_at', '=', $this->dateRangeFrom);
            } else {
                $model->whereDate('created_at', '>=', $this->dateRangeFrom->startOfDay());
                $model->whereDate('created_at', '<=', $this->dateRangeTo->endOfDay());
            }
        }
        
        if (is_array($this->specificOutlets) && count($this->specificOutlets) > 0) {
            $arr = $this->specificOutlets;
            $model->whereHas('outlet', function($q) use ($arr) {
                $q->whereIn('id', $arr);
            });
        }
        
        if (is_array($this->specificTellers) && count($this->specificTellers) > 0) {
            $arr = $this->specificTellers;
            $model->whereHas('user', function($q) use ($arr) {
                $q->whereIn('id', $arr);
            });
        }
        
        if ($this->isCanceled) {
            $model->whereHas('tickets', function($q) {
                $q->where('is_cancelled', true);
            });
        }

        return $model;
    }
}

