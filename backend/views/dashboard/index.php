<?php
/* @var $this yii\web\View */
?>
<div class="col-md-12">
    <h3>Dashboard</h3>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Total</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= ! empty($invoiceTotal) ? $invoiceTotal : 0 ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Tax Total</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= ! empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Enrolments</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $enrolments ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Payments</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= ! empty($payments) ? $payments : 0 ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Active Students</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $students ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
    </div>
</div>