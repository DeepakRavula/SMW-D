<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Location;
use common\models\User;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$this->title = 'Exploded Lessons';
?>

<?php
	$columns = [
        [
            'label' => 'Lesson ID',
            'value' => function ($data) {
                return $data->id;
            }
        ],
        [
            'label' => 'Date',
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:10%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:10%'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->date);
            }
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->hasInvoice() ? 'Invoiced' : ($data->isCompleted() ? 'Completed' : 'Scheduled');
            }
        ],
        [
            'label' => "Original Lesson's Payment Frequecy Discount",
            'value' => function ($data) {
                $discount = null;
                if ($data->rootLesson->enrolmentPaymentFrequencyDiscount) {
                    $discount = $data->rootLesson->enrolmentPaymentFrequencyDiscount->value;
                } else if ($data->rootLesson->proFormaLineItemDeleted) {
                    $discount = $data->rootLesson->proFormaLineItemDeleted->enrolmentPaymentFrequencyDiscount ? 
                        $data->rootLesson->proFormaLineItemDeleted->enrolmentPaymentFrequencyDiscount->value : null;
                }
                return $discount;
            }
        ],
        [
            'label' => "Exploded Lesson's Payment Frequecy Discount",
            'value' => function ($data) {
                return $data->enrolmentPaymentFrequencyDiscount ? $data->enrolmentPaymentFrequencyDiscount->value : null;
            }
        ],
        [
            'label' => "Exploded Lesson's Invoice Line Item Payment Frequecy Discount",
            'value' => function ($data) {
                return $data->hasInvoice() ? ($data->invoiceLineItem->enrolmentPaymentFrequencyDiscount ? 
                    $data->invoiceLineItem->enrolmentPaymentFrequencyDiscount->value : null) : null;
            }
        ],
        [
            'label' => "Original Lesson's Multiple Enrolment Discount",
            'value' => function ($data) {
                $discount = null;
                if ($data->rootLesson->multiEnrolmentDiscount) {
                    $discount = $data->rootLesson->multiEnrolmentDiscount->value;
                } else if ($data->rootLesson->proFormaLineItemDeleted) {
                    $discount = $data->rootLesson->proFormaLineItemDeleted->multiEnrolmentDiscount ? 
                        $data->rootLesson->proFormaLineItemDeleted->multiEnrolmentDiscount->value : null;
                }
                return $discount;
            }
        ],
        [
            'label' => "Exploded Lesson's Multiple Enrolment Discount",
            'value' => function ($data) {
                return $data->multiEnrolmentDiscount ? $data->multiEnrolmentDiscount->value : null;
            }
        ],
        [
            'label' => "Exploded Lesson's Invoice Line Item Multiple Enrolment Discount",
            'value' => function ($data) {
                return $data->hasInvoice() ? ($data->invoiceLineItem->multiEnrolmentDiscount ? 
                    $data->invoiceLineItem->multiEnrolmentDiscount->value : null) : null;
            }
        ],
        [
            'label' => "Original Lesson's customer Discount",
            'value' => function ($data) {
                $discount = null;
                if ($data->rootLesson->customerDiscount) {
                    $discount = $data->rootLesson->customerDiscount->value;
                } else if ($data->rootLesson->proFormaLineItemDeleted) {
                    $discount = $data->rootLesson->proFormaLineItemDeleted->customerDiscount ? 
                        $data->rootLesson->proFormaLineItemDeleted->customerDiscount->value : null;
                }
                return $discount;
            }
        ],
        [
            'label' => "Exploded Lesson's Customer Discount",
            'value' => function ($data) {
                return $data->customerDiscount ? $data->customerDiscount->value : null;
            }
        ],
        [
            'label' => "Exploded Lesson's Invoice Line Item Customer Discount",
            'value' => function ($data) {
                return $data->hasInvoice() ? ($data->invoiceLineItem->customerDiscount ? 
                    $data->invoiceLineItem->customerDiscount->value : null) : null;
            }
        ],
        [
            'label' => "Original Lesson's Line Iitem Discount",
            'value' => function ($data) {
                $discount = null;
                if ($data->rootLesson->lineItemDiscount) {
                    $discount = $data->rootLesson->lineItemDiscount->value;
                } else if ($data->rootLesson->proFormaLineItemDeleted) {
                    $discount = $data->rootLesson->proFormaLineItemDeleted->lineItemDiscount ? 
                        $data->rootLesson->proFormaLineItemDeleted->lineItemDiscount->value : null;
                }
                return $discount;
            }
        ],
        [
            'label' => "Exploded Lesson's Line Iitem Discount",
            'value' => function ($data) {
                return $data->lineItemDiscount ? $data->lineItemDiscount->value : null;
            }
        ],
        [
            'label' => "Exploded Lesson's Invoice Line Item Line Iitem Discount",
            'value' => function ($data) {
                return $data->hasInvoice() ? ($data->invoiceLineItem->lineItemDiscount ? 
                    $data->invoiceLineItem->lineItemDiscount->value : null) : null;
            }
        ]
    ];
?>

<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => false,
    'columns' => $columns
]); ?>
