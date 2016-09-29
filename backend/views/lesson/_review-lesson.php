<?php

use yii\bootstrap\Modal;
use common\models\Lesson;

Modal::begin([
    'header' => '<h4 class="m-0">Add Lesson</h4>',
    'id'=>'add-review-lesson-modal',
]);
echo $this->render('_form-review-lesson', [
		'model' => new Lesson(),
		'teachers' => $teachers,
]);
Modal::end();
?>