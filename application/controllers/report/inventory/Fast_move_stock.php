<?php
class Fast_move_stock extends PS_Controller
{
  public $menu_code = 'RCFMST';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือในโซน Fast move';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/fast_move_stock';
    $this->load->model('report/inventory/fast_move_stock_model');
    $this->load->model('inventory/buffer_model');
  }


  public function index()
  {
    $min_stock = getConfig('MIN_STOCK');
    $min_stock = $min_stock <= 0 ? 0 : intval($min_stock);
    $this->load->view('report/inventory/report_fast_move_stock', ['min_stock' => $min_stock]);
  }


  public function get_report()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $data = [];

    if( ! empty($ds))
    {
      $filter = array(
        'zone_code' => $ds->zone_code,
        'product_code' => $ds->product_code,
        'min_stock' => $ds->min_stock,
        'is_min' => $ds->is_min
      );

      $zones = $this->fast_move_stock_model->get_fast_move_zone($ds->zone_code);
      $items = [];
      $min_stock = intval($ds->min_stock);
      $is_min = $ds->is_min == 0 ? 0 : 1;
      $no = 1;

      if( ! empty($zones))
      {
        if( ! empty($ds->product_code))
        {
          $items = $this->fast_move_stock_model->get_item_list($ds->product_code);
        }

        foreach($zones as $zone)
        {
          $stock = $this->fast_move_stock_model->get_stock($zone->code, $is_min, $min_stock, $items);

          if( ! empty($stock))
          {
            foreach($stock as $rs)
            {
              $buffer = $this->buffer_model->get_buffer_zone($zone->code, $rs->product_code);
              $qty = $rs->qty - $buffer;
              $qty = $qty < 0 ? 0 : $qty;

              $data[] = array(
                'no' => $no,
                'zone_code' => $zone->code,
                'zone_name' => $zone->name,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $qty,
                'color' => $qty <= $min_stock ? 'color:red' : ''
              );

              $no++;
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $data
    );

    echo json_encode($arr);
  }

} //--- end class

?>
