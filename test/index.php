<?php
/** This set of examples shows usage of the GDAX API.  It contains all
 * test cases in the test folder.  Some of these examples represent 
 * test cases for issues that have occurred (and are tracked on github.com.)
 *
 * These examles are used for tutorial and testing.
 *
 * All examples should be placed in the test folder and should have a prefix
 * of test_.
 *
 */
use SomePHP\Test\Example;
if (! $docRoot = $_SERVER['DOCUMENT_ROOT']) {
  // Repo clone - root path - no web server.
  $docRoot = dirname(dirname(__FILE__));
}
require_once "$docRoot/vendor/autoload.php";
require_once "$docRoot/config.php";
$time = time();

$examples = new Example();
// Examples automatically track run time if not not empty; Example();
$examples->startTimer();
$tests = scandir("$docRoot/test");
array_walk($tests, function($path) use($examples) {
  if (substr($path, -3, 3) == 'php') {
    if ($path == 'index.php') {
      return;
    }

    // Run the example file; it should set an $example object.
    include $path;
    if (isset($example)) {
      $examples->results = array_merge($examples->results, $example->results);
    }
  } 
});
$examples->stopTimer();
$examples->show(true);
echo "Total Time: ". $examples->getTime();

