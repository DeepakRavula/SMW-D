<?php

use kartik\grid\GridView;

?>

<?= $this->render('/print/_header', [
    'userModel' => $model,
    'locationModel' => $model->userLocation->location,
]); ?>

<div class="row">
    <div class="col-xs-12 table-responsive">
        <h1><?= $model->publicIdentity . '\'s Invoiced Lessons'?></h1>
        <?php if ($fromDate === $toDate): ?>
        <h2><?=  (new \DateTime($toDate))->format('F jS, Y'); ?></h2>
        <?php else: ?>
        <h2><?=  (new \DateTime($fromDate))->format('F jS, Y'); ?> to <?=  (new \DateTime($toDate))->format('F jS, Y') ?></h2>
        <?php endif; ?>
        <div class="report-grid">
            <?= $this->render('_cost-time-voucher-content', [
                'model' => $model,
                'searchModel' => $searchModel,
                'timeVoucherDataProvider' => $timeVoucherDataProvider
            ]); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        setTimeout(function(){window.print()},2000);
    });
</script>