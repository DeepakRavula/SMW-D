<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use yii\grid\GridView;

?>



<div class="private-lesson-index">
<div class="pull-right m-r-10">
    	<a href="#"  title="Add" id="new-lesson" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
    </div>
    <?php $columns = [
             [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Lesson Status',
                'value' => function ($data) {
                    return $data->getStatus();
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) use($model) {
                    $status = null;
                    if ($data->isPrivate()) {
                        if (!empty($data->invoice)) {
                            return $data->invoice->getStatus();
                        } else {
                            $status = 'Not Invoiced';
                        }
                    } else {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $data->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        $invoice = $enrolment->getInvoice($data->id);
                        if ($invoice) {
                            return $invoice->getStatus();
                        } else {
                            $status = 'Not Invoiced';
                        }
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date).' @ '.Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Prepaid?',
                'value' => function ($data) use($model) {
                    $pfi = null;
                    if ($data->isPrivate()) {
                        if ($data->proFormaInvoice) {
                            $pfi = $data->proFormaInvoice;
                        }
                    } else {
                        $enrolment = Enrolment::find()->notDeleted()->isConfirmed()
                            ->andWhere(['courseId' => $data->courseId])
                            ->andWhere(['studentId' => $model->id])->one();
                        $hasItem = $data->hasGroupProFormaLineItem($enrolment);
                        if ($hasItem) {
                            $pfi = $data->getGroupProFormaLineItem($enrolment)->invoice;
                        }
                    }
                    if ($pfi) {
                        $status = $pfi->hasCreditUsed() ? 'Yes' : 'No';
                    } else {
                        $status = 'No';
                    }
                    return $status;
                },
            ],
            [
                'label' => 'Present?',
                'value' => function ($data) {
                    return $data->getPresent();
                },
            ],
        ];

    ?>
    <div class="grid-row-open">
        <?php yii\widgets\Pjax::begin(['id' => 'lesson-index', 'timeout' => 6000,]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'options' => ['id' => 'student-lesson-grid'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>

<script>
    $(document).on('depdrop.afterChange', '#lesson-teacher', function() {
        var programs = <?php echo Json::encode($allEnrolments); ?>;
        var selectedProgram = $('#lesson-program').val();
        $.each(programs, function( index, value ) {
            if (value.programId == selectedProgram) {
                $('#lesson-teacher').val(value.teacherId).trigger('change.select2');
            }
        });
        return false;
    });

    $(document).on('click', '#new-lesson', function () {
        $.ajax({
            url    : '<?= Url::to(['extra-lesson/create-private', 'studentId' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Add Lesson</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                }
            }
        });
        return false;
    });
</script>

