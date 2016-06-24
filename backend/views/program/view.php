<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-view p-10">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'rate'
        ],
    ]) ?>

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
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
