<?php
namespace mrteye\Gdax;
class MarketOrderModel extends CommonOrderItf {

  protected $type = 'market';

  public $size;
  public $funds = "0";
}

