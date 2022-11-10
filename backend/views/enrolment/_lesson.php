<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
?>

<div class="row-fluid p-10">
    <div class="pull-left">
	<p>
    <h4><strong class="m-r-10"><?= 'Schedule of Lessons' ?></strong> 
        <?= Html::a('<i class="fa fa-print"></i> ', ['print/course', 'id' => $model->course->id], ['class' => 'm-r-10', 'target' => '_blank']) ?>  
        <?= Html::a('<i class="fa fa-envelope-o"></i> ', '#', [
        'id' => 'schedule-mail-button',
        'class' => '']) ?> </h4></p>
    </div>
    <div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
                'dataProvider' => $lessonDataProvider,
                'summary' => false,
                'emptyText' => false,
                'tableOptions' => ['class' => 'table table-bordered'],
                'showHeader' => false,
                'columns' => [
	[
	    'value' => function ($data) {
		    $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
		    $lessonDuration = (new \DateTime($data->duration))->format('H:i');
		    $duration = substr($lessonDuration,3);
		    $date = $lessonDate->format('l, F jS, Y @ g:i a');
		    return $date .", " . $duration."mins";
	    },
	],
    ],
]); ?>
    <?php Pjax::end(); ?> 
</div>

<script>
    $(document).on('click', '#schedule-mail-button', function () {
        $.ajax({
            url    : '<?= Url::to(['email/enrolment', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Email Preview</h4>');
                    $('.modal-save').text('Send');
                }
            }
        });
        return false;
    });
</script>