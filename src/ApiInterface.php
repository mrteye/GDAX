<?php
namespace mrteye\Gdax;

interface ApiInterface {

  /***                                                  ***/
  /*** Order & Account API Calls: Private API endpoints ***/
  /***                                                  ***/

  public function getAccounts();
  public function getAccount($accountId);
  public function getAccountHistory($accountId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]);
  public function getAccountHolds($accountId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]);
  public function createOrder($param = [
      /* 1: Common Order Parameters */
      'client_oid' => 'c8600929-dc03-4a70-8334-dab22313a8f6',
      'type' => 'limit',
      'side' => 'buy',
      'product_id' => 'BTC-USD',
      'stp' => '',
      /* 2a: Limit Order Parameters */
      'price' => '.01',
      'size' => '.01',
      'time_in_force' => '',
      'cancel_after' => '',
      'post_only' => '',
      /* 2b: Markert Order Parameters */
      'size' => '',
      'funcds' => '',
      /* 2c: Stop Order Parameters */
      'price' => '',
      'size' => '',
      'funds' => '',
      /* 2d: Margin Parameters */
      'overdraft_enabled' => '',
      'funding_amount' => ''
  ]);
  public function cancelOrder($orderId);
  public function cancelAllOrders($param = [
      'product_id' => 'BTC-USD'
  ]);
  public function getOrders($param = [
      'status' => 'open',
      'product_id' => '',
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]);
  public function getOrder($orderId);
  public function getFills($param = [
      'order_id' => '', 
      'product_id' => '',
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]);
  public function getFundings($param = [
      'status' => 'settled',
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]);
  public function repay($param = [
      'amount' => '',
      'currency' => 'USD'
  ]);
  public function marginTransfer($param = [
      'margin_profile_id' => '',
      'type' => '',
      'currency' => '',
      'amount' => ''
  ]);
  public function getPosition();
  public function closePosition($param = [
      'repay_only' => true
  ]);
  public function deposit($param = [
      'amount' => '',
      'currency' => '',
      'payment_method_id' => ''
  ]);
  public function depositCoinbase($param = [
      'amount' => '',
      'currency' => '',
      'coinbase_account_id' => ''
  ]);
  public function withdraw($param = [
      'amount' => '',
      'currency' => '',
      'payment_method_id' => ''
  ]);
  public function withdrawCoinbase($param = [
      'amount' => '',
      'currency' => '',
      'coinbase_account_id' => ''
  ]);
  public function withdrawCrypto($param = [
      'amount' => '',
      'currency' => '',
      'crypto_address' => ''
  ]);
  public function getPaymentMethods();
  public function getCoinbaseAccounts();
  public function createReport($param = [
      'type' => 'fills',
      'start_date' => '2017-9-01T00:00:00.000Z',
      'end_date' => '2017-9-01T00:00:00.000Z',
      'product_id' => 'BTC-USD',
      'account_id' => '',
      'format' => 'pdf',
      'email' => '@gmail.com'
  ]);
  public function getReportStatus($reportId);
  public function getTrailingVolume();

  /***                                             ***/
  /*** Market Data API Calls: Public API endpoints ***/
  /***                                             ***/

  public function getProducts();
  public function getProductOrderBook($productId, $param = [
      'level' => 1
  ]);
  public function getProductTicker($productId);
  public function getProductTrades($productId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]);
  public function getProductHistoricRates($productId, $param = [
      'start' => '',
      'end' => '',
      'granularity' => 600
  ]);
  public function getProduct24HrStats($productId);
  public function getCurrencies();
  public function getTime();


}

