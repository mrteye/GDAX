<?php
namespace mrteye\Gdax;
use Curl\Curl;
/**
*
*
*/

class Api implements ApiInterface{
  protected $_error = [];
  protected $_debug = false;

  function __construct($api, $auth = false) {
    if (! $api) {
      throw new Exception(__METHOD__ ." Missing API URL");
    }

    $this->curl = new Curl();
    $this->auth = $auth;
    $this->api = $api;
  }

  /** The first 5 methods are support methods and the
   * remaining method calls are the GDAX API calls.
   * They can be used directly in a script or extended
   * in a custom class.
   */

  /***                        ***/
  /*** Debug & Error Logging  ***/
  /***                        ***/
  public function _setDebug($val) {
    $this->_debug = $val;
  }
  public function _getError() {
    return (empty($this->_error)? false: $this->_error);
  }
  private function _dump($path, $param) {
      return (object) [
        "api" => $this->api,
        "path" => $path,
        "parameters" => $param,
        "info" => (object) [
          'http_status_code' => $this->curl->http_status_code,
          'http_error_message' => $this->curl->http_error_message,
          'error_message' => $this->curl->error_message,
          'curl_error_message' => $this->curl->curl_error_message,
          'request_headers' => $this->curl->request_headers,
          'response_headers' => $this->curl->response_headers,
          'response' => $this->curl->response
        ]
      ];
  }


  /***                ***/
  /*** API Call Logic ***/
  /***                ***/
  private function _publicRequest($method, $path, $param = '') {
    return $this->_call($method, $path, $param);
  }
  private function _privateRequest($method, $path, $param = '') {
    $headers = $this->auth->getAuthHeaders($path, $param, $method);

    return $this->_call($method, $path, $param, $headers);
  }
  private function _call($method, $path, $param, $header = false) {
    $this->curl->reset();
    $this->curl->setHeader("Content-Type", "application/json");

    // Set additional headers.
    if ($header) {
      foreach ($header as $name => $value) {
        $this->curl->setHeader($name, $value);
      }
    }

    // Make an API request to the GDAX server.
    if ($param) {
      // If the method is not GET, pass JSON in the request body.
      $payload = false;
      if ($method != 'GET') {
        $param = json_encode($param);
        $payload = true;
      }
      $this->curl->{strtolower($method)}("$this->api/$path", $param, $payload);
    } else {
      $this->curl->{strtolower($method)}("$this->api/$path");
    }

    // TODO: Move error logs out of class. ~: Monolog
    // Error Logging
    if ($this->curl->error) {
      $this->_error[] = "$path: {$this->curl->error_message}";
      if ($this->_debug) {
        $this->_error[] = $this->_dump($path, $param);
      }
    }

    if (is_null($ret = json_decode($this->curl->response))) {
      throw new Exception(__METHOD__ ." - Invalid JSON or null returned."); 
    }

    return $ret;
  }


  /***                                                  ***/
  /*** Order & Account API Calls: Private API endpoints ***/
  /***                                                  ***/


  /**
   * Get accounts.
   * https://docs.gdax.com/#list-accounts
   * 
   * @api
   *
   * @return object[] Accounts 
   */
  public function getAccounts() {
    return $this->_privateRequest("GET", "accounts");
  }


  /**
   * Get an account.
   * https://docs.gdax.com/#get-an-account
   * 
   * @api
   *
   * @return object Account 
   */
  public function getAccount($accountId) {
    return $this->_privateRequest("GET", "accounts/$accountId");
  }


  /**
   * Get account activity.
   * https://docs.gdax.com/#get-account-history
   * This API is paginated.
   * 
   * @api
   *
   * @param string    $accountId  The UUID for an account.
   * @param string[]  $param An associative Array with the following options:
   *  before: Request page before this pagination id. 
   *  after:  Request page after this pagination id.
   *  limit:  Results per request.  Default 100; max 100.
   *
   * @return object[] Account Activity; sorted oldest first 
   */
  public function getAccountHistory($accountId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]) {
    return $this->_privateRequest("GET", "accounts/$accountId/ledger", $param);
  }


  /**
   * Get order holds.
   * https://docs.gdax.com/#get-holds
   * This API is paginated.
   * 
   * @api
   *
   * @param string    $accountId  The UUID for an account.
   * @param string[]  $param An associative Array with the following options:
   *  before: Request page before this pagination id. 
   *  after:  Request page after this pagination id.
   *  limit:  Results per request.  Default 100; max 100.
   *
   * @return object[] Orders on hold. 
   */
  public function getAccountHolds($accountId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]) {
    return $this->_privateRequest("GET", "accounts/$accountId/holds", $param);
  }


  /**
   * Place a new order.
   * https://docs.gdax.com/#place-a-new-order
   * 
   * @api
   *
   * @param LimitOrderModel|StopOrderModel|MarginOrderModel|MarketOrderModel
   *    $param An order-model object for the type of order you want to create.
   *
   * @return object[] Orders on hold. 
   */
  public function createOrder(CommonOrderItf $model) {
    return $this->_privateRequest("POST", "orders", $model->extract());
  }


  /**
   * Cancel an order.
   * https://docs.gdax.com/#cancel-an-order
   * 
   * @api
   *
   * @param string $orderId UUID of order to cancel
   *
   * @return string[] UUID of canceled Order
   */
  public function cancelOrder($orderId) {
    return $this->_privateRequest("DELETE", "orders/$orderId");
  }
  

  /**
   * Cancel all orders.
   * https://docs.gdax.com/#cancel-all
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  product_id: [optionl] valid product ID
   *
   * @return string[] UUID's of canceled orders
   */
  public function cancelAllOrders($param = [
      'product_id' => 'BTC-USD'
  ]) {
    return $this->_privateRequest("DELETE", "orders", $param);
  }


  /**
   * Get open and unselttled orders.
   * https://docs.gdax.com/#list-orders
   * This API is paginated.
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  status: opVen, pending active, all; status limiter
   *  product_id: [optionl] list orders for a specific product ID
   *
   * @return object Order
   */
  public function getOrders($param = [
      'status' => 'open',
      'product_id' => '',
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]) {
    return $this->_privateRequest("GET", "orders", $param);
  }


  /**
   * Get a GDAX order.
   * https://docs.gdax.com/#get-an-order
   * 
   * @api
   *
   * @param string    $orderId  The UUID for an order.
   *
   * @return object Order
   */
  public function getOrder($orderId) {
    return $this->_privateRequest("GET", "orders/$orderId");
  }


  /**
   * Get a list of fills.
   * https://docs.gdax.com/#list-fills
   * This API is paginated.
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  order_id: [optional] limit list to this order ID
   *  product_id: [optional] limit list to this product ID
   *  before: Request page before this pagination id. 
   *  after:  Request page after this pagination id.
   *  limit:  Results per request.  Default 100; max 100.
   *
   * @return object[] Order Fills
   */
  public function getFills($param = [
      'order_id' => '', 
      'product_id' => '',
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]) {
    return $this->_privateRequest("GET", "fills", $param);
  }


  /**
   * Get fundings.
   * https://docs.gdax.com/#funding
   * This API is paginated.
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  status: outstanding, settled, rejected
   *  before: Request page before this pagination id. 
   *  after:  Request page after this pagination id.
   *  limit:  Results per request.  Default 100; max 100.
   *
   * @return object[] Account Funding Transactions
   */
  public function getFundings($param = [
      'status' => 'settled',
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]) {
    return $this->_privateRequest("GET", "funding", $param);
  }


  /**
   * Repay a funding record.
   * https://docs.gdax.com/#repay
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount:   amount to repay
   *  currency: currency to be repaid (USD, GBP, EUR)
   *
   * @return TODO: What does this return.
   */
  public function repay($param = [
      'amount' => '',
      'currency' => 'USD'
  ]) {
    return $this->_privateRequest("POST", "funding/repay", $param);
  }


  /**
   * Transfer funds between a profile and a margin profile.
   * https://docs.gdax.com/#margin-transfer
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  margin_profile_id:  UUID of margin profile
   *  type:     deposit, withdraw
   *  currency: currency to transfer; USD, BTC
   *  amount:   amount to transfer
   *
   * @return object Transaction Details
   */
  public function marginTransfer($param = [
      'margin_profile_id' => '',
      'type' => '',
      'currency' => '',
      'amount' => ''
  ]) {
    return $this->_privateRequest("POST", "profiles/margin-transfer", $param);
  }


  /**
   * Get an overview of your profile.
   * https://docs.gdax.com/#position
   * 
   * @api
   *
   * @return object Profile Overview
   */
  public function getPosition() {
    return $this->_privateRequest("GET", "position");
  }


  /**
   * Close Position TODO: Identify this API.
   * https://docs.gdax.com/#close
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  repay_only:     true or false
   *
   * @return TODO: What does this return.
   */
  public function closePosition($param = [
      'repay_only' => true
  ]) {
    return $this->_privateRequest("POST", "position/close", $param);
  }


  /**
   * Deposit funds from a payment method. 
   * https://docs.gdax.com/#payment-method
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount: The amount to deposit.
   *  currency: The type of currency.
   *  payment_method_id: UUID of payment method.
   *
   * @return object Transaction Detail
   */
  public function deposit($param = [
      'amount' => '',
      'currency' => '',
      'payment_method_id' => ''
  ]) {
    return $this->_privateRequest("POST", "deposits/payment-method", $param);
  }


  /**
   * Deposit funds from a coinbase account.
   * https://docs.gdax.com/#coinbase
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount: The amount to deposit.
   *  currency: The type of currency.
   *  coinbase_account_id: UUID of coinbase account.
   *
   * @return object Transaction Detail
   */
  public function depositCoinbase($param = [
      'amount' => '',
      'currency' => '',
      'coinbase_account_id' => ''
  ]) {
    return $this->_privateRequest("POST", "deposits/coinbase-account", $param);
  }


  /**
   * Widthdraw funds to a payment method.
   * https://docs.gdax.com/#payment-method53
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount: The amount to withdraw.
   *  currency: The type of currency.
   *  payment_method_id: UUID of payment method.
   *
   * @return object Transaction Detail
   */
  public function withdraw($param = [
      'amount' => '',
      'currency' => '',
      'payment_method_id' => ''
  ]) {
    return $this->_privateRequest("POST",
        "withdrawals/payment-method", $param);
  }


  /**
   * Withdraw funds to a coinbase account. 
   * https://docs.gdax.com/#coinbase54
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount: The amount to withdraw.
   *  currency: The type of currency.
   *  coinbase_account_id: UUID of coinbase account.
   *
   * @return object Transaction Detail
   */
  public function withdrawCoinbase($param = [
      'amount' => '',
      'currency' => '',
      'coinbase_account_id' => ''
  ]) {
    return $this->_privateRequest("POST", "withdrawals/coinbase", $param);
  }


  /**
   * Withdraw funds to a crypto address. 
   * https://docs.gdax.com/#crypto
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  amount: The amount to withdraw.
   *  currency: The type of currency.
   *  crypto_address: A receiving crypto address.
   *
   * @return object Transaction Detail
   */
  public function withdrawCrypto($param = [
      'amount' => '',
      'currency' => '',
      'crypto_address' => ''
  ]) {
    return $this->_privateRequest("POST", "withdrawals/crypto", $param);
  }


  /**
   * Get a list of your payment methods.
   * https://docs.gdax.com/#payment-methods
   * 
   * @api
   *
   * @return object[] Payment Methods
   */
  public function getPaymentMethods() {
    return $this->_privateRequest("GET", "payment-methods");
  }


  /**
   * Get a list of your coinbase accounts.
   * https://docs.gdax.com/#list-accounts59
   * 
   * @api
   *
   * @return object[] Coinbase Accounts
   */
  public function getCoinbaseAccounts() {
    return $this->_privateRequest("GET", "coinbase-accounts");
  }


  /**
   * Create a report.
   * https://docs.gdax.com/#create-a-new-report
   * 
   * @api
   *
   * @param string[]  $param An associative Array with the following options:
   *  type: fills, account
   *  start_date: (inclusive) star date for report.
   *  end_date: (inclusive) end date for report.
   *  product_id: [required for type:fills] valid product ID; BTC-USD, etc.
   *  account_id: [required for type:account] account UUID.
   *  format: pdf, csv; default is pdf.
   *  email: [optional] Send report to:.
   *
   * @return object Transaction Detail
   */
  public function createReport($param = [
      'type' => 'fills',
      'start_date' => '2017-9-01T00:00:00.000Z',
      'end_date' => '2017-9-01T00:00:00.000Z',
      'product_id' => 'BTC-USD',
      'account_id' => '',
      'format' => 'pdf',
      'email' => '@gmail.com'
  ]) {
    return $this->_privateRequest("POST", "reports", $param);
  }


  /**
   * Get report status.
   * https://docs.gdax.com/#get-report-status
   * 
   * @api
   *
   * @param string  $reportId UUID of a report.
   *
   * @return object Transaction Detail
   */
  public function getReportStatus($reportId) {
    return $this->_privateRequest("POST", "reports/$reportId");
  }


  /**
   * Get 30 day trailing volume.
   * https://docs.gdax.com/#trailing-volume
   * 
   * @api
   *
   * @return object[] Product Trading Volume 
   */
  public function getTrailingVolume() {
    return $this->_privateRequest("GET", "users/self/trailing-volume");
  }


  /***                                             ***/
  /*** Market Data API Calls: Public API endpoints ***/
  /***                                             ***/
  

  /**
   * Get available currency trading pairs.
   * https://docs.gdax.com/#get-products
   * 
   * @api
   *
   * @return object[] Products 
   */
  public function getProducts() {
    return $this->_publicRequest("GET", "products");
  }
  

  /**
   * Get product order book.
   * https://docs.gdax.com/#get-product-order-book.
   * 
   * @api
   *
   * @param string $productId  a valid product ID; BTC-USD, etc.
   * @param string[] $param An associative Array with the following options:
   *  level:  1, 2, 3 - Only the Best, Top 50, Full order Book 
   *
   * @return object Product Order Book
   */
  public function getProductOrderBook($productId, $param = [
      'level' => 1
  ]) {
    return $this->_publicRequest("GET", "products/$productId/book", $param);
  }
  

  /**
   * Get product ticker.
   * https://docs.gdax.com/#get-product-ticker
   * This API is paginated.
   * 
   * @api
   *
   * @param string $productId  a valid product ID; BTC-USD, etc.
   *
   * @return object Last Trade for Product
   */
  public function getProductTicker($productId) {
    return $this->_publicRequest("GET", "products/$productId/ticker");
  }


  /**
   * Get the trades for a specific product.
   * https://docs.gdax.com/#get-trades
   * This API is paginated.
   * 
   * @api
   *
   * @param string $productId  a valid product ID; BTC-USD, etc.
   * @param string[]  $param An associative Array with the following options:
   *  before: int The number of items before. 
   *  after:  int The number of items after.
   *  limit:  int Limit the results. 
   *
   * @return object[] Product Trades
   */
  public function getProductTrades($productId, $param = [
      'before' => 0,
      'after' => 1000,
      'limit' => 100,
  ]) {
    return $this->_publicRequest("GET", "products/$productId/trades", $param);
  }  


  /**
   * Get historic rates for a product. Max 200 data points.
   * https://docs.gdax.com/#get-historic-rates
   * 
   * @api
   *
   * @param string $productId  a valid product ID; BTC-USD, etc.
   * @param string[]  $param An associative Array with the following options:
   *  start:  start time; ISO 8601 
   *  end:    end time; ISO 8601
   *  granularity: timeslice in seconds 
   *
   * @return mixed[][]  [0] -> Header; [1...] -> Data 
   */
  public function getProductHistoricRates($productId, $param = [
      'start' => '',
      'end' => '',
      'granularity' => 600
  ]) {
    return $this->_publicRequest("GET", "products/$productId/candles", $param);
  }


  /**
   * Get 24 hour statistics for a prdoduct.
   * https://docs.gdax.com/#get-24hr-stats
   * 
   * @api
   *
   * @param string $productId  a valid product ID; BTC-USD, etc.
   *
   * @return object 24Hr Stats 
   */
  public function getProduct24HrStats($productId) {
    return $this->_publicRequest("GET", "products/$productId/stats");
  }


  /**
   * Get list of known currencies.
   * https://docs.gdax.com/#get-currencies
   * 
   * @api
   *
   * @return object[] 
   */
  public function getCurrencies() {
    return $this->_publicRequest("GET", "currencies");
  }


  /**
   * Get GDAX server time.
   * https://docs.gdax.com/#time
   * 
   * @api
   *
   * @return object epoch and iso
   */
  public function getTime() {
    return $this->_publicRequest("GET", "time");
  }


}

