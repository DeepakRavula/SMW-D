<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::Begin(['id' => 'lesson-cost'])?>
<div class="box box-default collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Cost</h3>
                <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" id= 'show_hide_bt' data-widget="collapse"><i class="fa fa-eye"></i>
            	</button>
                <i title="Edit" class="fa fa-pencil edit-cost"></i>                
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: none;">
                <dl class="dl-horizontal">
                <dt>Cost/hr </dt>
                <dd><?= Yii::$app->formatter->asCurrency(round($model->teacherRate, 2)); ?></dd>
                <dt>Cost </dt>
                <dd><?= Yii::$app->formatter->asCurrency(round($model->netCost, 2)); ?></dd>
                <?php if (!$model->isGroup()): ?>
                <dt>Price </dt>
                <dd><?= Yii::$app->formatter->asCurrency(round($model->getSubTotal(), 2)); ?></dd>
                <?php $lessonProfit = $model->getSubTotal() - $model->netCost; ?> 
                <dt>Profit </dt>
                <dd><?= Yii::$app->formatter->asCurrency(round($lessonProfit, 2)); ?></dd>
                <?php endif;?>
                <?php if ($model->isGroup()): ?>
                <dt>Cost Per Student</dt>
                <?php $number_of_students = $model->netCost / count($model->enrolments); ?>
                <dd><?= Yii::$app->formatter->asCurrency(round($number_of_students, 2)); ?></dd>
                <?php endif;?>
                </dl>
            </div>
            <!-- /.box-body -->
          </div>
<?php Pjax::end();?>				

<script>
   $(document).off('click', '#show_hide_bt').on('click', '#show_hide_bt', function () {
        $(this).find('i').toggleClass('fa-eye-slash');
    });
</script>