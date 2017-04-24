<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h4><strong><?= 'Payment Cycle' ?> </strong></h4> 
    <?php echo GridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'startDate:date',
            'endDate:date',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {create}',
                'buttons' => [
                    'create' => function ($url, $model) { 
                        $url = Url::to(['invoice/invoice-payment-cycle', 'id' => $model->id]);
                        if ($model->hasProFormaInvoice()) {
                            return null;
                        }
                        return Html::a('Create PFI', $url, [
                            'title' => Yii::t('yii', 'Create PFI'),
							'class' => ['btn-success btn-sm']
                        ]);                                
                    },
                    'view' => function ($url, $model) { 
                        if (!$model->hasProFormaInvoice()) {
                            return null;
                        }
                        $url = Url::to(['invoice/view', 'id' => $model->proFormaInvoice->id]);
                        return Html::a('View PFI', $url, [
                            'title' => Yii::t('yii', 'View PFI'),
							'class' => ['btn-info btn-sm']
                        ]);                                
                    }
                ]                            
            ],
        ],
    ]); ?>
    

