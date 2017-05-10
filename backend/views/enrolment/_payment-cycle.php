<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>

    <?php echo GridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => '',
        'columns' => [
            'startDate:date',
            'endDate:date',
            [
                'label' => 'Due Date',
                'value' => function($data) {
                    return !empty($data->proFormaInvoice->dueDate) ? 
                        (new \DateTime($data->proFormaInvoice->dueDate))->format('d-m-Y') : '-';
                }
            ],
            [
                'label' => 'Pro-Forma Invoice',
                'value' => function($data) {
                    $invoiceNumber = '-';
                    if($data->hasProformaInvoice()) {
                        $invoiceNumber = $data->proFormaInvoice->getInvoiceNumber();
                    }
                    return $invoiceNumber; 
                }
            ],
            [
                'label' => 'Status',
                'value' => function($data) {
                    $result = 'Owing';
                    if(empty($data->proFormaInvoice)) {
                        $result = '-';
                    }
                    if(!empty($data->proFormaInvoice) && $data->proFormaInvoice->isPaid()) {
                        $result = 'Paid';
                    }	
                    return $result;
                }

            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {create}',
                'buttons' => [
                    'create' => function ($url, $model) { 
                        $url = Url::to(['invoice/invoice-payment-cycle', 'id' => $model->id]);
                        if ($model->canRiseProformaInvoice() && !$model->hasProFormaInvoice()) {
                            return Html::a('Create PFI', $url, [
                                'title' => Yii::t('yii', 'Create PFI'),
                                                            'class' => ['btn-success btn-sm']
                            ]);
                        } else {
                            return null;
                        }
                                                      
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
    

