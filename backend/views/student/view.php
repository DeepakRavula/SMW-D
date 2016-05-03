<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-view">

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

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'first_name',
            'last_name',
            'birth_date',
			[
				'label' => 'Age',
				'value' => DateTime::createFromFormat('Y-m-d', $model->birth_date)->diff(new DateTime('now'))->y, 
			],	
			[
				'label' => 'Customer Name',
				'value' => ! empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null , 
				
			],
        ],
    ]) ?>

</div>
