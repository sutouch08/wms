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
  public $wmsApi;
  public $sokoApi;
  public $soko_user = 'api@sokochan';
  public $wms_user = 'api@wms';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_payment';
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $this->load->helper('order');
    $this->load->helper('channels');

    $this->wmsApi = is_true(getConfig('WMS_API'));
    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));
  }



  public function index()
  {
    $filter = array(
      'code'  => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'account' => get_filter('account', 'account', 'all'),
      'user'  => get_filter('user', 'user', ''),
      'channels' => get_filter('channels', 'channels', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date'  => get_filter('to_date', 'to_date', ''),
      'valid' => get_filter('valid', 'valid', '0'),
      'is_pre_order' => get_filter('is_pre_order', 'is_pre_order', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
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
      $orders   = $this->order_payment_model->get_list($filter, $perpage, $this->uri->segment($segment));

      $filter['orders'] = $orders;

      $this->pagination->initialize($init);
      $this->load->view('orders/payment/order_payment_list', $filter);
    }
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
      $detail = $this->order_payment_model->get_detail($id);
			$order = $this->orders_model->get($detail->order_code);
      $is_wms = $order->is_wms;

      $is_api = $is_wms == 0 ? FALSE : ($is_wms == 1 && $this->wmsApi ? TRUE : ($is_wms == 2 && $this->sokoApi ? TRUE : FALSE));

      $arr = array(
        'order_code' => $detail->order_code,
        'state' => 3,
        'update_user' => $this->_user->uname
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

        if($is_api)
        {
          $this->wms = $this->load->database('wms', TRUE);

          if($is_wms == 1 && ! $order->is_pre_order)
  				{
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

          //---- export to soko
          if($order->is_wms == 2 && ! $order->is_pre_order)
          {
            $this->load->library('soko_order_api');

            if( ! $this->soko_order_api->export_order($order->code))
            {
              $sc = FALSE;
              $this->error = "ส่งข้อมูลไป Sokochan ไม่สำเร็จ <br/> (SOKOCHAN Error : ".$this->soko_order_api->error.")";
            }
          } //--- if($order->is_wms == 2)
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

  private function novalue($value = NULL)
  {
    $re = ($value == NULL OR $value == "") ? "ไม่ระบุ" : $value;
    return $re;
  }


  public function export_filter()
  {
    $this->load->model('masters/channels_model');

    $filter = array(
      'code'  => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'account' => get_filter('account', 'account', 'all'),
      'user'  => get_filter('user', 'user', ''),
      'channels' => get_filter('channels', 'channels', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date'  => get_filter('to_date', 'to_date', ''),
      'valid' => get_filter('valid', 'valid', '0'),
      'is_pre_order' => get_filter('is_pre_order', 'is_pre_order', 'all')
    );

    $token = $this->input->post('token');

    $data = $this->order_payment_model->get_data($filter);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('ตรวจสอบยอดชำระเงิน');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', "รายกงานตรวจสอบยอดชำระเงิน @".date('d/m/Y'));
    $this->excel->getActiveSheet()->mergeCells('A1:L1');
    $row = 2;
    $date = empty($filter['from_date'] OR $filter['to_date']) ? 'ไม่ระบุ' : $filter['from_date'].' ถึง '.$filter['to_date'];

    $bank = $filter['account'] == 'all' ? NULL : $this->bank_model->get_detail($filter['account']);
    $acc = $filter['account'] == 'all' ? 'ทั้งหมด' : (empty($bank) ? "" : $bank->account_no);

    $channels = $filter['channels'] == 'all' ? 'ทั้งหมด' : $this->channels_model->get_name($filter['channels']);

    $this->excel->getActiveSheet()->setCellValue('A'.$row, 'Filter');
    $this->excel->getActiveSheet()->setCellValue('B'.$row, $this->novalue($filter['code']));
    $this->excel->getActiveSheet()->setCellValue('C'.$row, $filter['is_pre_order'] == '1' ? 'Y' : ($filter['is_pre_order'] == '0' ? 'N' : 'ทั้งหมด'));
    $this->excel->getActiveSheet()->setCellValue('D'.$row, $channels);
    $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->novalue($filter['customer']));
    $this->excel->getActiveSheet()->setCellValue('F'.$row, $this->novalue($filter['user']));
    $this->excel->getActiveSheet()->setCellValue('G'.$row, $date);
    $this->excel->getActiveSheet()->setCellValue('I'.$row, $acc);
    $this->excel->getActiveSheet()->setCellValue('J'.$row, $filter['valid'] == 'all' ? 'ทั้งหมด' : ($filter['valid'] == '1' ? 'ยืนยันแล้ว' : 'รอยืนยัน'));

    //--- set Table header
		$row = 3;

    $this->excel->getActiveSheet()->setCellValue('A'.$row, '#');
    $this->excel->getActiveSheet()->setCellValue('B'.$row, 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('C'.$row, 'Pre order');
    $this->excel->getActiveSheet()->setCellValue('D'.$row, 'ช่องทางขาย');
    $this->excel->getActiveSheet()->setCellValue('E'.$row, 'ลูกค้า');
    $this->excel->getActiveSheet()->setCellValue('F'.$row, 'พนักงาน');
    $this->excel->getActiveSheet()->setCellValue('G'.$row, 'วัน-เวลา');
    $this->excel->getActiveSheet()->setCellValue('H'.$row, 'ยอดเงิน');
    $this->excel->getActiveSheet()->setCellValue('I'.$row, 'เลขที่บัญชี');
    $this->excel->getActiveSheet()->setCellValue('J'.$row, 'สถานะ');
    $this->excel->getActiveSheet()->setCellValue('K'.$row, 'ยืนยันโดย');
    $this->excel->getActiveSheet()->setCellValue('L'.$row, 'วันที่ยืนยัน');

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);

		$row++;


    if( ! empty($data))
    {
      $no = 1;

      foreach($data as $rs)
      {
        $customer_name = ( ! empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name;

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->order_code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, ($rs->is_pre_order ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->channels);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $customer_name);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->user);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, thai_date($rs->pay_date, TRUE));
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->pay_amount);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->acc_no);
        $this->excel->getActiveSheet()->setCellValue('J'.$row, ($rs->valid == 1 ? 'ยืนยันแล้ว' : 'รอยืนยัน'));
        $this->excel->getActiveSheet()->setCellValue('K'.$row, $rs->validate_by);
        $this->excel->getActiveSheet()->setCellValue('L'.$row, thai_date($rs->date_upd, TRUE));

        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle("H4:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

    }
		else
		{
			$this->excel->getActiveSheet()->setCellValue('A'.$row, "ไม่พบข้อมูลตามเงื่อนไขที่ระบุ");
		}

    setToken($token);
    $file_name = "รายงานตรวจสอบยอดชำระเงิน_".date('dmY').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function clear_filter()
  {
    $filter = array('code', 'account', 'user', 'channels','from_date', 'to_date', 'customer', 'valid', 'is_pre_order');
    clear_filter($filter);
  }
} //--- end class

?>
