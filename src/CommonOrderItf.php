<?php
namespace mrteye\Gdax;

abstract class CommonOrderItf extends Hydration {
  /** 
   *  client_oid:     [optional] UUID for client reference only
   *  type:           [optional] limit, market, stop; default is limit
   *  side:           buy or sell
   *  product_id:     Valid Product ID
   *  stp:            [optional]  self-trade prevention flag
   */
  public $client_oid;
  public $type;
  public $side;
  public $product_id;
  public $stp;

}

