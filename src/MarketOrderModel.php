<?php
/**
 * Market Order Model
 */
namespace mrteye\Gdax;

/**
 * The market order type for createOrder().
 */
class MarketOrderModel extends CommonOrderItf {

  /**
   * exchange order type
   * @var string
   */
  protected $type = 'market';

  /**
   * amount of BTC
   * @var string
   */
  public $size;

  /**
   * amount of quote currency to use
   * @var string
   */
  public $funds = "0";
}

