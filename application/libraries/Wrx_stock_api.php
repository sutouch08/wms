<?php

class Wrx_stock_api
{
  private $url;
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = TRUE;
  public $test = FALSE;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wrx_api_logs_model');
    $this->ci->load->model('stock/stock_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('orders/reserv_stock_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }


  //--- Full function
  public function update_available_stock(array $items = array(), $warehouse_code)
  {
    $action = "update stock";
    $type = "Stock";
    $url = $this->api['WRX_API_HOST'];
    $url .= "wms/updateStock";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    if( ! empty($items) && ! empty($warehouse_code))
    {
      $data = [];
      $skus = [];

      foreach($items as $item)
      {
        $skus[] = $item->code;
      }

      $skip_mkp = TRUE; //--- skip marketplace reserv stock
      $stock = $this->ci->stock_model->get_sell_items_stock($skus, $warehouse_code);
      $ordered = $this->ci->orders_model->get_items_reserv_stock($skus, $warehouse_code);
      $reserved = $this->ci->reserv_stock_model->get_items_reserv_stock($skus, $warehouse_code, $skip_mkp);

      foreach($items as $item)
      {
        if( ! empty($stock))
        {
          $rate = $item->rate > 0 ? ($item->rate < 100 ? $item->rate * 0.01 : 1) : 1;
          $sell_stock = empty($stock[$item->code]) ? 0 : intval($stock[$item->code]);
          $order_qty = empty($ordered[$item->code]) ? 0 : intval($ordered[$item->code]);
          $reserv_qty = empty($reserved[$item->code]) ? 0 : intval($reserved[$item->code]);
          $available = $sell_stock - $order_qty - $reserv_qty;
          $receive_qty = empty($item->receive_qty) ? 0 : intval(floor($item->receive_qty));
          $qty = intval(floor(($available + $receive_qty) * $rate));

          $data[] = array(
            'sku' => $item->code,
            'stock' => $qty,
            'sellableStock' => $qty
          );
        }
        else
        {
          $data[] = array(
            'sku' => $item->code,
            'stock' => 0,
            'sellableStock' => 0
          );
        }
      }

      if( ! empty($data))
      {
        $req = array("stockList" => $data);

        $json = json_encode($req);

        if($this->test === TRUE)
        {
          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $type,
              'api_path' => $api_path,
              'code' => NULL,
              'action' => 'test',
              'status' => 'test',
              'message' => 'test',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->wrx_api_logs_model->add_logs($logs);
          }

          return TRUE;
        }
        else
        {
          $logs = array(
            'trans_id' => genUid(),
            'type' => $type,
            'api_path' => $api_path,
            'code' => NULL,
            'action' => $action,
            'status' => 'success',
            'message' => 'test logs',
            'request_json' => $json,
            'response_json' => NULL
          );

          $this->ci->wrx_api_logs_model->add_logs($logs);

          $cmd = "curl -X POST {$apiUrl}"
          ." -H 'Content-Type:application/json'"
          ." -H 'Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}'"
          ." -d '" . $json . "'"
          ." > /dev/null 2>&1 &";
          exec($cmd);
          return TRUE;
        }
      }
    }
    else
    {
      $this->error = "Missing required parameter";
      return FALSE;
    }
  }


  //--- TEST

  public function test_update_available_stock(array $items = array(), $warehouse_code)
  {
    $action = "update stock";
    $type = "Stock";
    $url = $this->api['WRX_API_HOST'];
    $url .= "wms/updateStock";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    if( ! empty($items) && ! empty($warehouse_code))
    {
      $data = [];
      $skus = [];

      foreach($items as $item)
      {
        $skus[] = $item->code;
      }

      echo "get-stock: ".now()."<br/>";
      $stock = $this->ci->stock_model->get_sell_items_stock($skus, $warehouse_code);
      echo "get-orderd: ".now()."<br/>";
      $ordered = $this->ci->orders_model->get_items_reserv_stock($skus, $warehouse_code);
      echo "get-reserv:".now()."<br/>";
      $skip_mkp = TRUE;
      $reserved = $this->ci->reserv_stock_model->get_items_reserv_stock($skus, $warehouse_code, $skip_mkp);

      echo "build-data: ".now()."<br/>";
      foreach($items as $item)
      {
        if( ! empty($stock))
        {
          $rate = $item->rate > 0 ? ($item->rate < 100 ? $item->rate * 0.01 : 1) : 1;
          $sell_stock = empty($stock[$item->code]) ? 0 : intval($stock[$item->code]);
          $order_qty = empty($ordered[$item->code]) ? 0 : intval($ordered[$item->code]);
          $reserv_qty = empty($reserved[$item->code]) ? 0 : intval($reserved[$item->code]);
          $available = $sell_stock - $order_qty - $reserv_qty;
          $receive_qty = empty($item->receive_qty) ? 0 : intval(floor($item->receive_qty));
          $qty = intval(floor(($available + $receive_qty) * $rate));

          $data[] = array(
            'sku' => $item->code,
            'stock' => $qty,
            'sellableStock' => $qty
          );
        }
      }

      if( ! empty($data))
      {
        $req = array("stockList" => $data);

        $json = json_encode($req);

        if($this->test === TRUE)
        {
          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $type,
              'api_path' => $api_path,
              'code' => NULL,
              'action' => 'test',
              'status' => 'test',
              'message' => 'test',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->wrx_api_logs_model->add_logs($logs);
          }

          return TRUE;
        }
        else
        {
          echo "Start API : ".now()."<br/>";

          $cmd = "curl -X POST {$apiUrl}"
          ." -H 'Content-Type:application/json'"
          ." -H 'Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}'"
          ." -d '" . $json . "'"
          ." > /dev/null 2>&1 &";
          //echo $cmd ."<br/>";
          exec($cmd, $output, $exit);
          print_r($output);
          echo "End Api : ".now()."<br/>";

          // return TRUE;
        }

        return TRUE;
      }
    }
    else
    {
      $this->error = "Missing required parameter";
      return FALSE;
    }
  }


  public function get_available_stock($item_code, $warehouse = NULL)
  {
    $skip_mkp = TRUE;
    $sell_stock = $this->ci->stock_model->get_sell_stock($item_code, $warehouse);
    $ordered = $this->ci->orders_model->get_reserv_stock($item_code, $warehouse);
    $reserv_stock = $this->ci->reserv_stock_model->get_reserv_stock($item_code, $warehouse, $skip_mkp);
    $availableStock = $sell_stock - $ordered - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }

} //-- end class


 ?>
