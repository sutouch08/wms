<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WW extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $api = FALSE;
  public $logs_json = FALSE;
  private $path = "/rest/api/pos/WW/";

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->logs = $this->load->database('logs', TRUE); //--- api logs database
      $this->logs_json = is_true(getConfig('POS_LOG_JSON'));
      $this->user = "pos@warrix.co.th";

      $this->load->model('inventory/transfer_model');
      $this->load->model('rest/V1/order_api_logs_model');
    }
    else
    {
      $this->response(['status' => FALSE, 'message' => "Access denied"], 400);
    }
  }

  //--- for POS
	public function get_get($code = NULL, $test = FALSE)
	{
    $api_path = $this->path."get";

    $sc = TRUE;
    $rows = array();

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => NULL,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $order = $this->transfer_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }


    if($order->status != 1)
    {
      $sc = FALSE;
      $this->error = "Invalid document status";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $details = $this->transfer_model->get_details($code);

    if( empty($details))
    {
      $sc = FALSE;
      $this->error = "No item in document.";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      foreach($details as $rs)
      {
        $qty = ($order->is_wms == 1 && $order->api == 1) ? $rs->wms_qty : $rs->qty;
        $row = new stdClass();
        $row->product_code = $rs->product_code;
        $row->product_name = $rs->product_name;
        $row->unit_code = $rs->unit_code;
        $row->qty = $qty;
        $row->from_zone = $rs->from_zone;
        $row->to_zone = $rs->to_zone;

        array_push($rows, $row);
      }
    }

    $ds = array(
      'code' => $order->code,
      'from_warehouse' => $order->from_warehouse,
      'to_warehouse' => $order->to_warehouse,
      'doc_date' => $order->date_add,
      'shipped_date' => $order->shipped_date,
      'rows' => $rows
    );

    $arr = array(
      'status' => TRUE,
      'message' => 'success',
      'data' => $ds
    );

    if($this->logs_json)
    {
      $logs = array(
        'trans_id' => genUid(),
        'api_path' => $api_path,
        'type' =>'WW',
        'code' => $code,
        'action' => 'get',
        'status' => 'success',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
      );

      $this->pos_api_logs_model->add_api_logs($logs);
    }

    $this->response($arr, 200);
	}

} //--- end class
?>
