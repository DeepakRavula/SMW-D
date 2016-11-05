<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Holidays';
?>

<?php $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);?>
<?php if ($lastRole->name === User::ROLE_ADMINISTRATOR):?>
<?php $this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);?>
<?php endif; ?>

<div class="grid-row-open">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

<?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['holiday/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'attribute' => 'date',
                'label' => 'Date',
                'value' => function ($data) {
                    return !(empty($data->date)) ? Yii::$app->formatter->asDate($data->date) : null;
                },
            ],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>

</div>
