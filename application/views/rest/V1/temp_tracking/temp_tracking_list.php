<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" name="product_code"  value="<?php echo $product_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Tracking No</label>
    <input type="text" class="form-control input-sm search" name="tracking_no"  value="<?php echo $tracking_no; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Carton No</label>
    <input type="text" class="form-control input-sm search" name="carton_code"  value="<?php echo $carton_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Courier</label>
    <input type="text" class="form-control input-sm search" name="courier"  value="<?php echo $courier; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่เข้า temp</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>">
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>">
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<input type="hidden" name="search" value="1" />
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="result-window" style="overflow:auto;">
    <table class="table table-striped border-1 tableFixHead" style="min-width:1060px;">
      <thead>
        <tr>
          <th class="fix-width-60 text-center fix-header">#</th>
					<th class="fix-width-150 fix-header">Date</th>
          <th class="fix-width-120 fix-header">Order No</th>
					<th class="fix-width-150 fix-header">Tracking No </th>
          <th class="fix-width-120 fix-header">Carton No</th>
          <th class="fix-width-300 fix-header">Item</th>
          <th class="fix-width-60 text-center fix-header">Qty</th>
					<th class="min-width-100 fix-header">Courier</th>
        </tr>
      </thead>
      <tbody>
<?php if( ! empty($list))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($list as $rs)  : ?>

        <tr class="font-size-11">
          <td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo thai_date($rs->date_add, TRUE); ?></td>
          <td class="middle"><?php echo $rs->order_code; ?></td>
					<td class="middle"><?php echo $rs->tracking_no; ?></td>
          <td class="middle"><?php echo $rs->carton_code; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle text-center"><?php echo number($rs->qty); ?></td>
					<td class="middle"><?php echo $rs->courier_code . " : ".$rs->courier_name; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
var HOME = "<?php echo $this->home; ?>/";

window.addEventListener('load', () => {
	let height = $(window).height();
	let pageContentHeight = height - 128;
	let tableHeight = pageContentHeight - (190);

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#result-window').css('height', tableHeight + 'px');
})


function goBack() {
	window.location.href = HOME;
}


function getSearch() {
	$('#searchForm').submit();
}

function clearFilter() {
	$.get(HOME + 'clear_filter', function() {
		goBack();
	})
}


	$("#fromDate").datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd){
			$("#toDate").datepicker('option', 'minDate', sd);
		}
	});


	$("#toDate").datepicker({
		dateFormat:'dd-mm-yy',
		onClose:function(sd){
			$("#fromDate").datepicker('option', 'maxDate', sd);
		}
	});

</script>
<?php $this->load->view('include/footer'); ?>
