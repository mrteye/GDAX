<?php
/**
 * Stop Order Model
 */
namespace mrteye\Gdax;

/**
 * The stop order type for createOrder().
 */
class StopOrderModel extends CommonOrderItf {
  /**
   * exchange order type
   * @var string
   */
  protected $type = 'stop';

  /**
   * amount when the stop order triggers
   * @var string
   */
  public $price;

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

