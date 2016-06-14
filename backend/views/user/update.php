<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */
$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
$roles = array_flip($roles);
$role = array_search($model->roles,$roles);
$this->title = Yii::t('backend', 'Update {modelClass} ', ['modelClass' => $role]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', $role.'s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('backend', 'Update')];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
		'programs' => $programs,
		'phoneNumberModels' => $phoneNumberModels,
		'addressModels' => $addressModels
    ]) ?>

</div>
