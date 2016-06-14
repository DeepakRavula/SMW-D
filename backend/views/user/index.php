<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
$roles = array_flip($roles);
$role = array_search($searchModel->role_name,$roles);
$this->title = Yii::t('backend',  ! ($role) ? 'User' : $role.'s');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) use ($role){
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?UserSearch%5Brole_name%5D='.$role.'&id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'userProfile.firstname',
            'userProfile.lastname',
            'email',
			[
				'label' => 'Phone',
				'value' => function($data) {
					return ! empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
			],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	<p>
<?php echo Html::a(Yii::t('backend', 'Add '), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-success pull-left m-r-20']) ?>
    </p>

	<?php if($searchModel->role_name === User::ROLE_CUSTOMER):?>
    <p>
        <?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
    'modelClass' => 'User',
]), ['delete-all'], ['class' => 'btn btn-danger pull-left']) ?>
    </p>
    <div class="clearfix"></div>
	<?php endif;?>
</div>
