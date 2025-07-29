<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends PS_Controller
{
  public $menu_code = 'DBPKSI';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข Package';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/package';
    $this->load->model('masters/package_model');
  }


  public function index()
  {
    $filter = array(
      'name' => get_filter('name', 'package_name', ''),
      'type' => get_filter('type', 'package_type', 'all'),
      'active' => get_filter('active', 'package_active', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->package_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['data'] = $this->package_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$this->pagination->initialize($init);

    $this->load->view('masters/package/package_list', $filter);
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $arr = array(
        'name' => $ds->name,
        'type' => $ds->type,
        'width' => $ds->w,
        'length' => $ds->l,
        'height' => $ds->h
      );

      if( ! $this->package_model->add($arr))
      {
        $sc = FALSE;
        set_error('insert');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function get_edit($id)
  {
    $sc = TRUE;
    $ds = $this->package_model->get($id);

    if(empty($ds))
    {
      $sc = FALSE;
      $this->error  = "Package not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $arr = array(
        'name' => $ds->name,
        'type' => $ds->type,
        'width' => $ds->w,
        'length' => $ds->l,
        'height' => $ds->h,
        'active' => $ds->active == 0 ? 0 : 1
      );

      if( ! $this->package_model->update($ds->id, $arr))
      {
        $sc = FALSE;
        set_error('update');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function delete($id)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      if( ! $this->package_model->delete($id))
      {
        $sc = FALSE;
        set_error('delete');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function clear_filter()
	{
		$filter = array('package_name', 'package_type', 'package_active');
    return clear_filter($filter);
	}

}//--- end class
 ?>
