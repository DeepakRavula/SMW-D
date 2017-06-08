<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="group-course-student-index"> 
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Student Name',
				'format' => 'raw',
                'value' => function ($data) {
					$url = Url::to(['/student/view', 'id' => $data->id]); 
                    return Html::a($data->fullName, $url);
                },
            ],
            [
                'label' => 'Customer Name',
				'format' => 'raw',
                'value' => function ($data) {
					$url = Url::to(['/user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $data->customer->id]); 
                    return Html::a($data->customer->publicIdentity, $url);
                },
            ],
        ],
    ]); ?>

</div>