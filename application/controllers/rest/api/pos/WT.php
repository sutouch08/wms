<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WT extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $api = FALSE;

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->load->model('inventory/invoice_model');
      $this->load->model('inventory/transfer_model');
      $this->load->model('orders/orders_model');

      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
    }
    else
    {
      $this->response(['status' => FALSE, 'message' => "Access denied"], 400);
    }
  }

  //--- for POS
	public function get_get($code = NULL)
	{
    $sc = TRUE;

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    $order = $this->orders_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($order->role != 'N')
    {
      $sc = FALSE;
      $this->error = "Invalid document type : {$code}";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($order->state != 8)
    {
      $sc = FALSE;
      $this->error = "Invalid document status : document not shipping";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $draft = $this->transfer_model->get_transfer_draft($code);

    if( empty($draft))
    {
      $sc = FALSE;
      $this->error = "The document was not found in the temp transfer draft.";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $ds = array(
      'code' => $order->code,
      'customer_code' => $order->customer_code,
      'zone_code' => $order->zone_code,
      'is_received' => $order->is_valid == 1 ? 'Y' : 'N',
      'rows' => $this->invoice_model->get_details_summary_group_by_item($code)
    );

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
      $this->response(['status' => FALSE, 'message' => $this->error], 400);
    }

    //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
    $draft = $this->transfer_model->get_transfer_draft($ds->code);

    if( empty($draft))
    {
      $sc = FALSE;
      $this->error = "The document was not found in the temp transfer draft.";
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
        $this->response(['status' => TRUE, 'message' => 'success'], 200);
      }
      else
      {
        $this->mc->trans_rollback();
        $this->db->trans_rollback();
        $this->response(['status' => FALSE, 'message' => $this->error], 200);
      }
    }
    else
    {
      $this->response(['status' => TRUE, 'message' => 'success'], 200);
    }
  } //--- end confirm

} //--- end class
?>
