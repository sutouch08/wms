<?php
class Qc extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    header('Access-Control-Allow-Origin:*');
  }

  public function index()
  {
    $sc = TRUE;
    $secret = "YXBpQHdhcnJpeDpaSzExbzE1bzE1TDEycyRwMHJ0==";
    $key = $this->input->post('secret');
    $code = $this->input->post('order');
    $role = $this->input->post('role');
    $user = $this->input->post('user');

    if($key !== $secret)
    {
      $sc = FALSE;
      $this->error = "unauthorized";
    }
    else
    {
      $file = $_FILES['video'];
      $path = $this->config->item('upload_path').'video/';

      if( ! empty($file))
      {
        $fileName = $file['name'];

        $config = array(
          "allowed_types" => "*",
          "upload_path" => $path,
          "file_name"	=> $fileName, // name canbe change
          "max_size" => 102400, //100 MB in KB base on php.ini setting
          "overwrite" => TRUE
        );

        $this->load->library("upload", $config);

        if( ! $this->upload->do_upload('video'))
        {
          $sc = FALSE;
          $this->error = $this->upload->display_errors();
        }

        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'role' => $role,
            'user' => $user
          );

          $this->db->insert('order_pack_video', $arr);
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'secret' => $key
    );

    echo json_encode($arr);
  }
}
?>
