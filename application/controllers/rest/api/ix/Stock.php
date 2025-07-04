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
      $this->load->model('masters/products_model');
			$this->load->model('stock/stock_model');
	    $this->load->model('orders/orders_model');
      $this->load->model('orders/reserv_stock_model');
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
      $item = $this->products_model->get($code);

      if(empty($item))
      {
        $ds = array(
          'status' => FALSE,
          'error' => 'Invalid SKU Code'
        );

        $this->response($ds, 400);
      }

      $rate = $item->api_rate <= 0 ? 1 : ($item->api_rate > 100 ? 1 : round($item->api_rate * 0.01, 2));
      $sell_stock = floatval($this->stock_model->get_sell_stock($code, $this->whsCode));
      $ordered = round(floatval($this->orders_model->get_reserv_stock($code, $this->whsCode)), 2);
      $reserv_stock = round(floatval($this->reserv_stock_model->get_reserv_stock($code, $this->whsCode)), 2);
      $availableStock = $sell_stock - $ordered - $reserv_stock;
      $stock = $availableStock < 0 ? 0 : $availableStock;

      $ds = array(
        'status' => 'success',
        'warehouse' => $this->whsCode,
        'data' => array(
          'item_code' => $code,
          'is_api' => $item->is_api == 1 ? 'Y' : 'N',
          'api_rate' => $rate,
          'on_hand' => $sell_stock,
          'ordered' => $ordered,
          'reserved' => $reserv_stock,
          'available' => $stock,
          'qty' => intval(floor($stock * $rate))
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
      $limit = 300;
      $count = count($data->items);

      if($count > $limit)
      {
        $ds = array(
          'status' => 'FALSE',
          'error' => "Requested items are exceeds limit {$limit} per request."
        );

        $this->response($ds, 400);
      }
      else
      {
        $warehouse_code = (isset($data->warehouse) && ! empty($data->warehouse)) ? $data->warehouse : $this->whsCode;

        $items = [];
        $stocks = [];
        $orders = [];
        $items_in = [];

        foreach($data->items as $item)
        {
          $items_in[] = $item->item_code;
        }

        if( ! empty($items_in))
        {
          $items = $this->products_model->get_products_in($items_in);
          $stocks = $this->stock_model->get_sell_items_stock($items_in, $warehouse_code);
          $orders = $this->orders_model->get_items_reserv_stock($items_in, $warehouse_code);
          $reservs = $this->reserv_stock_model->get_items_reserv_stock($items_in, $warehouse_code);
        }

        $res = [];
        $results = 0;

        foreach($items_in as $code)
        {
          if( ! empty($items[$code]))
          {
            $pd = $items[$code];
            $rate = $pd->api_rate <= 0 ? 1 : ($pd->api_rate > 100 ? 1 : round($pd->api_rate * 0.01, 2));
      			$sell_stock = isset($stocks[$code]) ? intval($stocks[$code]) : 0;
      			$ordered = isset($orders[$code]) ? intval($orders[$code]) : 0;
            $reserv_stock = isset($reservs[$code]) ? intval($reservs[$code]) : 0;
      			$availableStock = $sell_stock - $ordered - $reserv_stock;
      			$stock = $availableStock < 0 ? 0 : $availableStock;

            $res[] = array(
              'status' => 'success',
              'error' => NULL,
              'item_code' => $code,
              'is_api' => $pd->is_api == 1 ? 'Y' : 'N',
              'api_rate' => $rate,
              'on_hand' => $sell_stock,
              'ordered' => $ordered,
              'reserved' => $reserv_stock,
              'available' => $stock,
              'qty' => intval(floor($stock * $rate))
            );
          }
          else
          {
            $res[] = array(
              'status' => 'failed',
              'error' => 'Invalid SKU Code',
              'item_code' => $code,
              'is_api' => 'N',
              'api_rate' => 0,
              'on_hand' => 0,
              'ordered' => 0,
              'reserved' => 0,
              'available' => 0,
              'qty' => 0
            );
          }

          $results++;
        }

        $ds = array(
          'status' => 'success',
          'request_items' => $count,
          'result_items' => $results,
          'warehouse' => $warehouse_code,
          'data' => $res
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
