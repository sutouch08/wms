<?php
class Test extends CI_Controller
{
  public $ms;
  public $error = NULL;

  public function __construct()
  {
    parent::__construct();
    // $this->ms = $this->load->database('ms', TRUE);
  }

  public function index()
  {
    $i = str_replace(",", "", "3,000");
    $i = ($i == "" OR $i == NULL) ? 0 : $i;
    $i = is_numeric($i) ? $i : "x";
    echo $i;
  }
}
 ?>
