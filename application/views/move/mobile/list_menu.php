<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-25">
				<span class="width-100" onclick="refresh()">
					<i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
				</span>
			</div>
			<div class="footer-menu width-25">
				<?php if($this->pm->can_add) : ?>
				<span class="width-100" onclick="addNew()">
					<i class="fa fa-plus-circle fa-2x white"></i><span class="fon-size-12">Add New</span>
				</span>
				<?php endif; ?>
			</div>
			<div class="footer-menu width-25">
				<span class="width-100" onclick="clearFilter()">
					<i class="fa fa-retweet fa-2x white"></i><span class="fon-size-12">Clear Filter</span>
				</span>
			</div>
			<div class="footer-menu width-25">
				<span class="width-100" onclick="toggleFilter()">
					<i class="fa fa-search fa-2x white"></i><span class="fon-size-12">Filter</span>
				</span>
			</div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>
