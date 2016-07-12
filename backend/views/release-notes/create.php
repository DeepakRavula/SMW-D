<?php

use yii\helpers\Html; 


/* @var $this yii\web\View */ 
/* @var $model common\models\ReleaseNotes */ 

$this->title = 'Create Release Notes'; 
$this->params['breadcrumbs'][] = ['label' => 'Release Notes', 'url' => ['index']]; 
$this->params['breadcrumbs'][] = $this->title; 
?> 
<div class="release-notes-create p-10"> 

    <?php echo $this->render('_form', [ 
        'model' => $model, 
    ]) ?> 

</div> 