<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Move extends PS_Controller
{
  public $menu_code = 'ICTRMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'TRANFER';
	public $title = 'ย้ายพื้นที่จัดเก็บ';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/move';
    $this->load->model('inventory/move_model');
    $this->load->model('inventory/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');

  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'move_code', ''),
      'from_warehouse'  => get_filter('from_warehouse', 'move_from_warehouse', ''),
      'user'      => get_filter('user', 'move_user', ''),
      'from_date' => get_filter('fromDate', 'move_fromDate', ''),
      'to_date'   => get_filter('toDate', 'move_toDate', ''),
      'status' => get_filter('status', 'move_status', 'all'),
      'is_export' => get_filter('is_export', 'move_is_export', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->move_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs     = $this->move_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->from_warehouse_name = $this->warehouse_model->get_name($rs->from_warehouse);
        $rs->to_warehouse_name = $this->warehouse_model->get_name($rs->to_warehouse);
      }
    }

    $filter['docs'] = $docs;
		$this->pagination->initialize($init);
    $this->load->view('move/move_list', $filter);
  }



  public function view_detail($code)
  {
    $doc = $this->move_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->move_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => FALSE
    );

    $this->load->view('move/move_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('move/move_add');
  }


  public function add()
  {
    if($this->input->post('date'))
    {
      $date_add = db_date($this->input->post('date'), TRUE);
      $from_warehouse = $this->input->post('from_warehouse_code');
      $to_warehouse = $this->input->post('to_warehouse_code');
      $remark = $this->input->post('remark');
      $bookcode = getConfig('BOOK_CODE_MOVE');
      $isManual = getConfig('MANUAL_DOC_CODE');

      if($isManual == 1 && $this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      $ds = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'from_warehouse' => $from_warehouse,
        'to_warehouse' => $to_warehouse,
        'remark' => $remark,
        'user' => get_cookie('uname'),
        'date_add' => $date_add
      );

      $rs = $this->move_model->add($ds);
      if($rs === TRUE)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูลเอกสาร กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }



  public function edit($code, $barcode = '')
  {
    $doc = $this->move_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->move_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == '' ? FALSE : TRUE
    );

    $this->load->view('move/move_edit', $ds);
  }



  public function update($code)
  {
    $arr = array(
      'date_add' => db_date($this->input->post('date_add'), TRUE),
      'from_warehouse' => $this->input->post('from_warehouse'),
      'to_warehouse' => $this->input->post('to_warehouse'),
      'remark' => $this->input->post('remark'),
      'update_user' => get_cookie('uname')
    );

    $rs = $this->move_model->update($code, $arr);

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
    $temp = $this->move_model->is_exists_temp($code);
    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function save_move($code)
  {
    $sc = TRUE;
    $this->db->trans_start();
    //--- change state to 1
    $this->move_model->set_status($code, 1);
    $this->move_model->valid_all_detail($code, 1);

    $details = $this->move_model->get_details($code);
    $doc = $this->move_model->get($code);
    if(!empty($details))
    {
      $this->load->model('inventory/movement_model');
      foreach($details as $rs)
      {
        //--- 2. update movement
        $move_out = array(
          'reference' => $code,
          'warehouse_code' => $doc->from_warehouse,
          'zone_code' => $rs->from_zone,
          'product_code' => $rs->product_code,
          'move_in' => 0,
          'move_out' => $rs->qty,
          'date_add' => $doc->date_add
        );

        $move_in = array(
          'reference' => $code,
          'warehouse_code' => $doc->to_warehouse,
          'zone_code' => $rs->to_zone,
          'product_code' => $rs->product_code,
          'move_in' => $rs->qty,
          'move_out' => 0,
          'date_add' => $doc->date_add
        );

        //--- move out
        if($this->movement_model->add($move_out) === FALSE)
        {
          $sc = FALSE;
          $message = 'บันทึก movement ขาออกไม่สำเร็จ';
          break;
        }

        //--- move in
        if($this->movement_model->add($move_in) === FALSE)
        {
          $sc = FALSE;
          $message = 'บันทึก movement ขาเข้าไม่สำเร็จ';
          break;
        }
      }
    }


    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $sc = FALSE;
      $message = $this->db->error();
    }

    if($sc === TRUE)
    {
      $this->do_export($code);
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function unsave_move($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    $this->db->trans_start();
    //--- change state to 1
    $this->move_model->set_status($code, 0);
    $this->move_model->valid_all_detail($code, 0);
    $this->movement_model->drop_movement($code);
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $sc = FALSE;
      $message = $this->db->error();
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function add_to_move()
  {
    $sc = TRUE;
    $code = $this->input->post('move_code');
    if($code)
    {
      $this->load->model('masters/products_model');

      $from_zone = $this->input->post('from_zone');
      $to_zone = $this->input->post('to_zone');
      $trans_products = $this->input->post('items');

      if($from_zone != $to_zone)
      {
        if(!empty($trans_products))
        {
          $items = json_decode($trans_products);
          $this->db->trans_start();
          foreach($items as $item)
          {
            $id = $this->move_model->get_id($code, $item->code, $from_zone, $to_zone);
            if(!empty($id))
            {
              $this->move_model->update_qty($id, $item->qty);
            }
            else
            {
              $arr = array(
                'move_code' => $code,
                'product_code' => $item->code,
                'product_name' => $this->products_model->get_name($item->code),
                'from_zone' => $from_zone,
                'to_zone' => $to_zone,
                'qty' => $item->qty
              );

              $this->move_model->add_detail($arr);
            }
          }

          $this->db->trans_complete();


          if($this->db->trans_status() === FALSE)
          {
            $sc = FALSE;
            $message = 'เพิ่มข้อมูลไม่สำเร็จ';
          }
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'โซนต้นทาง - ปลายทาง ต้องเป็นคนละโซนกัน';
      }

    }

    echo $sc === TRUE ? 'success' : $message;

  }




  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('move_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('move_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->move_model->get_temp_qty($code, $product_code, $zone_code);
        //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
        $move_qty = $this->move_model->get_move_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $move_qty);

        if($qty <= $cqty)
        {
          $arr = array(
            'move_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->move_model->update_temp($arr) === FALSE)
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
    if($this->input->post('move_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('move_code');
      $barcode = trim($this->input->post('barcode'));
      $to_zone = $this->input->post('zone_code');
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า move detail
        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
        $temp = $this->move_model->get_temp_product($code, $item->code);
        if(!empty($temp))
        {
          //--- เริ่มใช้งาน transction
          $this->db->trans_begin();
          foreach($temp as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            if($rs->zone_code != $to_zone)
            {
              if($qty > 0 && $rs->qty > 0)
              {
                //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
                //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
                //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
                $temp_qty = $qty <= $rs->qty ? $qty : $rs->qty;
                $id = $this->move_model->get_id($code, $item->code, $rs->zone_code, $to_zone);
                //--- ถ้าพบไอดีให้แก้ไขจำนวน
                if(!empty($id))
                {
                  if($this->move_model->update_qty($id, $temp_qty) === FALSE)
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
                    'move_code' => $code,
                    'product_code' => $item->code,
                    'product_name' => $item->name,
                    'from_zone' => $rs->zone_code,
                    'to_zone' => $to_zone,
                    'qty' => $temp_qty
                  );

                  if($this->move_model->add_detail($ds) === FALSE)
                  {
                    $sc = FALSE;
                    $message = 'เพิ่มรายการไม่สำเร็จ';
                    break;
                  }
                }
                //--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
                if($this->move_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
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
            }
            else
            {
              $sc = FALSE;
              $message = 'โซนต้นทาง - ปลายทาง ต้องไม่ใช่โซนเดียวกัน';
            }


            //--- ลบ temp ที่ยอดเป็น 0
            $this->move_model->drop_zero_temp();
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
    $exists = $this->move_model->is_exists($code, $old_code);
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
    $detail = $this->move_model->is_exists_detail($code);
    $temp = $this->move_model->is_exists_temp($code);

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
    $temp = $this->move_model->get_move_temp($code);
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




  public function get_move_table($code)
  {
    $ds = array();
    $details = $this->move_model->get_details($code);

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



  public function get_move_zone($warehouse = NULL)
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
    $sc = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $move_code = $this->input->get('move_code');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);
      if(!empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->move_model->get_temp_qty($move_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
          $move_qty = $this->move_model->get_move_qty($move_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $move_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              'barcode' => $this->products_model->get_barcode($rs->product_code),
              'products' => $rs->product_code,
              'qty' => $qty
            );

            array_push($sc, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($sc, array("nodata" => "nodata"));
      }
      echo json_encode($sc);
    }
  }





  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_MOVE');
    $run_digit = getConfig('RUN_DIGIT_MOVE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->move_model->get_max_code($pre);
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




  public function delete_detail($id)
  {
    $rs = $this->move_model->drop_detail($id);
    if($rs === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->db->error();
    }
  }




  public function delete_move($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');

    $move = $this->move_model->get($code);
    if(!empty($move))
    {
      $docNum = $this->move_model->get_sap_doc_num($code);
      if(empty($move->inv_code) && empty($docNum))
      {
        $this->db->trans_begin();
        //--- clear temp
        if(! $this->move_model->drop_all_temp($code))
        {
          $sc = FALSE;
          $this->error = "ลบ temp ไม่สำเร็จ";
        }
        //--- delete detail
        if(! $this->move_model->drop_all_detail($code))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }

        //--- Mare as Cancled
        if(! $this->move_model->set_status($code, 2))
        {
          $sc = FALSE;
          $this->error = "ลบเอกสารไม่สำเร็จ";
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
          //---- delete middle
          $middle = $this->move_model->get_middle_move_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->move_model->drop_middle_exits_data($rows->DocEntry);
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารเข้า SAP แล้วไม่สามารถยกเลิกได้";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function print_move($code)
  {
    $this->load->library('printer');
    $doc = $this->move_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->move_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        // $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        // $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_move', $ds);
  }



  private function do_export($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_move($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }



  public function export_move($code)
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


  public function clear_filter()
  {
    $filter = array(
      'move_code',
      'move_from_warehouse',
      'move_user',
      'move_to_warehouse',
      'move_fromDate',
      'move_toDate',
      'move_status',
      'move_is_export'
    );

    clear_filter($filter);
    echo 'done';
  }

} //--- end class
?>
