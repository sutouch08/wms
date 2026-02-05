<?php
class Import_consign_order extends CI_Controller
{
  public $ms;
  public $mc;
	public $wms;
  public $_user;
  public $error;
  public $message;


  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
		$this->wms = $this->load->database('wms', TRUE);

    $uid = get_cookie('uid');

		$this->_user = $this->user_model->get_user_by_uid($uid);

    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
		$this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
		$this->load->model('masters/sender_model');
    $this->load->model('address/customer_address_model');
    $this->load->model('orders/order_import_logs_model');

    $this->load->library('excel');
    $this->load->library('stock');
  }


  public function index()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $import = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;
    $role = $this->input->post('role');
    $prefix = $role == 'C' ? 'WC-Orders-' : 'WT-Orders-';

    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path'). ($role == 'C' ? 'consign_so/' : 'consign_tr/');
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> $prefix.date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      $excel->setActiveSheetIndex(0);

      $worksheet	= $excel->getSheet(0);

      if( ! empty($worksheet))
      {
        $count = $worksheet->getHighestRow();
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

        if($count > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        if($sc === TRUE)
        {
          $ds = $this->parse_order_data($role, $worksheet);

          if( ! empty($ds))
          {
            $ix_backorder = is_true(getConfig('IX_BACK_ORDER'));
            $ix_warehouse = getConfig('IX_WAREHOUSE');
            $sync_api_stock = is_true(getConfig('SYNC_IX_STOCK'));
            $sync_stock = [];

            foreach($ds as $order)
            {
              $import++;

              $res = TRUE;
              $message = "";
              //---- เช็คว่ามีออเดอร์ที่สร้างด้วย reference แล้วหรือยัง
              //---- ถ้ายังไม่มีให้สร้างใหม่
              //---- ถ้ามีแล้วและยังไม่ได้ยกเลิก ไม่สามารถเพิ่มใหม่ได้
              $order_code = $this->orders_model->get_active_order_code_by_reference($order->reference);
              $is_backorder = 0;
              $backorderList = [];
              $total_amount = 0;
              $total_qty = 0;

              if( empty($order_code) )
              {
                $this->db->trans_begin();

                $order_code = $this->get_new_code($order->role, $order->date_add);

                $arr = array(
                  'code' => $order_code,
                  'role' => $order->role,
                  'bookcode' => $order->bookcode,
                  'reference' => $order->reference,
                  'customer_code' => $order->customer_code,
                  'customer_name' => $order->customer_name,
                  'state' => $order->state,
                  'is_paid' => $order->is_paid,
                  'is_term' => $order->is_term,
                  'status' => $order->status,
                  'gp' => $order->gp,
                  'date_add' => $order->date_add,
                  'warehouse_code' => $order->warehouse_code,
                  'zone_code' => $order->zone_code,
                  'user' => $order->user,
                  'is_import' => $order->is_import,
                  'remark' => $order->remark,
                  'id_address' => $order->id_address,
                  'id_sender' => $order->id_sender
                );

                //--- add order
                if( ! $this->orders_model->add($arr))
                {
                  $res = FALSE;
                  $message = "Failed to create order for orderNumber {$order->reference}";
                }

                if($res === TRUE)
                {
                  if( ! empty($order->items))
                  {
                    foreach($order->items as $row)
                    {
                      $arr = array(
                        'order_code' => $order_code,
                        'style_code' => $row->style_code,
                        'product_code' => $row->product_code,
                        'product_name' => $row->product_name,
                        'cost' => $row->cost,
                        'price' => $row->price,
                        'qty' => $row->qty,
                        'discount1' => $row->discount1,
                        'discount2' => $row->discount2,
                        'discount3' => $row->discount3,
                        'discount_amount' => $row->discount_amount,
                        'total_amount' => $row->total_amount,
                        'is_count' => $row->is_count,
                        'is_import' => $row->is_import
                      );

                      if( ! $this->orders_model->add_detail($arr))
                      {
                        $res = FALSE;
                        $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                      }
                      else
                      {
                        $total_amount += $row->total_amount;
                        $total_qty += $row->qty;

                        if($ix_backorder && $row->is_count)
                        {
                          $available = $this->stock->get_available_stock($row->product_code, $order->warehouse_code);

                          if($available < $row->qty)
                          {
                            $is_backorder = 1;

                            $backorderList[] = (object) array(
                              'order_code' => $order_code,
                              'product_code' => $row->product_code,
                              'order_qty' => $row->qty,
                              'available_qty' => $available
                            );
                          }
                        }

                        if($row->is_count && $row->is_api && $sync_api_stock && $order->warehouse_code == $ix_warehouse)
                        {
                          if( ! isset($sync_stock[$row->product_code]))
                          {
                            $sync_stock[$row->product_code] = (object) array('code' => $row->product_code, 'rate' => $row->api_rate);
                          }
                        }
                      }

                      if($res == FALSE)
                      {
                        break;
                      }
                    } //--- end foreach
                  } //--- end if ! empty($order->items)
                } //--- $sc === TRUE

                //-- add state
                if($res === TRUE)
                {
                  $arr = array(
                    'doc_total' => $total_amount,
                    'total_sku' => $this->orders_model->count_order_sku($order_code),
                    'is_backorder' => $is_backorder,
                    'is_approved' => 1
                  );

                  $this->orders_model->update($order_code, $arr);

                  $arr = array(
                    'order_code' => $order_code,
                    'state' => $order->state,
                    'update_user' => $this->_user->uname
                  );

                  //--- add state event
                  $this->order_state_model->add_state($arr);

                  if($ix_backorder && ! empty($backorderList))
                  {
                    foreach($backorderList as $rs)
                    {
                      $backlogs = array(
                        'order_code' => $rs->order_code,
                        'product_code' => $rs->product_code,
                        'order_qty' => $rs->order_qty,
                        'available_qty' => $rs->available_qty
                      );

                      $this->orders_model->add_backlogs_detail($backlogs);
                    }
                  }
                }

                if($res === TRUE)
                {
                  $this->db->trans_commit();
                  $success++;
                }
                else
                {
                  $this->db->trans_rollback();
                  $failed++;
                }

                //--- add logs
                $logs = array(
                  'reference' => $order->reference,
                  'order_code' => $order_code,
                  'action' => 'A', //-- A = add , U = update
                  'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                  'message' => $res === TRUE ? NULL : $message,
                  'user' => $this->_user->uname
                );

                $this->order_import_logs_model->add($logs);
              }
              else
              {
                if($order->force_update)
                {
                  $doc = $this->orders_model->get($order_code);

                  if( ! empty($doc) && $doc->state <= 3)
                  {
                    $this->db->trans_begin();

                    $arr = array(
                      'customer_code' => $order->customer_code,
                      'customer_name' => $order->customer_name,
                      'state' => $order->state,
                      'is_term' => $order->is_term,
                      'status' => $order->status,
                      'date_add' => $order->date_add,
                      'warehouse_code' => $order->warehouse_code,
                      'zone_code' => $order->zone_code,
                      'gp' => $order->gp,
                      'user' => $order->user,
                      'is_import' => $order->is_import,
                      'remark' => $order->remark,
                      'id_address' => $order->id_address,
                      'id_sender' => $order->id_sender
                    );

                    if( ! $this->orders_model->update($order_code, $arr))
                    {
                      $res = FALSE;
                      $message = "Failed to update order {$order_code} for {$order->reference}";
                    }

                    if($res === TRUE)
                    {
                      //---- drop previous order rows
                      if( ! $this->orders_model->remove_all_details($order_code))
                      {
                        $res = FALSE;
                        $message = "Failed to remove previous order rows";
                      }
                      else
                      {
                        if( ! empty($order->items))
                        {
                          foreach($order->items as $row)
                          {
                            $arr = array(
                              'order_code' => $order_code,
                              'style_code' => $row->style_code,
                              'product_code' => $row->product_code,
                              'product_name' => $row->product_name,
                              'cost' => $row->cost,
                              'price' => $row->price,
                              'qty' => $row->qty,
                              'discount1' => $row->discount1,
                              'discount2' => $row->discount2,
                              'discount3' => $row->discount3,
                              'discount_amount' => $row->discount_amount,
                              'total_amount' => $row->total_amount,
                              'is_count' => $row->is_count,
                              'is_import' => $row->is_import
                            );

                            if( ! $this->orders_model->add_detail($arr))
                            {
                              $res = FALSE;
                              $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                            }
                            else
                            {
                              $total_amount += $row->total_amount;
                              $total_qty += $row->qty;

                              if($ix_backorder && $row->is_count)
                              {
                                $available = $this->stock->get_available_stock($row->product_code, $order->warehouse_code);

                                if($available < $row->qty)
                                {
                                  $is_backorder = 1;

                                  $backorderList[] = (object) array(
                                    'order_code' => $order_code,
                                    'product_code' => $row->product_code,
                                    'order_qty' => $row->qty,
                                    'available_qty' => $available
                                  );
                                }
                              }

                              if($row->is_count && $row->is_api && $sync_api_stock && $order->warehouse_code == $ix_warehouse)
                              {
                                if( ! isset($sync_stock[$row->product_code]))
                                {
                                  $sync_stock[$row->product_code] = (object) array('code' => $row->product_code, 'rate' => $row->api_rate);
                                }
                              }
                            }

                            if($res == FALSE)
                            {
                              break;
                            }
                          } //--- end foreach
                        } //--- end if ! empty($order->items)
                      } //--- end if remove all detail
                    } //--- if($res === TRUE)

                    //-- add state
                    if($res === TRUE)
                    {
                      $arr = array(
                        'doc_total' => $total_amount,
                        'total_sku' => $this->orders_model->count_order_sku($order_code),
                        'is_approved' => 1,
                        'is_backorder' => $is_backorder
                      );

                      $this->orders_model->update($order_code, $arr);

                      $arr = array(
                        'order_code' => $order_code,
                        'state' => $order->state,
                        'update_user' => $this->_user->uname
                      );

                      //--- add state event
                      $this->order_state_model->add_state($arr);

                      if($ix_backorder)
                      {
                        if( $this->orders_model->drop_backlogs_list($order_code))
                        {
                          if( ! empty($backorderList))
                          {
                            foreach($backorderList as $rs)
                            {
                              $backlogs = array(
                                'order_code' => $rs->order_code,
                                'product_code' => $rs->product_code,
                                'order_qty' => $rs->order_qty,
                                'available_qty' => $rs->available_qty
                              );

                              $this->orders_model->add_backlogs_detail($backlogs);
                            }
                          }
                        }
                      }
                    }

                    if($res === TRUE)
                    {
                      $this->db->trans_commit();
                      $success++;
                    }
                    else
                    {
                      $this->db->trans_rollback();
                      $failed++;
                    }

                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                      'message' => $message,
                      'user' => $this->_user->uname
                    );

                    $this->order_import_logs_model->add($logs);
                  }
                  else
                  {
                    $failed++;
                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => 'E', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                      'message' => "Invalid order state",
                      'user' => $this->_user->uname
                    );

                    $this->order_import_logs_model->add($logs);
                  }
                }
                else
                {
                  $skip++;
                  //--- add logs
                  $logs = array(
                    'reference' => $order->reference,
                    'order_code' => $order_code,
                    'action' => 'A', //-- A = add , U = update
                    'status' => 'D', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                    'message' => "{$order->reference} already exists",
                    'user' => $this->_user->uname
                  );

                  $this->order_import_logs_model->add($logs);
                }
              } //--- end if order exists
            } //--- end foreach

            if($sync_api_stock && ! empty($sync_stock))
            {
              $this->update_api_stock($sync_stock);
            }
          } //--- end if ! empty ds
          else
          {
            $sc = FALSE;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Cannot get data from import file : empty data collection";
      }
    } //-- end upload success

    $message = "Imported : {$import} <br/> Success : {$success} <br/> Failed : {$failed} <br/> Skip : {$skip}";
    $message .= $failed > 0 ? "<br/><br/> พบรายการที่ไม่สำเร็จ กรุณาตรวจสอบ Import logs" : "";

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? $message : $this->error
    );

    echo json_encode($arr);
  }


  private function parse_order_data($role, $sheet)
  {
    $sc = TRUE;    
    $whsItems = [];

    if( ! empty($sheet))
    {
      $bookcode = $role == 'C' ? getConfig('BOOK_CODE_CONSIGN_SO') : getConfig('BOOK_CODE_CONSIGN_TR');
      $default_warehouse = getConfig('DEFAULT_WAREHOUSE');
      $default_sender = 'WARRIX';

      $ds = array(); //---- ได้เก็บข้อมูล orders

      $whsCache = array(); //--- ไว้เก็บ  warehouse cache

      //--- เก็บ customer cache
      $customerCache = [];
      $warehouseCache = [];
      $zoneCache = [];
      $senderCache = [];
      $itemCache = [];

      $totalSKU = 0;
      $docTotal = 0;

      $headCol = array(
        'A' => 'Ref Code',
        'B' => 'Date',
        'C' => 'Customer Code',
        'D' => 'Warehouse Code',
        'E' => 'Zone Code',
        'F' => 'Item Code',
        'G' => 'Price',
        'H' => 'GP(%)',
        'I' => 'Qty',
        'J' => 'Sender Code',
        'K' => 'Remark',
        'L' => 'Force Update'
      );

      $rows = $sheet->getHighestRow();

      $i = 1;

      //---- รวมข้อมูลให้เป็น array ก่อนนำไปใช้สร้างออเดอร์
      while($i < $rows)
      {
        if($sc === FALSE) { break; }

        if($i == 1)
        {
          foreach($headCol as $col => $field)
          {
            $value = $sheet->getCell($col.$i)->getValue();

            if(empty($value) OR $value !== $field)
            {
              $sc = FALSE;
              $this->error .= 'Column '.$col.' Should be '.$field.'<br/>';
            }
          }

          if($sc === FALSE)
          {
            $this->error .= "<br/><br/>You should download new template !";
            break;
          }

          $i++;
        }
        else
        {
          $rs = [];

          foreach($headCol as $col => $field)
          {
            $column = $col.$i;

            $rs[$col] = $sheet->getCell($column)->getValue();
          }

          //--- Ref Code / Customer code / Wareouse Code / Zone Code / Item Code ต้องไม่เป็นค่าว่าง
          if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['C']) && ! empty($rs['D']) && ! empty($rs['E']) && ! empty($rs['F']))
          {
            $ref_code = trim($rs['A']);
            $customer_code = trim($rs['C']);
            $warehouse_code = trim($rs['D']);
            $zone_code = trim($rs['E']);
            $itemCode = trim($rs['F']);
            $sender_code = empty(trim($rs['J'])) ? 'WARRIX' : trim($rs['J']);
            $remark = get_null(trim($rs['K']));
            $force = trim($rs['L']);
            $force = ($force == 'Y' OR $force == 'y' OR $force == 1) ? TRUE : FALSE;

            //--- เช็คว่ามี key อยู่แล้วหรือไม่
            //--- ถ้ายังไม่มีให้สร้างใหม่ ถ้ามีแล้ว ให้เพิ่ม รายการสินค้าเข้าไป
            if( ! isset($ds[$ref_code]))
            {
              $customer = empty($customerCache[$customer_code]) ? $this->customers_model->get($customer_code) : $customerCache[$customer_code];
              $zone = empty($zoneCache[$zone_code]) ? $this->zone_model->get($zone_code) : $zoneCache[$zone_code];
              $warehouse = empty($warehouseCache[$warehouse_code]) ? $this->warehouse_model->get($warehouse_code) : $warehouseCache[$warehouse_code];
              $item = empty($itemCache[$itemCode]) ? $this->products_model->get($itemCode) : $itemCache[$itemCode];
              $sender = empty($senderCache[$sender_code]) ? $this->sender_model->get_by_code($sender_code) : $senderCache[$sender_code];

              if(empty($customer))
              {
                $sc = FALSE;
                $this->error = "รหัสลูกค้าไม่ถูกต้อง ในบรรทัดที่ {$i}";
              }

              if($sc === TRUE)
              {
                if(empty($warehouse))
                {
                  $sc = FALSE;
                  $this->error = "รหัสคลังไม่ถูกต้อง ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                if(empty($zone) OR $zone->role != 2 OR ($role == 'C' && ! $zone->is_consignment))
                {
                  $sc = FALSE;
                  $this->error = "รหัสโซนไม่ถูกต้องหรือประเภทโซนไม่สอดคล้องกลับเอกสาร ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->zone_model->is_exists_customer($zone->code, $customer->code))
                {
                  $sc = FALSE;
                  $this->error = "รหัสลูกค้าไม่เชื่อมโยงกับโซน ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                if(empty($item))
                {
                  $sc = FALSE;
                  $this->error = "รหัสสินค้าไม่ถูกต้อง ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                if(empty($sender))
                {
                  $sc = FALSE;
                  $this->error = "รหัสผู้จัดส่งไม่ถูกต้อง ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                $cell = $sheet->getCell("B{$i}");
                $date = trim($cell->getValue());

                if (PHPExcel_Shared_Date::isDateTime($cell))
                {
                  $dateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($date);
                  $date_add = $dateTimeObject->format('Y-m-d');
                }
                else
                {
                  $date_add = db_date($date);
                }

                //--- check date format only check not convert
                if( ! is_valid_date($date_add))
                {
                  $sc = FALSE;
                  $this->error = "รูปแบบวันที่ไม่ถูกต้อง ในบรรทัดที่ {$i}";
                }
              }

              //---- add Cache
              if($sc === TRUE)
              {
                if( ! isset($customerCache[$customer->code]))
                {
                  $customerCache[$customer->code] = $customer;
                }

                if( ! isset($warehouseCache[$warehouse->code]))
                {
                  $warehouseCache[$warehouse->code] = $warehouse;
                }

                if( ! isset($zoneCache[$zone->code]))
                {
                  $zoneCache[$zone->code] = $zone;
                }

                if( ! isset($itemCache[$item->code]))
                {
                  $itemCache[$item->code] = $item;
                }

                if( ! isset($senderCache[$sender->code]))
                {
                  $senderCache[$sender->code] = $sender;
                }
              }

              if($sc === TRUE)
              {
                $id_address = $this->customer_address_model->get_customer_ship_to_id($customer->code);

                $price = str_replace(',', '', trim($rs['G']));
                $price = is_numeric($price) ? $price : "";
                $price = ($price == "" OR $price == NULL) ? $item->price : $price;

                $gp = str_replace(',', '', trim($rs['H']));
                $gp = is_numeric($gp) ? $gp : 0.00;
                $gp = $gp > 100 ? 100 : $gp;

                $discLabel = $gp > 0 ? $gp.'%' : $gp;
                $discAmount = $price * ($gp * 0.01);

                $qty = str_replace(',', '', trim($rs['I']));
                $qty = is_numeric($qty) ? $qty : 1;

                $lineTotal = $price * $qty;
                $lineDiscAmount = $discAmount * $qty;

                $total_amount = $lineTotal - $lineDiscAmount;

                //---- now create order data
                $ds[$ref_code] = (object) array(
                  'role' => $role,
                  'bookcode' => $bookcode,
                  'reference' => $ref_code,
                  'customer_code' => $customer->code,
                  'customer_name' => $customer->name,
                  'gp' => $discLabel,
                  'is_paid' => 0,
                  'is_term' => 1,
                  'status' => 1,
                  'state' => 3,
                  'date_add' => $date_add,
                  'warehouse_code' => $warehouse->code,
                  'zone_code' => $zone->code,
                  'user' => $this->_user->uname,
                  'is_import' => 1,
                  'remark' => $remark,
                  'id_address' => $id_address,
                  'id_sender' => $sender->id,
                  'force_update' => $force,
                  'items' => []
                );

                $ds[$ref_code]->items[] = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discLabel,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $lineDiscAmount,
                  "total_amount"	=> round($total_amount,2),
                  "is_count" => $item->count_stock,
                  "is_api" => $item->is_api,
                  "api_rate" => $item->api_rate,
                  "is_import" => 1
                );

                if($item->count_stock)
                {
                  if( ! isset($whsItems[$warehouse->code][$item->code]))
                  {
                    $whsItems[$warehouse->code][$item->code] = $qty;
                  }
                  else
                  {
                    $whsItems[$warehouse->code][$item->code] += $qty;
                  }
                }
              }
            }
            else
            {
              $item = empty($itemCache[$itemCode]) ? $this->products_model->get($itemCode) : $itemCache[$itemCode];

              if($sc === TRUE)
              {
                if(empty($item))
                {
                  $sc = FALSE;
                  $this->error = "รหัสสินค้าไม่ถูกต้อง ในบรรทัดที่ {$i}";
                }
              }

              if($sc === TRUE)
              {
                if( ! isset($itemsCache[$item->code]))
                {
                  $itemCache[$item->code] = $item;
                }
              }

              if($sc === TRUE)
              {
                $price = str_replace(',', '', trim($rs['G']));
                $price = is_numeric($price) ? $price : "";
                $price = ($price == "" OR $price == NULL) ? $item->price : $price;

                $gp = str_replace(',', '', trim($rs['H']));
                $gp = is_numeric($gp) ? $gp : 0.00;
                $gp = $gp > 100 ? 100 : $gp;

                $discLabel = $gp > 0 ? $gp.'%' : $gp;
                $discAmount = $price * ($gp * 0.01);

                $qty = str_replace(',', '', trim($rs['I']));
                $qty = is_numeric($qty) ? $qty : 1;

                $lineTotal = $price * $qty;
                $lineDiscAmount = $discAmount * $qty;

                $total_amount = $lineTotal - $lineDiscAmount;

                $ds[$ref_code]->items[] = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discLabel,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $lineDiscAmount,
                  "total_amount"	=> round($total_amount,2),
                  "is_count" => $item->count_stock,
                  "is_api" => $item->is_api,
                  "api_rate" => $item->api_rate,
                  "is_import" => 1
                );

                if($item->count_stock)
                {
                  $warehouse_code = $ds[$ref_code]->warehouse_code;

                  if( ! isset($whsItems[$warehouse_code][$item->code]))
                  {
                    $whsItems[$warehouse_code][$item->code] = $qty;
                  }
                  else
                  {
                    $whsItems[$warehouse_code][$item->code] += $qty;
                  }
                }
              }
            }
          }

          $i++;
        }
      } //-- end while
    }
    else
    {
      $sc = FALSE;
      $this->error = "Empty data collection";
    }

    if($sc === TRUE && ! is_true(getConfig('ALLOW_IMPORT_BACKORDER')) && ! empty($whsItems))
    {
      $this->error = "สต็อกคงเหลือไม่พอ <br/>";

      foreach($whsItems as $warehouse_code => $items)
      {
        if( ! empty($items))
        {
          foreach($items as $item_code => $qty)
          {
            $available = $this->stock->get_available_stock($item_code, $warehouse_code);

            if($available < $qty)
            {
              $sc = FALSE;
              $this->error .= "{$warehouse_code} | {$item_code} | Qty : {$qty} | Available : {$available} <br/>";
            }
          }
        }
      }
    }

    return $sc === TRUE ? $ds : FALSE;
  }

  //---- send calcurated stock to marketplace
  public function update_api_stock(array $ds = array())
  {
    if(is_true(getConfig('SYNC_IX_STOCK')) && ! empty($ds))
    {
      $this->load->library('wrx_stock_api');
      $warehouse_code = getConfig('IX_WAREHOUSE');

      $i = 0;
      $j = 0;

      $items = [];

      foreach($ds as $rs)
      {
        if($i == 20)
        {
          $i = 0;
          $j++;
        }

        $items[$j][$i] = $rs;
        $i++;
      }

      foreach($items as $item)
      {
        $this->wrx_stock_api->update_available_stock($item, $warehouse_code);
      }

      return TRUE;
    }
  }


  public function get_new_code($role = 'N', $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = $role == 'C' ? getConfig('PREFIX_CONSIGN_SO') : getConfig('PREFIX_CONSIGN_TR');
    $run_digit = $role == 'C' ? getConfig('RUN_DIGIT_CONSIGN_SO') : getConfig('RUN_DIGIT_CONSIGN_TR');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);

    if( ! is_null($code))
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
}

 ?>
