<?php

use yii\helpers\Html;
use common\models\Program;

$this->title = $model->student->fullName;
?>
<?= $this->render('_view-enrolment',[
	'model' => $model,
]);?>
<div class="row-fluid p-10">
    
    <?= Html::a('<i class="fa fa-print"></i> Print', ['course/print', 'id' => $model->course->id], ['class' => 'btn btn-default pull-left', 'target'=>'_blank',]) ?>  
    <?= Html::a('<i class="fa fa-envelope-o"></i> Email Lessons', ['send-mail', 'id' => $model->id], ['class' => 'btn btn-default pull-left  m-l-20',]) ?>
	<?php if((int) $model->course->program->type !== (int) Program::TYPE_GROUP_PROGRAM) : ?>
		<?php $this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => ' m-l-20 btn btn-sm btn-primary']) ?>
	<?php endif; ?>
    <div class="clearfix"></div>
</div>
