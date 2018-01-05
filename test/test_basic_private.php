<?php
// Example #2 Basic - Private access, authentication is required. 
// This example is loaded from index.php file which sets an autoload and
// creates $basePath and $config variables.

use mrteye\Gdax\Api;
use mrteye\Gdax\Auth;

// Create a GDAX object; the GDAX time API is optional.
$auth = new Auth(
  $config->key, 
  $config->secret, 
  $config->pass, 
  $config->time_url
);

// Authenticate per GDAX documentation; the time url is optional.
$gdax = new Api($config->api_url, $auth);
$accounts = false;

try {
  // Usage examples with some private calls.
  $accounts = $gdax->getAccounts();
  $account = $gdax->getAccount($accounts[0]->id);
  $order = $gdax->createOrder([
      // Common Order Parameters
      'type' => 'limit',
      'side' => 'buy',
      'product_id' => 'BTC-USD',
      // Limit Order Parameters
      'price' => ".01",
      'size' => ".01"
  ]);

  $orders = $gdax->getOrders($param = [
      'status' => 'open',
      'product_id' => '',
      'before' => 0,
      'after' => 1000,
      'limit' => 100
  ]);

  $uuids = $gdax->cancelOrder($orders[0]->id);

  $uuids = $gdax->cancelAllOrders($param = [
      'product_id' => 'BTC-USD'
  ]);
} catch (\Exception $e) {
  $test[] = (object) array(
    'name' => __FILE__,
    'msg' => $e->getMessage(),
    'detail' => $gdax->getError()
  );
}

if ($accounts) {
  echo 'Accounts: <pre>'. print_r($accounts, true) .'</pre>';
}
