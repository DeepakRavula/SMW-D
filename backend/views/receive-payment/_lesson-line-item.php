<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;

?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'lesson-line-item-listing','timeout' => 6000,]); ?>
    <?php  
        $columns = [];
        if ($searchModel->showCheckBox) {
            array_push($columns, [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['style' => 'width:30px;'],
                'checkboxOptions' => function($model, $key, $index, $column) {
                    return ['checked' => true,'class' =>'check-checkbox'];
                }
            ]);
        }

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'attribute' => 'dateRange',
            'label' => 'Date',
            'filterType' => KartikGridView::FILTER_DATE_RANGE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'convertFormat' => true,
                'initRangeExpr' => true,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d, Y',
                    ],
                    'opens' => 'right'
                ]
            ],
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ]);

        array_push($columns, [
            'label' => 'Student',
            'attribute' => 'student',
            'value' => function ($data) {
                return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
            },
            'filterType' => KartikGridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Student::find()
                ->orderBy(['first_name' => SORT_ASC])
                ->joinWith(['enrolment' => function ($query) {
                    $query->joinWith(['course' => function ($query) {
                        $query->confirmed()
                            ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                    }]);
                }])
                ->customer($model->userId)
                ->all(), 'id', 'fullName'),
            'filterWidgetOptions' => [
                'options' => [
                    'id' => 'student'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ],
            'filterInputOptions' => ['placeholder' => 'Student'],
        ]);

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'program',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ]);

        array_push($columns, [
            'attribute' => 'teacher',
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ]);

        array_push($columns, [
            'label' => 'Amount',
            'attribute' => 'amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->netPrice);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);

        array_push($columns, [
            'attribute' => 'balance',
            'label' => 'Balance',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->getOwingAmount($data->enrolment->id));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);
    ?>
<?php if ($searchModel->showCheckBox) : ?>
    <?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $searchModel->userId]),
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php else: ?>
<?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php endif; ?>
<?php Pjax::end(); ?>
