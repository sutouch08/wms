<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_barcode extends PS_Controller
{
  public $menu_code = 'DBPROD';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('masters/product_barcode_model');
  }



  public function valid_barcode()
  {
    $barcode = $this->input->post('barcode');
    $id = $this->input->post('id');

    $rs = $this->product_barcode_model->is_exists($barcode, $id);

    echo $rs === TRUE ? 'exists' : 'ok';
  }

}//--- end class
  ?>
