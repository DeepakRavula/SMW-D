<?php

use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Import {modelClass}', [
    'modelClass' => 'User',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Import'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

<?php echo \trntv\filekit\widget\Upload::widget([
    'model' => $model,
    'attribute' => 'file',
    'url' => ['upload'],
    'sortable' => true,
    'maxFileSize' => 10 * 1024 * 1024, // 10Mb
    //'minFileSize' => 1 * 1024 * 1024, // 1Mb
    'maxNumberOfFiles' => 3, // default 1,
    'acceptFileTypes' => new JsExpression('/(\.|\/)(csv|CSV)$/i'),
    'clientOptions' => [],
]);?>
</div>
