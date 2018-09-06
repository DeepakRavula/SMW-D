<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
?>

<?php $boxTools = '';?>
<?php if ($model->course->program->isPrivate()) : ?>
	<?php $boxTools = $this->render('_details-box-tools');?>
<?php endif;?>
<?php 
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
        'boxTools' => $boxTools,
    'withBorder' => true,
])
?>
<style>
@media (min-width: 768px) {
  .enrolment-view dt {
    float: left;
    width: 250px;
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .enrolment-view dd {
    margin-left: 270px;
  }
}
</style>
<dl class="enrolment-view">
	<dt>Program</dt>
	<dd>
	<?= $model->course->program->name; ?>
	</dd>
  <?php 
  $currentCourseSchedule = $model->course->currentCourseSchedule; ?>
    <dt>Teacher</dt>
    <dd> <?= $currentCourseSchedule->teacher->publicIdentity ?></dd>
        <?php foreach ($model->courseProgramRates as $courseProgramRate) : ?>
	<dt>Rate From <?= Yii::$app->formatter->asDate($courseProgramRate->startDate) . ' To ' . Yii::$app->formatter->asDate($courseProgramRate->endDate) ?></dt>
	<dd><?= $courseProgramRate->programRate; ?></dd>
        <?php endforeach; ?>
        <dt>Auto Renewal</dt>
	<dd><?= $model->isAutoRenew ? "Enabled" : 'Disabled' ; ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
  <?php if ($model->course->program->isPrivate()) : ?>
  <dt>Student</dt>
    <dd>
        <a href= "<?= Url::to(['student/view', 'id' => $model->student->id]) ?>">
            <?= $model->student->fullName ?? null; ?>
        </a></dd>
    <dt>Customer</dt>
    <dd>
        <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER,
                'id' => $model->student->customer->id]) ?>">
            <?= $model->student->customer->publicIdentity ?? null; ?>
        </a>
    </dd>
    <?php endif; ?> 
</dl>
<?php LteBox::end()?>