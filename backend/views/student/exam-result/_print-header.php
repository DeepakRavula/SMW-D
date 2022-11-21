<section class="invoice">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <span class="logo-lg"><b>Arcadia</b>SMW</span>
                <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-6">
                <div class="invoice-print-address">
                    From
                    <address>
                        <strong>Arcadia Academy of Music ( <?= $studentModel->customer->userLocation->location->name; ?> )</strong><br>
                        <?php if (!empty($studentModel->customer->userLocation->location->address)): ?>
                            <?= $studentModel->customer->userLocation->location->address; ?>
                        <?php endif; ?><br>
                        <?php if (!empty($studentModel->customer->userLocation->location->city_id)): ?>
                            <?= $studentModel->customer->userLocation->location->city->name; ?>
                        <?php endif; ?>
                        <?php if (!empty($studentModel->customer->userLocation->location->province_id)): ?>
                            <?= ', ' . $studentModel->customer->userLocation->location->province->name; ?>
                        <?php endif; ?><br>
                        <?php if (!empty($studentModel->customer->userLocation->location->postal_code)): ?>
                            <?= $studentModel->customer->userLocation->location->postal_code; ?>
                        <?php endif; ?><br/>
                        <?php if (!empty($studentModel->customer->userLocation->location->phone_number)): ?>
                            Phone:<?= $studentModel->customer->userLocation->location->phone_number ?>
                        <?php endif; ?><br/>
                        <?php if (!empty($studentModel->customer->userLocation->location->email)): ?>
                            E-mail:<?= $studentModel->customer->userLocation->location->email ?>
                        <?php endif; ?><br/>
                        www.arcadiamusicacademy.com
                    </address>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="invoice-print-address">

                    To
                    <address>
                        <strong><?php echo isset($studentModel->customer->publicIdentity) ? $studentModel->customer->publicIdentity : null ?></strong><br>
					<?php if (!empty($studentModel->customer->billingAddress)) : ?>
						<?= $studentModel->customer->billingAddress->address; ?><br>
						<?= $studentModel->customer->billingAddress->city->name; ?>
						<?= ', ' . $studentModel->customer->billingAddress->province->name; ?><br>
						<?= $studentModel->customer->billingAddress->postalCode; ?><br/>
					<?php endif; ?>
					<?php if (!empty($studentModel->customer->phoneNumber)) : ?>
						Phone:<?php echo $studentModel->customer->phoneNumber->number; ?>
					<?php endif; ?><br>
					<?php if (!empty($studentModel->customer->email)): ?>
						E-mail:<?php echo $studentModel->customer->email ?>
					<?php endif; ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
    <!-- info row -->
    <!-- /.row -->

    <!-- Table row -->

    <!-- /.row -->
</section>