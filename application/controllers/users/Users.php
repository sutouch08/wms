<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends PS_Controller{
	public $menu_code = 'SCUSER'; //--- Add/Edit Users
	public $menu_group_code = 'SC'; //--- System security
	public $title = 'Users';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/users';
  }



  public function index()
  {
		$filter = array(
			'uname' => get_filter('uname', 'user', ''),
			'dname' => get_filter('dname', 'dname', ''),
			'profile' => get_filter('profile', 'profile', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->user_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->user_model->get_users($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('users/users_view', $filter);
  }





  public function add_user()
  {
		$this->load->helper('profile');
		$this->load->helper('saleman');
    $this->load->view('users/user_add_view');
  }


	public function edit_user($id)
	{
		$this->load->helper('profile');
		$this->load->helper('saleman');
		$ds['data'] = $this->user_model->get_user($id);
		$this->load->view('users/user_edit_view', $ds);
	}


	public function reset_password($id)
	{
			$this->title = 'Reset Password';
			$data['data'] = $this->user_model->get_user($id);
			$this->load->view('users/user_reset_pwd_view', $data);
	}



	public function change_password()
	{
		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$rs = $this->user_model->change_password($id, $pwd);

			if($rs === TRUE)
			{
				$arr = array(
					'last_pass_change' => date('Y-m-d')
				);
				//--- update last pass change
				$this->user_model->update_user($user->id, $arr);
				$this->session->set_flashdata('success', 'Password changed');
			}
			else
			{
				$this->session->set_flashdata('error', 'Change password not successfull, please try again');
			}
		}

		redirect($this->home);
	}



	public function delete_user($id)
	{
		$sc = TRUE;
		$user = $this->user_model->get_user($id);
		if(!empty($user))
		{
			if(!$this->user_model->has_transection($user->uname))
			{
				if(!$this->user_model->delete_user($id))
				{
					$sc = FALSE;
					$this->error = "Delete user failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบ user ได้ เนื่องจากมี transection ในระบบแล้ว";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ User ที่ต้องการลบ";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function update_user()
	{
		$sc = TRUE;
		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$uname = $this->input->post('uname');
			$dname = $this->input->post('dname');
			$id_profile = $this->input->post('profile') === '' ? NULL : $this->input->post('profile');
			$sale_id = $this->input->post('sale_id') === '' ? NULL : $this->input->post('sale_id');
			$status = $this->input->post('status');
			$is_viewer = $this->input->post('is_viewer');

			$data = array(
				'uname' => $uname,
				'name' => $dname,
				'id_profile' => $id_profile,
				'sale_id' => $sale_id,
				'active' => $status,
				'is_viewer' => $is_viewer
			);

			$rs = $this->user_model->update_user($id, $data);
			if($rs === FALSE)
			{
				$this->session->set_flashdata('error', 'Update user not successfully');
			}
			else
			{
				$this->session->set_flashdata('success', 'User updated');
			}
		}
		else
		{
			$this->session->set_flashdata('error','Update fail : data not found');
		}

		redirect($this->home.'/edit_user/'.$id);

	}




	public function new_user()
	{
		if($this->input->post('uname'))
		{
			$uname = $this->input->post('uname');
			$dname = $this->input->post('dname');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$uid = md5(uniqid());
			$id_profile = $this->input->post('profile') === '' ? NULL : $this->input->post('profile');
			$sale_id = $this->input->post('sale_id') === '' ? NULL : $this->input->post('sale_id');
			$status = $this->input->post('status');
			$is_viewer = $this->input->post('is_viewer');

			$data = array(
				'uname' => $uname,
				'pwd' => $pwd,
				'name' => $dname,
				'uid' => $uid,
				'id_profile' => $id_profile,
				'sale_id' => $sale_id,
				'active' => $status,
				'is_viewer' => $is_viewer,
				'last_pass_change' => date('Y-m-d')
			);

			$rs = $this->user_model->new_user($data);

			if($rs === FALSE)
			{
				set_error('Create User fail');
			}
			else
			{
				set_message('User created');
			}

		}
		else
		{

			set_error('Create User fail : Empty data');
		}

		redirect($this->home.'/add_user');
	}




	public function valid_dname($dname, $id = '')
	{
		$rs = $this->user_model->is_exists_display_name($dname, $id);

		if($rs === TRUE)
		{
			echo 'exists';
		}
		else
		{
			echo 'not exists';
		}
	}



	public function valid_uname($uname, $id = '')
	{
		$rs = $this->user_model->is_exists_uname($uname, $id);
		if($rs === TRUE)
		{
			echo 'exists';
		}
		else
		{
			echo 'not exists';
		}
	}




	//--- Activeate suspend user by id;
	public function active_user($id)
	{
		$rs = $this->user_model->active_user($id);
		echo $rs === TRUE ? 'success' : json_encode($rs);
	}






	//--- Suspend activated user by id
	public function disactive_user($id)
	{
		$rs = $this->user_model->disactive_user($id);

		echo $rs === TRUE ? 'success' : $rs;
	}





	public function clear_filter()
	{
		$filter = array('user', 'dname', 'profile');
		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
