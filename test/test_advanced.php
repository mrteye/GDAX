<?php
// Example #3 Advanced. Extend the Api class. Create your own exchange logic.
// This example is loaded from index.php file which sets an autoload and
// creates $basePath and $config variables.

use mrteye\Gdax\Api;
use mrteye\Gdax\Auth;
use mrteye\Gdax\AppCurl;

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

// Example usage of the AppGdaxApi class
$gdax = new MyBot(true, $config);
$accounts = false;

// Detail debugging is on by default.
//$gdax->setDebug(true);

try {
  // Get all accounts and products.
  $accounts = $gdax->getAccounts();
  $products = $gdax->getProducts();
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
