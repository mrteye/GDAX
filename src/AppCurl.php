<?php
namespace mrteye\Gdax;
/**
*
*
*/
class AppCurl extends \Curl\Curl {
  private $timeout;

  /** */
  function __construct($timeout = 5000) {
    parent::__construct();
    $this->timeout = $timeout;

    // Get quicker feedback when the API is down. (sandbox or other)
    $this->setOpt(CURLOPT_TIMEOUT_MS, $timeout);
  }

  /** Reset the curl connection. */
  public function resetConnection() {
    $this->reset();

    // Get quicker feedback when the API is down. (sandbox or other)
    $this->setOpt(CURLOPT_TIMEOUT_MS, $this->timeout);

    $this->setHeader("Content-Type", "application/json");
  }

}

