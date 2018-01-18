<?php
/** */
namespace SomePHP\Test;
use SomePHP\Hydrator;

/** */
class Result extends Hydrator {
  public $status = 'broken';
  public $title = '';
  public $issueLink = null;
  public $file = '';
  public $fullpath = '';
  public $time = '';
  public $msg = '';


  /** */
  function __construct($obj = []) {
    parent::__construct($obj);
  }

}

