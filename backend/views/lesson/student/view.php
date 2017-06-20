<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Enrolment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="group-course-student-index"> 
	<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
        'timeout' => 6000,
    ]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'rowOptions' => function ($model, $key, $index, $grid) {
        	$url = Url::to(['student/view', 'id' => $model->id]);

	        return ['data-url' => $url];
    	},
        'columns' => [
            [
                'label' => 'Student Name',
                'value' => function ($data) {
                    return !empty($data->fullName) ? $data->fullName : null;
                },
            ],
            [
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return !empty($data->customer->publicIdentity) ? $data->customer->publicIdentity : null;
                },
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {create}',
                'buttons' => [
                    'create' => function ($url, $model) use ($lessonModel){
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->where(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                    
                        $url = Url::to(['invoice/group-lesson', 'lessonId' => $lessonModel->id, 'enrolmentId' => $enrolment->id]);
                        if (!$enrolment->hasInvoice($lessonModel->id)) {
                            return Html::a('Create Invoice', $url, [
                                'title' => Yii::t('yii', 'Create Invoice'),
                                                            'class' => ['btn-success btn-sm']
                            ]);
                        } else {
                            return null;
                        }

                    },
                    'view' => function ($url, $model) use ($lessonModel){
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->where(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        if (!$enrolment->hasInvoice($lessonModel->id)) {
                            return null;
                        }
                        $url = Url::to(['invoice/view', 'id' => $enrolment->getInvoice($lessonModel->id)->id]);
                        return Html::a('View Invoice', $url, [
                            'title' => Yii::t('yii', 'View Invoice'),
							'class' => ['btn-info btn-sm']
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
	</div>
</div>