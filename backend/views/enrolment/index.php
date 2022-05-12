<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\Holiday;
use kartik\select2\Select2;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<div class="nav-tabs-custom">
    <?php

    $gridView = $this->render('_enrolment-grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);

    $calendarView = $this->render('_enrolment-calendar',  [
        'searchModel' => $searchModel,
        'locationAvailabilities'   => $locationAvailabilities,
        'scheduleVisibilities'     => $scheduleVisibilities,

    ]);

    ?>

    <?php echo Tabs::widget([
        'items' => [
            [
                'label' => 'Enrolments',
                'content' => $gridView,
                'options' => [
                    'id' => 'grid-view',
                ],
            ],
            [
                'label' =>'Schedule',
                'content' => $calendarView,
                'options' => [
                        'id' => 'calendar-view',
                    ],
            ],
        ],
    ]); ?>
</div>