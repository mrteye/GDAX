<?php
namespace mrteye\Gdax;

class Hydration {
  function __construct(array $properties) {
    foreach($properties as $prop => $value) {
      if (property_exists($this, $prop)) {
        $this->$prop = $value;
      }
    }
  }

  function extract() {
    return get_object_vars($this);
  }
}

