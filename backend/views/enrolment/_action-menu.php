<?php

use yii\helpers\Html;

?>

<?= Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#',
    ['class' => 'new-enrol-btn']) ?>

<div id="bulk-action-menu" class="m-b-10 pull-right">
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Bulk Action&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a class="multiselect-disable" id="enrolment-teacher-change" href="#">ChangeTeacher</a></li>
        </ul>
    </div>
</div>