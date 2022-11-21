<?php


/* @var $this yii\web\View */
/* @var $model common\models\ReminderNote */

$this->title = 'Create Reminder Notes';
$this->params['breadcrumbs'][] = ['label' => 'Reminder Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-notes-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
