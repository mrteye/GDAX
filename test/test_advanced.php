<?php
/** This example (#3 Advanced) extends the Api class. Create your own 
 * exchange logic.  
 */
use mrteye\Gdax\Api as Api;
use mrteye\Gdax\Auth as Auth;
use mrteye\Gdax\AppCurl as AppCurl;
use mrteye\Gdax\Exception as Exception;

use SomePHP\Test\Example as Example;


// Setup; All tests should be one level below doc root.
if (! $docRoot = $_SERVER['DOCUMENT_ROOT']) {
  $docRoot = dirname(dirname(__FILE__));
}
require_once "$docRoot/vendor/autoload.php";
require "$docRoot/config.php";


// Some additional code used in this example.
class MyBot extends Api {
  function __construct($private = false, $config) {
    // Create an authentication object if necessary.
    $auth = false;
    if ($private) {
      // TODO: Reminder; define values for key, secret, pass and gdax_time_api.
      // These values should be stored in an external file or other source.
      // - OR - you could simply hard code them here.
      $auth = new Auth(
        $config->key, 
        $config->secret, 
        $config->pass, 
        $config->time_url
      );
    }

    // Load the Gdax API.
    parent::__construct($config->api_url, $auth);

    // Set a different timeout for curl.
    $this->curl = new AppCurl(2000);
  }

  // ~ Add custom methods application methods...
}


// Run the example code; Throw an exception for errors; Return any output;
$example = new Example('An Exchange Bot (skeleton)', 
  function() use ($config) {

  // Example usage of the AppGdaxApi class
  $gdax = new MyBot(true, $config);
  $accounts = false;

  // Get all accounts and products.
  $accounts = $gdax->getAccounts();
  $products = $gdax->getProducts();

  $msg = print_r($accounts[0], true) . "\n";
  $msg .= print_r($products[0], true) . "\n";
  return $msg;
});

// Display results if this file was loaded directly.
$example->show();

