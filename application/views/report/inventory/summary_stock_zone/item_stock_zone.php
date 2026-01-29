<?php $this->load->view('include/header'); ?>
<style>

	label {
		margin-top:10px;
	}
</style>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title text-center"><?php echo $binName; ?></h4>
  </div>
</div>
<hr>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table tableFixHead">
      <thead>
        <tr class="font-size-11">
          <th class="min-width-250">Item Code</th>
          <th class="fix-width-100 text-center">Qty</th>
        </tr>
      </thead>
      <tbody>
        <?php if( ! empty($items)) : ?>
          <?php $i = 1; ?>
          <?php $total = 0; ?>
          <?php foreach($items as $rs) : ?>
            <tr>
              <td><?php echo $rs->ItemCode; ?></td>
              <td class="text-center"><?php echo number($rs->Qty); ?></td>
            </tr>
            <?php $i++; ?>
            <?php $total += $rs->Qty; ?>
          <?php endforeach; ?>
          <tr><td class="text-right">Total</td><td class="text-center"><?php echo number($total); ?></td></td>
        <?php endif; ?>
      </tbody>
    </table>
	</div>	
</div>

<?php $this->load->view('include/footer'); ?>
