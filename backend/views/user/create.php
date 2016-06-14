<?php
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */

$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
$roles = array_flip($roles);
$role = array_search($model->roles,$roles);
$this->title = Yii::t('backend', 'Add new {modelClass}', [
    'modelClass' => $role,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', $role.'s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Create')];
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
        'programs' => $programs,
		'phoneNumberModels' => $phoneNumberModels,
		'addressModels' => $addressModels
    ]) ?>

</div>
