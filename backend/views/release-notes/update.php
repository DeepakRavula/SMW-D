<?php


/* @var $this yii\web\View */
/* @var $model common\models\ReleaseNotes */

$this->title = 'Edit Release Notes';
$this->params['breadcrumbs'][] = ['label' => 'Release Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?> 
<div class="release-notes-update p-10"> 

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?> 

</div> 