<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Stock_zone extends REST_Controller
{
  public $ms;
  public $cn;
  public $error;
  public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->load->model('masters/zone_model');
      $this->load->model('stock/stock_model');
    }
    else
    {
      $this->response(['status' => FALSE, 'error' => "Access denied"], 400);
    }
  }


  public function countItems_get()
  {
    $sc = TRUE;

    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data) OR empty($data->zone_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    $zone = $this->zone_model->get($data->zone_code);

    if( empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid zone code";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $count = 0;

    if($zone->is_consignment)
    {
      $this->cn = $this->load->database('cn', TRUE);
      $count = $this->stock_model->count_items_consignment_zone($zone->code);
    }
    else
    {
      $this->ms = $this->load->database('ms', TRUE);
      $count = $this->stock_model->count_items_zone($zone->code);
    }

    $this->response(['status' => TRUE, 'message' => 'success', 'count' => $count], 200);
  }



  //-- for POS
  public function getStock_get()
  {
    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(empty($data->zone_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : zone code";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(! isset($data->limit) OR ! isset($data->offset))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter: 'limit' OR 'offset'";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    $limit = intval($data->limit);
    $offset = intval($data->offset);

    $zone = $this->zone_model->get($data->zone_code);

    if( empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid zone code";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $result = NULL;

    if($zone->is_consignment)
    {
      $this->cn = $this->load->database('cn', TRUE);
      $result = $this->stock_model->getAllStockInConsignmentZone($zone->code, $limit, $offset);
    }
    else
    {
      $this->ms = $this->load->database('ms', TRUE);
      $result = $this->stock_model->getAllStockInZone($zone->code, $limit, $offset);
    }

    $this->response(['status' => TRUE, 'message' => 'success', 'count' => count($result), 'data' => $result], 200);
  }


  //-- for POS
  public function getItemStock_get()
  {
    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(empty($data->zone_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : zone code";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(empty($data->product_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : product_code";
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    $zone = $this->zone_model->get($data->zone_code);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid zone code";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $result = NULL;

    if($zone->is_consignment)
    {
      $this->cn = $this->load->database('cn', TRUE);
      $qty = $this->stock_model->get_consign_stock_zone($zone->code, $data->product_code);
    }
    else
    {
      $this->ms = $this->load->database('ms', TRUE);
      $qty = $this->stock_model->get_stock_zone($zone->code, $data->product_code);
    }

    $arr = array(
      'status' => TRUE,
      'zone' => $zone->code,
      'product_code' => $data->product_code,
      'qty' => $qty
    );

    $this->response($arr, 200);
  }
}// End Class
