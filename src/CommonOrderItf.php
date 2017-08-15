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
  protected $type = 'limit';
  public $side;
  public $product_id;
  public $stp = 'dc'; // dc, co, cn, cb

  /**
   *      Margin Parameters
   *  overdraft_enabled: overdraft flag
   *  funding_amount: amount of funding to be provided for the order
   */
  public $overdraft_enabled = false;
  public $funding_amount = "";
}

