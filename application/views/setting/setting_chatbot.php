
<div class="tab-pane fade" id="chatbot">
	<?php
		$chatbot_api_on = $CHATBOT_API == 1 ? 'btn-success' : '';
		$chatbot_api_off = $CHATBOT_API == 0 ? 'btn-danger' : '';
		$stock_on = $SYNC_CHATBOT_STOCK == 1 ? 'btn-success' : '';
		$stock_off = $SYNC_CHATBOT_STOCK == 0 ? 'btn-danger' : '';
		$log_on = $CHATBOT_LOG_JSON == 1 ? 'btn-success' : '';
		$log_off = $CHATBOT_LOG_JSON == 0 ? 'btn-danger' : '';
	 ?>
	<form id="chatbotForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-sm-4">
        <span class="form-control left-label">Chatbot API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $chatbot_api_on; ?>" style="width:50%;" id="btn-chatbot-api-on" onClick="toggleChatbotApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $chatbot_api_off; ?>" style="width:50%;" id="btn-chatbot-api-off" onClick="toggleChatbotApi(0)">OFF</button>
				</div>
				<input type="hidden" name="CHATBOT_API" id="chatbot-api" value="<?php echo $CHATBOT_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
      </div>
      <div class="divider-hidden"></div>

    	<div class="col-sm-4">
        <span class="form-control left-label">Chatbot api endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="CHATBOT_API_HOST"  value="<?php echo $CHATBOT_API_HOST; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Chatbot api user name</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="CHATBOT_API_USER_NAME" value="<?php echo $CHATBOT_API_USER_NAME; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Chatbot api secret</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="CHATBOT_API_SECRET" value="<?php echo $CHATBOT_API_SECRET; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Chatbot api credential</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="CHATBOT_API_CREDENTIAL" value="<?php echo $CHATBOT_API_CREDENTIAL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">รหัสคลัง CHATBOT</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="chatbot-warehouse" name="CHATBOT_WAREHOUSE_CODE" value="<?php echo $CHATBOT_WAREHOUSE_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>


			<div class="col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $stock_on; ?>" style="width:50%;" id="btn-stock-on" onClick="toggleSyncStock(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $stock_off; ?>" style="width:50%;" id="btn-stock-off" onClick="toggleSyncStock(0)">OFF</button>
				</div>
				<input type="hidden" name="SYNC_CHATBOT_STOCK" id="sync-chatbot-stock" value="<?php echo $SYNC_CHATBOT_STOCK; ?>" />
				<span class="help-block">Sync available stock to chatbot api</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $log_on; ?>" style="width:50%;" id="btn-log-on" onClick="toggleLogJson(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $log_off; ?>" style="width:50%;" id="btn-log-off" onClick="toggleLogJson(0)">OFF</button>
				</div>
				<input type="hidden" name="CHATBOT_LOG_JSON" id="chatbot-log-json" value="<?php echo $CHATBOT_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('chatbotForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>
