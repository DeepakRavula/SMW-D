<?php

use common\models\Note;
use yii\widgets\ListView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
	.lesson-note-content .empty{
		padding:0;
	}
</style>
<div id="lesson-note" class="col-md-12 p-l-10">
	<h4 class="pull-left m-r-20">Notes</h4>
	<a href="#" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Notes</h4>',
    'id'=>'lesson-note-modal',
]);
 echo $this->render('_form', [
		'model' => new Note(),
]);
Modal::end();
?>
<div class="lesson-note-content p-10">
<?=
	$this->render('_view', [
		'noteDataProvider' =>  $noteDataProvider,
		'itemView' => '_list',
]);
?>
</div>
            <div class="box-header ui-sortable-handle" style="cursor: move;">
              <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Status">
              </div>
            </div>
            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;"><div class="box-body chat" id="chat-box" style="overflow: hidden; width: auto; height: 250px;">
              <!-- chat item -->
              <div class="item">
                <img src="dist/img/user4-128x128.jpg" alt="user image" class="online">

                <p class="message">
                  <a href="#" class="name">
                    <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                    Mike Doe
                  </a>
                  I would like to meet you to discuss the latest news about
                  the arrival of the new theme. They say it is going to be one the
                  best themes on the market
                </p>
              </div>
              <!-- /.item -->
            </div>
				<div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 184.911px;"></div>
			<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
				
			</div>
            <!-- /.chat -->
            <div class="box-footer">
              <div class="input-group">
                <input class="form-control" placeholder="Type message...">

                <div class="input-group-btn">
                  <button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>