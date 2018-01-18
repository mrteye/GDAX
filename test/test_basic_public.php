<?php
/** This example (#1 Basic) shows public access and gets 
 * product information.
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
$example = new Example('Public Call: Get product information.',
    function() use ($config) {

  // Get the GDAX API and start making calls.
  $gdax = new Api($config->api_url);
  $products = false;

  // Example usage of public calls.
  $productId = 'BTC-USD';
  $products = $gdax->getProducts();
  $msg = "getProducts: (...)". print_r($products[0], true) . "\n";
  $productOrderBook = $gdax->getProductOrderBook($productId, $param = [
      'level' => 1
  ]);
  $msg .= "getProductOrderBook: ". print_r($productOrderBook, true) ."\n";
  $productTrades = $gdax->getProductTrades($productId, $param = [
      'before' => 1,
      'limit' => 100
  ]);
  $msg .= "getProductTrades: (...)". print_r($productTrades[0], true) ."\n";
  return $msg;
});

// Display results if this file was loaded directly.
$example->show();

