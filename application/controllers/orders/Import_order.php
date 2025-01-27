<?php
class Import_order extends CI_Controller
{
  public $ms;
  public $mc;
	public $wms;
  public $_user;
  public $sokoApi;
  public $wmsApi;
  public $isAPI;
	public $sync_chatbot_stock = FALSE;
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
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
		$this->load->model('masters/warehouse_model');
		$this->load->model('masters/sender_model');
    $this->load->model('address/address_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/order_import_logs_model');

    $this->load->library('excel');

    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));

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

    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import_order-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);
    $this->load->library("soko_order_api");

    if(! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      if( ! empty($collection))
      {
        $count = count($collection);
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

        if($count > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        if($sc === TRUE)
        {
          $ds = $this->parse_order_data($collection);

          if( ! empty($ds))
          {
            $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');
            $shipping_item = ! empty($shipping_item_code) ? $this->products_model->get($shipping_item_code) : NULL;
            $ix_backorder = is_true(getConfig('IX_BACK_ORDER'));

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

              if( empty($order_code) )
              {
                $this->db->trans_begin();

                $order_code = $this->get_new_code($order->date_add);

                $arr = array(
                  'code' => $order_code,
                  'role' => $order->role,
                  'bookcode' => $order->bookcode,
                  'reference' => $order->reference,
                  'customer_code' => $order->customer_code,
                  'customer_name' => $order->customer_name,
                  'customer_ref' => $order->customer_ref,
                  'channels_code' => $order->channels_code,
                  'payment_code' => $order->payment_code,
                  'sale_code' => $order->sale_code,
                  'state' => $order->state,
                  'is_paid' => $order->is_paid,
                  'is_term' => $order->is_term,
                  'shipping_code' => $order->shipping_code,
                  'shipping_fee' => $order->shipping_fee,
                  'cod_amount' => $order->cod_amount,
                  'status' => $order->status,
                  'date_add' => $order->date_add,
                  'warehouse_code' => $order->warehouse_code,
                  'user' => $order->user,
                  'is_import' => $order->is_import,
                  'remark' => $order->remark,
                  'is_wms' => $order->is_wms,
                  'id_address' => $order->id_address,
                  'id_sender' => $order->id_sender,
                  'tax_status' => $order->tax_status,
                  'is_etax' => $order->is_etax,
                  'tax_id' => $order->tax_id,
                  'name' => $order->name,
                  'branch_code' => $order->branch_code,
                  'branch_name' => $order->branch_name,
                  'address' => $order->address,
                  'sub_district' => $order->sub_district,
                  'district' => $order->district,
                  'province' => $order->province,
                  'postcode' => $order->postcode,
                  'phone' => $order->phone,
                  'email' => $order->email
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
                        'id_rule' => $row->id_rule,
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

                        if($ix_backorder && $row->is_count)
                        {
                          $available = $this->get_available_stock($row->product_code, $order->warehouse_code);

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
                      }

                      if($res == FALSE)
                      {
                        break;
                      }
                    } //--- end foreach

                    //---- if has shipping fee  add shipping sku to order
                    if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                    {
                      $arr = array(
                        "order_code" => $order_code,
                        "style_code" => $shipping_item->style_code,
                        "product_code" => $shipping_item->code,
                        "product_name" => $shipping_item->name,
                        "cost" => $shipping_item->cost,
                        "price"	=> $order->shipping_fee,
                        "qty"	=> 1,
                        "discount1"	=> 0,
                        "discount2" => 0,
                        "discount3" => 0,
                        "discount_amount" => 0,
                        "total_amount"	=> $order->shipping_fee,
                        "id_rule"	=> NULL,
                        "is_count" => $shipping_item->count_stock,
                        "is_import" => 1
                      );

                      if( ! $this->orders_model->add_detail($arr))
                      {
                        $sc = FALSE;
                        $message = "Failed to insert shipping item row of {$order->reference}";
                      }
                      else
                      {
                        $total_amount += $order->shipping_fee;
                      }
                    } //--- end if($order->shipping_fee)
                  } //--- end if ! empty($order->items)
                } //--- $sc === TRUE

                //-- add state
                if($res === TRUE)
                {
                  $arr = array(
                    'doc_total' => $total_amount,
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

                if($order->is_wms == 2 && $this->sokoApi && ! $order->hold)
      					{
                  $this->soko_order_api->export_order($order_code);
      					}
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
                      'customer_ref' => $order->customer_ref,
                      'channels_code' => $order->channels_code,
                      'payment_code' => $order->payment_code,
                      'sale_code' => $order->sale_code,
                      'state' => $order->state,
                      'is_term' => $order->is_term,
                      'shipping_code' => $order->shipping_code,
                      'shipping_fee' => $order->shipping_fee,
                      'cod_amount' => $order->cod_amount,
                      'status' => $order->status,
                      'date_add' => $order->date_add,
                      'warehouse_code' => $order->warehouse_code,
                      'user' => $order->user,
                      'is_import' => $order->is_import,
                      'remark' => $order->remark,
                      'is_wms' => $order->is_wms,
                      'id_address' => $order->id_address,
                      'id_sender' => $order->id_sender,
                      'tax_status' => $order->tax_status,
                      'is_etax' => $order->is_etax,
                      'tax_id' => $order->tax_id,
                      'name' => $order->name,
                      'branch_code' => $order->branch_code,
                      'branch_name' => $order->branch_name,
                      'address' => $order->address,
                      'sub_district' => $order->sub_district,
                      'district' => $order->district,
                      'province' => $order->province,
                      'postcode' => $order->postcode,
                      'phone' => $order->phone,
                      'email' => $order->email
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
                              'id_rule' => $row->id_rule,
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

                              if($ix_backorder && $row->is_count)
                              {
                                $available = $this->get_available_stock($row->product_code, $order->warehouse_code);

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
                            }

                            if($res == FALSE)
                            {
                              break;
                            }
                          } //--- end foreach

                          //---- if has shipping fee  add shipping sku to order
                          if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                          {
                            $arr = array(
                              "order_code" => $order_code,
                              "style_code" => $shipping_item->style_code,
                              "product_code" => $shipping_item->code,
                              "product_name" => $shipping_item->name,
                              "cost" => $shipping_item->cost,
                              "price"	=> $order->shipping_fee,
                              "qty"	=> 1,
                              "discount1"	=> 0,
                              "discount2" => 0,
                              "discount3" => 0,
                              "discount_amount" => 0,
                              "total_amount"	=> $order->shipping_fee,
                              "id_rule"	=> NULL,
                              "is_count" => $shipping_item->count_stock,
                              "is_import" => 1
                            );

                            if( ! $this->orders_model->add_detail($arr))
                            {
                              $sc = FALSE;
                              $message = "Failed to insert shipping item row of {$order->reference}";
                            }
                            else
                            {
                              $total_amount += $order->shipping_fee;
                            }
                          } //--- end if($order->shipping_fee)
                        } //--- end if ! empty($order->items)
                      } //--- end if remove all detail
                    } //--- if($res === TRUE)

                    //-- add state
                    if($res === TRUE)
                    {
                      $arr = array(
                        'doc_total' => $total_amount,
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

                    if($order->is_wms == 2 && $this->sokoApi && ! $order->hold)
          					{
                      $this->soko_order_api->export_order($order_code);
          					}
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


	private function parse_order_data($collection)
  {
    $sc = TRUE;

    if( ! empty($collection))
    {
      $bookcode = getConfig('BOOK_CODE_ORDER');
      $role = 'S';

      $sokoWh = getConfig('SOKOJUNG_WAREHOUSE');

      $default_warehouse = getConfig('WEB_SITE_WAREHOUSE_CODE');
      //---- กำหนดช่องทางขายสำหรับเว็บไซต์ เพราะมีลูกค้าแยกตามช่องทางการชำระเงินอีกที
      //---- เลยต้องกำหนดลูกค้าแยกตามช่องทางการชำระเงินต่างๆ สำหรับเว็บไซต์เท่านั้น
      //---- เพราะช่องทางอื่นๆในการนำเข้าจะใช้ช่องทางการชำระเงินแบบเดียวทั้งหมด
      //---- เช่น K plus จะจ่ายด้วยบัตรเครดิตทั้งหมด  LAZADA จะไปเรียกเก็บเงินกับทาง ลาซาด้า
      $web_channels = getConfig('WEB_SITE_CHANNELS_CODE');

      //--- รหัสลูกค้าสำหรับ COD เว็บไซต์
      $web_customer_cod = getConfig('CUSTOMER_CODE_COD');

      //--- รหัสลูกค้าสำหรับ 2c2p บนเว็บไซต์
      $web_customer_2c2p = getConfig('CUSTOMER_CODE_2C2P');

      //--- รหัสลูกค้าเริ่มต้น หากพอว่าไม่มีการระบุรหัสลูกค้าไว้ จะใช้รหัสนี้แทน
      $default_customer = getConfig('DEFAULT_CUSTOMER');

      $prefix = getConfig('PREFIX_SHIPPING_NUMBER');

      $ds = array(); //---- ได้เก็บข้อมูล orders

      $whsCache = array(); //--- ไว้เก็บ  warehouse cache

      //--- เก็บ channels cache
      $channelsCache = array();

      //--- เก็บ payment cache
      $paymentsCache = array();

      //--- เก็บ customer cache
      $customerCache = array(
        $default_customer => $this->customers_model->get($default_customer)
      );

      $senderCache = array();

      $addrCache = array();

      $itemsCache = array(); //--- เก็บ item cache

      $taxType = array(
        'NIDN' => 'NIDN', //-- บุคคลธรรมดา
        'TXID' => 'TXID', //-- นิติบุคคล
        'CCPT' => 'CCPT', //--- Passport
        'OTHR' => 'OTHR' //--- N/A
      );

      $headCol = array(
        'A' => 'Consignee Name',
        'B' => 'Address Line 1',
        'C' => 'Province',
        'D' => 'District',
        'E' => 'Sub District',
        'F' => 'postcode',
        'G' => 'email',
        'H' => 'tel',
        'I' => 'orderNumber',
        'J' => 'CreateDateTime',
        'K' => 'Payment Method',
        'L' => 'Channels',
        'M' => 'itemId',
        'N' => 'amount',
        'O' => 'price',
        'P' => 'discount amount',
        'Q' => 'shipping fee',
        'R' => 'service fee',
        'S' => 'force update',
        'T' => 'Shipping code',
        'U' => 'Hold',
        'V' => 'Remark',
        'W' => 'Carrier',
        'X' => 'Warehouse code',
        'Y' => 'Country',
        'Z' => 'Customer code',
        'AA' => 'COD amount',
        'AB' => 'Tax status',
        'AC' => 'E-TAX',
        'AD' => 'Tax type',
        'AE' => 'Tax ID',
        'AF' => 'Name',
        'AG' => 'Branch code',
        'AH' => 'Branch name',
        'AI' => 'Address',
        'AJ' => 'Sub district',
        'AK' => 'District',
        'AL' => 'Province',
        'AM' => 'Post code',
        'AN' => 'Phone',
        'AO' => 'Email'
      );

      $i = 1;
      //---- รวมข้อมูลให้เป็น array ก่อนนำไปใช้สร้างออเดอร์
      foreach($collection as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        if($i == 1)
        {
          $i++;

          foreach($headCol as $col => $field)
          {
            if( ! isset($rs[$col]) OR $rs[$col] !== $field)
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
        }
        else
        {
          //--- ชื่อผู้รับ / orderNumber / itemId ต้องไม่เป็นค่าว่าง
          if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['I']) && ! empty($rs['M']))
          {
            //--- ใช้ orderNumber เป็น key array
            //---  ถ้า orderNumber เริ่มต้นด้วย LA ให้ตัด 2 ตัวหน้าออก
            $ref_code = trim($rs['I']);
            $ref_code = substr($ref_code, 0, 2) == 'LA' ? substr($ref_code, 2) : $ref_code;

            //--- เช็คว่ามี key อยู่แล้วหรือไม่
            //--- ถ้ายังไม่มีให้สร้างใหม่ ถ้ามีแล้ว ให้เพิ่ม รายการสินค้าเข้าไป
            if( ! isset($ds[$ref_code]))
            {
              $date_add = db_date(trim($rs['J']));

              //--- check date format only check not convert
              if( ! is_valid_date($date_add))
              {
                $sc = FALSE;
                $this->error = "Invalid Date format at Line{$i}";
              }

              $warehouse_code = empty(trim($rs['X'])) ? $default_warehouse : trim($rs['X']);

              //--- check warehouse cache
              //--- if not extsts get form database and add to cache
              if( ! isset($whsCache[$warehouse_code]))
              {
                $warehouse = $this->warehouse_model->get($warehouse_code);

                if( ! empty($warehouse))
                {
                  $whsCache[$warehouse_code] = $warehouse_code;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid warehouse_code at Line{$i} <br/>";
                }
              }

              $is_wms = $warehouse_code == $sokoWh ? 2 : 0;

              $hold = $rs['U'] == 1 ? TRUE : FALSE;

              //---- กำหนดช่องทางการขายเป็นรหัส
              $channels_code = trim($rs['L']);

              if( ! empty($channels_code))
              {
                //--- check channels cache
                if( ! isset($channelsCache[$channels_code]))
                {
                  $channels = $this->channels_model->get($channels_code);

                  if( ! empty($channels))
                  {
                    $channelsCache[$channels_code] = $channels;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid channels at Line {$i} <br/>";
                  }
                }

                $channels = $channelsCache[$channels_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Channels is required at Line {$i} <br/>";
              }

              //--- กำหนดช่องทางการชำระเงิน
              $payment_code = trim($rs['K']);

              if( ! empty($payment_code))
              {
                if( ! isset($paymentsCache[$payment_code]))
                {
                  $payment = $this->payment_methods_model->get($payment_code);

                  if( ! empty($payment))
                  {
                    $paymentsCache[$payment_code] = $payment;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid Payment method at Line {$i} <br/>";
                  }
                }

                $payment = $paymentsCache[$payment_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Payment Method is required at Line {$i} <br/>";
              }

              $customer_code = empty(trim($rs['Z'])) ? NULL : trim($rs['Z']);

              if(empty($customer_code))
              {
                if($channels_code === $web_channels)
                {
                  if($payment_code === '2C2P')
                  {
                    //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระโดยบัตรเครดติ(2c2p)
                    $customer_code = $web_customer_2c2p;
                  }
                  else if($payment_code === 'COD')
                  {
                    //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระแบบ COD
                    $customer_code = $web_customer_cod;
                  }
                  else
                  {
                    $customer_code = empty($channels) ? $detault_customer : $channels->customer_code;
                  }
                }
                else
                {
                  //--- หากไม่ใช่ช่องทางเว็บไซต์
                  //--- กำหนดรหัสลูกค้าตามช่องทางขายที่ได้ผูกไว้
                  //--- หากไม่มีการผูกไว้ให้
                  $customer_code = empty($channels->customer_code) ? $default_customer : $channels->customer_code;
                }
              }

              //--- check customer Cache
              if( ! isset($customerCache[$customer_code]))
              {
                $customer = $this->customers_model->get($customer_code);

                if( ! empty($customer))
                {
                  $customerCache[$customer_code] = $customer;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid customer at Line {$i}";
                }
              }

              $customer = $customerCache[$customer_code];

              //--- check item cache
              $item_code = trim($rs['M']);

              if( ! isset($itemsCache[$item_code]))
              {
                $item = $this->products_model->get($item_code);

                if(empty($item))
                {
                  $item = $this->products_model->get_by_old_code($item_code);
                }

                if( ! empty($item))
                {
                  $itemsCache[$item->code] = $item;
                  $item_code = $item->code;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid Item code '{$item_code}' at Line {$i} <br/>";
                }
              }

              $item = empty($itemsCache[$item_code]) ? NULL : $itemsCache[$item_code];

              $tax_status = empty(trim($rs['AB'])) ? 0 : (trim($rs['AB']) == 1 ? 1 : 0);

              if($tax_status)
              {
                $is_etax = empty(trim($rs['AC'])) ? 0 : (trim($rs['AC']) && $tax_status == 1 ? 1 : 0);

                $tax_type = empty(trim($rs['AD'])) ? "NIDN" : trim($rs['AD']);
                $tax_type = empty($taxType[$tax_type]) ? "NIDN" : $tax_type;

                $tax_id = empty(trim($rs['AE'])) ? NULL : trim($rs['AE']);
                $name = empty(trim($rs['AF'])) ? NULL : trim($rs['AF']);
                $branch_code = empty(trim($rs['AG'])) ? '00000' : trim($rs['AG']);
                $branch_name = empty(trim($rs['AH'])) ? 'สำนักงานใหญ่' : trim($rs['AH']);
                $address = empty(trim($rs['AI'])) ? NULL : trim($rs['AI']);
                $sub_district = empty(trim($rs['AJ'])) ? NULL : trim($rs['AJ']);
                $district = empty(trim($rs['AK'])) ? NULL : trim($rs['AK']);
                $province = empty(trim($rs['AL'])) ? NULL : trim($rs['AL']);
                $postcode = empty(trim($rs['AM'])) ? NULL : trim($rs['AM']);
                $phone = empty(trim($rs['AN'])) ? NULL : trim($rs['AN']);
                $email = empty(trim($rs['AO'])) ? NULL : trim($rs['AO']);

                if($is_etax && empty($email))
                {
                  $sc  = FALSE;
                  $this->error .= "Email is required for E-Tax receipt at row{$i}";
                }

                if(empty($tax_id) OR empty($name) OR empty($address) OR empty($sub_district) OR empty($sub_district) OR empty($province))
                {
                  $sc = FALSE;
                  $this->error .= "When Tax staus = 1. You must fill in all required fields [tax_id, name, address, sub_district, district, province] at row{$i}";
                }
              }


              if($sc === TRUE)
              {
                //---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
                $sale_code = empty($customer) ? NULL : $customer->sale_code;

                //---	หากเป็นออนไลน์ ลูกค้าออนไลน์ชื่ออะไร
                $customer_ref = addslashes(trim($rs['A']));

                //--- ค่าจัดส่ง
                $shipping_fee = empty($rs['Q']) ? 0.00 : trim($rs['Q']);
                $shipping_fee = is_numeric($shipping_fee) ? $shipping_fee : 0.00;

                //--- shipping Number
                $shipping_code = trim($rs['T']);

                //--- ค่าบริการอื่นๆ
                $service_fee = 0;

                //---	Cod amount
                $cod_amount = $payment_code == 'COD' ? (empty($rs['AA']) ? 0.00 : $rs['AA']) : 0.00;
                $cod_amount = is_numeric($cod_amount) ? $cod_amount : 0;

                //-- remark
                $remark = get_null(trim($rs['V']));

                $id_address = $this->address_model->get_id($customer_ref, trim($rs['B']));

                if( empty($id_address))
                {
                  $arr = array(
                    'code' => $customer_ref,
                    'name' => $customer_ref,
                    'address' => trim($rs['B']),
                    'sub_district' => trim($rs['E']),
                    'district' => trim($rs['D']),
                    'province' => trim($rs['C']),
                    'postcode' => trim($rs['F']),
                    'phone' => trim($rs['H']),
                    'country' => empty(trim($rs['Y'])) ? "Thailand" : trim($rs['Y']),
                    'alias' => 'Home',
                    'is_default' => 1
                  );

                  $id_address = $this->address_model->add_shipping_address($arr);
                }

                $qty = empty(trim($rs['N'])) ? 1 : str_replace(',', '', $rs['N']);
                $qty = is_numeric($qty) ? $qty : 1;

                //--- ราคา (เอาราคาที่ใส่มา / จำนวน + ส่วนลดต่อชิ้น)
                $price = empty($rs['O']) ? 0.00 : str_replace(",", "", $rs['O']); //--- ราคารวมไม่หักส่วนลด
                $price = is_numeric($price) ? $price : 0;
                $price = $price > 0 ? ($price/$qty) : 0; //--- ราคาต่อชิ้น

                //--- ส่วนลด (รวม)
                $discount_amount = empty(trim($rs['P'])) ? 0.00 : str_replace(',', '', trim($rs['P']));
                $discount_amount = is_numeric($discount_amount) ? $discount_amount : 0;

                //--- ส่วนลด (ต่อชิ้น)
                $discount = $discount_amount > 0 ? ($discount_amount / $qty) : 0;

                //--- total_amount
                $total_amount = ($price * $qty) - $discount_amount;

                //---- now create order data
                $ds[$ref_code] = (object) array(
                  'role' => $role,
                  'bookcode' => $bookcode,
                  'reference' => $ref_code,
                  'customer_code' => $customer_code,
                  'customer_name' => $customer->name,
                  'customer_ref' => $customer_ref,
                  'channels_code' => $channels_code,
                  'payment_code' => $payment_code,
                  'sale_code' => $sale_code,
                  'state' => $hold ? 1 : 3,
                  'is_paid' => 0,
                  'is_term' => $payment->has_term,
                  'shipping_code' => $shipping_code,
                  'shipping_fee' => $shipping_fee,
                  'cod_amount' => $cod_amount,
                  'status' => 1,
                  'date_add' => $date_add,
                  'warehouse_code' => $warehouse_code,
                  'user' => $this->_user->uname,
                  'is_import' => 1,
                  'remark' => $remark,
                  'is_wms' => $is_wms,
                  'id_address' => $id_address,
                  'id_sender' => empty(trim($rs['W'])) ? NULL : $this->sender_model->get_id(trim($rs['W'])),
                  'force_update' => $rs['S'] == 1 ? TRUE : FALSE,
                  'hold' => $hold,
                  'tax_status' => $tax_status,
                  'tax_type' => $tax_status ? $tax_type : NULL,
                  'is_etax' => $tax_status ? $is_etax : 0,
                  'tax_id' => $tax_status ? $tax_id : NULL,
                  'name' => $tax_status ? $name : NULL,
                  'branch_code' => $tax_status ? $branch_code : NULL,
                  'branch_name' => $tax_status ? $branch_name : NULL,
                  'address' => $tax_status ? $address : NULL,
                  'sub_district' => $tax_status ? $sub_district : NULL,
                  'district' => $tax_status ? $district : NULL,
                  'province' => $tax_status ? $province : NULL,
                  'postcode' => $tax_status ? $postcode : NULL,
                  'phone' => $tax_status ? $phone : NULL,
                  'email' => $tax_status ? $email : NULL,
                  'items' => array()
                );

                $row = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discount,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> round($total_amount,2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                );

                $ds[$ref_code]->items[$item->code] = $row;
              } //--- end if $sc == TRUE
            }
            else
            {
              //--- check item cache
              $item_code = trim($rs['M']);

              if( ! isset($itemsCache[$item_code]))
              {
                $item = $this->products_model->get($item_code);

                if(empty($item))
                {
                  $item = $this->products_model->get_by_old_code($item_code);
                }

                if( ! empty($item))
                {
                  $itemsCache[$item_code] = $item;
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid Item code at Line {$i}";
                }
              }

              $item = $itemsCache[$item_code];

              $qty = empty(trim($rs['N'])) ? 1 : str_replace(',', '', $rs['N']);
              $qty = is_numeric($qty) ? $qty : 1;

              //--- ราคา (เอาราคาที่ใส่มา / จำนวน + ส่วนลดต่อชิ้น)
              $price = empty($rs['O']) ? 0.00 : str_replace(",", "", $rs['O']); //--- ราคารวมไม่หักส่วนลด
              $price = is_numeric($price) ? $price : 0;
              $price = $price > 0 ? ($price/$qty) : 0; //--- ราคาต่อชิ้น

              //--- ส่วนลด (รวม)
              $discount_amount = empty(trim($rs['P'])) ? 0.00 : str_replace(',', '', trim($rs['P']));
              $discount_amount = is_numeric($discount_amount) ? $discount_amount : 0;

              //--- ส่วนลด (ต่อชิ้น)
              $discount = $discount_amount > 0 ? ($discount_amount / $qty) : 0;

              //--- total_amount
              $total_amount = ($price * $qty) - $discount_amount;

              if(isset($ds[$ref_code]->items[$item->code]))
              {
                $row = $ds[$ref_code]->items[$item->code];
                $newPrice = ($row->price + $price) / 2; //--- เฉลี่ยราคา
                $newQty = $row->qty + $qty;
                $newDisc = $row->discount1 + $discount;
                $newDiscAmount = $row->discount_amount + $discount_amount;
                $newTotal = $row->total_amount + $total_amount;

                $ds[$ref_code]->items[$item->code]->qty = $newQty;
                $ds[$ref_code]->items[$item->code]->price = $newPrice;
                $ds[$ref_code]->items[$item->code]->discount1 = $newDisc;
                $ds[$ref_code]->items[$item->code]->discount_amount = $newDiscAmount;
                $ds[$ref_code]->items[$item->code]->total_amount = $newTotal;
              }
              else
              {
                $row = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> $discount,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> round($total_amount,2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                );

                $ds[$ref_code]->items[$item->code] = $row;
              }
            } //--- end if( ! isset($ds[$ref_code]));
          } //--- end if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['I']));

          $i++;
        } //--- end if $i == 1
      } //---- end foreach collection
    }
    else
    {
      $sc = FALSE;
      $this->error = "Empty data collection";
    }

    return $sc === TRUE ? $ds : FALSE;
  }


  public function get_available_stock($item_code, $warehouse_code)
  {
    //---- สต็อกคงเหลือในคลัง
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse_code);

    //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse_code);

    $available = $sell_stock - $reserv_stock;

    return $available < 0 ? 0 : $available;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
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
}

 ?>
