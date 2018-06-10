<?php
//print_r($model->id);die;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
$this->title = 'Proforma Invoice';
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = $this->render('_buttons', [
    'model' => $model,
]); ?>
