<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Stock extends REST_Controller
{
  public $ms;
  public $error;
	public $api = FALSE;
  public $whsCode = NULL;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE);
    $this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
			$this->load->model('stock/stock_model');
	    $this->load->model('orders/orders_model');
      $this->whsCode = getConfig('IX_WAREHOUSE');
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied"
			);

			$this->response($arr, 400);
		}
  }


	public function index_get($code = NULL)
  {
    if( ! empty($code))
    {
      $code = trim($code);
			$sell_stock = $this->stock_model->get_sell_stock($code, $this->whsCode);
			$reserv_stock = $this->orders_model->get_reserv_stock($code, $this->whsCode);
			$availableStock = $sell_stock - $reserv_stock;
			$stock = $availableStock < 0 ? 0 : $availableStock;

			$ds = array(
				'status' => 'success',
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
        'status' => FALSE,
        'error' => 'Missing required parameter : sku_code'
      );

      $this->response($ds, 400);
    }
  }


  public function index_post()
  {
    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );
      $this->response($arr, 400);
    }

    if(empty($data->items))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty items'
      );
      $this->response($arr, 400);
    }


    if( ! empty($data->items))
    {
      $count = count($data->items);

      if($count > 100)
      {
        $ds = array(
          'status' => 'FALSE',
          'error' => 'Requested items are over limited items per request ('.$count.'/100)'
        );

        $this->response($ds, 400);
      }
      else
      {
        $stocks = array();
        $items = 0;

        foreach($data->items as $item)
        {
          $code = $item->item_code;
          $sell_stock = $this->stock_model->get_sell_stock($code, $this->whsCode);
          $reserv_stock = $this->orders_model->get_reserv_stock($code, $this->whsCode);
          $availableStock = $sell_stock - $reserv_stock;
          $stock = $availableStock < 0 ? 0 : $availableStock;

          $stocks[] = array(
          'item_code' => $code,
          'qty' => $stock
          );

          $items++;
        }

        $ds = array(
        'status' => 'success',
        'request_items' => $count,
        'result_items' => $items,
        'data' => $stocks
        );

        $this->response($ds, 200);
      }

    }
    else
    {
      $ds = array(
      'status' => 'FALSE',
      'error' => 'Missing required parameter : items'
      );

      $this->response($ds, 400);
    }
  }
}// End Class
