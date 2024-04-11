<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WT extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $api = FALSE;
  private $path = "/rest/api/pos/WT/";

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

      $this->load->model('inventory/invoice_model');
      $this->load->model('inventory/transfer_model');
      $this->load->model('orders/orders_model');
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

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";
      $this->add_logs('WT', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    $order = $this->orders_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";
      $this->add_logs('WT', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($order->role != 'N')
    {
      $sc = FALSE;
      $this->error = "Invalid document type : {$code}";
      $this->add_logs('WT', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($order->state != 8)
    {
      $sc = FALSE;
      $this->error = "Invalid document status : document not shipping";
      $this->add_logs('WT', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($test != FALSE)
    {
      echo $this->mc
      ->where('U_ECOMNO', $code)
      ->group_start()
      ->where_in('F_Sap', array('N', 'D'))
      ->or_where('F_Sap IS NULL', NULL, FALSE)
      ->group_end()
      ->get_compiled_select('DFOWTR');

      exit();
    }

    $draft = $this->transfer_model->get_transfer_draft($code);

    if( empty($draft))
    {
      $sc = FALSE;
      $this->error = "The document was not found in the temp transfer draft.";
      $this->add_logs('WT', 'get', 'error', $this->error, $code);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $ds = array(
      'code' => $order->code,
      'customer_code' => $order->customer_code,
      'zone_code' => $order->zone_code,
      'is_received' => $order->is_valid == 1 ? 'Y' : 'N',
      'rows' => $this->invoice_model->get_details_summary_group_by_item($code)
    );

    $this->add_logs('WT', 'get', 'success', 'success', $code);
    $this->add_logs('WT', 'get', 'response', 'success', json_encode($ds));

    $this->response(['status' => TRUE, 'message' => 'success', 'data' => $ds], 200);
	}


  public function confirm_post()
  {
    $sc = TRUE;

    $json = file_get_contents("php://input");
		$ds = json_decode($json);

    if(empty($ds) OR empty($ds->code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->add_logs('WT', 'update', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
    $draft = $this->transfer_model->get_transfer_draft($ds->code);

    if( empty($draft))
    {
      $sc = FALSE;
      $this->error = "The document was not found in the temp transfer draft.";
      $this->add_logs('WT', 'update', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if(empty($draft->F_Receipt) OR $draft->F_Receipt == 'N' OR $draft->F_Receipt == 'D')
    {
      //---- ยืนยันรับสินค้า
      $this->mc->trans_begin();

      if( ! $this->transfer_model->confirm_draft_receipted($draft->DocEntry))
      {
        $sc = FALSE;
        $this->error = "Failed to update temp status";
      }

      $this->db->trans_begin();

      if( ! $this->orders_model->valid_transfer_draft($code))
      {
        $sc = FALSE;
        $this->error = "Failed to update confirm status";
      }

      if($sc === TRUE)
      {
        $this->mc->trans_commit();
        $this->db->trans_commit();
        $this->add_logs('WT', 'update', 'success', 'success', $json);
        $this->add_logs('WT', 'update', 'response','success', json_encode(['status' => TRUE, 'message' => 'success']));
        $this->response(['status' => TRUE, 'message' => 'success'], 200);
      }
      else
      {
        $this->mc->trans_rollback();
        $this->db->trans_rollback();
        $this->add_logs('WT', 'update', 'error', $this->error, $json);
        $this->response(['status' => FALSE, 'message' => $this->error], 200);
      }
    }
    else
    {
      $this->add_logs('WT', 'update', 'success', 'success', $json);
      $this->add_logs('WT', 'update', 'response','success', json_encode(['status' => TRUE, 'message' => 'success']));
      $this->response(['status' => TRUE, 'message' => 'success'], 200);
    }
  } //--- end confirm


  public function get_list_get($zone_code = NULL)
  {
    $sc = TRUE;

    if(empty($zone_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->add_logs('WT', 'get', 'error', $this->error, $zone_code);
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    if($sc === TRUE)
    {
      //---- check zone_code
      $count = $this->db
      ->from('zone AS z')
      ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
      ->where('w.role', 2)
      ->where('z.code', $zone_code)
      ->count_all_results();

      if($count != 1)
      {
        $sc = FALSE;
        $this->error = "Invalid zone code Or zone not in consignment warehouse";
        $this->add_logs('WT', 'get', 'error', $this->error, $zone_code);
        $this->response(['status' => FALSE, 'message' => $this->error], 400);
      }
    }

    if($sc === TRUE)
    {
      $rs = $this->db
      ->select('o.code, o.customer_code, o.zone_code, c.name AS customer_name, z.name AS zone_name')
      ->from('orders AS o')
      ->join('customers AS c', 'o.customer_code = c.code', 'left')
      ->join('zone AS z', 'o.zone_code = z.code', 'left')
      ->where('o.role', 'N')
      ->where('o.zone_code', $zone_code)
      ->where('o.state', 8)
      ->where('o.status', 1)
      ->where('o.is_valid', 0)
      ->where('o.is_cancled', 0)
      ->where('o.is_expired', 0)
      ->order_by('o.code', 'DESC')
      ->limit(100)
      ->get();

      $ds = array(
        'status' => TRUE,
        'message' => 'success',
        'count' => $rs->num_rows(),
        'data' => $rs->num_rows() > 0 ? $rs->result() : NULL
      );

      $this->add_logs('WT', 'get', 'success', 'success', $zone_code);
      $this->add_logs('WT', 'update', 'response','success', json_encode($ds));
      $this->response($ds, 200);
    }
  }


  public function add_logs($code = 'WT', $action = 'create', $status = 'error', $message = NULL, $json = NULL)
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
