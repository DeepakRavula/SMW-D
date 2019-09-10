<div class="invoice-view">
    <div class="row">
        <div class="col-xs-12 page-header">
            <div class="col-xs-8">
            <h2 class="">
                <span class="logo-lg"><img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" /></span>
            </h2>
            </div>
                    <div class="col-xs-4 invoice-col">
                        <div class="pull-right">
            <?php if (!empty($invoiceModel)): ?>
                            <div class="right-align">
                            <div class="f-s-30">      <b>#<?= $invoiceModel->getInvoiceNumber(); ?></b></div>
                            <div> Status   :      <?= $invoiceModel->getStatus(); ?></div>
                            <div> Date     :      <?= Yii::$app->formatter->asDate($invoiceModel->date); ?></div>
                     <?php if (!empty($invoiceModel->dueDate)) : ?>
                            <div>Due Date :    <?= Yii::$app->formatter->asDate($invoiceModel->dueDate); ?></div>
                            
            <?php endif; ?>
                            </div>
                            </div>
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
        </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="">
                From
                <address>
                    <b>Arcadia Academy of Music ( <?= $locationModel->name; ?> )</b><br>
                    <?php if (!empty($locationModel->address)): ?>
                        <?= $locationModel->address; ?>
                    <?php endif; ?>
                    <br/>
                    <?php if (!empty($locationModel->city_id)): ?>
                        <?= $locationModel->city->name; ?>,
                    <?php endif; ?>        
                    <?php if (!empty($locationModel->province_id)): ?>
                        <?= $locationModel->province->name; ?>
                    <?php endif; ?>
                    <br/>
                    <?php if (!empty($locationModel->postal_code)): ?>
                        <?= $locationModel->postal_code; ?>
                    <?php endif; ?>
                    <br/>
                    <?php if (!empty($locationModel->phone_number)): ?>
                        <?= $locationModel->phone_number ?>
                    <?php endif; ?>
                    <br/>
                    <?php if (!empty($locationModel->email)): ?>
                        <?= $locationModel->email ?>
                    <?php endif; ?>
                    <br/>
                    www.arcadiamusicacademy.com
                </address>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
            <div class="">

                <?php if (!empty($userModel)) : ?>
                    To
                    <address>
                        <strong><?php echo isset($userModel->publicIdentity) ? $userModel->publicIdentity : null ?></strong><br>
                        <?php
                        $addresses = $userModel->addresses;
                        if (!empty($userModel->primaryAddress)) {
                            $primaryAddress = $userModel->primaryAddress;
                        }

                        $phoneNumber = $userModel->phoneNumber;
                        ?>
                        <?php if (!empty($primaryAddress->address)) : ?>
                            <?= $primaryAddress->address;
                            echo '<br/>'; ?>
                        <?php endif; ?>
                        <?php if (!empty($primaryAddress->city->name)) : ?>
                            <?= $primaryAddress->city->name; ?>,
                        <?php endif; ?>  
                        <?php if (!empty($primaryAddress->province->name)) : ?>
                            <?= $primaryAddress->province->name;
                            echo '<br/>'; ?>
                        <?php endif; ?>  
                        <?php if (!empty($primaryAddress->postal_code)) : ?>
                            <?= $primaryAddress->postal_code;
                            echo '<br/>'; ?>
                        <?php endif; ?>
                        <?php if (!empty($phoneNumber)) : ?>
                            <?php echo $phoneNumber->number;
                            echo '<br/>'; ?>
                        <?php endif; ?>
                        <?php if (!empty($userModel->email)): ?>
        <?php echo $userModel->email;
        echo '<br/>'; ?>
    <?php endif; ?>
<?php endif; ?>
                </address>
            </div>
        </div>


        <!-- /.col -->
        <!-- /.col -->
    </div>
</div>

