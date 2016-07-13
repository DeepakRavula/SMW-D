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
foreach($roles as $name => $description){
	if($name === $searchModel->role_name){
		$role = $description;
		break;
	}
}
$roleName = $searchModel->role_name;
$this->title = Yii::t('backend',  ! isset($role) ? 'User' : $role.'s');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
        	'filterModel' => $searchModel,
            'rowOptions' => function ($model, $key, $index, $grid) use ($roleName){
                $u= \yii\helpers\StringHelper::basename(get_class($model));
                $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
                return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?UserSearch%5Brole_name%5D='.$roleName.'&id="+(this.id);'];
            },
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
			[
				'attribute' => 'firstname',
				'label' => 'First Name',
				'value' => function($data){
					return ! empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
				}
			],
			[
				'attribute' => 'lastname',
				'label' => 'Last Name',
				'value' => function($data){
					return ! empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
				}
			],
			'email',
			[
				'label' => 'Phone',
				'value' => function($data) {
					return ! empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
				},
			],
		],
	]); ?>
<?php yii\widgets\Pjax::end(); ?>
<div class="p-l-20 m-b-20">
<?php echo Html::a(Yii::t('backend', 'Add '), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-success pull-left m-r-20']) ?>

<?php if($searchModel->role_name === User::ROLE_CUSTOMER):?>
	<?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
		'modelClass' => 'User',
		]), 
		['delete-all-customer'], 
		['class' => 'btn btn-danger pull-left']) 
	?>
<?php endif;?>
<?php if($searchModel->role_name === User::ROLE_STAFFMEMBER):?>
	<?php echo Html::a(Yii::t('backend', 'Delete All Staff Members', [
		'modelClass' => 'User',
		]), 
		['delete-all-staff-members'], 
		['class' => 'btn btn-danger pull-left']) 
	?>
<?php endif;?>    
<div class="clearfix"></div>
</div>
</div>
