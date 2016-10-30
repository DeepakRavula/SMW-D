<?php
use yii\bootstrap\Modal;
use common\models\Lesson;
?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Add Lesson</h4>',
    'id'=>'new-lesson-modal',
]);
 echo $this->render('//lesson/_form', [
		'model' => new Lesson(),
        'studentModel' => $studentModel,
]);
Modal::end();
?>