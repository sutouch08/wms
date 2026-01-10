<?php
class Test extends CI_Controller
{
  public $ms;

  public function __construct()
  {
    parent::__construct();
    // $this->ms = $this->load->database('ms', TRUE);
  }

  public function index()
  {
    $i = str_replace(",", "", "3,000");
    $i = ($i == "" OR $i == NULL) ? 0 : $i;
    $i = is_numeric($i) ? $i : "x";
    echo $i;

    // $i = 0;
    // $n = ['A', 'B', 'C', 'D', 'E'];
    //
    // foreach($n as $no)
    // {
    //   if($i == 0)
    //   {
    //     echo $i ." - " .$no."<br/>";
    //     $i++;
    //   }
    //   else if($i == 1)
    //   {
    //     echo $i ." - " .$no."<br/>";
    //     $i++;
    //   }
    //   else
    //   {
    //     echo $i ." - " .$no."<br/>";
    //     $i++;
    //   }
    // }
  }
}
 ?>
