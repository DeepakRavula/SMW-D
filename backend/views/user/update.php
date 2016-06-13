<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Update {modelClass} ', ['modelClass' => ucwords($model->roles)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', ucwords($model->roles).'s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->email]];
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
