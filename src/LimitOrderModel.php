<?php
/**
 * Limit Order Model
 */
namespace mrteye\Gdax;

/**
 * The limit order type for createOrder().
 */
class LimitOrderModel extends CommonOrderItf {
  /**
   * exchange order type
   * @var string
   */
  protected $type = 'limit';

  /**
   * price per coin
   * @var string
   */
  public $price;

  /**
   * amount of coin to buy and sell
   * @var string
   */
  public $size;

  /**
   * GTC, GTT, IOC, FOK;
   * @var string
   */
  public $time_in_force = "GTC";

  /**
   * min, hour, day
   * @var string
   */
  public $cancel_after;

  /**
   * post only flag
   * @var string
   */
  public $post_only;
}

