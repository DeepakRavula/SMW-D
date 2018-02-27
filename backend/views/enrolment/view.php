<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = $model->course->program->name;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = Html::a('<i class="fa fa-trash-o"></i>', [
    'enrolment/delete', 'id' => $model->id
], [
        'id' => 'enrolment-delete-' . $model->id,
        'title' => Yii::t('yii', 'Delete'),
        'class' => 'enrolment-delete btn btn-box-tool',
    ])?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div id="enrolment-enddate-alert" style="display: none;" class="alert-info alert fade in"></div>
<?= $this->render('_view-enrolment', [
    'model' => $model,
]);?>
<div class="row">
    <div class="col-md-6">
        <?=
        $this->render('_student', [
            'model' => $model,
        ]);
        ?>
    </div>
</div>
    <div class="nav-tabs-custom">
<?php

    $lessonContent = $this->render('_lesson', [
        'model' => $model,
        'lessonDataProvider' => $lessonDataProvider,
    ]);

    $noteContent = $this->render('_payment-cycle', [
        'model' => $model,
        'paymentCycleDataProvider' => $paymentCycleDataProvider,
    ]);
    $logContent=$this->render('log/index', [
        'logDataProvider' => $logDataProvider,
    ]);
    $items       = [
    [
        'label' => 'Payment Cycle',
        'content' => $noteContent,
        'options' => [
            'id' => 'payment-cycle',
        ],
    ],
    [
        'label' => 'Lesson',
        'content' => $lessonContent,
        'options' => [
            'id' => 'lesson',
        ],
    ],
    [
        'label' => 'History',
        'content' => $logContent,
        'options' => [
            'id' => 'history',
        ],
    ]
];
if ($model->course->program->isGroup()) {
    array_shift($items);
}
echo Tabs::widget([
        'items' => $items,
    ]);
?>
</div>

<script>
    $(document).on('click', '.enrolment-delete', function () {
        var enrolmentId = '<?= $model->id;?>';
        bootbox.confirm({
            message: "Are you sure you want to delete this enrolment?",
            callback: function(result){
                if(result) {
                    $('.bootbox').modal('hide');
                    $.ajax({
                        url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
                        dataType: "json",
                        data   : $(this).serialize(),
                        success: function (response)
                        {
                            if (response.status) {
                                window.location.href = response.url;
                            } else {
                                $('#enrolment-delete').html('You are not allowed to delete this enrolment.').fadeIn().delay(3000).fadeOut();
                            }
                        }
                    });
                }
            }
        });
        return false;
    });
</script>
