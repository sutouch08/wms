<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabbable">
      <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#details-tab" aria-expanded="true">รายการ</a></li>
        <li class=""><a data-toggle="tab" href="#orders-tab" aria-expanded="false">ออเดอร์</a></li>
        <li class=""><a data-toggle="tab" href="#rows-tab" aria-expanded="false">สรุป</a></li>
        <li class=""><a data-toggle="tab" href="#trans-tab" aria-expanded="false">transection</a></li>
      </ul>
      <div class="tab-content" style="padding:0px;">
        <?php $this->load->view('inventory/pick_list/pick_tab_details'); ?>
        <?php $this->load->view('inventory/pick_list/pick_tab_orders'); ?>
        <?php $this->load->view('inventory/pick_list/pick_tab_rows'); ?>
        <?php $this->load->view('inventory/pick_list/pick_tab_trans'); ?>
      </div>
    </div>
  </div>
</div>
