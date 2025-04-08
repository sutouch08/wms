<?php
class Auto_check_backorder extends CI_Controller
{
  public $home;
  public $ms;
  public $title = "Auto check backorder";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;
  public $limit = 1000;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->home = base_url().'auto/auto_check_backorder';
    $this->load->model('orders/orders_model');
    $this->load->model('stock/stock_model');
    $this->load->model('sync_data_model');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    ini_set('memory_limit','2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','2097152'); // Setting to 512M
    ini_set('sqlsrv.client_buffer_max_kb_size','2097152'); // Setting to 512M - for pdo_sqlsrv

    $count = 0;
    $update = 0;

    $orders = $this->get_backorder_list($this->limit);

    if( ! empty($orders))
    {
      foreach($orders as $rs)
      {
        $count++;

        if($rs->state == '9' OR $rs->is_expired)
        {
          $this->orders_model->update($rs->code, ['is_backorder' => 0]);
          $update++;
        }
        else
        {
          $details = $this->get_details($rs->code);

          if( ! empty($details))
          {
            $is_backorder = 0;

            foreach($details as $rd)
            {
              if($rd->is_count)
              {
                $available = $this->get_available_stock($rd->product_code, $rs->warehouse_code);

                if($available < $rd->qty)
                {
                  $is_backorder = 1;
                }
              }
            }

            if($is_backorder == 0)
            {
              $this->orders_model->update($rs->code, ['is_backorder' => 0, 'last_sync' => now()]);
              $this->orders_model->drop_backlogs_list($rs->code);
              $update++;
            }
            else
            {
              $this->orders_model->update($rs->code, ['last_sync' => now()]);
            }
          }
        }
      }
    }

    $logs = array(
      'sync_item' => 'BACKORDER',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);
  }


  public function get_details($code)
  {
    $rs = $this->db
    ->select('product_code, qty, is_count')
    ->where('order_code', $code)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_backorder_list($limit = 100)
  {
    $max_id = $this->orders_model->get_max_id();

    $rs = $this->db
    ->select('code, state, status, is_expired, warehouse_code')
    ->where('role', 'S')
    ->where('id >', $max_id)
    ->where('is_pre_order', 0)
    ->where('is_backorder', 1)
    ->order_by('last_sync', 'ASC')
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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

} //--- end class
 ?>
