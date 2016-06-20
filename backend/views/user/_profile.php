<?php

use yii\helpers\Html;
?>
<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?php echo!empty($model->userProfile->firstname) ? $model->userProfile->firstname : null ?>
            <?php echo!empty($model->userProfile->lastname) ? $model->userProfile->lastname : null ?> 
            <!-- <em>
                <small><?php //echo!empty($model->email) ? $model->email : null ?></small>
            </em> -->
        </p>
        <div class="m-l-20 pull-left m-t-5">
            <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Update details'), ['update', 'id' => $model->id,'section' => 'profile'], ['class' => 'm-r-20']) ?>
            <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => '',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
<!--     <div class="col-md-2">
        <i class="fa fa-phone-square"></i> <?php //echo!empty($model->phoneNumber->number) ? $model->phoneNumber->number : null ?>
    </div>
    <div class="col-md-2 relative">
        <i class="fa fa-map-marker"></i> <?php //echo!empty($address->address) ? $address->address : null ?>
    </div> -->
    
    <div class="clearfix"></div>
</div>

<?php if (!empty($role) && $role->name === User::ROLE_TEACHER): ?>
    <div class="col-md-12">
        <div class="col-md-2">
            <h4>Qualifications</h4>
        </div>
        <div class="col-md-10">
            <h4> <?= $program ?></h4>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <h4 class="pull-left m-r-20">Teachers Availability</h4>
        <a href="#" class="availability text-add-new"><i class="fa fa-plus-circle"></i> Add availability</a>
        <div class="clearfix"></div>
    </div>
    <div class="teacher-availability-create row-fluid">

        <?php
        echo $this->render('//teacher-availability/_form', [
            'model' => $teacherAvailabilityModel,
        ])
        ?>

    </div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider1,
        'options' => ['class' => 'col-md-5'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Day',
                'value' => function($data) {
                    if (!empty($data->day)) {
                        $dayList = TeacherAvailability::getWeekdaysList();
                        $day = $dayList[$data->day];
                        return !empty($day) ? $day : null;
                    }
                    return null;
                },
            ],
            [
                'label' => 'From Time',
                'value' => function($data) {
                    if (!empty($data->from_time)) {
                        $fromTime = date("g:i a", strtotime($data->from_time));
                        return !empty($fromTime) ? $fromTime : null;
                    }
                    return null;
                },
            ],
            [
                'label' => 'To Time',
                'value' => function($data) {
                    if (!empty($data->to_time)) {
                        $toTime = date("g:i a", strtotime($data->to_time));
                        return !empty($toTime) ? $toTime : null;
                    }
                    return null;
                },
            ],
            ['class' => 'yii\grid\ActionColumn', 'controller' => 'teacher-availability', 'template' => '{delete}'],
        ],
    ]);
    ?>
    <div class="clearfix"></div>
<?php endif; ?>
