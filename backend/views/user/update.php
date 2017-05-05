<?php

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */
$roles = ArrayHelper::getColumn(
             Yii::$app->authManager->getRoles(), 'description'
        );
foreach ($roles as $name => $description) {
    if ($name === $model->roles) {
        $role = $description;
    }
}
$this->title = Yii::t('backend', 'Edit {modelClass} ', ['modelClass' => $role]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', $role.'s'), 'url' => ['index', 'UserSearch[role_name]' => $model->roles]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Edit')];
?>
<style>
	.address-fields, .phone-fields, .quali-fields, .teacher-availability-fields{
        display: block !important;
    }
    .hr-ad, .hr-ph, .add-quali{
    	display: none !important;
    }
</style>
<div class="user-update p-T-10">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
        'programs' => $programs,
        'locations' => $locations,
        'phoneNumberModels' => $phoneNumberModels,
        'addressModels' => $addressModels,
    ]) ?>

</div>
