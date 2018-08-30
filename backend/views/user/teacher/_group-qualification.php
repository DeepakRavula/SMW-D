<?php

use yii\grid\GridView;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>

<?php Pjax::Begin(['id' => 'group-grid'])?>
<div class="box box-default collapsed-box">
            <div class="box-header with-border">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
              <h3 class="box-title">Group Qualifications</h3>
                <div class="box-tools pull-right">
                <i title="Add" class="fa fa-plus add-new-group-qualification"></i>                
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: none;">
            <?php echo GridView::widget([
             'id' => 'qualification-grid',
                'dataProvider' => $groupQualificationDataProvider,
                'tableOptions' => ['class' => 'table table-condensed'],
                'headerRowOptions' => ['class' => 'bg-light-gray'],
                'summary' => false,
                                'emptyText' => false,
                'columns' => [
                    'program.name',
                    [
                        'label' => 'Rate ($/hr)',
                        'format' => 'currency',
                        'value' => function ($data) {
                            return $data->rate;
                        },
                        'visible' => Yii::$app->user->can('teacherQualificationRate')
                ]
            ],
            ]); ?>	
            </div>
            <!-- /.box-body -->
          </div>
<?php Pjax::end();?>