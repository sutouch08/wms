<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancel_reason extends PS_Controller
{
  public $menu_code = 'SCODCR';
	public $menu_group_code = 'SC';
	public $title = 'เหตุผลในการยกเลิก';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/cancel_reason';
    $this->load->model('masters/cancel_reason_model');
  }


  public function index()
  {
		$filter = array(
      'name' => get_filter('name', 'cancel_name', ''),
      'active' => get_filter('active', 'cancel_active', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();

  		$rows = $this->cancel_reason_model->count_rows($filter);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
  		$filter['data'] = $this->cancel_reason_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

  		$this->pagination->initialize($init);
      $this->load->view('masters/cancel_reason/cancel_reason_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('masters/cancel_reason/cancel_reason_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $name = trim($this->input->post('name'));
      $active = $this->input->post('active');

      if( ! empty($name))
      {
        if( ! $this->cancel_reason_model->is_exists($name))
        {
          $arr = array(
            'name' => $name,
            'active' => $active == 0 ? 0 : 1,
            'user' => $this->_user->uname
          );

          if( ! $this->cancel_reason_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "{$name} มีในระบบแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }



  public function edit($id)
  {
    if($this->pm->can_edit)
    {
      $rs = $this->cancel_reason_model->get($id);

      if( ! empty($rs))
      {
        $this->load->view('masters/cancel_reason/cancel_reason_edit', $rs);
      }
      else
      {
        $this->error_page();
      }
    }
    else
    {
      $this->deny_page();
    }
  }



  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $id = $this->input->post('id');
      $name = trim($this->input->post('name'));
      $active = $this->input->post('active');

      if( ! empty($name))
      {
        $row = $this->cancel_reason_model->get($id);

        if( ! empty($row))
        {
          if( ! $this->cancel_reason_model->is_exists($name, $id))
          {
            $arr = array(
              'name' => $name,
              'active' => $active == 0 ? 0 : 1,
              'update_user' => $this->_user->uname,
              'date_upd' => now(),
              'prev_name' => $row->name
            );

            if( ! $this->cancel_reason_model->update($id, $arr))
            {
              $sc = FALSE;
              set_error('update');
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "{$name} มีในระบบแล้ว";
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
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }



  public function get($id)
  {
    $sc = TRUE;

    $row = $this->cancel_reason_model->get($id);

    if( ! empty($row))
    {
      $row->date_add = thai_date($row->date_add, TRUE);
      $row->date_upd = empty($row->date_upd) ? NULL : thai_date($row->date_upd, TRUE);
      $row->status = $row->active == 1 ? 'Active' : 'Inactive';
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $row : NULL
    );

    echo json_encode($arr);
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      $row = $this->cancel_reason_model->get($id);

      if( ! empty($row))
      {
        if( ! $this->cancel_reason_model->has_transection($id))
        {
          if( ! $this->cancel_reason_model->delete($id))
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
        $this->error = "Invalid reason id";
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
		return clear_filter(array('cancel_name', 'cancel_active'));    
	}

}//--- end class
 ?>
