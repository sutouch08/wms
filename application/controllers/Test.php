<?php
class Test extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    echo date('Y-m-d H:i:s', 1743757200)."<br/>";
    echo date('Y-m-d H:i:s', 1743843600)."<br/>";
    echo date('Y-m-d H:i:s', 1743930000)."<br/>";
    echo date('Y-m-d H:i:s', 1744016400)."<br/>";
    echo date('Y-m-d H:i:s', 1744362000)."<br/>";
  }
}
 ?>
