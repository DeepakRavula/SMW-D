<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\ArrayHelper;
use common\models\Location;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>  

<?php $form = ActiveForm::begin([
    'action' => Url::to(['enrolment/group-confirm', 'GroupCourseForm[studentId]' => $model->studentId, 'GroupCourseForm[courseId]' => $model->courseId]),
    'id' => 'modal-form'
]); ?>

<style>
    .col-xs-3 {
        width: 23%;
    }
</style>
    <?php if (!$model->studentId) : ?>
        <?php $courseId = $model->courseId; 
            $enroledStudents = Student::find()
                ->notDeleted()
                ->joinWith(['enrolments' => function ($query) use ($courseId) {
                    $query->joinWith(['course' => function ($query) use ($courseId) {
                        $query->andWhere(['course.id' => $courseId]);
                    }]);
                }])
                ->orderBy(['first_name' => SORT_ASC])
                ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
        ?>
    <div class="row">
        <div class="col-xs-5 pull-left">
            <label class="dollar-symbol">Student</label>
        </div>
        <div class="col-xs-7">
            <?= $form->field($model, 'studentId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Student::find()
                    ->notDeleted()
                    ->leftJoin(['enrolled_student' => $enroledStudents], 'student.id = enrolled_student.id')
                    ->andWhere(['enrolled_student.id' => null])
                    ->orderBy(['first_name' => SORT_ASC])
                    ->location(Location::findOne(['slug' => \Yii::$app->location])->id)
                    ->all(), 'id', 'fullName'),
                'pluginOptions' => [
                    'placeholder' => 'Select Student'
                ]
            ])->label(false); ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-xs-3 pull-left">
            <label class="dollar-symbol">Discount</label>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-4 btn-group on-off">
            <button class="btn btn-default" data-size="mini" id="off">$</button>
            <button class="btn btn-default" data-size="mini" id="on">%</button>
        </div>
        <div class="col-xs-3">
            <div class="col-xs-1 discount-edit-label">
                <label class="off discount-dollar-symbol on-off-symbol">$</label>
            </div>
            <?= $form->field($model, 'discount')->textInput([
                    'value' => Yii::$app->formatter->asDecimal($model->discount, 2),
                    'class' => 'text-right form-control'])->label(false); ?>
        </div>
        <label class="on percent dollar-symbol on-off-symbol">%</label>
        <?= $form->field($model, 'discountType')->hiddenInput()->label(false); ?>
    </div>

<?php ActiveForm::end(); ?>


<script>
    $(document).ready(function () {
        var studentId = '<?= $model->studentId ?>';
        $('#popup-modal').modal('show');
        $('.modal-save').show();
        $('#modal-back').hide();
        $('.modal-save').text('Confirm');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Discount Detail</h4>');
        if (!studentId) {
            $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Enrolment Detail</h4>');
        }
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        
    });

    $(document).off('click', '#on').on('click', '#on', function () {
        $('#on').addClass('btn-info');
        $('#off').removeClass('btn-info');
        $('.on').show();
        $('.off').hide();
        $('#groupcourseform-discounttype').val(0);
        return false;
    });

    $(document).off('click', '#off').on('click', '#off', function () {
        $('#off').addClass('btn-info');
        $('#on').removeClass('btn-info');
        $('.on').hide();
        $('.off').show();
        $('#groupcourseform-discounttype').val(1);
        return false;
    });

    $(document).ready(function() {
        var button = '<?= $model->discountType;?>';
        if (button == '0') {
            $('#on').addClass('btn-info');
            $('#off').removeClass('btn-info');
            $('.on').show();
            $('.off').hide();
        } else {
            $('#off').addClass('btn-info');
            $('#on').removeClass('btn-info');
            $('.on').hide();
            $('.off').show();
        }
    });
</script>