<?php
/** */
namespace SomePHP;

/**
 * Copy object or array parameters into this object.
 * Use for data models, and parameter classes.
 */
class Hydrator {
  function __construct($obj = []) {
    // If null or false is passed in, throw a error.
    if (! $obj && ! is_array($obj)) {
      $p = get_called_class();
      echo "\n*** Hydrator($p): An object or array is expected.\n\n";
      return;
    }

    // Hydrate either objects or arrays into this object.
    foreach ($obj as $prop => $val) {
      if (property_exists($this, $prop)) {
        // Hydrate sub objetcs.
        if (is_object($this->$prop) &&
            method_exists($this->$prop, '__construct')) { 
          $this->$prop->__construct($val);
        } else {
          $this->$prop = $val;
        }
      }
    }
  }

  // Used by var_export to save objects.
  public static function __set_state($obj) {
    $class = get_called_class();
    $ret = new $class([]);
    foreach ($obj as $prop => $val) {
      if (property_exists($class, $prop)) {
        $ret->$prop = $val;
      }
    }

    return $ret;
  }

}

