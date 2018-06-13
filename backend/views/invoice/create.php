<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;

$this->title =  'Proforma Invoice';
?>
<?php

echo AdminLteGridView::widget([
    'dataProvider' => $paymentCycleDataProvider,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'summary' => false,
        'emptyText' => false,
    'columns' => [
        [
            'label' => 'Student',
            'value' => function ($data) {
                return $data->enrolment->student->fullName;
            }
        ],
        [
            'label' => 'Program',
            'value' => function ($data) {
                return $data->enrolment->course->program->name;
            }
        ],
        'startDate:date',
        'endDate:date',
            [
            'label' => 'Due Date',
            'value' => function ($data) {
                return !empty($data->proFormaInvoice->dueDate) ?
                    (new \DateTime($data->proFormaInvoice->dueDate))->format('d-m-Y') : '-';
            }
        ],
            [
            'label' => 'Pro-Forma Invoice',
            'value' => function ($data) {
                $invoiceNumber = '-';
                if ($data->hasProformaInvoice()) {
                    $invoiceNumber = $data->proFormaInvoice->getInvoiceNumber();
                }
                return $invoiceNumber;
            }
        ],
            [
            'label' => 'Status',
            'value' => function ($data) {
                $result = 'Owing';
                if (empty($data->proFormaInvoice)) {
                    $result = '-';
                }
                if (!empty($data->proFormaInvoice) && $data->proFormaInvoice->isPaid()) {
                    $result = 'Paid';
                }
                return $result;
            }
        ],
    ],
]);
?>
    

