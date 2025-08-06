
<?php
$open = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
$close = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
$freze = $CLOSE_SYSTEM == 2 ? 'btn-warning' : '';
?>

  <form id="systemForm">
    <div class="row">
  	<?php if( $cando === TRUE ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
    	<div class="col-lg-3 col-md-3 col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-lg-9 col-md-9 col-sm-9">
      	<div class="btn-group fix-width-300">
        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:33%;" id="btn-open" onClick="toggleSystem(0)">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:33%;" id="btn-close" onClick="toggleSystem(1)">ปิด</button>
          <button type="button" class="btn btn-sm <?php echo $freze; ?>" style="width:34%" id="btn-freze" onclick="toggleSystem(2)">ดูอย่างเดียว</button>
        </div>
      	<input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      </div>
      <div class="divider"></div>

      <div class="col-lg-3 col-md-3 col-sm-3"><span class="form-control left-label">UAT Environment</span></div>
      <div class="col-lg-9 col-md-9 col-sm-9">
        <label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="IS_UAT" type="checkbox" value="1" <?php echo is_checked($IS_UAT , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="IS_UAT" id="is-uat" value="<?php echo $IS_UAT; ?>" />
        <span class="help-block">เปิดการป้อนเลขที่เอกสารด้วยมือ หากปิดระบบจะรับเลขที่เอกสารให้อัตโนมัติ</span>
      </div>
      <div class="divider"></div>
      <div class="divider-hidden"></div>
      <div class="divider-hidden"></div>
      <div class="divider-hidden"></div>
    <?php endif; ?>

      <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3">
        <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
      	<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('systemForm')">SAVE</button>
        <?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>
