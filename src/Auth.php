<?php
namespace mrteye\Gdax;
use Curl\Curl;

// Requires Curl
class Auth {
  function __construct($key, $secret, $pass, $timeApi = false) {
    $this->key = $key;
    $this->secret = $secret;
    $this->pass = $pass;
    $this->gdaxTimeApi = $timeApi;
  }

  public function setTime() {
    if ($this->gdaxTimeApi) {
      $curl = new AppCurl(); 
      $curl->get($this->gdaxTimeApi);

      if ($curl->error) {
        throw new \Exception("getTime: $curl->error_message");
      }
      $timeSet = json_decode($curl->response);
      $this->timestamp = $timeSet->epoch;
    } else {
      $this->timestamp = time();
    }
  }

  public function getAuthHeaders($path, $body, $method = 'GET') {
    $this->setTime();

    if ($method == 'GET' && ! empty($body)) {
      $path .= '?'. http_build_query($body);
      $body = '';
    } else {
      $body = is_array($body) ? json_encode($body) : $body;
   }

    $what = implode([$this->timestamp, $method, '/'. $path, $body]);
    $sig = base64_encode(hash_hmac(
        "sha256", $what, base64_decode($this->secret), true));

    $headers = [
        "CB-ACCESS-KEY" => $this->key,
        "CB-ACCESS-SIGN" => $sig,
        "CB-ACCESS-TIMESTAMP" => $this->timestamp,
        "CB-ACCESS-PASSPHRASE" => $this->pass
    ];

    return $headers;
  }
}
