<?php
class Auto_process_order extends CI_Controller
{  
	public $user = 'system';
	
  public function __construct()
  {
    parent::__construct();    
		$this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
		$this->load->model('inventory/prepare_model');
		$this->load->model('inventory/buffer_model');
		$this->load->model('inventory/qc_model');
    $this->load->model('masters/warehouse_model');		
  }


  public function index()
  {
    $start = now();
    $get_items = 0;
    $update_items = 0;

    $limit = intval(getConfig('AUTO_CONFRIM_ORDER_LIMIT') ?? 100);
    $list = $this->get_list($limit);
    $whsCode = ""; //-- cache for checking warehouse change
    $whs = NULL; //--- cache for warehouse object    

    if( ! empty($list))
    {
      $get_items += count($list ?? []);

      foreach($list as $order)
      {
        $res = TRUE;

        if ($whsCode !== $order->warehouse_code)
        {
          $whs = $this->warehouse_model->get($order->warehouse_code);
          $whsCode = $order->warehouse_code;
        }

        if(empty($whs) || empty($whs->default_zone))
        {
          $update_items++;
          $logs = [
            'code' => $order->code,
            'status' => 'failed',
            'message' => "Warehouse or default zone is empty"
          ];

          $this->db->insert('auto_process_order_logs', $logs);
          continue;
        }

        $details = $this->orders_model->get_order_details($order->code);

        if(empty($details))
        {
          $update_items++;
          $logs = [
            'code' => $order->code,
            'status' => 'failed',
            'message' => "Order details not found"
          ];

          $this->db->insert('auto_process_order_logs', $logs);
          continue;
        }

        if( ! empty($details))
        {
          $this->db->trans_begin();

          $arr = array(
            'state' => 7,
            'update_user' => $this->user
          );

          if ($res === TRUE && ! $this->orders_model->update($order->code, $arr))
          {
            $res = FALSE;
            $this->error = "Failed to update order";
          }

          $arr = array(
            'order_code' => $order->code,
            'state' => 7,
            'update_user' => $this->user
          );

          if($res === TRUE && ! $this->order_state_model->add_state($arr))
          {
            $res = FALSE;
            $this->error = "Failed to add order state";
          }

          if($res === TRUE && ! $this->prepare_model->drop_prepare($order->code))
          {
            $res = FALSE;
            $this->error = "Failed to drop prepare";
          }

          if($res === TRUE && ! $this->buffer_model->drop_buffer($order->code))
          {
            $res = FALSE;
            $this->error = "Failed to drop buffer";
          }

          if($res === TRUE && ! $this->qc_model->drop_qc($order->code))
          {
            $res = FALSE;
            $this->error = "Failed to drop qc";
          }

          if($res === TRUE)
          {
            foreach($details as $rs)
            {
              if($res === FALSE) break;

              if($rs->is_count)
              {
                //--- insert prepare 
                $prepare = array(
                  'order_code' => $order->code,
                  'product_code' => $rs->product_code,
                  'warehouse_code' => $whs->code,
                  'zone_code' => $whs->default_zone,
                  'qty' => $rs->qty,
                  'user' => $this->user,
                  'order_detail_id' => $rs->id
                );

                if($res === TRUE && ! $this->prepare_model->add($prepare))
                {
                  $res = FALSE;
                  $this->error = "Failed to add prepare";
                }

                //--- insert buffer
                $buffer = array(
                  'order_code' => $order->code,
                  'product_code' => $rs->product_code,
                  'warehouse_code' => $whs->code,
                  'zone_code' => $whs->default_zone,
                  'qty' => $rs->qty,
                  'user' => $this->user,
                  'order_detail_id' => $rs->id
                );

                if($res === TRUE && ! $this->buffer_model->add($buffer))
                {
                  $res = FALSE;
                  $this->error = "Failed to add buffer";
                }

                //--- insert qc
                $qc = array(
                  'order_code' => $order->code,
                  'product_code' => $rs->product_code,                  
                  'qty' => $rs->qty,
                  'box_id' => NULL,
                  'user' => $this->user,
                  'order_detail_id' => $rs->id
                );

                if($res === TRUE && ! $this->qc_model->add($qc))
                {
                  $res = FALSE;
                  $this->error = "Failed to add qc";
                }                                            
              }              
            }
          }

          if($res === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }

        $update_items++;

        $logs = array(
          'code' => $order->code,
          'status' => $res === TRUE ? 'success' : 'failed',
          'message' => $res === TRUE ? 'success' : $this->error
        );

        $this->db->insert('auto_process_order_logs', $logs);
      }
    }

    $logs = array(
      'sync_item' => 'PROCESS_ORDER',
      'get_item' => $get_items,
      'update_item' => $update_items,
      'process_start' => $start,
      'process_end' => now()
    );

    $this->db->insert('ix_sync_logs', $logs);
  }

  public function get_list($limit = 100)
  {
    $rs = $this->db
    ->where('role', 'S')
    ->where('state', 3)
    ->where('status', 1)
    ->where('auto_shipping', 1)
    ->where('is_cancled', 0)
    ->order_by('date_add', 'ASC')
    ->limit($limit)
    ->get('orders');

    return $rs->num_rows() > 0 ? $rs->result() : NULL;
  }
}
