<div class="box-header ui-sortable-handle" style="cursor: move;">
	<div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Status">
	</div>
</div>
<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;"><div class="box-body chat" id="chat-box" style="overflow: hidden; width: auto; height: 250px;">
		<!-- chat item -->
		<div class="item">
			<p class="message">
                    <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                    <?= $model->createdUser->publicIdentity;?>
				<?= $model->content; ?>
			</p>
		</div>
		<!-- /.item -->
	</div>
	<div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 184.911px;"></div>
	<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>

</div>


