<?php
namespace mrteye\Gdax;
class StopOrderModel extends CommonOrderItf {
  /**
   *      Stop Order Parameters
   *  price:          desired price at which the stop order triggers
   *  size:           [optional] desired amount in BTC
   *  funds:          [optional] desirec amount of quote currency to use
   */
  public $price;
  public $size;
  public $funds;
}

