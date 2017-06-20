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
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {create}',
                'buttons' => [
                    'create' => function ($url, $model) use ($courseModel){
                        $enrolment = $courseModel->getStudentEnrolment($model);
                        $url = Url::to(['invoice/enrolment', 'id' => $enrolment->id]);
                        if (!$enrolment->hasProFormaInvoice()) {
                            return Html::a('Create PFI', $url, [
                                'title' => Yii::t('yii', 'Create PFI'),
                                                            'class' => ['btn-success btn-sm']
                            ]);
                        } else {
                            return null;
                        }

                    },
                    'view' => function ($url, $model) use ($courseModel){
                        $enrolment = $courseModel->getStudentEnrolment($model);
                        if (!$enrolment->hasProFormaInvoice()) {
                            return null;
                        }
                        $url = Url::to(['invoice/view', 'id' => $enrolment->proFormaInvoice->id]);
                        return Html::a('View PFI', $url, [
                            'title' => Yii::t('yii', 'View PFI'),
							'class' => ['btn-info btn-sm']
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>