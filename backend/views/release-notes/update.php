<?php

use yii\helpers\Html; 

/* @var $this yii\web\View */ 
/* @var $model common\models\ReleaseNotes */ 

$this->title = 'Update Release Notes: ' . ' ' . $model->id; 
$this->params['breadcrumbs'][] = ['label' => 'Release Notes', 'url' => ['index']]; 
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]]; 
$this->params['breadcrumbs'][] = 'Update'; 
?> 
<div class="release-notes-update"> 

    <?php echo $this->render('_form', [ 
        'model' => $model, 
    ]) ?> 

</div> 