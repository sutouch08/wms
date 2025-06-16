<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
  public $ms;
  public $title = "Dashboard";

  public function __construct()
  {
    parent::__construct();

    $this->ms = $this->load->database('ms', TRUE);
  }


  public function index()
  {
    $this->load->view('inventory/dashboard/dashboard');
  }


} //--- end class
?>
