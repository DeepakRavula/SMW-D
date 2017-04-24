<?php

use common\models\Program;

?>
<div class="user-details-wrapper">
  <div class="row">
    <?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>
    <div class="col-md-12">
      <p class="users-name"><?= $courseModel->enrolment->student->fullName; ?></p>
    </div>
    <?php endif; ?>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program Name">
      <i class="fa fa-music detail-icon"></i> <?=    $courseModel->program->name; ?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
      <i class="fa fa-graduation-cap"></i> <?= $courseModel->teacher->publicIdentity; ?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Commencement Date">
      <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->startDate)?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Renewal Date">
      <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->endDate)?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
      <i class="fa fa-clock-o"></i> <?php
      $fromTime = \DateTime::createFromFormat('H:i:s', $courseModel->fromTime);
      echo $fromTime->format('h:i A'); ?>
    </div>
  </div>
</div>
<div class="clearfix"></div>
