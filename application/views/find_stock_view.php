<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="width-100 text-center search" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="width-100 text-center search" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
    <label>ราคา</label>
		<div class="input-reduction">
		<select class="width-100 input-discount" name="operater">
			<option value="equal">เท่ากับ</option>
			<option value="less_than" <?php echo is_selected('less_than', $operater); ?>> ไม่เกิน </option>
			<option value="more_than" <?php echo is_selected('more_than', $operater); ?>> มากกว่า </option>
		</select>
		<input type="number" class="width-100 input-unit search text-center" name="price" id="price" value="<?php echo $price; ?>" />
	</div>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>ไซส์</label>
    <input type="text" class="width-100 text-center search" name="size" id="size" value="<?php echo $size; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>สี</label>
    <select class="width-100" name="color_group" id="color_group">
      <option value="all">ทั้งหมด</option>
      <?php echo select_color_group($color_group); ?>
    </select>
  </div>

  <div class="col-lg-3 col-md-4-harf col-sm-6 col-xs-6 padding-5">
    <label>คลัง</label>
    <select class="width-100" id="warehouse" name="warehouse">
      <option value="">ทั้งหมด</option>
      <?php echo select_warehouse($warehouse); ?>
    </select>
  </div>


	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>กลุ่ม</label>
    <select class="width-100" name="group" id="group">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>หมวดหมู่</label>
		<select class="width-100" name="category" id="category">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ประเภท</label>
		<select class="width-100" name="kind" id="kind">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ชนิด</label>
		<select class="width-100" name="type" id="type">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ยี่ห้อ</label>
		<select class="width-100" name="brand" id="brand">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>คอเล็คชั่น</label>
		<select name="collection" class="width-100" id="collection">
			<option value="all">ทั้งหมด</option>
		<?php echo select_all_collection($collection); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ปี</label>
		<select class="width-100" name="year">
			<option value="all">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:1280px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-60 middle"></th>
					<th class="fix-width-200 middle">รหัส</th>
					<th class="fix-width-80 middle">ราคา</th>
					<th class="fix-width-80 middle text-center">On Hand</th>
					<th class="fix-width-80 middle text-center">Ordered</th>
					<th class="fix-width-80 middle text-center">Reserved</th>
					<th class="fix-width-80 middle text-center">Available</th>
					<th class="fix-width-80 middle text-center">Is api</th>
					<th class="fix-width-80 middle text-center">Api rate</th>
					<th class="fix-width-60 middle text-center">Color</th>
					<th class="fix-width-60 middle text-center">Size</th>
					<th class="min-width-300 middle">Description</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->code; ?>" class="font-size-11">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><img src="<?php echo get_product_image($rs->code, 'mini'); ?>" width="40" /></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo number($rs->price, 2); ?></td>
						<td class="middle text-center blue"><?php echo number($rs->OnHand); ?></td>
						<td class="middle text-center green"><?php echo number($rs->ordered); ?></td>
						<td class="middle text-center green"><?php echo number($rs->reserved); ?></td>
						<td class="middle text-center red"><?php echo number($rs->balance); ?></td>
						<td class="middle text-center green"><?php echo $rs->is_api ? 'Y' : 'N'; ?></td>
						<td class="middle text-center green"><?php echo $rs->api_rate; ?> %</td>
						<td class="middle text-center"><?php echo $rs->color_code; ?></td>
						<td class="middle text-center"><?php echo $rs->size_code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
function clearFilter(){
  $.get(BASE_URL+'find_stock/clear_filter', function(){
    window.location.href = BASE_URL + 'find_stock';
  })
}


function getSearch()
{
	load_in();
	$('#searchForm').submit();
}


$('.search').keyup(function(e){
	if(e.keyCode == 13){
		getSearch();
	}
})

$('#warehouse').select2();
$('#group').select2();
$('#category').select2();
$('#kind').select2();
$('#type').select2();
$('#brand').select2();
$('#collection').select2();
</script>

<?php $this->load->view('include/footer'); ?>
