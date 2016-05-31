<?php
/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => $model->roles,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', ucwords($model->roles)), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Create')];
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
        'programs' => $programs
    ]) ?>

</div>
