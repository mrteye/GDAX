<?php
/** */
namespace SomePHP\Test;

/** */
class Example {
  public $results = [];
  public $issueLink = null;
  public $start;
  public $stop;

  /** */
  function __construct($title = false, $example = null) {
    if ($title && $example) {
      try {
        $this->startTimer();
        $example = \Closure::bind($example, $this, $this);
        $msg = $example();
        $this->stopTimer();
        $this->addResult(new Result([
          'status' => 'working',
          'title' => $title,
          'issueLink' => $this->issueLink,
          'file' => basename($this->_getFilename()),
          'fullpath' => $this->_getFilename(),
          'msg' => $msg,
          'time' => $this->getTime()
        ]));

      } catch(\Exception $e) {
        $this->stopTimer();
        $this->addResult(new Result([
          'title' => $title,
          'issueLink' => $this->issueLink,
          'file' => basename($this->_getFilename()),
          'fullpath' => $this->_getFilename(),
          'msg' => $e->getMessage(),
          'time' => $this->getTime()
        ]));
      }
    }
  }

  /** Timer Functions: for tracking example run time. */
  function startTimer() {
    $this->start = time();
  }
  function stopTimer() {
    $this->stop = time();
  }
  function getTime() {
    return date('H:i:s', ($this->stop - $this->start));
  }
  function setIssueLink($link, $title = false) {
    $title = ($title) ? $title: 
        str_replace('/', '#', substr($link, strrpos($link, '/', -1)));
    $this->issueLink = (object) [
      'title' => $title,
      'link' => $link
    ];
  }

  /** */
  function addResult(Result $result) {
    $this->results[] = $result;
  }

  /** Get the filename of the script that called this method. */
  function getFilename() {
    $debug = debug_backtrace();
    return $debug[0][file];
  }

  /** Get the filename of the script that is using this class. */
  private function _getFilename() {
    $debug = debug_backtrace();
    return $debug[1][file];
  }

  /** Text Report */
  function getTextReport() {
    $report = "Status\tTitle\tFile\tMessage\n";
    foreach ($this->results as $i) {
      if ($i->issueLink) {
        $issue = $i->issueLink->link;
      }
      $status = ($i->status == 'broken' ? 'broken': 'working');
      $report .= "[$status] {$i->title} - {$i->file}\n";
      $report .= "$issue\n";
      $report .= $i->msg ."\n". str_repeat('----', 10). "\n\n";
    }
    return $report;
  }

  /** Html Report */
  function getHtmlReport() {
    $report = '<style>
      .working-title {
       background-color: lightgreen;
      }
      .broken-title {
        background-color: lightcoral;
      }
      .td-scroll {
        overflow:auto;
        height:100px; 
      }
      .cell-wrap {
        overflow-wrap: break-word;
      }
      .link-col {
        width: 100px;
      }
      .border-top td {
        border-top: 1px dashed black;
      }
    </style>';

    $report .= '<p><h1>GDAX API (PHP, Unofficial)</h1>
        <a href="https://github.com/mrteye/GDAX">github.com/mrteye/GDAX</a>
      </h1></p>
      <table><tr>
        <td>Status</td>
        <td>Title</td>
        <td>File</td>
        <td>Run Time</td>
        <td>Message</td>
      </tr>';

    foreach ($this->results as $i) {
      $status = ($i->status == 'broken' ? 'broken': 'working');
      if ($i->issueLink) {
        $issue = "<a href=\"{$i->issueLink->link}\">{$i->issueLink->title}</a>";
      }
      $msgClass = (substr_count($i->msg, "\n") > 6) ? 'td-scroll': '';
      $msg = str_replace("\n", "<br>", $i->msg);
      $report .= "<tr class='border-top'>
          <td class='$status-title'>$status</td>
          <td>$issue - {$i->title}</td>
          <td>
            <div class='cell-wrap link-col'>
              <a href=\"/test/{$i->file}\">{$i->fullpath}</a>
            </div>
          </td>
          <td>{$i->time}</td>
          <td><div class='$msgClass'>{$msg}</div></td>
        </tr>";
    }
    $report .= '</table>';
    return $report;
  }

  /** Display the results if this example was loaded directly. */
  public function show($override = false) {
    $files = get_included_files();
    if (basename($files[0]) != 'index.php' || $override) {
      if ($_SERVER['DOCUMENT_ROOT']) {
        echo $this->getHtmlReport();
      } else {
        echo $this->getTextReport();
      }
    }
  }

}

