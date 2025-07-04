<?php
class Test extends CI_Controller
{
  public $ms;

  public function __construct()
  {
    parent::__construct();
    // $this->ms = $this->load->database('ms', TRUE);
  }

  public function index($item_code = NULL)
  {
    $this->load->model('orders/reserv_stock_model');

    //--- skip mkp
    echo $item_code;
    echo "<br/>";
    echo $this->reserv_stock_model->get_reserv_stock($item_code, 'AFG-1112', TRUE);
    echo "<br/>";
    echo $this->reserv_stock_model->get_reserv_stock($item_code, 'AFG-1112', FALSE);
    echo "<br/>";
  }
}
 ?>
