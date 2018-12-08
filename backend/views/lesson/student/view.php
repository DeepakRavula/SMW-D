<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Enrolment;
use common\models\LessonPayment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="group-course-student-index"> 
	<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
        'timeout' => 6000,
        'id' => 'group-lesson-discount'
    ]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'summary' => false,
        'emptyText' => false,
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
            [
                'label' => 'Gross Price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) use ($lessonModel) {
                    return Yii::$app->formatter->asCurrency(round($lessonModel->grossPrice, 2));
                },
            ],
            [
                'label' => 'Discount',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) use ($lessonModel) {
                    $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $data->id])->one();
                    return Yii::$app->formatter->asCurrency($lessonModel->getGroupDiscount($enrolment));
                },
            ],
            [
                'label' => 'Net Price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) use ($lessonModel) {
                    $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $data->id])->one();
                    return Yii::$app->formatter->asCurrency(round($lessonModel->getGroupNetPrice($enrolment), 2));
                },
            ],
            [
                'label' => 'Owing',
                'attribute' => 'owing',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) use ($lessonModel) {
                    $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $data->id])->one();
                    return Yii::$app->formatter->asCurrency($lessonModel->getOwingAmount($enrolment->id));
                },
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {view} {create} {payment}',
                'buttons' => [
                    'edit' => function ($url, $model) use ($lessonModel) {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        if (!$enrolment->hasInvoice($lessonModel->id)) {
                            $url = Url::to(['group-lesson/apply-discount', 'GroupLesson[enrolmentId]' => $enrolment->id, 'GroupLesson[lessonId]' => [$lessonModel->id]]);
                            return Html::a('Edit Discount', '#', [
                                'title' => Yii::t('yii', 'Edit Discount'),
                                'class' => ['btn-info btn-sm group-lesson-discount'],
                                'action-url' => $url
                            ]);
                        }
                    },
                    'view' => function ($url, $model) use ($lessonModel) {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        if ($enrolment->hasInvoice($lessonModel->id)) {
                            $url = Url::to(['invoice/view', 'id' => $enrolment->getInvoice($lessonModel->id)->id]);
                            return Html::a('View Invoice', $url, [
                                'title' => Yii::t('yii', 'View Invoice'),
                                'class' => ['btn-info btn-sm']
                            ]);
                        }
                    },
                    'create' => function ($url, $model) use ($lessonModel) {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                    
                        $url = Url::to(['invoice/group-lesson', 'lessonId' => $lessonModel->id, 'enrolmentId' => $enrolment->id]);
                        if (!$enrolment->hasInvoice($lessonModel->id)) {
                            return Html::a('Create Invoice', $url, [
                                'title' => Yii::t('yii', 'Create Invoice'),
                                'class' => ['btn-success btn-sm']
                            ]);
                        }
                    },
                    'payment' => function ($url, $model) use ($lessonModel) {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $lessonModel->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        $lessonPayment = LessonPayment::find()
                            ->notDeleted()
                            ->andWhere(['enrolmentId' => $enrolment->id, 'lessonId' => $lessonModel->id])
                            ->all();
                        if ($lessonPayment) {
                            $url = Url::to(['lesson/payment', 'lessonId' => $lessonModel->id, 'enrolmentId' => $enrolment->id]);
                                return Html::a('View Payment', null, [
                                'id' => 'view-payment',
                                'url' => $url,
                                'title' => Yii::t('yii', 'View Payment'),
                                'class' => ['btn-info btn-sm']
                            ]);
                        }
                    }
                ]
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
	</div>
</div>