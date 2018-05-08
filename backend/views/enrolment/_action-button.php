<?php
use yii\helpers\Html;
?>

<?= Html::a('<i class="fa fa-trash-o"></i>', ['enrolment/delete', 'id' => $model->id], [
    'id' => 'enrolment-delete-' . $model->id,
    'title' => Yii::t('yii', 'Delete'),
    'class' => 'enrolment-delete btn btn-box-tool'
])?>
<?= Html::a('<i class="fa fa-trash fa-2x btn-danger" aria-hidden="true"></i>', null, [
    'id' => 'enrolment-full-delete-' . $model->id,
    'title' => Yii::t('yii', 'Full Delete'),
    'class' => 'enrolment-full-delete btn btn-box-tool'
])?>