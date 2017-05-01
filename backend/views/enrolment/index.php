<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enrolments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enrolment-index">

    <p>
        <?php echo Html::a('Create Enrolment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			[
				'label' => 'Program',
				'value' => function($data) {
					return $data->course->program->name;
				}
			],
			[
				'label' => 'Student',
				'value' => function($data) {
					return $data->student->fullName;
				}
			],
			[
				'label' => 'Teacher',
				'value' => function($data) {
					return $data->course->teacher->publicIdentity;
				}
			],
			[
				'label' => 'Expiry Date',
				'value' => function($data) {
					return Yii::$app->formatter->asDate($data->course->endDate);
				}
			],
        ],
    ]); ?>

</div>
