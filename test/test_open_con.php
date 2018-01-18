<?php
/**
 * This example demos that this API reuses Curl connections.
 *  - requires lsof, grep, and wc, to show the open connections from PHP.
 */
use mrteye\Gdax\Api as Api;
use mrteye\Gdax\Auth as Auth;
use mrteye\Gdax\Exception as Exception;

use SomePHP\Test\Example as Example;

// Setup; All tests should be one level below doc root.
if (! $docRoot = $_SERVER['DOCUMENT_ROOT']) {
  $docRoot = dirname(dirname(__FILE__));
}
require_once "$docRoot/vendor/autoload.php";
require "$docRoot/config.php";


// Some additional code used in this example.
function showOpenConnections() {
  return "[INFO] ". getOpenConnections() ." open connections to GDAX.\n";
}
function getOpenConnections() {
  $pid = getPid();
  return exec("lsof -p $pid | grep ESTABLISHED | wc -l");
}
function getPid() {
  return getmypid();
}


/** Run the example code; 
 * Throw an exception for errors; 
 * Return any output;
 */
$example = new Example('Re-use Curl Connections', 
    function() use ($config) {

  $products = false;
  $apiAuth = new Auth(
    $config->key,
    $config->secret,
    $config->pass,
    $config->time_url
  );

  $msg = 'This PID: '. getPid() ."\n";

  // Get the GDAX API and start making calls.
  $gdaxPrivate = new Api($config->api_url, $apiAuth);

  $start = getOpenConnections();
  $msg .= "[INFO] Api has been instantiated.\n";
  $msg .= "[INFO] Starting open connections to GDAX: $start\n";
  for ($i = 1; $i < 5; $i++) {
    // A private request.
    $msg .= "[INFO] Executing cancelAllOrders\n";
    $gdaxPrivate->cancelAllOrders($param = [
      'product_id' => 'BTC-USD'
    ]);
    $open = getOpenConnections();
    $msg .= "Open: $open, Started w/: $start, Calls: $i\n";
    
    if ($open - $start > 2) {
      throw Exception('Too many connections are open.');
    }
  }

  // Set the issue link for this example.
  $this->setIssueLink('https://github.com/mrteye/GDAX/issues/5'); //, 'reuse or destroy curl handles');

  return $msg;
});

// Display results if this file was loaded directly.
$example->show();

