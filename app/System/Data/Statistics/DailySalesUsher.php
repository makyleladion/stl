<?php
namespace App\System\Data\Statistics;

use App\Outlet;
use App\System\Utils\ConfigUtils;

class DailySalesUsher extends DailySales
{
    private $usherPercentage;
    
    public function __construct(Outlet $outlet, $drawDate, $schedKey = null, $origin)
    {
        if ('outlet' == $origin) {
            throw new \Exception('DailySalesUsher cannot use origin from outlet.');
        }
        $this->usherPercentage = (float) ConfigUtils::get('COMMISSION_PERCENTAGE_DECIMAL');
        parent::__construct($outlet, $drawDate, $schedKey, $origin, true);
    }
    
    public function forDeposit()
    {
        $forDeposit = $this->netSales() - $this->lessCommission($this->usherPercentage);
        return $this->finalizeNegative($forDeposit);
    }
    
    public function toArray($pretty = false)
    {
        return [
            'gross_sales' => $this->makeNumbersPretty($this->grossSales(), $pretty),
            'less_payments' => $this->makeNumbersPretty($this->lessPayments(), $pretty),
            'net_sales' => $this->makeNumbersPretty($this->netSales(), $pretty),
            'less_commission' => $this->makeNumbersPretty($this->lessCommission($this->usherPercentage), $pretty),
            'for_deposit' => $this->makeNumbersPretty($this->forDeposit(), $pretty),
        ];
    }
}
