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
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) use ($roleName){
                $u= \yii\helpers\StringHelper::basename(get_class($model));
                $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
                return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?UserSearch%5Brole_name%5D='.$roleName.'&id="+(this.id);'];
            },
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
            [
				'class' => 'yii\grid\SerialColumn',
				'header' => 'Serial No.',
			],
                'userProfile.firstname',
                'userProfile.lastname',
                'email',
                [
                    'label' => 'Role',
                    'value' => function($data) {
                        $roles = \Yii::$app->authManager->getRolesByUser($data->id);
                            foreach($roles as $roles){
                                return $roles->description;
                            }
                    },
                ],
                [
                    'label' => 'Phone',
                    'value' => function($data) {
                        return ! empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                    },
                ],
                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    <div class="p-l-20 m-b-20">
<?php echo Html::a(Yii::t('backend', 'Add '), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-success pull-left m-r-20']) ?>

	<?php if($searchModel->role_name === User::ROLE_CUSTOMER):?>
        <?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
            'modelClass' => 'User',
            ]), 
            ['delete-all'], 
            ['class' => 'btn btn-danger pull-left']) 
        ?>
    <?php endif;?>
    <div class="clearfix"></div>
    </div>
</div>
