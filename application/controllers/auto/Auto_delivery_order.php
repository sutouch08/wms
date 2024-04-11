<?php
class Auto_delivery_order extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Send To SAP";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_delivery_order';
		$this->load->library('export');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    $ds['data'] = NULL;

     $rs = $this->db->where('status', 0)->limit(100)->get('auto_send_to_sap_order');

     if($rs->num_rows() > 0)
     {
       $ds['count'] = $rs->num_rows();
       $ds['data'] = $rs->result();
     }

    $this->load->view('auto/auto_delivery_order', $ds);
  }

  public function send_to_sap()
  {
		$sc = "";
    $rs  = $this->db->where('status', 0)->limit(100)->get('auto_send_to_sap_order');

		if($rs->num_rows() > 0)
		{
			$i = 1;

			foreach($rs->result() as $rd)
			{
				if( ! $this->export->export_order($rd->code))
				{
					$arr = array(
						'status' => 3,
						'message' => $this->export->error
					);

					$this->update_status($rd->id, $arr);
					$sc .= "<p>{$i}.  {$rd->code} : Error: ".$this->export->error."</p>";
				}
				else
				{
					$arr = array(
						'status' => 1
					);

					$this->update_status($rd->id, $arr);

					$sc .= "<p>{$i}. {$rd->code} : Success</p>";
				}

				$i++;
			}
		}
		else
		{
			$sc .= "<p>Order Not found</p>";
		}

		echo $sc;
  }


	private function update_status($id, array $ds = array())
	{
		return $this->db->where('id', $id)->update('auto_send_to_sap_order', $ds);
	}

} //--- end class
 ?>
