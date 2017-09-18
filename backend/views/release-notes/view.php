<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ReleaseNotes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Release Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="release-notes-view p-10"> 

    <p> 
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?> 
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?> 
    </p> 

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'notes:raw',
            'date:date',
            'user.publicIdentity',
        ],
    ]) ?> 

</div> 