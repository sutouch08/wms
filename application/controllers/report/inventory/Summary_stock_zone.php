<?php
class Summary_stock_zone extends PS_Controller
{
  public $menu_code = 'RISMST';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานยอดรวมสินค้าในโซน';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/summary_stock_zone';
    $this->load->model('report/inventory/summary_stock_zone_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $whsCode = getConfig('DEFAULT_WAREHOUSE');
    $whsName = warehouse_name($whsCode);
    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
    $ds = [
      'whsCode' => $whsCode,
      'whsName' => $whsName,
      'rows' => $rows
    ];

    $this->load->view('report/inventory/summary_stock_zone/report_summary_stock_zone', $ds);
  }


  public function get_report()
  {
    ini_set('memory_limit','2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','2097152'); // Setting to 2048M
    ini_set('sqlsrv.client_buffer_max_kb_size','2097152'); // Setting to 512M - for pdo_sqlsrv

    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $data = [];

    if( ! empty($ds))
    {
      $whsCode = getConfig('DEFAULT_WAREHOUSE');
      $option = $ds->option; // E = Only zero sum, S = Only lessthan 1000, A = All
      $res = [];

      if( ! empty($ds->rows))
      {
        foreach($ds->rows as $row)
        {
          $stockZone = $this->summary_stock_zone_model->getStockZone($whsCode, $row, $option);

          if( ! empty($stockZone))
          {
            foreach($stockZone as $rs)
            {
              $data[] = [
                'code' => $rs->BinName,
                'qty' => ac_format($rs->Qty),
                'color' => $rs->Qty > 1000 ? 'box-1000' : ($rs->Qty > 0 ? 'box-100' : 'box-0')
              ];
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
