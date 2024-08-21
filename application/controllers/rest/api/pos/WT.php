<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WT extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $api = FALSE;
  public $logs_json = FALSE;
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
      $this->logs_json = is_true(getConfig('POS_LOG_JSON'));
      $this->user = "pos@warrix.co.th";

      $this->load->model('inventory/invoice_model');
      $this->load->model('inventory/transfer_model');
      $this->load->model('orders/orders_model');
      $this->load->model('rest/V1/pos_api_logs_model');
    }
    else
    {
      $this->response(['status' => FALSE, 'message' => "Access denied"], 400);
    }
  }

  //--- for POS
	public function get_get($code = NULL)
	{
    $api_path = $this->path."get/{$code}";

    $sc = TRUE;

    $json = file_get_contents("php://input");
		$ds = json_decode($json);
    $force = empty($ds) ? FALSE : ($ds->force ? TRUE : FALSE);

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
          'type' =>'WT',
          'code' => NULL,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $order = $this->orders_model->get($code);

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
          'type' =>'WT',
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

    if($order->role != 'N')
    {
      $sc = FALSE;
      $this->error = "Invalid document type : {$code}";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
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

    if($order->state != 8)
    {
      $sc = FALSE;
      $this->error = "Invalid document status : document not shipping";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
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

    if( ! $force)
    {
      $draft = $this->transfer_model->get_transfer_draft($code);

      if( empty($draft))
      {
        $sc = FALSE;
        $this->error = "The document was not found in the temp transfer draft.";

        $arr = array(
          'status' => FALSE,
          'message' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'WT',
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
    }

    $ds = array(
      'code' => $order->code,
      'customer_code' => $order->customer_code,
      'zone_code' => $order->zone_code,
      'is_received' => $order->is_valid == 1 ? 'Y' : 'N',
      'rows' => $this->invoice_model->get_details_summary_group_by_item($code)
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
        'type' =>'WT',
        'code' => $code,
        'action' => 'get',
        'status' => 'success',
        'message' => 'success',
        'request_json' => $json,
        'response_json' => json_encode($arr)
      );

      $this->pos_api_logs_model->add_api_logs($logs);
    }

    $this->response($arr, 200);
	}


  public function confirm_post()
  {
    $api_path = $this->path."confirm";

    $sc = TRUE;

    $json = file_get_contents("php://input");
		$ds = json_decode($json);

    if(empty($ds) OR empty($ds->code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
          'code' => NULL,
          'action' => 'confirm',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
    $draft = $this->transfer_model->get_transfer_draft($ds->code);

    if( empty($draft))
    {
      $sc = FALSE;
      $this->error = "The document was not found in the temp transfer draft.";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
          'code' => $ds->code,
          'action' => 'confirm',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
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

      if( ! $this->orders_model->valid_transfer_draft($ds->code))
      {
        $sc = FALSE;
        $this->error = "Failed to update confirm status";
      }

      if($sc === TRUE)
      {
        $this->mc->trans_commit();
        $this->db->trans_commit();

        $arr = array(
          'status' => TRUE,
          'message' => 'success'
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'WT',
            'code' => $ds->code,
            'action' => 'confirm',
            'status' => 'success',
            'message' => 'success',
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 200);
      }
      else
      {
        $this->mc->trans_rollback();
        $this->db->trans_rollback();

        $arr = array(
          'status' => FALSE,
          'message' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'WT',
            'code' => $ds->code,
            'action' => 'confirm',
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 200);
      }
    }
    else
    {
      $arr = array(
        'status' => TRUE,
        'message' => 'Document already confirmed'
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
          'code' => $ds->code,
          'action' => 'confirm',
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
  } //--- end confirm


  public function get_list_get($zone_code = NULL)
  {
    $api_path = $this->path."get_list/{$zone_code}";
    $json = NULL;

    $sc = TRUE;

    if(empty($zone_code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
          'code' => NULL,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
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

        $arr = array(
          'status' => FALSE,
          'message' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'WT',
            'code' => $zone_code,
            'action' => 'get',
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 400);
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

      $arr = array(
        'status' => TRUE,
        'message' => 'success',
        'count' => $rs->num_rows(),
        'data' => $rs->num_rows() > 0 ? $rs->result() : NULL
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WT',
          'code' => $zone_code,
          'action' => 'get',
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
  }

} //--- end class
?>
