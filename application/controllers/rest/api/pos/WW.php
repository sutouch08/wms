<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WW extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $api = FALSE;
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
      $this->log_json = is_true(getConfig('POS_LOG_JSON'));
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
    $sc = TRUE;
    $rows = array();

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";
      $this->add_logs('WW', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    $order = $this->transfer_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";
      $this->add_logs('WW', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }


    if($order->status != 1)
    {
      $sc = FALSE;
      $this->error = "Invalid document status";
      $this->add_logs('WW', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $details = $this->transfer_model->get_details($code);

    if( empty($details))
    {
      $sc = FALSE;
      $this->error = "No item in document.";
      $this->add_logs('WW', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
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

    $this->add_logs('WW', 'get', 'success', 'success', $code);
    $this->add_logs('WW', 'get', 'response', 'success', json_encode($ds));

    $this->response(['status' => TRUE, 'message' => 'success', 'data' => $ds], 200);
	}



  public function add_logs($code = 'WW', $action = 'create', $status = 'error', $message = NULL, $json = NULL)
  {
    if($this->log_json)
    {
      $log = array(
        'trans_id' => genUid(),
        'api_path' => $this->path,
        'code' => $code,
        'action' => $action,
        'status' => $status,
        'message' => $message,
        'json_text' => ($this->log_json ? $json : NULL)
      );

      $this->order_api_logs_model->logs_pos($log);
    }

    return TRUE;
  }

} //--- end class
?>
