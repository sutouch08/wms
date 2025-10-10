<?php $this->load->view('include/header'); ?>
<style>
  .pre-loader {
    width: 120px;
    height: 10px;
    margin-left: auto;
    margin-right: auto;
    -webkit-mask: linear-gradient(90deg,#000 70%,#0000 0) left/20% 100%;
    background:
     linear-gradient(#000 0 0) left -25% top 0 /20% 100% no-repeat
     #ddd;
    animation: l7 1s infinite steps(6);
  }
  @keyframes l7 {
      100% {background-position: right -25% top 0}
  }

  #txt-label, #total-label {
    font-size: 16px;
    padding-bottom: 10px;
    text-align: center;
  }
</style>

<div class="row hidden-print">
  <div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-primary btn-report" id="btn-export" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<div class="row">
  <div class="col-sm-2 padding-5">
    <label class="display-block">คลัง</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
	<input type="hidden" id="token" name="token" >
</div>


<div class="modal fade" id="wh-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" id="modal" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="title" id="modal_title">เลือกคลัง</h4>
      </div>
      <div class="modal-body" id="modal_body" style="padding:0px; height:600px; max-height: 70vh; overflow:auto;">
        <?php if(!empty($whList)) : ?>
          <?php foreach($whList as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk chk-whs" data-code="<?php echo $rs->code; ?>" data-name="<?php echo $rs->name; ?>" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <?php echo $rs->code; ?> | <?php echo $rs->name; ?>
              </label>
            </div>
          <?php endforeach; ?>
        <?php endif;?>

        <div class="divider" ></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
	<div class="col-sm-12" id="rs">

    </div>
</div>

<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width:600px; max-width:90vw;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title-site text-center" >รายงานสินค้าคงเหลือแยกตามคลัง</h4>
      </div>
      <div class="modal-body">
        <div class="row" style="margin-left:0; margin-right:0;">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <div class="text-center" id="total-label">Waiting ...</div>
            <div class="text-center" id="txt-label"></div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <div class="progress pos-rel progress-striped" style="background-color:#CCC;" id="txt-percent" data-percent="0%">
              <div class="progress-bar progress-bar-primary" id="progress-bar" style="width: 0%;"></div>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <div class="float-left pre-loader"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default btn-100" onclick="cancel_and_close()">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance_warehouse.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<?php $this->load->view('include/footer'); ?>
