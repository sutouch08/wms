<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pre_order_policy extends PS_Controller
{
  public $menu_code = 'SCPROL';
	public $menu_group_code = 'SC';
	public $title = 'นโยบาย Pre-order';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/pre_order_policy';
    $this->load->model('orders/pre_order_policy_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pre_code', ''),
      'name' => get_filter('name', 'pre_name', ''),
      'start_date' => get_filter('start_date', 'pre_from_date', ''),
      'end_date' => get_filter('end_date', 'pre_to_date', ''),
      'status' => get_filter('status', 'pre_status', 'all')
    );


    if( $this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $segment = 4; //-- url segment

      $rows = $this->pre_order_policy_model->count_rows($filter);

      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      $result = $this->pre_order_policy_model->get_data($filter, $perpage, $this->uri->segment($this->segment));

      $filter['data'] = $result;

      $this->pagination->initialize($init);

      $this->load->view('preorder/policy_list', $filter);

    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('preorder/policy_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $name = trim($this->input->post('name'));
      $status = $this->input->post('status') == 1 ? 1 : 0;
      $start_date = $this->input->post('start_date');
      $end_date = $this->input->post('end_date');

      if($name && $start_date && $end_date)
      {
        $arr = array(
          'code' => $this->get_new_code(),
          'name' => $name,
          'status' => $status,
          'start_date' => db_date($start_date),
          'end_date' => db_date($end_date),
          'user' => $this->_user->uname
        );

        $id = $this->pre_order_policy_model->add($arr);

        if( ! $id)
        {
          $sc = FALSE;
          $this->error = "Failed to Create Policy";
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'id' => $sc === TRUE ? $id : NULL
    );

    echo json_encode($arr);
  }


  public function edit($id)
  {
    if($this->pm->can_add OR $this->pm->can_eidt)
    {
      $doc = $this->pre_order_policy_model->get($id);

      if( ! empty($doc))
      {
        $ds = array(
          'doc' => $doc,
          'details' => $this->pre_order_policy_model->get_details($id)
        );

        $this->load->view('preorder/policy_edit', $ds);
      }
      else
      {
        $this->page_error();
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

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $id = $this->input->post('id');
      $name = trim($this->input->post('name'));
      $status = $this->input->post('status') == 1 ? 1 : 0;
      $start_date = $this->input->post('start_date');
      $end_date = $this->input->post('end_date');

      if($name && $start_date && $end_date)
      {
        $doc = $this->pre_order_policy_model->get($id);

        if( ! empty($doc))
        {
          $arr = array(
            'name' => $name,
            'status' => $status,
            'start_date' => db_date($start_date),
            'end_date' => db_date($end_date),
            'date_update' => now(),
            'update_user' => $this->_user->uname
          );

          if( ! $this->pre_order_policy_model->update($id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to Update Policy";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document no";
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function view_detail($id)
  {
    $doc = $this->pre_order_policy_model->get($id);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->pre_order_policy_model->get_details($id)
      );

      $this->load->view('preorder/policy_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function delete_policy()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    if($this->pm->can_delete)
    {
      $this->db->trans_begin();

      if( ! $this->pre_order_policy_model->delete_items($id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete items";
      }

      if($sc === TRUE)
      {
        if( ! $this->pre_order_policy_model->delete($id))
        {
          $sc = FALSE;
          $this->error = "Failed to delete policy";
        }
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_item()
  {
    $sc = TRUE;
    $id_policy = $this->input->post('id');
    $item_code = $this->input->post('product_code');
    $result = array();

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      if($id_policy && $item_code)
      {
        //--- check policy
        $po = $this->pre_order_policy_model->get($id_policy);

        if( ! empty($po))
        {
          $pd = $this->products_model->get($item_code);

          if( ! empty($pd))
          {
            //--- check exists item
            if( ! $this->pre_order_policy_model->is_exists_item($id_policy, $pd->code))
            {
              $arr = array(
                'id_policy' => $id_policy,
                'product_code' => $pd->code,
                'product_name' => $pd->name,
                'style_code' => $pd->style_code,
                'user' => $this->_user->uname
              );

              $id = $this->pre_order_policy_model->add_item($arr);

              if($id)
              {
                $arr['id'] = $id;
                $result = $arr;
              }
              else
              {
                $sc = FALSE;
                $this->error = "Failed to add item";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "{$pd->code} already exists";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid SKU Code";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document id";
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $result : NULL
    );

    echo json_encode($arr);
  }

  public function add_style()
  {
    $sc = TRUE;
    $id_policy = $this->input->post('id');
    $style_code = trim($this->input->post('style_code'));

    $result = array();

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      //--- check policy
      $po = $this->pre_order_policy_model->get($id_policy);

      if( ! empty($po))
      {
        $items = $this->products_model->get_style_items($style_code);

        if( ! empty($items))
        {
          $this->db->trans_begin();

          foreach($items as $item)
          {
            //--- check exists item
            if( ! $this->pre_order_policy_model->is_exists_item($id_policy, $item->code))
            {
              $arr = array(
                'id_policy' => $id_policy,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'style_code' => $item->style_code,
                'user' => $this->_user->uname
              );

              $id = $this->pre_order_policy_model->add_item($arr);

              if($id)
              {
                $arr['id'] = $id;

                array_push($result, $arr);
              }
              else
              {
                $sc = FALSE;
                $this->error = "Insert item failed : {$item->code}";
                break;
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Items not found or invalid Product model code";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid policy id";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'rows' => $sc === TRUE ? $result : NULL
    );

    echo json_encode($arr);
  }


  public function remove_items()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $ids = $this->input->post('ids');

      if( ! empty($ids))
      {
        if( ! $this->pre_order_policy_model->remove_items($ids))
        {
          $sc = FALSE;
          $this->error = "Failed to delete items";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_active_items()
  {    
    $items = $this->pre_order_policy_model->get_active_items();

    if( ! empty($items))
    {
      echo json_encode($items);
    }
    else
    {
      echo "Not found";
    }
  }


  public function get_new_code()
  {
    $date = date('Y-m-d');
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = "PRE";
    $run_digit = 3;
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->pre_order_policy_model->get_max_code($pre);

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array('policy_code', 'policy_name', 'active', 'start_date', 'end_date');
    clear_filter($filter);
    echo 'done';
  }

}//-- end class
?>
