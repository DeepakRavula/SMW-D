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
            'label' => 'ID',
            'value' => function ($data) {
                return $data->id;
            }
        ],
        [
            'label' => 'Root ID',
            'value' => function ($data) {
                return $data->rootLesson->id;
            }
        ],
        [
            'label' => 'PFD (RI)',
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
            'label' => 'PFD (LL)',
            'value' => function ($data) {
                return $data->enrolmentPaymentFrequencyDiscount ? $data->enrolmentPaymentFrequencyDiscount->value : null;
            }
        ],
        [
            'label' => 'MED (RI)',
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
            'label' => 'MED (LL)',
            'value' => function ($data) {
                return $data->multiEnrolmentDiscount ? $data->multiEnrolmentDiscount->value : null;
            }
        ],
        [
            'label' => 'CD (RI)',
            'value' => function ($data) {
                $discount = null;
                if ($data->rootLesson->customerDiscount) {
                    $discount = $data->rootLesson->multicustomerDiscountEnrolmentDiscount->value;
                } else if ($data->rootLesson->proFormaLineItemDeleted) {
                    $discount = $data->rootLesson->proFormaLineItemDeleted->customerDiscount ? 
                        $data->rootLesson->proFormaLineItemDeleted->customerDiscount->value : null;
                }
                return $discount;
            }
        ],
        [
            'label' => 'CD (LL)',
            'value' => function ($data) {
                return $data->customerDiscount ? $data->customerDiscount->value : null;
            }
        ],
        [
            'label' => 'LID (RI)',
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
            'label' => 'LID (LL)',
            'value' => function ($data) {
                return $data->lineItemDiscount ? $data->lineItemDiscount->value : null;
            }
        ]
    ];
?>

<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => false,
    'summary' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => false,
    'columns' => $columns
]); ?>
