<?php

use yii\helpers\Url;
use common\models\Lesson;
use common\models\Invoice;
use yii\grid\GridView;
use yii\bootstrap\Modal;

?>
<div class="">
    <div id="new-lesson" class="col-md-12">
    	<h4 class="pull-left m-r-20">Lessons</h4>
    	<a href="#" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
    	<div class="clearfix"></div>
    </div>
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Lesson</h4>',
		'id'=>'new-lesson-modal',
	]);
	 echo $this->render('_form-lesson', [
			'model' => new Lesson(),
			'studentModel' => $model,
	]);
	Modal::end();
	?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
    	'id' => 'student-lesson-listing',
    	'timeout' => 6000,
    ]) ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Lesson Status',
                'value' => function ($data) {
                    $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                    $currentDate = new \DateTime();

                    if ($lessonDate <= $currentDate) {
                        $status = 'Completed';
                    } else {
                        $status = 'Scheduled';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        return $data->invoice->getStatus();
                    } else {
                        $status = 'Not Invoiced';
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
                'value' => function ($data) {
                    if (!empty($data->proFormaInvoice) && ($data->proFormaInvoice->isPaid() || $data->proFormaInvoice->hasCredit())) {
                        return 'Yes';
                    }

                    return 'No';
                },
            ],
			[
                'label' => 'Present?',
                'value' => function ($data) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$currentDate = new \DateTime();
					if($lessonDate > $currentDate) {
						$result = '-';
					} else if($data->isMissed()) {
						$result = 'No';
					} else if($lessonDate < $currentDate) {
						$result = 'Yes';
					}
                    return $result;
                },
            ],
        ],
    ]);
    ?>
    </div>
    <?php \yii\widgets\Pjax::end(); ?>    
</div>
