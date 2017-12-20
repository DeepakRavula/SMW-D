<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="lesson-index">
<div class="row-fluid">
    <div class="col-md-12 p-t-10 p-l-0">
        <p class="users-name pull-left"><?php echo!empty($customer->userProfile->firstname) ? $customer->userProfile->firstname : null ?>
            <?php echo!empty($customer->userProfile->lastname) ? $customer->userProfile->lastname : null ?> 
             <em>
                <small><?php echo !empty($customer->email) ? $customer->email : null ?></small>
            </em> 
        </p>
    </div>
    <div class="row-fluid">
		<div id="w3" class="list-view">
            <div data-key="351">
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($customer->billingAddress->address) ? $customer->billingAddress->address : null) ?> </div>
                    <div><?= Html::encode(!empty($customer->billingAddress->city->name) ? $customer->billingAddress->city->name : null) ?> <?= Html::encode(!empty($customer->billingAddress->province->name) ? $customer->billingAddress->province->name : null) ?></div>
                    <div><?= Html::encode(!empty($customer->billingAddress->country->name) ? $customer->billingAddress->country->name : null) ?> <?= Html::encode(!empty($customer->billingAddress->postal_code) ? $customer->billingAddress->postal_code : null) ?></div>
                </div>
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($customer->primaryPhoneNumber->number) ? (!empty($customer->primaryPhoneNumber->number) ? $customer->primaryPhoneNumber->label->name.' : ' : null).''.$customer->primaryPhoneNumber->number : null) ?> </div>
                </div>
            </div>
        </div>		
    </div>
</div>
<?php if (!empty($dataProvider)): ?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                            // you may configure additional properties here
            ],
            [
                'label' => 'Lesson Id',
                'value' => function ($data) {
                    return !empty($data->id) ? $data->id : null;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date).' @ '.Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return !empty($data->enrolment->student->customer->publicIdentity) ? $data->enrolment->student->customer->publicIdentity : null;
                },
            ],
            [
                'label' => 'Student Name',
                'value' => function ($data) {
                    return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
                },
            ],
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return $data->course->program->name;
                },
        ],
        ],
    ]); ?>
 <?php yii\widgets\Pjax::end(); ?>
	<?php echo $form->field($model, 'notes')->label('Printed Notes')->textarea() ?>
<?php endif; ?>
</div>