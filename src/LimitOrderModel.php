<?php
namespace mrteye\Gdax;
class LimitOrderModel extends CommonOrderItf {
  /**
   *      Limit Order Parameters
   *  size:           Amount of Bitcoin to buy or sell
   *  time_in_force:  [optional] GTC, GTT, IOC, FOK; default is GTC
   *  cancel_after:   [optional] min, hour, day
   *  post_only:      [optional] post only flag
   */
  protected $type = 'limit';

  public $price;
  public $size;
  public $time_in_force;
  public $cancel_after;
  public $post_only;
}

