<?php
namespace mrteye\Gdax;
class MarginOrderModel extends CommonOrderItf {
  /**
   *      Margin Parameters
   *  overdraft_enabled: overdraft flag
   *  funding_amount: amount of funding to be provided for the order
   */
  public $overdraft_enabled;
  public $funding_amount;
}

