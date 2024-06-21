<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>เลขที่/รหัส</label>
		<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>"/>
	</div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">All</option>
			<option value="S" <?php echo is_selected('S', $status); ?>>Success</option>
			<option value="E" <?php echo is_selected('E', $status); ?>>Error</option>
		</select>
	</div>

	<div class="col-sm-2 col-xs-6 padding-5">
		<label>Message</label>
		<input type="text" class="form-control input-sm search-box" name="message" value="<?php echo $message; ?>"/>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>List No</label>
		<input type="text" class="form-control input-sm search-box" name="trans_no" value="<?php echo $trans_no; ?>"/>
	</div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<table class="table table-striped border-1" style="min-width:700px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">วันที่</th>
					<th class="fix-width-150 middle">เลขที่/รหัส</th>
					<th class="fix-width-60 middle text-center">สถานะ</th>
					<th class="min-width-100 middle">Message</th>
					<th class="fix-width-150 middle">List No</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($logs)) : ?>
          <?php $no = $this->uri->segment(5) + 1; ?>
          <?php foreach($logs as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle text-center"><?php echo ($rs->status === 'E' ? 'Error' : 'Success'); ?></td>
              <td class="middle"><?php echo $rs->message; ?></td>
							<td class="middle"><?php echo $rs->trans_no; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	var HOME = "<?php echo current_url(); ?>";

	function goBack() {
		window.location.href = HOME;
	}

	function getSearch() {
		$('#searchForm').submit();
	}


	$('.search-box').keyup(function(e) {
		if(e.keyCode == 13) {
			getSearch();
		}
	})


	function clearFilter() {
		$.get(HOME+'/clear_filter', function(){
			goBack();
		})
	}


	$("#fromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#toDate").datepicker("option", "minDate", ds);
		}
	});

	$("#toDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#fromDate").datepicker("option", "maxDate", ds);
		}
	});
</script>


<?php $this->load->view('include/footer'); ?>
