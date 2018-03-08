<?php

use yii\grid\GridView;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
 <?php yii\widgets\Pjax::begin(['id' => 'group-grid']); ?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Group Qualifications',
        'boxTools' => '<i class="fa fa-plus add-new-group-qualification"></i>',
        'withBorder' => true,
    ])
    ?>
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
	<?php LteBox::end() ?>
<?php yii\widgets\Pjax::end(); ?>