<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .content > .box{
        border-top: 0;
        background: transparent;
        box-shadow: 0px 0px 0px;
    }
</style>
          <!-- Box Comment -->
          <div class="box box-widget">
            <div class="box-header with-border">
              <div class="user-block">
                <span class="username"><a href="#"><?= !empty($model->title) ? $model->title : null ?></a></span>
                <span class="description"><i class="fa fa-clock"></i> <?php 
    $postDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
    echo $postDate->format('F j, Y'); ?></span>
              </div>
              <!-- /.user-block -->
              <div class="box-tools">
                <?php echo Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                <?php echo Html::a('<i class="fa fa-trash-o"></i>', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-primary btn-sm',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body p-10">
              <?= !empty($model->content) ? $model->content : null ?> 
            </div>
          </div>
          <!-- /.box -->
