<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
 <div class="row-fluid">
	<div id="course-calendar">
    <div id="spinner" class="spinner" style="display:none;">
    <img src="/backend/web/img/loader.gif" alt="" height="50" width="50"/>
</div>
    </div>
</div>
 <div class="pull-right">
     <?= Html::a('Cancel', '#', ['class' => 'btn btn-default course-cancel']);?><?= Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-info course-apply', 'name' => 'button']) ?>
	
	<div class="clearfix"></div>
</div>