<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;
use common\models\InvoiceLineItem;
use kartik\switchinput\SwitchInput;
use common\models\ItemType;
use yii\widgets\ActiveForm;

?>
<?php if ($searchModel->toggleAdditionalColumns) {
    $columns = [
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->itemType->itemCode;
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:50px;'],
            'attribute' => 'isRoyalty',
            'label' => 'R',
            'value' => function ($model) {
                return $model->isRoyalty ? 'Yes' : 'No';
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
        ],
        [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'attribute' => 'discount',
            'value' => function ($model) {
                if ((int) $model->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                    return Yii::$app->formatter->format($model->discount, ['currency']);
                } else {
                    return $model->discount . '%';
                }
            },
        ],
        [
            'attribute' => 'tax_status',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;'],
        ],
        [
            'label' => 'Cost',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function($data) {
                $cost = 0;
                $itemTypes = [ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON, ItemType::TYPE_GROUP_LESSON];
                if(in_array($data->item_type_id,$itemTypes)) {
                    if((int)$data->item_type_id === ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON) {
                        $cost = !empty($data->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate) ? $data->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate : null;
                    } else {
                        $cost = !empty($data->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate) ? $data->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate : null;
                    }
                }
                return $cost;
            }
        ],
        [
            'label' => 'Price',
			'value' => function($data) {
				return Yii::$app->formatter->asCurrency($data->amount - $data->getDiscountValue());	
			},
        ],
    ];
} else {
    $columns = [
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->itemType->itemCode;
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
        ],
        [
            'label' => 'Price',
			'value' => function($data) {
				return Yii::$app->formatter->asCurrency($data->amount - $data->discount);	
			},
        ],
    ];
}?>

<?= \kartik\grid\GridView::widget([
	'id' => 'line-item-grid',
    'dataProvider' => $invoiceLineItemsDataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'line-item-listing',
        ],
    ],
    'columns' => $columns,
    'responsive' => false,
]); ?>
