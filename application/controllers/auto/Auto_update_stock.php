<?php
class Auto_update_stock extends CI_Controller
{
  public $home;
  public $ms;
  public $title = "Auto Cancel orders";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;
  public $sync_api_stock = FALSE;
  public $ix_warehouse = NULL;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_update_stock';

    $this->load->model('masters/products_model');

    $this->ms = $this->load->database('ms', TRUE);
    $this->sync_api_stock = is_true(getConfig('SYNC_IX_STOCK'));
    $this->ix_warehouse = getConfig('IX_WAREHOUSE');

    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }


  public function index()
  {
    $limit = getConfig('AUTO_SEND_STOCK_LIMIT');

    $limit = empty($limit) ? 50 : $limit;

    $ds['data'] = NULL;
    $all = $this->db->where('status', 0)->count_all_results('auto_send_stock');
    $rs = $this->db->where('status', 0)->limit($limit)->get('auto_send_stock');

    $ds['count'] = $rs->num_rows();
    $ds['all'] = $all;
    $ds['data'] = $rs->result();

    $this->load->view('auto/auto_send_stock', $ds);
  }


  public function send_stock()
  {
    $sc = TRUE;

    if($this->sync_api_stock)
    {
      $limit = getConfig('AUTO_SEND_STOCK_LIMIT');
      $limit = empty($limit) ? 50 : $limit;
      $syncList = $this->get_sync_list($limit);

    //  print_r($syncList);

      $sync_stock = [];

      if( ! empty($syncList))
      {
        foreach($syncList as $rs)
        {
          $item = $this->get_item($rs->code);

          if( ! empty($item) && $item->count_stock)
          {
            $sync_stock[] = (object) array('id' => $rs->id, 'code' => $item->code, 'rate' => $item->api_rate);
          }
          else
          {
            $this->update_result([$rs->id], 3, "item not found");
          }
        }
      }

      if( ! empty($sync_stock))
      {
        $this->update_api_stock($sync_stock);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "API not enable";
    }

    echo $sc === TRUE ? 'success' : 'failed';
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
        $pd_in = [];

        if($this->wrx_stock_api->update_available_stock($item, $warehouse_code))
        {
          foreach($item as $pd)
          {
            $pd_in[] = $pd->id;
          }

          $this->update_result($pd_in, 1, NULL);
        }
        else
        {
          foreach($item as $pd)
          {
            $pd_in[] = $pd->id;
          }

          $this->update_result($pd_in, 3, "API Error : {$this->wrx_stock_api->error}");
        }
      }

      return TRUE;
    }
  }

  public function update_result(array $ids = array(), $status = 1, $message = NULL)
  {
    if( ! empty($ids))
    {
      $this->db->set('status', $status);

      if( ! empty($message))
      {
        $this->db->set('message', $message);
      }

      return $this->db->where_in('id', $ids)->update('auto_send_stock');
    }

    return FALSE;
  }

  public function get_sync_list($limit = 1000)
  {
    $rs = $this->db
    ->where('status', 0)
    ->order_by('id', 'ASC')
    ->limit($limit)
    ->get('auto_send_stock');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_item($code)
  {
    $rs = $this->db->select('code, is_api, api_rate, count_stock')->where('code', $code)->get('products');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

} //--- end class
 ?>
