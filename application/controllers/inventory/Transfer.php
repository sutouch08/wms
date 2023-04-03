<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
  public $menu_code = 'ICTRWH';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'TRANSFER';
	public $title = 'โอนสินค้าระหว่างคลัง';
  public $filter;
  public $error;
	public $isAPI;
  public $require_remark = 1;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transfer';
    $this->load->model('inventory/transfer_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'tr_code', ''),
      'from_warehouse' => get_filter('from_warehouse', 'tr_from_warehouse', ''),
      'user' => get_filter('user', 'tr_user', ''),
      'to_warehouse' => get_filter('to_warehouse', 'tr_to_warehouse', ''),
      'from_date' => get_filter('fromDate', 'tr_fromDate', ''),
      'to_date' => get_filter('toDate', 'tr_toDate', ''),
      'status' => get_filter('status', 'tr_status', 'all'),
      'is_approve' => get_filter('is_approve', 'tr_is_approve', 'all'),
			'api' => get_filter('api', 'tr_api', 'all'),
      'valid' => get_filter('valid', 'tr_valid', 'all'),
      'sap' => get_filter('sap', 'tr_sap', 'all'),
      'must_accept' => get_filter('must_accept', 'tr_must_accept', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->transfer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs     = $this->transfer_model->get_list($filter, $perpage, $this->uri->segment($segment));



    $filter['docs'] = $docs;
		$this->pagination->initialize($init);
    $this->load->view('transfer/transfer_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->transfer_model->get($code);

    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_logs' => $this->approve_logs_model->get($code),
      'accept_list' => $this->transfer_model->get_accept_list($code),
      'barcode' => FALSE
    );

    $this->load->view('transfer/transfer_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('transfer/transfer_add');
  }


  public function add()
  {
    if($this->input->post())
    {
      $sc = TRUE;
      $date_add = db_date($this->input->post('date'), TRUE);
      $from_warehouse = $this->input->post('from_warehouse_code');
      $to_warehouse = $this->input->post('to_warehouse_code');
			$wx_code = get_null(trim($this->input->post('wx_code')));
      $remark = $this->input->post('remark');
      $bookcode = getConfig('BOOK_CODE_TRANSFER');
      $isManual = getConfig('MANUAL_DOC_CODE');

			$api = $this->input->post('api'); //--- 1 = ส่งข้อมูลไป wms ตามหลักการ 0 = ไม่ส่งข้อมูลไป WMS

			$fromWh = $this->warehouse_model->get($from_warehouse);
			$toWh = $this->warehouse_model->get($to_warehouse);

			$is_wms = $fromWh->is_wms == 1 ? 1 : ($toWh->is_wms == 1 ? 1 : 0);

			//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
			$direction = $toWh->is_wms == 1 ? 1 :($fromWh->is_wms == 1 ? 2 : 0);

      if($isManual == 1 && $this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      if( ! empty($code))
      {
        $must_approve = getConfig('STRICT_TRANSFER') == 1 ? 1 : 0;

        $ds = array(
          'code' => $code,
          'bookcode' => $bookcode,
          'from_warehouse' => $from_warehouse,
          'to_warehouse' => $to_warehouse,
          'remark' => trim($remark),
          'user' => $this->_user->uname,
          'date_add' => $date_add,
          'is_wms' => $is_wms,
          'direction' => $direction,
          'api' => $api,
          'wx_code' => $wx_code,
          'must_approve' => $must_approve
        );

        if( ! $this->transfer_model->add($ds))
        {
          $sc = FALSE;
          $this->error = 'เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Number";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function is_document_avalible()
  {
    $code = $this->input->get('code');
    $uuid = $this->input->get('uuid');
    if( ! $this->transfer_model->is_document_avalible($code, $uuid))
    {
      echo "not_available";
    }
    else
    {
      echo "available";
    }
  }



  public function edit($code, $uuid, $barcode = '')
  {
    $doc = $this->transfer_model->get($code);

    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == '' ? FALSE : TRUE
    );

    $this->transfer_model->update_uuid($code, $uuid);

    $this->load->view('transfer/transfer_edit', $ds);
  }


  public function update_uuid()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $uuid = trim($this->input->post('uuid'));

    if( ! empty($uuid))
    {
      return $this->transfer_model->update_uuid($code, $uuid);
    }
  }


  public function update($code)
  {
		$fromWh = $this->warehouse_model->get($this->input->post('from_warehouse'));
		$toWh = $this->warehouse_model->get($this->input->post('to_warehouse'));

		$is_wms = $fromWh->is_wms == 1 ? 1 : ($toWh->is_wms == 1 ? 1 : 0);
		$api = $this->input->post('api'); //--- 1 = ส่งข้อมูลไป wms ตามหลักการ 0 = ไม่ส่งข้อมูลไป WMS
		$wx_code = get_null(trim($this->input->post('wx_code')));

		//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
		$direction = $toWh->is_wms == 1 ? 1 :($fromWh->is_wms == 1 ? 2 : 0);


    $must_approve = getConfig('STRICT_TRANSFER') == 1 ? 1 : 0;

    $arr = array(
      'date_add' => db_date($this->input->post('date_add'), TRUE),
      'from_warehouse' => $fromWh->code,
      'to_warehouse' => $toWh->code,
      'remark' => get_null(trim($this->input->post('remark'))),
			'is_wms' => $is_wms,
			'direction' => $direction,
			'api' => $api,
			'wx_code' => $wx_code,
      'must_approve' => $must_approve,
      'update_user' => $this->_user->uname
    );

    $rs = $this->transfer_model->update($code, $arr);

    if($rs)
    {
      echo 'success';
    }
    else
    {
      echo 'ปรับปรุงข้อมูลไม่สำเร็จ';
    }
  }




  public function check_temp_exists($code)
  {
    $temp = $this->transfer_model->is_exists_temp($code);
    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }



	public function save_transfer($code)
  {
    $sc = TRUE;
    $ex = 1;
		$doc = $this->transfer_model->get($code);

		if(!empty($doc))
		{
			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

			if($doc->status == -1 OR $doc->status == 0)
			{
				$details = $this->transfer_model->get_details($code);

				if(!empty($details))
				{
          $this->db->trans_begin();

          //--- ถ้าต้องอนุมัติ แค่เปลี่ยนสถานะเป็น 0 พอ
          if($doc->must_approve == 1)
          {
            $arr = array(
              'status' => 0,
              'is_approve' => 0
            );

            if( ! $this->transfer_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Update Status Failed";
            }
          }
          else
          {
            if($doc->must_accept == 1)
            {
              $arr = array(
                'status' => 4,
                'is_accept' => 0
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Update Status Failed";
              }
            }
            else
            {
              if($this->isAPI === TRUE && $doc->is_wms == 1 && $doc->api == 1)
              {
                $arr = array(
                  'status' => 3
                );

                if( ! $this->transfer_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Status Failed";
                }
              }
              else
              {
                //--- movement
                $this->load->model('inventory/movement_model');

                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  //--- 2. update movement
                  $move_out = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->from_warehouse,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->qty,
                  'date_add' => $date_add
                  );

                  //--- move out
                  if(! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                  }

                  $move_in = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->to_warehouse,
                  'zone_code' => $rs->to_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->qty,
                  'move_out' => 0,
                  'date_add' => $date_add
                  );

                  //--- move in
                  if(! $this->movement_model->add($move_in))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                  }

                } //--- end foreach

                if($sc === TRUE)
                {
                  if(! $this->transfer_model->set_status($code, 1))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
                  }

                  if(! $this->transfer_model->valid_all_detail($code, 1))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                  }
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE)
          {
            if($doc->must_approve == 0 && $doc->must_accept == 0)
            {
              //--- ถ้าต้อง process ที่ wms แค่เปลี่ยนสถานะเป็น 3 แล้ส่งข้อมูลออกไป wms
              if($this->isAPI === TRUE && $doc->is_wms == 1 && $doc->api == 1)
              {
                $this->wms = $this->load->database('wms', TRUE);

                //---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
                if($doc->direction == 1)
                {
                  $this->load->library('wms_receive_api');

                  if(! $this->wms_receive_api->export_transfer($doc, $details))
                  {
                    $sc = FALSE;
                    $ex = 0;
                    $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                  }
                }

                if($doc->direction == 2)
                {
                  $this->load->library('wms_order_api');

                  if( ! $this->wms_order_api->export_transfer_order($doc, $details))
                  {
                    $sc = FALSE;
                    $ex = 0;
                    $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                  }
                }
              }
              else
              {
                $this->transfer_model->update($code, array('shipped_date' => now())); //--- update transferd date

                if( ! $this->do_export($code))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ";
                }
              } //-- is isAPI
            } //-- if must_approve && must_accept = 0
          } //-- if $sc = TRUE
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการโอนย้าย";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "สถานะเอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}


    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex = 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function do_approve()
  {
    $sc = TRUE;
    $ex = 1;
    $code = $this->input->post('code');

    if($this->pm->can_approve)
    {
      $doc = $this->transfer_model->get($code);

      if(!empty($doc))
      {
        $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();
        $is_wms = ($this->isAPI && $doc->is_wms == 1 && $doc->api == 1) ? TRUE : FALSE;

        if($doc->status == 0 && ($doc->is_approve == 0 OR $doc->is_approve == 3))
        {
          $this->db->trans_begin();

          $arr = array(
            'is_approve' => 1,
            'status' => $doc->must_accept == 1 ? 4 : ($is_wms ? 3 : 1)
          );

          if( ! $this->transfer_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Update Status Failed";
          }

          $this->load->model('approve_logs_model');
          $this->approve_logs_model->add($code, 1, $this->_user->uname);


          if($sc === TRUE)
          {
            if($doc->must_accept == 0 && $is_wms == FALSE)
            {
              $this->load->model('inventory/movement_model');

              $details = $this->transfer_model->get_details($code);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  //--- 2. update movement
                  $move_out = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->from_warehouse,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->qty,
                  'date_add' => $date_add
                  );

                  //--- move out
                  if(! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                    break;
                  }

                  $move_in = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->to_warehouse,
                  'zone_code' => $rs->to_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->qty,
                  'move_out' => 0,
                  'date_add' => $date_add
                  );


                  if(! $this->movement_model->add($move_in))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                    break;
                  }
                } //--- end foreach

                if($sc === TRUE)
                {

                  if(! $this->transfer_model->valid_all_detail($code, 1))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการโอนย้าย";
              }
            }
          }

          if( $sc === TRUE)
          {

            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }


          if($sc === TRUE && $doc->must_accept == 0)
          {
            if( ! empty($details))
            {
              if($is_wms == TRUE)
              {
                $this->wms = $this->load->database('wms', TRUE);
                //---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
                if($doc->direction == 1)
                {
                  $this->load->library('wms_receive_api');

                  if( ! $this->wms_receive_api->export_transfer($doc, $details))
                  {
                    $sc = FALSE;
                    $ex = 0;
                    $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                  }
                }

                if($doc->direction == 2)
                {
                  $this->load->library('wms_order_api');

                  if( ! $this->wms_order_api->export_transfer_order($doc, $details))
                  {
                    $sc = FALSE;
                    $ex = 0;
                    $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                  }
                }
              }
              else
              {
                $this->transfer_model->update($code, array('shipped_date' => now()));

                if( ! $this->do_export($code))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบรายการโอนย้าย";
            }
          } //--- if must_accept == 0
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการอนุมัติ";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex = 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function do_reject()
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');

    $code = $this->input->post('code');

    if($this->pm->can_approve)
    {
      if( ! empty($code))
      {
        $doc = $this->transfer_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 0 && $doc->is_approve == 0)
          {
            $arr = array(
              'is_approve' => 3
            );

            if($this->transfer_model->update($code, $arr))
            {
              $this->approve_logs_model->add($code, 3, $this->_user->uname);
            }
            else
            {
              $sc = FALSE;
              $this->error = "Update Status Failed";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid document status";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document number";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการอนุมัติ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  function accept_confirm()
  {
    $sc = TRUE;
    $ex = 1;
    $code = $this->input->post('code');
    $remark = $this->input->post('accept_remark');
    $doc = $this->transfer_model->get($code);

    if( ! empty($doc))
    {
      $is_wms = ($this->isAPI == TRUE && $doc->is_wms == 1 && $doc->api == 1) ? TRUE : FALSE;

      if($doc->status == 4)
      {
        if($this->canAccept())
        {
          $this->db->trans_begin();

          if( ! $this->transfer_model->accept_all($code, $this->_user->uname))
          {
            $sc = FALSE;
            $this->error = "Update Accept Status Failed";
          }

          if( $sc === TRUE)
          {
            $arr = array(
              'status' => $is_wms ? 3 : 1,
              'is_accept' => 1,
              'accept_by' => $this->_user->uname,
              'accept_on' => now(),
              'accept_remark' => $remark
            );

            if( ! $this->transfer_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Update Status Failed";
            }

            if($sc === TRUE && $is_wms === FALSE)
            {
              $this->load->model('inventory/movement_model');

              $details = $this->transfer_model->get_details($code);

              if( ! empty($details))
              {
                $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  $move_out = array(
                    'reference' => $code,
                    'warehouse_code' => $doc->from_warehouse,
                    'zone_code' => $rs->from_zone,
                    'product_code' => $rs->product_code,
                    'move_in' => 0,
                    'move_out' => $rs->qty,
                    'date_add' => $date_add
                  );

                  $move_in = array(
                    'reference' => $code,
                    'warehouse_code' => $doc->to_warehouse,
                    'zone_code' => $rs->to_zone,
                    'product_code' => $rs->product_code,
                    'move_in' => $rs->qty,
                    'move_out' => 0,
                    'date_add' => $date_add
                  );

                  if( ! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Movement (out) Failed";
                  }

                  if( ! $this->movement_model->add($move_in))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Movement (in) Failed";
                  }
                } //-- foreach

                if($sc === TRUE)
                {
                  if( ! $this->transfer_model->valid_all_detail($code, 1))
                  {
                    $sc = FALSE;
                    $this->error = "Update Row(s) Status Failed";
                  }
                }
              } //-- empty details
            } //--- sc == TURE
          } //--- sc == TURE

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE)
          {
            if($is_wms === TRUE)
            {
              $this->wms = $this->load->database('wms', TRUE);
              //---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
              if($doc->direction == 1)
              {
                $this->load->library('wms_receive_api');

                if( ! $this->wms_receive_api->export_transfer($doc, $details))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                }
              }

              if($doc->direction == 2)
              {
                $this->load->library('wms_order_api');

                if( ! $this->wms_order_api->export_transfer_order($doc, $details))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                }
              }
            }
            else
            {
              $this->transfer_model->update($code, array('shipped_date' => now()));

              if(! $this->do_export($code))
              {
                $sc = FALSE;
                $ex = 0;
                $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ";
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "You don't have permission to perform this operation.";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Document Number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  function accept_zone()
  {
    $sc = TRUE;
    $ex = 1;
    $code = $this->input->post('code');
    $doc = $this->transfer_model->get($code);
    $is_wms = ($this->isAPI === TRUE && $doc->is_wms == 1 && $doc->api ==1) ? TRUE : FALSE;
    $is_accept_all = FALSE;

    if( ! empty($doc))
    {
      if($doc->status == 4)
      {
        $my_zone = $this->transfer_model->get_my_zone($code, $this->_user->id);

        if( ! empty($my_zone))
        {
          $this->db->trans_begin();

          if( ! $this->transfer_model->accept_zone($code, $my_zone, $this->_user->uname))
          {
            $sc = FALSE;
            $this->error = "Update Accept Status Failed";
          }

          if( $sc === TRUE)
          {
            //--- check all accept ?
            $is_accept_all = $this->transfer_model->is_accept_all($code);

            if($is_accept_all)
            {
              $arr = array(
                'status' => $is_wms ? 3 : 1,
                'is_accept' => 1,
                'accept_by' => NULL,
                'accept_on' => now(),
                'accept_remark' => NULL
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Update Status Failed";
              }

              if($sc === TRUE && $is_wms === FALSE)
              {
                $this->load->model('inventory/movement_model');

                $details = $this->transfer_model->get_details($code);

                if( ! empty($details))
                {
                  $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }

                    $move_out = array(
                      'reference' => $code,
                      'warehouse_code' => $doc->from_warehouse,
                      'zone_code' => $rs->from_zone,
                      'product_code' => $rs->product_code,
                      'move_in' => 0,
                      'move_out' => $rs->qty,
                      'date_add' => $date_add
                    );

                    $move_in = array(
                      'reference' => $code,
                      'warehouse_code' => $doc->to_warehouse,
                      'zone_code' => $rs->to_zone,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->qty,
                      'move_out' => 0,
                      'date_add' => $date_add
                    );

                    if( ! $this->movement_model->add($move_out))
                    {
                      $sc = FALSE;
                      $this->error = "Insert Movement (out) Failed";
                    }

                    if( ! $this->movement_model->add($move_in))
                    {
                      $sc = FALSE;
                      $this->error = "Insert Movement (in) Failed";
                    }
                  } //-- foreach

                  if($sc === TRUE)
                  {
                    if( ! $this->transfer_model->valid_all_detail($code, 1))
                    {
                      $sc = FALSE;
                      $this->error = "Update Row(s) Status Failed";
                    }
                  }
                } //-- empty details
              } //--- sc == TURE
            }
          } //--- sc == TURE

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }


          if($sc === TRUE && $is_accept_all === TRUE)
          {
            if($is_wms === TRUE)
            {
              $this->wms = $this->load->database('wms', TRUE);
              //---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
              if($doc->direction == 1)
              {
                $this->load->library('wms_receive_api');

                if( ! $this->wms_receive_api->export_transfer($doc, $details))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                }
              }

              if($doc->direction == 2)
              {
                $this->load->library('wms_order_api');

                if( ! $this->wms_order_api->export_transfer_order($doc, $details))
                {
                  $sc = FALSE;
                  $ex = 0;
                  $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                }
              }
            }
            else
            {
              $this->transfer_model->update($code, array('shipped_date' => now()));

              if(! $this->do_export($code))
              {
                $sc = FALSE;
                $ex = 0;
                $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ";
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "You don't have permission to perform this operation.";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Document Number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function canAccept()
  {
    $pm = get_permission('APACWW', $this->_user->uid, $this->_user->id_profile);

    if( ! empty($pm))
    {
      return ($pm->can_view + $pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0 ? TRUE : FALSE;
    }

    return FALSE;
  }


	public function send_to_wms($code)
  {
    $sc = TRUE;

		$doc = $this->transfer_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == -1)
			{
				$sc = FALSE;
				$this->error = "Invalid Document status";
			}

      if($doc->must_approve == 1 && $doc->is_approve = 0)
      {
        $sc = FALSE;
        $this->error = "Invalid Approve Status";
      }

      if($sc === TRUE)
			{
				$details = $this->transfer_model->get_details($code);

				if(!empty($details))
				{
					//--- ถ้าต้อง process ที่ wms แค่เปลี่ยนสถานะเป็น 3 แล้ส่งข้อมูลออกไป wms
					if($doc->is_wms == 1 && $doc->api == 1)
					{
						$this->wms = $this->load->database('wms', TRUE);
						//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
						if($doc->direction == 1)
						{
							$this->load->library('wms_receive_api');

							$rs = $this->wms_receive_api->export_transfer($doc, $details);

							if(! $rs)
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_receive_api->error;
							}
						}

						if($doc->direction == 2)
						{
							$this->load->library('wms_order_api');

							$rs = $this->wms_order_api->export_transfer_order($doc, $details);

							if(! $rs)
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_order_api->error;
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Document must process at Warrix";
					} //-- end if is_wms
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการโอนย้าย";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function unsave_transfer($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    //--- check Transfer doc exists in SAP
    $doc = $this->transfer_model->get_sap_transfer_doc($code);
    if(!empty($doc))
    {
      $sc = FALSE;
      $this->error = "เอกสารเข้า SAP แล้วไม่อนุญาติให้ยกเลิก";
    }
    else
    {
      //--- check middle doc delete it if exists
      $middle = $this->transfer_model->get_middle_transfer_doc($code);
      if(!empty($middle))
      {
        foreach($middle as $rs)
        {
          $this->transfer_model->drop_middle_exits_data($rs->DocEntry);
        }
      }


      $this->db->trans_start();
      //--- change state to -1
      $arr = array(
        'status' => -1,
        'is_approve' => 0
      );
      $this->transfer_model->update($code, $arr);
      $this->transfer_model->valid_all_detail($code, 0);
      $this->movement_model->drop_movement($code);
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
        $this->error = $this->db->error();
      }
    }



    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function add_to_transfer()
  {
    $sc = TRUE;

		$data = json_decode($this->input->post('data'));

		if(!empty($data))
		{
			if(! empty($data->transfer_code))
	    {
	      $this->load->model('masters/products_model');

				$code = $data->transfer_code;
	      $from_zone = $data->from_zone;
	      $to_zone = $data->to_zone;

        $zone = $this->zone_model->get($to_zone);

        $must_accept = (empty($zone) ? 0 : (empty($zone->user_id) ? 0 : 1));

	      $items = $data->items;

	      if(!empty($items))
	      {
	        $this->db->trans_begin();

	        foreach($items as $item)
	        {
            if($sc === FALSE)
            {
              break;
            }

	          $id = $this->transfer_model->get_id($code, $item->item_code, $from_zone, $to_zone);

	          if(!empty($id))
	          {
	            if( !$this->transfer_model->update_qty($id, $item->qty))
              {
                $sc = FALSE;
                $this->error = "Update data failed";
              }
	          }
	          else
	          {
	            $arr = array(
	              'transfer_code' => $code,
	              'product_code' => $item->item_code,
	              'product_name' => $this->products_model->get_name($item->item_code),
	              'from_zone' => $from_zone,
	              'to_zone' => $to_zone,
	              'qty' => $item->qty,
                'must_accept' => $must_accept
	            );

	            if( ! $this->transfer_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert data failed";
              }
	          }
	        }

	        if($sc === TRUE)
	        {
            if($must_accept == 1)
            {
              $arr = array(
                'status' => -1,
                'must_accept' => 1
              );
            }
            else
            {
              $arr = array('status' => -1);
            }

            $this->transfer_model->update($data->transfer_code, $arr);

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
					$this->error = "ไม่พบรายการสินค้า";
				}
	    }
			else
			{
				$sc = FALSE;
				$this->error = "Missing document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing form data";
		}

    echo $sc === TRUE ? 'success' : $this->error;

  }




  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if(!empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->transfer_model->get_temp_qty($code, $product_code, $zone_code);
        //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
        $transfer_qty = $this->transfer_model->get_transfer_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $transfer_qty);

        if($qty <= $cqty)
        {
          $arr = array(
            'transfer_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->transfer_model->update_temp($arr) === FALSE)
          {
            $sc = FALSE;
            $message = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
          }

        }
        else
        {
          $sc = FALSE;
          $message = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function move_to_zone()
  {
    $sc = TRUE;
    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $barcode = trim($this->input->post('barcode'));
      $to_zone = $this->input->post('zone_code');
      $zone = $this->zone_model->get($to_zone);
      $must_accept = empty($zone) ? 0 : (empty($zone->user_id) ? 0 : 1);
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if(!empty($item))
      {
        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า transfer detail
        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
        $temp = $this->transfer_model->get_temp_product($code, $item->code);
        if(!empty($temp))
        {
          //--- เริ่มใช้งาน transction
          $this->db->trans_begin();
          foreach($temp as $rs)
          {
            if($qty > 0 && $rs->qty > 0)
            {
              //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
              //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
              //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
              $temp_qty = $qty <= $rs->qty ? $qty : $rs->qty;
              $id = $this->transfer_model->get_id($code, $item->code, $rs->zone_code, $to_zone);
              //--- ถ้าพบไอดีให้แก้ไขจำนวน
              if(!empty($id))
              {
                if($this->transfer_model->update_qty($id, $temp_qty) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'แก้ไขยอดในรายการไม่สำเร็จ';
                  break;
                }
              }
              else
              {
                //--- ถ้ายังไม่มีรายการ ให้เพิ่มใหม่
                $ds = array(
                  'transfer_code' => $code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'from_zone' => $rs->zone_code,
                  'to_zone' => $to_zone,
                  'qty' => $temp_qty,
                  'must_accept' => $must_accept
                );

                if($this->transfer_model->add_detail($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'เพิ่มรายการไม่สำเร็จ';
                  break;
                }
              }
              //--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
              if($this->transfer_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
              {
                $sc = FALSE;
                $message = 'แก้ไขยอดใน temp ไม่สำเร็จ';
                break;
              }

              //--- ตัดยอดที่ต้องการย้ายออก เพื่อยกยอดไปรอบต่อไป
              $qty -= $temp_qty;
            }
            else
            {
              break;
            } //-- end if qty > 0

            //--- ลบ temp ที่ยอดเป็น 0
            $this->transfer_model->drop_zero_temp();
          } //--- end foreach


          //--- เมื่อทำงานจนจบแล้ว ถ้ายังเหลือยอด แสดงว่ายอดที่ต้องการย้ายเข้า มากกว่ายอดที่ย้ายออกมา
          //--- จะให้ทำกร roll back แล้วแจ้งกลับ
          if($qty > 0)
          {
            $sc = FALSE;
            $message = 'ยอดที่ย้ายเข้ามากกว่ายอดที่ย้ายออกมา';
          }

          if($sc === FALSE)
          {
            if($must_accept == 1)
            {
              $arr = array(
                'must_accept' => 1
              );

              $this->transfer_model->update($code, $arr);
            }

            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
        else
        {
          $sc = FALSE;
          $message = 'ไม่พบรายการใน temp';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->transfer_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function is_exists_detail($code)
  {
    $detail = $this->transfer_model->is_exists_detail($code);
    $temp = $this->transfer_model->is_exists_temp($code);

    if($detail === FALSE && $temp === FALSE)
    {
      echo 'not_exists';
    }
    else
    {
      echo 'exists';
    }
  }



  public function get_temp_table($code)
  {
    $ds = array();
    $temp = $this->transfer_model->get_transfer_temp($code);
    if(!empty($temp))
    {
      $no = 1;
      foreach($temp as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $rs->zone_code,
          'fromZone' => $this->zone_model->get_name($rs->zone_code),
          'qty' => $rs->qty
        );

        array_push($ds, $arr);
        $no++;
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }




  public function get_transfer_table($code)
  {
    $ds = array();
    $details = $this->transfer_model->get_details($code);

    if(!empty($details))
    {
      $no = 1;
      $total_qty = 0;
      foreach($details as $rs)
      {
        $btn_delete = '';
        if($this->pm->can_add OR $this->pm->can_edit && $rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="deleteMoveItem('.$rs->id.', \''.$rs->product_code.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $this->zone_model->get_name($rs->from_zone),
          'to_zone' => $this->zone_model->get_name($rs->to_zone),
          'qty' => number($rs->qty),
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
      } //--- end foreach

      $arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



  public function get_transfer_zone($warehouse = '')
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $zone = $this->zone_model->search($txt, $warehouse);
    if(!empty($zone))
    {
      foreach($zone as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบโซน';
    }

    echo json_encode($sc);
  }



  public function get_product_in_zone()
  {
    $sc = TRUE;
    $ds = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $transfer_code = $this->input->get('transfer_code');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);

      if( ! empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->transfer_model->get_temp_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
          $transfer_qty = $this->transfer_model->get_transfer_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $transfer_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              'barcode' => $this->products_model->get_barcode($rs->product_code),
              'products' => $rs->product_code,
              'qty' => $qty
            );

            array_push($ds, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($ds, array("nodata" => "nodata"));
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc = TRUE ? json_encode($ds) : $this->error;
  }





  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }




  public function delete_detail()
  {
    $sc = TRUE;

    $code = $this->input->post('code');
    $id = $this->input->post('id');

    $this->db->trans_begin();

    if($this->transfer_model->drop_detail($id))
    {
      $must_accept = $this->transfer_model->must_accept($code) ? 1 : 0;

      $arr = array(
        'must_accept' => $must_accept
      );

      if( ! $this->transfer_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Update Acception Status Failed";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Delete Failed";
    }

    if( $sc === TRUE)
    {
      $this->db->trans_commit();
    }
    else
    {
      $this->db->trans_rollback();
    }

    $this->_response($sc);
  }




  public function delete_transfer($code)
  {
    $this->load->model('inventory/movement_model');

    $this->db->trans_start();

    //--- clear temp
    $this->transfer_model->drop_all_temp($code);
    //--- delete detail
    $this->transfer_model->drop_all_detail($code);
    //--- drop movement
    $this->movement_model->drop_movement($code);
    //--- change status to 2 (cancled)
    $this->transfer_model->set_status($code, 2);

    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      echo $this->db->error();
    }
    else
    {
      echo 'success';
    }
  }




  public function print_transfer($code)
  {
    $this->load->library('printer');
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        // $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        // $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_transfer', $ds);
  }

	public function print_wms_transfer($code)
  {
    $this->load->library('xprinter');
    $doc = $this->transfer_model->get($code);
		if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);

    $ds = array(
      'order' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_wms_transfer', $ds);
  }



  private function do_export($code)
  {
    $sc = TRUE;

    $this->load->library('export');

    if( ! $this->export->export_transfer($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }
    else
    {
      $this->transfer_model->set_export($code, 1);
    }

    return $sc;
  }



  public function export_transfer($code)
  {
    if($this->do_export($code) === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->error;
    }
  }

  //---- Update status transfer draft to receipted
  public function confirm_receipted()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    if(!empty($code))
    {
      $this->load->model('orders/orders_model');

      //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
      $draft = $this->transfer_model->get_transfer_draft($code);
      if(!empty($draft))
      {
        if(empty($draft->F_Receipt) OR $draft->F_Receipt == 'N' OR $draft->F_Receipt == 'D')
        {
          //---- ยืนยันรับสินค้า
          if($this->transfer_model->confirm_draft_receipted($draft->DocEntry))
          {
            $this->orders_model->valid_transfer_draft($code);
          }
          else
          {
            $sc = FALSE;
            $this->error = "ยืนยันการรับสินค้าใน Transfer Draft ไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกยืนยันไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเอกสาร Transfer draft";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'tr_code',
      'tr_from_warehouse',
      'tr_user',
      'tr_to_warehouse',
      'tr_fromDate',
      'tr_toDate',
      'tr_status',
			'tr_api',
      'tr_is_approve',
      'tr_valid',
      'tr_sap',
      'tr_must_accept'
    );

    clear_filter($filter);

    echo 'done';
  }


} //--- end class
?>
