<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Stock extends REST_Controller
{
  public $ms;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE);

    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');
  }


  public function get_get($code = NULL)
  {
    if(!empty($code))
    {
      $code = trim($code);
      $item = $this->products_model->get($code);
      if(!empty($item))
      {
        $sell_stock = $this->stock_model->get_sell_stock($code);
        $reserv_stock = $this->orders_model->get_reserv_stock($code);
        $availableStock = $sell_stock - $reserv_stock;
        $stock = $availableStock < 0 ? 0 : $availableStock;

        $ds = array(
          'status' => 'SUCCESS',
          'data' => array(
            'item_code' => $code,
            'qty' => $stock
          )
        );

        $this->response($ds, 200);
      }
      else
      {
        $ds = array(
          'status' => 'FALSE',
          'message' => 'Invalid SKU'
        );

        $this->response($ds, 200);
      }

    }
    else
    {
      $ds = array(
        'status' => 'FALSE',
        'message' => 'Missing required parameter : sku_code'
      );

      $this->response($ds, 400);
    }

  }



}// End Class
