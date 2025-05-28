<?php
class Auto_cancel_order extends CI_Controller
{
  public $home;
  public $sync_api_stock = FALSE;
  public $ix_warehouse = NULL;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_cancel_order';
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
		$this->load->model('inventory/movement_model');
    $this->load->model('masters/products_model');

    $this->sync_api_stock = is_true(getConfig('SYNC_IX_STOCK'));
    $this->ix_warehouse = getConfig('IX_WAREHOUSE');
  }

  public function index()
  {
    $limit = 100;
    $order_list = $this->get_cancel_list($limit);

    if( ! empty($order_list))
    {
      foreach($order_list as $rs)
      {
        $sc = TRUE;

        $code = $rs->code;

        $this->db->trans_begin();

        //--- 1. เคลียร์ buffer
        if( ! $this->buffer_model->delete_all($code) )
        {
          $sc = FALSE;
        }

        //--- 2. ลบประวัติการจัดสินค้า
        if($sc === TRUE)
        {
          if( ! $this->prepare_model->clear_prepare($code) )
          {
            $sc = FALSE;
          }
        }

        //--- 3. ลบประวัติการตรวจสินค้า
        if($sc === TRUE)
        {
          if( ! $this->qc_model->clear_qc($code) )
          {
            $sc = FALSE;
          }
        }

  			//--- remove movement
  	    if($sc === TRUE)
  	    {
  	      if( ! $this->movement_model->drop_movement($code) )
  	      {
  	        $sc = FALSE;
  	      }
  	    }

        //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
        if($sc === TRUE)
        {
          if( ! $this->orders_model->cancle_order_detail($code) )
          {
            $sc = FALSE;
            $this->error = "Cancle Order details failed";
          }
        }

        //--- 5. ยกเลิกออเดอร์
        if($sc === TRUE)
        {
          $arr = array(
            'state' => 9,
            'status' => 2,
            'inv_code' => NULL,
            'is_exported' => 0,
            'is_report' => NULL
          );

          if(! $this->orders_model->update($code, $arr) )
          {
            $sc = FALSE;
          }
        }

        // 6. add state change
        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'state' => 9,
            'update_user' => "System"
          );

          if( ! $this->order_state_model->add_state($arr) )
          {
            $sc = FALSE;
          }
        }

        // 7. add reason to table order_cancle_reason
        if($sc === TRUE)
        {
          $reason = array(
            'code' => $code,
            'reason_id' => 4,
            'reason' => "ยกเลิกอัตโนมัติ เนื่องจากลูกค้ายกเลิกบน marketplace",
            'user' => "System"
          );

          $this->orders_model->add_cancle_reason($reason);
        }

        // 8. drop back order list
        if($sc === TRUE)
        {
          $this->orders_model->drop_backlogs_list($code);
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();

          if($this->sync_api_stock)
          {
            $order = $this->orders_model->get($code);
            $warehouse_code = empty($order) ? "" : $order->warehouse_code;

            if($this->ix_warehouse == $warehouse_code && ! $order->is_pre_order)
            {
              $details = $this->orders_model->get_order_details($code);

              if( ! empty($details))
              {
                $sync_stock = array();

                foreach($details as $detail)
                {
                  if($detail->is_count == 1)
                  {
                    $item = $this->products_model->get($detail->product_code);

                    if( ! empty($item) && $item->is_api)
                    {
                      $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                    }
                  }
                }

                if( ! empty($sync_stock))
                {
                  $this->update_api_stock($sync_stock);
                }
              }
            }
          }
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      // end foreach
    }
  }


  //---- send calcurated stock to marketplace
  private function update_api_stock(array $ds = array())
  {
    if($this->sync_api_stock && ! empty($ds))
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


  private function get_cancel_list($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'S')
    ->where('is_cancled', 1)
    ->where_in('state', [1,3])
    ->order_by('id', 'ASC')
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>
