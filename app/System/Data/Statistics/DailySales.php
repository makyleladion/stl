<?php

namespace App\System\Data\Statistics;

use App\Outlet;
use App\System\Services\PayoutService;
use App\System\Services\TransactionService;

class DailySales
{
    private $outlet;
    private $drawDate;
    private $schedKey;
    private $transactionService;
    private $payoutService;
    
    private $gross;
    private $payouts;
    
    private $includeNegative;
    private $origin;
    private $fromMobile;
    
    public function __construct(Outlet $outlet, $drawDate, $schedKey = null, $origin = 'outlet', $fromMobile = false)
    {
        $this->outlet = $outlet;
        $this->drawDate = $drawDate;
        $this->schedKey = $schedKey;
        $this->transactionService = new TransactionService($outlet, $origin);
        $this->payoutService = new PayoutService($outlet);
        $this->includeNegative(true);
        $this->origin = $origin;
        $this->fromMobile = $fromMobile;
    }
    
    /**
     * Gross Sales.
     * 
     * @return number
     */
    public function grossSales()
    {
        if (empty($this->gross)) {
            $userId = 0;
            if ($this->fromMobile) {
                $userId = auth()->user()->id;
            }
            $this->gross = $this->transactionService->dailySales($this->drawDate, $this->schedKey, true, $userId);
        }
        return $this->gross;
    }
    
    /**
     * Less Payments.
     * 
     * @return number|unknown
     */
    public function lessPayments()
    {
        if (empty($this->payouts)) {
            $userId = 0;
            if ($this->fromMobile) {
                $userId = auth()->user()->id;
            }
            $this->payouts = $this->payoutService->getTotalPayoutsAmountBySchedule($this->drawDate, $this->schedKey, $userId);
        }
        return $this->finalizeNegative($this->payouts);
    }
    
    /**
     * Net Sales.
     * 
     * @return number|unknown
     */
    public function netSales()
    {
        $netSales = $this->grossSales() - $this->lessPayments();
        return $this->finalizeNegative($netSales);
    }
    
    /**
     * Less Commission.
     * 
     * @param real $percent
     * @return number|unknown
     */
    public function lessCommission($percent = 0.2)
    {
        $lessCommission = $this->grossSales() * $percent;
        return $this->finalizeNegative($lessCommission);
    }
    
    /**
     * For Deposit.
     * 
     * @return number|unknown
     */
    public function forDeposit()
    {
        $forDeposit = $this->netSales() - $this->lessCommission(($this->outlet->is_affiliated) ? 0.2 : 0.15);
        return $this->finalizeNegative($forDeposit);
    }
    
    /**
     * Configuration to show calculations with negative results.
     * 
     * @param string $set
     */
    public function includeNegative($set = true)
    {
        $this->includeNegative = $set;
    }
    
    /**
     * 
     * 
     * @return number[]|\App\System\Data\Statistics\unknown[]
     */
    public function toArray($pretty = false)
    {
        return [
            'gross_sales' => $this->makeNumbersPretty($this->grossSales(), $pretty),
            'less_payments' => $this->makeNumbersPretty($this->lessPayments(), $pretty),
            'net_sales' => $this->makeNumbersPretty($this->netSales(), $pretty),
            'less_commission' => $this->makeNumbersPretty($this->lessCommission(($this->outlet->is_affiliated) ? 0.2 : 0.15), $pretty),
            'for_deposit' => $this->makeNumbersPretty($this->forDeposit(), $pretty),
        ];
    }
    
    /**
     * Wrap results which may disallow negative results.
     * 
     * @param integer|float $number
     * @return number|unknown
     */
    protected function finalizeNegative($number)
    {
        if (!$this->includeNegative) {
            $number = ($number < 0) ? 0 : $number;
        }
        return $number;
    }
    
    /**
     * Wrap number to make it pretty if chosen.
     * 
     * @param unknown $number
     * @param string $toPretty
     * @return string|unknown
     */
    protected function makeNumbersPretty($number, $toPretty = false)
    {
        if ($toPretty) {
            return number_format($number, 2, '.', ',');
        }
        return $number;
    }
}
