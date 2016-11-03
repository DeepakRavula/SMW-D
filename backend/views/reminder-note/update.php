<?php


/* @var $this yii\web\View */
/* @var $model common\models\ReminderNote */

$this->title = 'Update Reminder Notes';
$this->params['breadcrumbs'][] = ['label' => 'Reminder Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reminder-notes-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
