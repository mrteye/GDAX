<?php
/** This example (#2 Basic) shows access to private API calls, authentication
 * is required.
 */
use mrteye\Gdax\Api as Api;
use mrteye\Gdax\Auth as Auth;

use SomePHP\Test\Example as Example;


// Setup; All tests should be one level below doc root.
if (! $docRoot = $_SERVER['DOCUMENT_ROOT']) {
  $docRoot = dirname(dirname(__FILE__));
}
require_once "$docRoot/vendor/autoload.php";
require "$docRoot/config.php";


// Run the example code; Throw an exception for errors; Return any output;
$example = new Example('Private Calls: Create and Cancel Orders',
    function() use ($config) {

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

  return 'UUIDs of canceled Orders: '. print_r($uuids, true);
});

// Display results if this file was loaded directly.
$example->show();

