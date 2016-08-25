<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
<h2>Title:<?= ! empty($model->title) ? $model->title : null ?></h2>
<div class="author">
  Date:<?php  
	$postDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
	echo $postDate->format('F j, Y'); ?>
</div>
<div>
	Content:<?= ! empty($model->content) ? $model->content : null ?> 
</div>

</div>
