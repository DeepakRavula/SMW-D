<?php

use yii\helpers\Html;
?>
<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?php echo!empty($model->userProfile->firstname) ? $model->userProfile->firstname : null ?>
            <?php echo!empty($model->userProfile->lastname) ? $model->userProfile->lastname : null ?> 
            <!-- <em>
                <small><?php //echo!empty($model->email) ? $model->email : null ?></small>
            </em> -->
        </p>
        <div class="m-l-20 pull-left m-t-5">
            <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Update Profile'), ['update', 'id' => $model->id,'section' => 'profile'], ['class' => 'm-r-20']) ?>
            <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => '',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
