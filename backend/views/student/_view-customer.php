<?php
use yii\helpers\Html;

?>
<div class="row-fluid">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?= $model->customer->publicidentity ?>
             <em>
                <small><?php echo !empty($model->customer->email) ? $model->customer->email : null ?></small>
            </em> 
        </p>
    </div>
    <div class="row-fluid">
		<div id="w3" class="list-view">
            <div data-key="351">
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($model->customer->billingAddress->address) ? $model->customer->billingAddress->address : null) ?> </div>
                    <div><?= Html::encode(!empty($model->customer->billingAddress->city->name) ? $model->customer->billingAddress->city->name : null) ?> <?= Html::encode(!empty($model->customer->billingAddress->province->name) ? $model->customer->billingAddress->province->name : null) ?></div>
                    <div><?= Html::encode(!empty($model->customer->billingAddress->country->name) ? $model->customer->billingAddress->country->name : null) ?> <?= Html::encode(!empty($model->customer->billingAddress->postal_code) ? $model->customer->billingAddress->postal_code : null) ?></div>
                </div>
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($model->customer->primaryPhoneNumber->number) ? (!empty($model->customer->primaryPhoneNumber->number) ? $model->customer->primaryPhoneNumber->label->name.' : ' : null).''.$model->customer->primaryPhoneNumber->number : null) ?> </div>
                </div>
            </div>
        </div>		
    </div>
</div>