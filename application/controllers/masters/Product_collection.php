<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_collection extends PS_Controller
{
  public $menu_code = 'DBPDCT';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข คอลเล็คชั่น';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_collection';
    $this->load->model('masters/product_collection_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'collection_code', ''),
      'name' => get_filter('name', 'collection_name', ''),
      'active' => get_filter('active', 'collection_active', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->product_collection_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['data'] = $this->product_collection_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $data = array();

    if(!empty($filter['data']))
    {
      foreach($filter['data'] as $rs)
      {
        $rs->member = $this->product_collection_model->count_members($rs->code);
      }
    }

		$this->pagination->initialize($init);

    $this->load->view('masters/product_collection/product_collection_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_collection/product_collection_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $name = trim($this->input->post('name'));
    $active = $this->input->post('active') == 1 ? 1 : 0;

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_collection_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "{$code} มีในระบบแล้ว";
      }

      if($sc === TRUE)
      {
        if($this->product_collection_model->is_exists_name($name))
        {
          $sc = FALSE;
          $this->error = "{$name} มีในระบบแล้ว";
        }
      }

      if($sc === TRUE)
      {
        $ds = array(
          'code' => $code,
          'name' => $name,
          'active' => $active,
          'update_user' => $this->_user->uname
        );

        if( ! $this->product_collection_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "เพิ่มรายการไม่สำเร็จ";
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
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function edit($id)
  {
    $data = $this->product_collection_model->get_by_id($id);

    if( ! empty($data))
    {
      $this->load->view('masters/product_collection/product_collection_edit', $data);
    }
    else
    {
      $this->page_error();
    }
  }



  public function update()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $name = trim($this->input->post('name'));
    $active = $this->input->post('active') == 1 ? 1 : 0;

    if( ! empty($name))
    {
      if($this->product_collection_model->is_exists_name($name, $id))
      {
        $sc = FALSE;
        $this->error = "{$name} มีในระบบแล้ว";
      }

      if($sc === TRUE)
      {
        $arr = array(
          'name' => $name,
          'active' => $active,
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->product_collection_model->update($id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขรายการไม่สำเร็จ";
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
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function delete($id)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $data = $this->product_collection_model->get_by_id($id);

      if( ! empty($data))
      {
        $count = $this->product_collection_model->count_members($data->code);

        if( ! $count)
        {
          if( ! $this->product_collection_model->delete($id))
          {
            $sc = FALSE;
            set_error('delete');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('transection');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function clear_filter()
	{
		$filter = array('collection_code', 'collection_name', 'collection_active');
    return clear_filter($filter);
	}

}//--- end class
 ?>
