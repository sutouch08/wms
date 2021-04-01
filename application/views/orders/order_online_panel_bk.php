<?php if($order->is_term == 0) : ?>
<div class="row">
  <div class="col-sm-12">
  <?php echo paymentLabel($order->code, paymentExists($order->code), $order->is_paid); ?>
  </div>
</div>
<hr />
<div class="row">
  <div class="col-sm-12">
    <div class="tabable">
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
  <?php if( $order->is_term == 0 ) : ?>
      	<li role="presentation">
          <a href="#address" aria-expanded="false" aria-controls="address" role="tab" data-toggle="tab">ที่อยู่</a>
        </li>
  <?php endif; ?>
      </ul>
          <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">
<?php if( $order->is_term == 0 ) : ?>
				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class='row'>
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class='table table-bordered' style="margin-bottom:0px;">
                  <tr>
                    <td align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="row">
                      <?php if(!empty($addr)) : ?>
                      <?php 	foreach($addr as $rs) : ?>

                        <div class="col-sm-xs-12" id="<?php echo $rs->id; ?>">
                          <span class="col-sm-2 col-xs-12 padding-5"><?php echo $rs->name; ?></span>
                          <span class="col-sm-6 col-xs-12 padding-5"><?php echo $rs->address.' '. $rs->sub_district.' '.$rs->district.' '.$rs->province.' '. $rs->postcode; ?></span>
                          <span class="col-sm-2 col-xs-6 padding-5"><?php echo $rs->phone; ?></span>
                          <span class="col-sm-2 col-xs-12 padding-5">
                    <?php if( $rs->is_default == 1 ) : ?>
                            <button type="button" class="btn btn-mini btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                              <i class="fa fa-check"></i>
                            </button>
                    <?php else : ?>
                            <button type="button" class="btn btn-mini btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setDefault(<?php echo $rs->id; ?>)">
                              <i class="fa fa-check"></i>
                            </button>
                    <?php endif; ?>
                            <button type="button" class="btn btn-mini btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>)"><i class="fa fa-print"></i></button>
                            <button type="button" class="btn btn-mini btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                            <button type="button" class="btn btn-mini btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                          </span>
                        </div>
                      <?php 	endforeach; ?>
                      <?php else : ?>
                              <span>ไม่พบที่อยู่</span>
                      <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div><!-- /row-->
      </div>
<?php endif; ?>
      <div role="tabpanel" class="tab-pane active" id="state">
<?php $this->load->view('orders/order_state'); ?>
      </div>
    </div>
      </div>
	</div>
</div>
<?php endif; ?>
