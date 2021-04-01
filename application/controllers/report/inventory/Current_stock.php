<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Current_stock extends PS_Controller
{
  public $menu_code = 'RICSTC';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือปัจจุบัน';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/current_stock';
    $this->load->model('report/inventory/current_stock_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_group_model');
    $this->load->helper('product_images');
  }

  public function index()
  {
    $this->load->view('report/inventory/current_stock_report_view');
  }


  public function get_report()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    ini_set('max_execution_time', 600);
    $show_stock = FALSE;

    //--- จำนวนคงเหลือ มูลค่าคงเหลือ
    $stock = $this->current_stock_report_model->get_stock_summary();

    $sumQty = !empty($stock) ? $stock->qty : 0;

    $sumCost = !empty($stock) ? $stock->amount : 0;

    //--- จำนวนรุ่นสินค้า
    $sumStyle = $this->current_stock_report_model->get_count_style();

    //--- จำนวนรายการสินค้า
    $sumItem = $this->current_stock_report_model->get_count_item();

    $page = '';
    $page .= '<div class="row">';
    $page .= '  <div class="col-sm-3 padding-5 first">';
    $page .= '   <div class="icon-box i-blue">Qty</div>';
    $page .= '   <div class="info-box c-blue">'.number($sumQty).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-3 padding-5">';
    $page .= '    <div class="icon-box i-green">Cost</div>';
    $page .= '    <div class="info-box c-green">'.number($sumCost, 2).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-3 padding-5">';
    $page .= '    <div class="icon-box i-orange">Items</div>';
    $page .= '    <div class="info-box c-orange">'.number($sumItem).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-3 padding-5 last">';
    $page .= '    <div class="icon-box i-red">Style</div>';
    $page .= '    <div class="info-box c-red">'.number($sumStyle).'</div>';
    $page .= '  </div>';
    $page .= '</div>';
    $page .= '<hr/>';


    $productGroup = $this->product_group_model->get_data();

    $page .= '<div class="row">';
    $page .= '  <div class="col-sm-2 padding-right-0" style="padding-top:15px;">';
    $page .= '    <ul id="myTab1" class="setting-tabs">';

    if(!empty($productGroup))
    {
      $i = 1;
      foreach($productGroup AS $rs)
      {
        $page .= '<li class="li-block '.($i == 1 ? 'active' : '').'">';
        $page .= ' <a href="#'.$rs->code.'" data-toggle="tab">'.$rs->name.'</a>';
        $page .= '</li>';
        $i++;
      }
    }

    $page .= '    </ul>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc;">';
    $page .= '    <div class="tab-content" style="border:0;">';


    if(!empty($productGroup))
    {
      $i = 1;
      foreach($productGroup as $rs)
      {
        $page .= '<div role="tabpanel" class="tab-pane '.($i == 1 ? 'active' : '').'" id="'.$rs->code.'">';

        //--- จำนวนคงเหลือ มูลค่าคงเหลือ
        $stock = $this->current_stock_report_model->get_stock_summary_by_group($rs->code);
        $sumQty = !empty($stock) ? $stock->qty : 0;
        $sumCost = !empty($stock) ? $stock->amount : 0;

        //--- จำนวนรุ่นสินค้า
        $sumStyle = $this->current_stock_report_model->get_count_style_by_group($rs->code);

        //--- จำนวนรายการสินค้า
        $sumItem = $this->current_stock_report_model->get_count_item_by_group($rs->code);

        $page .= '<div class="row">';
        $page .= '  <div class="col-sm-3 padding-5 first">';
        $page .= '   <div class="sub-icon i-blue">Qty</div>';
        $page .= '   <div class="sub-info c-blue font-size-16">'.number($sumQty).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-3 padding-5">';
        $page .= '    <div class="sub-icon i-green">Cost</div>';
        $page .= '    <div class="sub-info c-green font-size-16">'.number($sumCost, 2).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-3 padding-5">';
        $page .= '    <div class="sub-icon i-orange">Items</div>';
        $page .= '    <div class="sub-info c-orange font-size-16">'.number($sumItem).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-3 padding-5 last">';
        $page .= '    <div class="sub-icon i-red">Style</div>';
        $page .= '    <div class="sub-info c-red">'.number($sumStyle).'</div>';
        $page .= '  </div>';
        $page .= '</div>';
        $page .= '<hr/>';

        $style = $this->current_stock_report_model->get_style_by_group($rs->code);

        if(!empty($style))
        {
          $count = 1;
          foreach($style as $rb)
          {
            if($show_stock)
            {
              $ds = $this->current_stock_report_model->get_style_summary($rb->code);
              $qty = !empty($ds) ? $ds->qty : 0;
              $amount = !empty($ds) ? $ds->amount : 0;
            }

            if($count === 1)
            {
              $page .= '<div class="row"><div calss="col-sm-12">';
            }

            $page .= '<div class="item2 col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center margin-bottom-15">';
        		$page .= 	'<div class="product padding-5">';
        		$page .= 		'<div class="image">';
        		$page .= 			'<a href="javascript:void(0)" onclick="getData(\''.$rb->code.'\')">';
        		$page .=			'<img src="'.get_cover_image($rb->code, 'default').'" class="img-responsoive" />';
        		$page .=			'</a>';
        		$page .= 		'</div>';
        		$page .= 		'<div class="description font-size-12">' . $rb->code . '</div>';

            if($show_stock)
            {
          		$page .= 		'<div class="price text-center">';
          		$page .=			'<span class="red font-size-10">' . number($qty) . '</span>';
          		$page .=			' | ';
          		$page .=			'<span class="blue font-size-10">' . number($amount, 2) . '</span>';
          		$page .=		'</div>';
            }

        		$page .= 	'</div>';
        		$page .= '</div>';

            if($count === 6)
            {
              $page .= '</div></div>';
            }

            $count++;

            if($count > 6)
            {
              $count = 1;
            }

          } //--- end foreach

          if($count != 1)
          {
            $page .= '</div></div>';
          }
        }


        $page .= '</div>';
        $i++;
      }
    }


    echo $page;

  }



  public function get_stock_grid()
  {
    $code = $this->input->get('style_code');
    if(!empty($code))
    {
      $this->load->library('product_grid');

      $rs = $this->product_grid->getOrderGrid($code);
      $tableWidth	= $this->products_model->countAttribute($code) == 1 ? 600 : $this->product_grid->getOrderTableWidth($code);
      $rs .= ' | ' . $tableWidth;
      $rs .= ' | ' . $code;
      $rs .= ' | ' ;
      echo $rs;
    }
    else
    {
      echo "ไม่พบรหัสสินค้า";
    }
  }

} //--- end class








 ?>
