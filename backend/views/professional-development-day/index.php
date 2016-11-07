<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProfessionalDevelopmentDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Professional Development Days';
?>

<?php $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);?>
<?php if ($lastRole->name === User::ROLE_ADMINISTRATOR):?>
<?php $this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);?>
<?php endif; ?>

<div class="professional-development-day-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
   <?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
</div>
