<?php

use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Location;

$this->title = Yii::t('backend', 'Timeline');
?>
<?php
$locationId  = Location::findOne(['slug' => \Yii::$app->location])->id;
$loggedUser  = User::findOne(['id' => Yii::$app->user->id]);
$findUserBot = User::findByRole(User::ROLE_BOT);
$botUser     = end($findUserBot);
$query       = User::find()
    ->excludeWalkin()
    ->notDeleted();
if ($loggedUser->isAdmin() || $loggedUser->isOwner()) {
    $query->backendUsers();
}
if ($loggedUser->isAdmin()) {

    $query->adminWithLocation($locationId);
} elseif ($loggedUser->isOwner()) {

    $query->location($locationId);
} else {
    $query->staffs()
        ->location($locationId);
}
$query->orWhere(['user.id' => $botUser->id]);
$users                = $query->all();
$usersList            = ArrayHelper::map($users, 'id', 'publicIdentity');
?>
<?php
$columns              = [
        [
        'attribute' => 'created_at',
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDateTime($data->log->createdOn);
        },
        'contentOptions' => ['style' => 'width:200px'],
        'filterType' => KartikGridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'id' => 'timeline-daterange-search',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'options' => [
				'readOnly' => true,
			],
            'pluginOptions' => [
                'autoApply' => true,
                'allowClear' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')",
                        "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')",
                        "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d,Y',
                ],
                'opens' => 'right',
            ],
        ],
    ],
        [
        'attribute' => 'createdUser',
        'label' => 'Created User',
        'value' => function ($data) {
            return $data->log->createdUser->publicIdentity;
        },
        'filterType' => KartikGridView::FILTER_SELECT2,
        'filter' => $usersList,
        'filterWidgetOptions' => [
            'options' => [
                'id' => 'user',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ],
        'filterInputOptions' => ['placeholder' => 'Select User'],
    ],
        [
        'attribute' => 'message',
        'label' => 'Message',
        'value' => function ($data) {
            return $data->getMessage();
        },
        'format' => 'raw'
    ]
];
?>   
<?php
yii\widgets\Pjax::begin([
    'timeout' => 6000,
])
?>
<?php
echo KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => $columns,
]);
?>

<?php \yii\widgets\Pjax::end(); ?>