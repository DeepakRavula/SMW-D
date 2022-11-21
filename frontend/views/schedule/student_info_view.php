<?php

use yii\helpers\Url;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Other Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Schedule Date</dt>
	<dd><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></dd>
    <dt>Time</dt>
    <dd><?= Yii::$app->formatter->asTime($model->date); ?></dd>
    <dt>Duration</dt>
    <dd><?= (new \DateTime($model->duration))->format('H:i'); ?></dd>
    <dt>Student</dt>
    <dd>
    <?= $model->enrolment->student->fullName; ?>
    </dd>
    <dt>Note</dt>
    <dd>
    <?= $model->enrolment->student->note; ?>
    </dd>
</dl>
<?php LteBox::end() ?>