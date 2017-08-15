<?php
/**
 * Common Order Interface
 */
namespace mrteye\Gdax;

/**
 *  The CommonOrder interface holds the common properties for the three
 * order types: limit, market, and stop.
 */
abstract class CommonOrderItf extends Hydration {
  /**
   * [optional] UUID from client; reference only
   * @var string
   */
  public $client_oid;

  /**
   * limit, market, stop
   * @var string
   */
  protected $type = 'limit';

  /**
   * buy or sell
   * @var string
   */
  public $side;

  /**
   * Valid Product ID
   * @var string
   */
  public $product_id;

  /**
   * self-trade prevention flag: dc, co, cn, cb
   * @var string
   */ 
  public $stp = 'dc';

  /**
   *      Margin Parameters
   */

  /**
   * overdraft flag
   *  @var string
   */
  public $overdraft_enabled = false;

  /**
   * funding amount to be provided for the order
   * @var string
   */
  public $funding_amount = "";
}

