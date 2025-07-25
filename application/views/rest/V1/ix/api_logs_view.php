<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่/รหัส</label>
		<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>"/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>Channels</label>
    <select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="all">All</option>
			<option value="NULL" <?php echo is_selected('NULL', $channels); ?>>NO Channels</option>
			<option value="0009" <?php echo is_selected('0009', $channels); ?>>TIKTOK</option>
			<option value="SHOPEE" <?php echo is_selected('SHOPEE', $channels); ?>>SHOPEE</option>
			<option value="LAZADA" <?php echo is_selected('LAZADA', $channels); ?>>LAZADA</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-4 padding-5">
		<label>Status</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">All</option>
			<option value="success" <?php echo is_selected('success', $status); ?>>SUCCESS</option>
			<option value="failed" <?php echo is_selected('failed', $status); ?>>FAILED</option>
			<option value="test" <?php echo is_selected('test', $status); ?>>TEST</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Type</label>
    <select class="form-control input-sm" name="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="ORDER" <?php echo is_selected('ORDER', $type); ?>>ORDER</option>
			<option value="RETURN" <?php echo is_selected('RETURN', $type); ?>>RETURN</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Action</label>
		<select class="form-control input-sm" name="action" onchange="getSearch()">
			<option value="all">All</option>
			<option value="create" <?php echo is_selected('create', $action); ?>>CREATE</option>
			<option value="update" <?php echo is_selected('update', $action); ?>>UPDATE</option>
			<option value="cancel" <?php echo is_selected('cancel', $action); ?>>CANCEL</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>Date</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>

</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped border-1" style="min-width:1130px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">Date</th>
					<th class="fix-width-200 middle">Code/Document No.</th>
					<th class="fix-width-100 middle">Channels</th>
					<th class="fix-width-80 middle text-center">Type</th>
					<th class="fix-width-80 middle text-center">Action</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-150 middle">API Path</th>
					<th class="min-width-150 middle">Message</th>
					<th class="fix-width-100"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($logs)) : ?>
          <?php $no = $this->uri->segment(5) + 1; ?>
          <?php foreach($logs as $rs) : ?>
            <tr class="font-size-11">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo $rs->channels; ?></td>
							<td class="middle text-center"><?php echo $rs->type; ?></td>
							<td class="middle text-center"><?php echo $rs->action; ?></td>
              <td class="middle text-center"><?php echo $rs->status; ?></td>
							<td class="middle"><?php echo $rs->api_path; ?></td>
              <td class="middle"><?php echo $rs->message; ?></td>
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">View detail</button>
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
	var HOME = "<?php echo $this->home; ?>";

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

	function viewDetail(id) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";

		var target  = HOME + '/view_detail/'+id+'?nomenu';
	 	window.open(target, '_blank', prop);
	}
</script>


<?php $this->load->view('include/footer'); ?>
