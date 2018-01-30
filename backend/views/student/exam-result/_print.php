<?php

use yii\grid\GridView;

?>
<div class="invoice-view">
			<?= $this->render("/print/_header.php", [
    'studentModel' => $studentModel,
        'locationModel'=>$studentModel->customer->userLocation->location,
        'userModel' => $studentModel->customer,
]); ?>
</div>
<div class="clearfix"></div><hr>
<div>
    <h3 class="col-md-12"><b>Evaluations for <?= $studentModel->fullName;?></b></h3>
<?php
echo GridView::widget([
    'dataProvider' => $examResultDataProvider,
    'options' => ['class' => 'col-md-12 p-0'],
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
            [
            'label' => 'Exam Date',
            'value' => function ($data) {
                return !empty($data->date) ? (new \DateTime($data->date))->format('M. d, Y') : null;
            }
        ],
            [
            'label' => 'Mark',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return !empty($data->mark) ? $data->mark : null;
            }
        ],
            [
            'label' => 'Level',
            'value' => function ($data) {
                return !empty($data->level) ? $data->level : null;
            }
        ],
            [
            'label' => 'Program',
            'value' => function ($data) {
                return !empty($data->programId) ? $data->program->name : null;
            }
        ],
            [
            'label' => 'Type',
            'value' => function ($data) {
                return !empty($data->type) ? $data->type : 'None';
            }
        ],
            [
            'label' => 'Teacher',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($data) {
                return !empty($data->teacherId) ? $data->teacher->publicIdentity : null;
            }
        ],
    ],
]);
?>
</div>
<script>
	$(document).ready(function () {
		window.print();
	});
</script>