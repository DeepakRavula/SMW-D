<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\User;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$title = (int)$model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Private Progam' : 'Group Progam';
$this->title = ucwords($model->name) . '-' . $title;
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'ProgramSearch[type]' => $model->type], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach($roles as $name => $description){
	$role = $name;
}
?>
<div class="program-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-xs-2">
        	<i class="fa fa-music"></i> <?php echo $model->name; ?>
    </div>
	<?php $rate = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour($)' : 'Rate Per Course'; ?>
	<div class="col-xs-2" data-toggle="tooltip" data-placement="bottom" title= "<?= $rate; ?>" >
		<i class="fa fa-money"></i> <?php echo $model->rate; ?>
	</div>
        <?php if($role === User::ROLE_ADMINISTRATOR):?>
            <div class="col-md-12 m-t-20">
                <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
                <?php
                echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                    'class' => '',
                    'data' => [
                        'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <div class="clearfix"></div>
            </div>
        <?php endif;?> 
    <div class="clearfix"></div>

</div>
<div class="tabbable-panel">
	<div class="tabbable-line">
		<?php
			$studentContent = $this->render('_student', [
				'model' => $model,
				'studentDataProvider' => $studentDataProvider,
			]);
			$teacherContent = $this->render('_teacher', [
				'model' => $model,
				'teacherDataProvider' => $teacherDataProvider, 
			]);
		?>
		<?php
		$items = [
			[
				'label' => 'Students',
				'content' => $studentContent,
                'options' => [
                      'id' => 'student',
                  ],
				'active' => true,
			],
			[
				'label' => 'Teachers',
				'content' => $teacherContent,
                'options' => [
                      'id' => 'teacher',
                  ],
			],
		];
		?>
		<?php
		echo Tabs::widget([
			'items' => $items,
		]);
		?>
		<div class="clearfix"></div>
	</div>
</div>
