<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $emailTemplate->header ?>
<div class="invoice-view">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-8">
            <div class = "row">
                
            
            <?php if ($invoiceLineItemsDataProvider) : ?>
            <?= Html::label('Invoice', ['class' => 'admin-login']) ?>
            <?= $this->render('_invoice-line-item', [
                'model' => $model,
                'isCreatePfi' => false,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'searchModel' => $searchModel
            ]);
            ?>
            </div>
            <?php endif; ?>

            <table style = "width:100%;">
            <table style = "width:50%">
            <table class = "table table-condensed">
            <tr>
            
            </tr>
            </table>
            </table>
            </table>
            </div>
            </div>
    </div>
    </div>
    </div>   
    </div>     
<?= $emailTemplate->footer ?>