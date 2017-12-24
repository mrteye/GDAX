<?php
// Set the base path; run composers autoload.
$basePath = dirname(dirname(__FILE__));
require_once "$basePath/vendor/autoload.php";

// ~ Create credentials on the gdax site -> api section.
// ~ copy config.php.example to config.php.
require_once "$basePath/config.php";
/***
<?php
// Example Config File
$config = array(
  'key' => '',
  'secret' => '',
  'pass' => '',

  'api_url' => 'https://api-public.sandbox.gdax.com',
  'time_url' => 'https://api-public.sandbox.gdax.com/time'
);
***/
$time = time();
$test = false;
include 'test_basic_public.php';
include 'test_basic_private.php';
include 'test_advanced.php';

// Print any failed tests.
$t2 = time();
$sec = ($t2 - $time) % 60;
$min = (int) (($t2 - $time) / 60);
echo "<pre>
Time: ". date('i:s', $t2 - $time). "
</pre>";

if ($test) {

  foreach($test as $fail) {
    echo '<pre>';
    echo $fail->name .' - '. $fail->msg;
    echo 'Errors: <pre>'. print_r($fail->detail, true) .'</pre>';
  }
}

