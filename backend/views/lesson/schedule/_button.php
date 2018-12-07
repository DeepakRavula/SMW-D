<?php
use yii\helpers\Url;

?>
    <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
        <?php if (!$model->hasInvoice()) : ?>
            <?php if (!$model->isExtendedLesson()) : ?>
                <li><a class="edit-lesson-schedule" href="#">Edit</a></li>
                <?php if (!$model->isUnscheduled()) : ?>
                    <li><a id="lesson-unschedule" href="#">Unschedule Lesson</a></li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($model->isPrivate()) : ?>
            <?php if (!$model->hasInvoice() && $model->isScheduledOrRescheduled()) : ?>
                <li>
                    <a href= "<?= Url::to(['lesson/invoice', 'id' => $model->id]); ?>">
                        Generate Invoice
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($model->hasLessonCredit($model->enrolment->id) && $model->hasInvoice()) : ?>
                <li>
                    <a id="credit-transfer" href="#">Transfer Credits to Invoice</a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    