<?php

use backend\models\search\CourseSearch;
use kartik\datetime\DateTimePickerAsset;
use common\models\Lesson;
DateTimePickerAsset::register($this);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup-with-teacher.php';
?>
<div id="index-success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php 
if ((int)$searchModel->type === Lesson::TYPE_PRIVATE_LESSON) : ?>
<?= $this->render('_index-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);?>
<?php else : ?>
<?php
$courseSearchModel = new CourseSearch();
$dataProvider = $courseSearchModel->search(Yii::$app->request->queryParams);
?>
<?= $this->render('/course/index', [
    'searchModel' => $courseSearchModel,
    'dataProvider' => $dataProvider,
]);
?>
<?php endif;?>
