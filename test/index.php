<?php
// Set the base path; run composers autoload.
if (! $docRoot = $_SERVER['DOCUMENT_ROOT']) {
  // Repo clone - root path - no web server.
  $docRoot = dirname(dirname(__FILE__));
}
require_once "$docRoot/vendor/autoload.php";

// ~ Create credentials on the gdax site -> api section.
// ~ copy config.php.example to $docRoot/config.php.
require_once "$docRoot/config.php";

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

// Display results of previous examples.
if ($test) {
  foreach($test as $fail) {
    $path = basename($fail->name);
    $file_link = "<a href=\"file://{$fail->name}\">$path</a>";

    echo "$file_link | <b>{$fail->msg}</b> | Details<hr>";
    echo '<pre>'. print_r($fail->detail, true) .'</pre>';
  }
}

