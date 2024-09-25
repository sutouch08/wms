<?php
class Soko_auto_products extends CI_Controller
{
  public $home;
	public $wms;
	public $user;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->home = base_url().'auto/soko_auto_products';
    $this->load->library('soko_product_api');
    $this->load->model('masters/products_model');
		$this->user = 'api@sokochan';
  }

  public function index()
  {
		$limit = 100;

		$list = $this->products_model->get_non_soko_list($limit);

		if( ! empty($list))
		{
      foreach($list as $item)
      {
        if(strlen($item->barcode) === 13)
        {
          $this->soko_product_api->create_item($item->code, $item);
        }

        $this->products_model->update($item->code, ['last_sync' => now()]);
      }
		}

		return $sc;
  }
} //--- end class
 ?>
