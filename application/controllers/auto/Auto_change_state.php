<?php
class Auto_change_state extends PS_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Change State";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = 'SOATCS';
  public $menu_group_code = 'SO';
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    //$this->ms = $this->load->database('ms', TRUE); //--- SAP database
    //$this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_change_state';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->library('export');
  }


  public function index()
  {
    $limit = getConfig('AUTO_CHANGE_STATE_LIMIT');
    $limit = empty($limit) ? 100 : $limit;

    $data = $this->get_all($limit);

    $ds['count'] = empty($data) ? 0 : count($data);
    $ds['all'] = $this->count_all();;
    $ds['limit'] = $limit;
    $ds['data'] = $data;

    $this->load->view('auto/auto_change_state', $ds);
  }


  public function get_all($limit = 100)
  {
    $rs = $this->db
    ->select('a.*, o.state')
    ->from('auto_send_to_sap_order AS a')
    ->join('orders AS o', 'a.code = o.code', 'left')
    ->where('a.status', 0)
    ->limit($limit)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_all()
  {
    $count = $this->db->where('status', 0)->count_all_results('auto_send_to_sap_order');

    return $count;
  }


  public function update_status()
	{
    $sc = TRUE;
    $code = $this->input->post('code');
    $status = $this->input->post('status');
    $message = $this->input->post('message');

    $ds = array(
      'status' => $status,
      'message' => $message
    );

		if( ! $this->db->where('code', $code)->update('auto_send_to_sap_order', $ds))
    {
      $sc = FALSE;
      $this->error = "Update false";
    }

    echo $sc === TRUE ? 'success' : $this->error;
	}


  public function import_order()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'import_files/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import_file",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      $this->load->library('excel');
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      if( ! empty($collection))
      {
        $i = 1;
        $j = 0;
        $ds = [];
        $ro = [];

        foreach($collection as $rs)
        {
          if($i > 1)
          {
            $j++;
            $ro[] = array('code' => trim($rs['A']));

            if($j == 1000)
            {
              $j = 0;
              $ds[] = $ro;
              $ro = [];
            }
          }

          $i++;
        }

        $ds[] = $ro;

        if( ! empty($ds))
        {
          foreach($ds as $rows)
          {
            if( ! $this->insert($rows))
            {
              $sc = FALSE;
              $this->error = "Cannot insert data";
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Cannot get data from import file : empty data collection";
      }
    }

    $this->_response($sc);
  }



  public function insert(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert_batch('auto_send_to_sap_order', $ds);
    }

    return FALSE;
  }


  public function clear_data()
  {
    $sc = TRUE;

    $qr = "TRUNCATE TABLE auto_send_to_sap_order";

    if( ! $this->db->query($qr))
    {
      $sc = FALSE;
      $this->error = "Failed to clear data";
    }

    $this->_response($sc);
  }


  public function change_order_limit()
  {
    $sc = TRUE;
    $limit = $this->input->post('limit');

    if($limit > 0)
    {
      $this->load->model('setting/config_model');

      if( ! $this->config_model->update('AUTO_CHANGE_STATE_LIMIT', $limit))
      {
        $sc = FALSE;
        $this->error = "Failed to update config value";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "จำนวนต้องมากกว่า 0";
    }

    $this->_response($sc);
  }

} //--- end class
 ?>
