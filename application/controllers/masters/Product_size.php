<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_size extends PS_Controller
{
  public $menu_code = 'DBPDSI';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข ไซส์';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_size';
    $this->load->model('masters/product_size_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'size_code', ''),
      'name' => get_filter('name', 'size_name', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $rows = $this->product_size_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $size = $this->product_size_model->get_list($filter, $perpage, $this->uri->segment($segment));

      if( ! empty($size))
      {
        foreach($size as $rs)
        {
          $rs->menber = $this->product_size_model->count_members($rs->code);
        }
      }


      $filter['data'] = $size;

  		$this->pagination->initialize($init);
      $this->load->view('masters/product_size/product_size_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/product_size/product_size_add_view');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = $this->input->post('name');
    $pos = $this->input->post('position');

    if($code != '' && $code != NULL)
    {
      if($this->product_size_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "'{$code}' มีในระบบแล้ว";
      }

      if($sc === TRUE)
      {
        if($this->product_size_model->is_exists_name($name))
        {
          $sc = FALSE;
          $this->error = "'{$name}' มีในระบบแล้ว";
        }

        if($sc === TRUE)
        {
          $ds = array(
            'code' => $code,
            'name' => $name,
            'position' => $pos
          );

          if( ! $this->product_size_model->add($ds))
          {
            $sc = FALSE;
            $this->error = "เพิ่มข้อมูลไม่สำเร็จ";
          }
          else
          {
            $this->export_to_sap($code, $code);
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Insert failed : Missing required parameter";
    }

    $this->_response($sc);
  }



  public function edit($id)
  {
    $rs = $this->product_size_model->get_by_id($id);
    $this->load->view('masters/product_size/product_size_edit_view', $rs);
  }



  public function update()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $name = trim($this->input->post('name'));
    $pos = $this->input->post('position');

    if( ! empty($id) && $code != '' && $name != '')
    {
      $size = $this->product_size_model->get_by_id($id);

      if( ! empty($size))
      {
        if($this->product_size_model->is_exists_name($name, $id))
        {
          $sc = FALSE;
          $this->error = "'{$name}' มีในระบบแล้ว โปรดใช้ชื่ออื่น";
        }

        if($sc === TRUE)
        {
          $ds = array(
            'name' => $name,
            'position' => $pos,
            'user' => $this->_user->uname,
            'date_upd' => now()
          );

          if($this->product_size_model->update_by_id($id, $ds))
          {
            //--- expor to sap
            $this->export_to_sap($code);
          }
          else
          {
            $sc = FALSE;
            $this->error = 'ปรับปรุงข้อมูลไม่สำเร็จ';
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid size id";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'Update failed : Missing required parameter';
    }

    $this->_response($sc);
  }



  public function delete()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    if($this->pm->can_delete)
    {
      $rs = $this->product_size_model->get_by_id($id);

      if( ! empty($rs))
      {
        $member = $this->product_size_model->count_members($rs->code);

        if($member == 0)
        {
          if( ! $this->product_size_model->delete($id))
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
        $this->error = "Invalid size id";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }



  public function export_to_sap($code, $old_code = NULL)
  {
    $rs = $this->product_size_model->get($code);
    if(!empty($rs))
    {
      $ext = $this->product_size_model->is_sap_exists($old_code);

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

      return $this->product_size_model->add_sap_size($arr);
    }

    return FALSE;
  }



  public function clear_filter()
	{
		$filter = array('size_code', 'size_name');
    clear_filter($filter);
		echo 'done';
	}


}//--- end class
 ?>
