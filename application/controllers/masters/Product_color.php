<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_color extends PS_Controller
{
  public $menu_code = 'DBPDCL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข สีสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_color';
    $this->load->model('masters/product_color_model');
    $this->load->helper('product_color');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'color_code', ''),
      'name' => get_filter('name', 'color_name', ''),
      'color_group' => get_filter('color_group', 'color_group', ''),
      'status' => get_filter('status', 'color_status', 2)
    );


		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->product_color_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$color = $this->product_color_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($color))
    {
      foreach($color as $rs)
      {
        $rs->member = $this->product_color_model->count_members($rs->code);
      }
    }

    $filter['data'] = $color;

		$this->pagination->initialize($init);
    $this->load->view('masters/product_color/product_color_view', $filter);
  }



  public function set_active()
  {
    $code = $this->input->post('code');
    $active = $this->input->post('active') == 1 ? 0 :1;
    if($code)
    {
      $rs = $this->product_color_model->set_active($code, $active);
      if($rs)
      {
        $sc = "<span class=\"pointer\" onClick=\"toggleActive({$active}, '{$code}')\">";
        $sc .= is_active($active);
        $sc .= "</span>";

        echo $sc;
      }
    }
  }

  public function add_new()
  {
    $this->load->view('masters/product_color/product_color_add_view');
  }


  public function add()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = trim($this->input->post('name'));
    $group_id = $this->input->post('group');

    if( ! empty($code) && ! empty($name))
    {
      $ds = array(
        'code' => $code,
        'name' => $name,
        'id_group' => $group_id
      );

      if($this->product_color_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "'{$code}' มีในระบบแล้ว";
      }

      if($sc === TRUE)
      {
        if($this->product_color_model->is_exists_name($name))
        {
          $sc = FALSE;
          $this->error = "'{$name}' มีในระบบแล้ว";
        }

        if($sc === TRUE)
        {
          if( ! $this->product_color_model->add($ds))
          {
            $sc = FALSE;
            set_error('insert');
          }
          else
          {
            $this->export_to_sap($code);
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }



  public function edit($id)
  {
    $data = $this->product_color_model->get_by_id($id);
    $this->load->view('masters/product_color/product_color_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $name = $this->input->post('name');
    $group_id = $this->input->post('group');

    if($this->input->post('code'))
    {
      $color = $this->product_color_model->get_by_id($id);

      if( ! empty($color))
      {
        if( ! $this->product_color_model->is_exists_name($name, $id))
        {
          $arr = array(
            'name' => $name,
            'id_group' => $group_id
          );

          if( ! $this->product_color_model->update_by_id($id, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }
          else
          {
            $this->export_to_sap($code);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "'{$name}' มีในระบบแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid color id";
      }

    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function delete()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $color = $this->product_color_model->get_by_id($id);

    if( ! empty($color))
    {
      if($this->pm->can_delete)
      {
        $member = $this->product_color_model->count_members($color->code);

        if($member == 0)
        {
          if( ! $this->product_color_model->delete_by_id($id))
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
        set_error('permission');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function export_to_sap($code, $old_code = NULL)
  {
    $rs = $this->product_color_model->get($code);
    if(!empty($rs))
    {
      $ext = $this->product_color_model->is_sap_exists($old_code);
      $arr = array(
        'Code' => $rs->code,
        'Name' => $rs->name,
        'UpdateDate' => sap_date(now(), TRUE)
      );

      if($ext)
      {
        $arr['Flag'] = 'U';
        if($code !== $old_code)
        {
          $arr['OLDCODE'] = $old_code;
        }
      }
      else
      {
        $arr['Flag'] = 'A';
      }

      return $this->product_color_model->add_sap_color($arr);
    }

    return FALSE;
  }


  public function clear_filter()
	{
    $filter = array('color_code', 'color_name', 'color_group', 'color_status');
    clear_filter($filter);
	}

}//--- end class
 ?>
