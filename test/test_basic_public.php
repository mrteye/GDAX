<?php
// Example #1 Basic - Pubic access, get product information.
// This example is loaded from index.php file which sets an autoload and
// creates $basePath and $config variables.

use mrteye\Gdax\Api;
use mrteye\Gdax\Auth;

// Get the GDAX API and start making calls.
$gdax = new Api($config->api_url);
$products = false;

// Debugging is enabled by default.
//$gdax->setDebug(false);

// Example usage of public calls.
try {
  $products = $gdax->getProducts();
  $productOrderBook = $gdax->getProductOrderBook('BTC-USD', $param = [
      'level' => 1
  ]);
  $productTrades = $gdax->getProductTrades($productId, $param = [
      'before' => 1,
      'limit' => 100
  ]);
} catch (\Exception $e) {
  $test[] = (object) array(
    'name' => 'basic public',
    'msg' => $e->getMessage(),
    'detail' => $gdax->getError()
  );
}

if ($products) {
  echo 'Products: <pre>'. print_r($products, true) .'</pre>';
}

