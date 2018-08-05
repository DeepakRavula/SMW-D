<?php

use kartik\grid\GridView;

?>

<?= $this->render('/print/_header', [
    'userModel' => $model,
    'locationModel' => $model->userLocation->location,
]); ?>

<div class="row">
    <div class="col-xs-12 table-responsive">
        <h2 class="col-md-12"><b><?= $model->publicIdentity . '\'s Time Voucher for ' . (new\DateTime($fromDate))->format('F jS, Y') . ' to ' . (new\DateTime($toDate))->format('F jS, Y');?></b></h2>
        <div class="report-grid">
            <?= $this->render('_cost-time-voucher-content', [
                'model' => $model,
                'searchModel' => $searchModel,
                'timeVoucherDataProvider' => $timeVoucherDataProvider
            ]); ?>
        </div>
    </div>
</div>

<div class="boxed col-md-12 pull-right">
    <div class="sign">
        Teacher Signature <span></span>
    </div>
    <div class="sign">
        Authorizing Signature <span></span>
    </div>
    <div class="sign">
        Date <span></span>
    </div>
</div>


<script>
    $(document).ready(function () {
        window.print();
    });
</script>