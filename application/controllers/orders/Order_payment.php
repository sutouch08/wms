<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_payment extends PS_Controller
{
  public $menu_code = 'ACPMCF';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจสอบยอดชำระเงิน';
  public $filter;
	public $wms;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_payment';
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $this->load->helper('order');
    $this->load->helper('channels');
  }



  public function index()
  {
    $filter = array(
      'code'  => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'account' => get_filter('account', 'account', ''),
      'user'  => get_filter('user', 'user', ''),
      'channels' => get_filter('channels', 'channels', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date'  => get_filter('to_date', 'to_date', ''),
      'valid' => get_filter('valid', 'valid', 0)
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_payment_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->order_payment_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('orders/payment/order_payment_list', $filter);
  }




  public function get_payment_detail()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $detail = $this->order_payment_model->get_detail($id);
    if(!empty($detail))
    {
      $img = payment_image_url($detail->order_code);
      $bank   = $this->bank_model->get_account_detail($detail->id_account);
      $ds  = array(
        'id' => $detail->id,
        'orderAmount' => number($detail->order_amount,2),
        'payAmount' => number($detail->pay_amount,2),
        'payDate' => thai_date($detail->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($detail->date_upd, TRUE, '/'),
        'imageUrl' => $img
      );

      if($detail->valid == 0)
      {
        $ds['valid'] = 'no';
      }
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }




  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');
			$isAPI = is_true(getConfig('WMS_API'));
      $detail = $this->order_payment_model->get_detail($id);
			$order = $this->orders_model->get($detail->order_code);

      $arr = array(
        'order_code' => $detail->order_code,
        'state' => 3,
        'update_user' => get_cookie('uname')
      );

      //--- start transection
      $this->db->trans_begin();

      //--- mark payment as paid
      $this->order_payment_model->valid_payment($id);

      //--- mark order as paid
      $this->orders_model->paid($detail->order_code, TRUE);

			if($order->state < 3)
			{
				//--- change state to waiting for prepare
	      $this->orders_model->change_state($detail->order_code, 3);

	      //--- add state event
	      $this->order_state_model->add_state($arr);

				if($order->is_wms && $isAPI)
				{
					$this->wms = $this->load->database('wms', TRUE);
					$this->load->library('wms_order_api');

					$ex = $this->wms_order_api->export_order($order->code);

					if(! $ex)
					{
						$this->error = "ส่งข้อมูลไป WMS ไม่สำเร็จ <br/> (".$this->wms_order_api->error.")";
						$txt = "998 : This order no {$order->code} was already processed by PLC operation.";
						if($this->wms_order_api->error == $txt)
						{
							if($order->wms_export != 1)
							{
								$arr = array(
									'wms_export' => 1,
									'wms_export_error' => NULL
								);

								$this->orders_model->update($order->code, $arr);
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ กรุณาโหลดหน้าเว็บใหม่แล้วกดส่งข้อมูลอีกครั้ง : ".$this->wms_order_api->error;
							$arr = array(
								'wms_export' => 3,
								'wms_export_error' => $this->wms_order_api->error
							);

							$this->orders_model->update($order->code, $arr);
						}
					}
					else
					{
						$arr = array(
							'wms_export' => 1,
							'wms_export_error' => NULL
						);

						$this->orders_model->update($order->code, $arr);
					}
				}
				
				//---- send api to chatbot
				if($order->is_api == 1 && !empty($order->reference))
				{
					$this->logs = $this->load->database('logs', TRUE);
					$this->load->library('chatbot_api');
					$arr = array(
						"order_number" => $order->reference,
						"amount" => round($detail->pay_amount, 2),
						"action" => "approve"
					);

					$this->chatbot_api->approve_payment($arr);
				}
			}

      //--- complete transecrtion with commit or rollback if any error
			if($sc === TRUE)
			{
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบรายการชำระเงิน';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');
      $detail = $this->order_payment_model->get_detail($id);
			$order = $this->orders_model->get($detail->order_code);

      $arr = array(
        'order_code' => $detail->order_code,
        'state' => 2,
        'update_user' => get_cookie('uname')
      );

      //--- start transection
      $this->db->trans_start();

      //--- mark payment as unpaid
      $this->order_payment_model->un_valid_payment($id);

      //--- mark order as unpaid
      $this->orders_model->paid($detail->order_code, FALSE);

			if($order->state != 8 && $order->state != 9)
			{
	      //--- change state to waiting for payment
	      $this->orders_model->change_state($detail->order_code, 2);

	      //--- add state event
	      $this->order_state_model->add_state($arr);
			}

	    //--- complete transecrtion with commit or rollback if any error
	    $this->db->trans_complete();

	    //--- check for any error
	    if($this->db->trans_status() === FALSE)
	    {
	      $sc = FALSE;
	      $message = $this->db->error();
	    }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบรายการชำระเงิน';
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function remove_payment()
  {
    $sc = TRUE;
    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');
      $detail = $this->order_payment_model->get_detail($id);

      if(!empty($detail))
      {
				$order = $this->orders_model->get($detail->order_code);

        //--- start transection
        $this->db->trans_start();

        //--- mark order as unpaid
        $this->orders_model->paid($detail->order_code, FALSE);

				if($order->state != 8 && $order->state != 9)
				{
	        //--- change state to pending
	        $this->orders_model->change_state($detail->order_code, 1);

	        //--- add state event
	        $arr = array(
	          'order_code' => $detail->order_code,
	          'state' => 1,
	          'update_user' => get_cookie('uname')
	        );

	        $this->order_state_model->add_state($arr);
				}

        //--- now remove payment row
        $this->order_payment_model->delete($id);

        //--- end transection commit if all success or rollback if any error
        $this->db->trans_complete();

        //--- check for any error
        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $message = $this->db->error();
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'ไม่พบรายการชำระเงิน';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบตัวแปร id กรุณา reload หน้าเว็บแล้วลองใหม่';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function clear_filter()
  {
    $filter = array('code', 'account', 'user', 'channels','from_date', 'to_date', 'customer', 'valid');
    clear_filter($filter);
  }
} //--- end class

?>
