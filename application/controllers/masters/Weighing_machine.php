<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Weighing_machine extends PS_Controller
{
  public $menu_code = 'DBWEMC';
  public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
  public $title = 'เพิ่ม/แก้ไข เครื่องชั่ง';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'weighing_machine';
    $this->load->model('masters/weighing_machine_model');
  }


  public function index()
  {
    $filter = array(
      'device_id' => get_filter('device_id', 'wm_device_id', ''),
      'code' => get_filter('code', 'wm_code', ''),      
      'name' => get_filter('name', 'wm_name', ''),
      'active' => get_filter('active', 'wm_active', 'all')     
    );
		
		$perpage = get_rows();		
		$rows = $this->weighing_machine_model->count_rows($filter);
    $filter['data'] = $this->weighing_machine_model->get_list($filter, $perpage, $this->uri->segment($this->segment));		
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);		
		$this->pagination->initialize($init);
    $this->load->view('masters/weighing_machine/device_list', $filter);
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $res = [];

    if(!empty($ds))
    {
      if($this->weighing_machine_model->is_exists($ds->deviceCode))
      {
        $sc = FALSE;
        $this->error = "รหัสเครื่องชั่ง {$ds->deviceCode} มีอยู่แล้วในระบบ";
      }
      else
      {
        $arr = array(
          'device_id' => $ds->deviceId,
          'code' => $ds->deviceCode,
          'name' => $ds->deviceName,
          'port' => $ds->devicePort,
          'baud_rate' => $ds->deviceBaudRate,
          'unit' => $ds->deviceUnit,
          'active' => $ds->active,
          'data_bit' => $ds->deviceDataBits,
          'stop_bit' => $ds->deviceStopBits,
          'parity' => $ds->deviceParity,
          'create_by' => $this->_user->uname,
          'create_date' => date('Y-m-d H:i:s')
        );

        $id = $this->weighing_machine_model->add($arr);

        if(! $id)
        {
          $sc = FALSE;
          $this->error = "เพิ่มรายการไม่สำเร็จ";
        }
        else 
        {
          $res = array(
            'id' => $id,
            'deviceId' => $ds->deviceId,
            'deviceCode' => $ds->deviceCode,
            'deviceName' => $ds->deviceName,
            'devicePort' => $ds->devicePort,
            'deviceBaudRate' => $ds->deviceBaudRate,
            'deviceUnit' => $ds->deviceUnit,
            'deviceDataBits' => $ds->deviceDataBits,
            'deviceStopBits' => $ds->deviceStopBits,
            'deviceParity' => $ds->deviceParity,
            'active' => is_active($ds->active),
            'uname' => $this->_user->uname,
            'date_upd' => date('d-m-Y H:i:s')
          );
        }
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $res
    );
    
    echo json_encode($arr);
  }

  public function get_data($id)
  {
    $sc = TRUE;
    $rs = $this->weighing_machine_model->get_by_id($id);
    $ds = [];

    if(!empty($rs))
    {
      $ds = array(
        'id' => $rs->id,
        'deviceId' => $rs->device_id,
        'deviceCode' => $rs->code,
        'deviceName' => $rs->name,
        'devicePort' => $rs->port,
        'deviceBaudRate' => $rs->baud_rate,
        'deviceUnit' => $rs->unit,
        'deviceDataBits' => $rs->data_bit,
        'deviceStopBits' => $rs->stop_bit,
        'deviceParity' => $rs->parity,
        'active' => $rs->active,
        'uname' => $this->_user->uname,
        'date_upd' => date('d-m-Y H:i:s')
      );      
    }
    else 
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );
    
    echo json_encode($arr);
  }

  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $res = [];

    if(!empty($ds))
    {
      if($this->weighing_machine_model->is_exists($ds->deviceCode, $ds->id))
      {
        $sc = FALSE;
        $this->error = "รหัสเครื่องชั่ง {$ds->deviceCode} มีอยู่แล้วในระบบ";
      }
      else
      {
        $arr = array(          
          'name' => $ds->deviceName,
          'port' => $ds->devicePort,
          'baud_rate' => $ds->deviceBaudRate,
          'unit' => $ds->deviceUnit,
          'active' => $ds->active,
          'data_bit' => $ds->deviceDataBits,
          'stop_bit' => $ds->deviceStopBits,
          'parity' => $ds->deviceParity,
          'update_by' => $this->_user->uname,
          'update_date' => date('Y-m-d H:i:s')
        );
       
        if(! $this->weighing_machine_model->update($ds->id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขรายการไม่สำเร็จ";
        }
        else 
        {
          $res = array(
            'id' => $ds->id,
            'deviceId' => $ds->deviceId,
            'deviceCode' => $ds->deviceCode,
            'deviceName' => $ds->deviceName,
            'devicePort' => $ds->devicePort,
            'deviceBaudRate' => $ds->deviceBaudRate,
            'deviceUnit' => $ds->deviceUnit,
            'deviceDataBits' => $ds->deviceDataBits,
            'deviceStopBits' => $ds->deviceStopBits,
            'deviceParity' => $ds->deviceParity,
            'active' => is_active($ds->active),
            'uname' => $this->_user->uname,
            'date_upd' => date('d-m-Y H:i:s')
          );
        }
      }
    }
    else 
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $res
    );
    
    echo json_encode($arr);
  }


  public function remove()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    if($this->pm->can_delete)
    {
      if(! $this->weighing_machine_model->delete($id))
      {
        $sc = FALSE;
        $this->error = "ลบรายการไม่สำเร็จ";
      }
    }
    else 
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error
    );
    
    echo json_encode($arr);
  }

  public function clear_filter()
  {
    return clear_filter(array('wm_device_id', 'wm_code', 'wm_name', 'wm_active'));
  }  
} //--- end class
