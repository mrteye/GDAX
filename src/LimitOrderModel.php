<?php

class LimitOrderModel extends CommonOrderModel {
  /**
   *      Limit Order Parameters
   *  price:          Price per bitcoin
   *  size:           Amount of Bitcoin to buy or sell
   *  time_in_force:  [optional] GTC, GTT, IOC, FOK; default is GTC
   *  cancel_after:   [optional] min, hour, day
   *  post_only:      [optional] post only flag
   */
  public $price;
  public $size;
  public $time_in_force;
  public $cancel_after;
  public $post_only;
}

