<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>

<?php
$boxTools = $this->render('_details-box-tools');
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
	<dt>Teacher</dt>
	<dd><?= $model->course->teacher->publicIdentity; ?></dd>
        <?php foreach ($model->enrolmentProgramRates as $enrolmentProgramRate) : ?>
	<dt>Rate From <?= $enrolmentProgramRate->startDate . ' To ' . $enrolmentProgramRate->endDate ?></dt>
	<dd><?= $enrolmentProgramRate->programRate; ?></dd>
        <?php endforeach; ?>
        <dt>Auto Renewal</dt>
	<dd><?= $model->isAutoRenew ? "Enabled" : 'Disabled' ; ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
</dl>
<?php LteBox::end()?>