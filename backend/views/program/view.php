<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach($roles as $name => $description){
	$role = $name;
}
?>
<div class="program-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left">
        	<?php echo $model->name; ?>
        	<br>
			
        </p>
        <?php if($role === User::ROLE_ADMINISTRATOR):?>
            <div class="row col-md-12">
                <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Update Program'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
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
    </div>
    <div class="clearfix"></div>

    <!-- <p>
        <?php //echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php //echo Html::a('Delete', ['delete', 'id' => $model->id], [
            //'class' => 'btn btn-danger',
            // 'data' => [
             //   'confirm' => 'Are you sure you want to delete this item?',
             //    'method' => 'post',
          //  ],
        //]) ?>
    </p> -->
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
				'active' => true,
			],
			[
				'label' => 'Teachers',
				'content' => $teacherContent,
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
