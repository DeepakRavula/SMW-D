<?php
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */

$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
foreach($roles as $name => $description){
	if($name === $model->roles){
		$role = $description;
	}
}
$this->title = Yii::t('backend', 'Add new {modelClass}', [
    'modelClass' => $role,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', $role.'s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Create')];
?>
<div class="user-create m-t-10">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
		'locations' => $locations,
        'programs' => $programs,
		'teacherAvailabilityModel' => $teacherAvailabilityModel,
		'phoneNumberModels' => $phoneNumberModels,
		'addressModels' => $addressModels,
		'section' => $section
    ]) ?>

</div>
