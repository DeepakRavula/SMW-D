<div class="invoice-view">
        <div class="col-md-2 invoice-col">
            <?php if (!empty($invoiceModel)): ?>
                <b><?= $invoiceModel->getInvoiceNumber(); ?></b><br>
                <br>
                <b>Date:</b><?= Yii::$app->formatter->asDate($invoiceModel->date); ?> <br>
                <b>Status:</b>  <?= $invoiceModel->getStatus(); ?><br>
                <?php if (!empty($invoiceModel->dueDate)) : ?>
                    <b>Due Date:</b><?= Yii::$app->formatter->asDate($invoiceModel->dueDate); ?>
    <?php endif; ?>
<?php endif; ?>
            <?php if (!empty($courseModel)): ?>
                <b><?= $courseModel->program->name; ?></b><br/>
                <b><?= Yii::$app->formatter->asDate($courseModel->startDate); ?>-<?= Yii::$app->formatter->asDate($courseModel->endDate); ?></b><br>
                <br>
                <b>Duration:</b>
                <?php
                $length = \DateTime::createFromFormat('H:i:s', $courseModel->courseSchedule->duration);
                echo $length->format('H:i');
                ?> <br>
                <b>Time:</b>
                <?php
                $fromTime = \DateTime::createFromFormat('H:i:s', $courseModel->courseSchedule->fromTime);
                echo $fromTime->format('h:i A');
                ?><br>
<?php endif; ?>
        </div>
        <!-- /.col -->
        <!-- /.col -->
    </div>

