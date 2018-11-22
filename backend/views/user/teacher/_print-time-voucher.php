<?php

use kartik\grid\GridView;

?>

<?= $this->render('/print/_header', [
    'userModel' => $model,
    'locationModel' => $model->userLocation->location,
]); ?>

<div class="row">
    <div class="col-xs-12 table-responsive">
        <h3><?= $model->publicIdentity . '\'s Invoiced Lessons'?></h3>
        <?php if ($fromDate === $toDate): ?>
        <h4><?=  (new \DateTime($toDate))->format('F jS, Y'); ?></h4>
        <?php else: ?>
        <h4><?=  (new \DateTime($fromDate))->format('F jS, Y'); ?> to <?=  (new \DateTime($toDate))->format('F jS, Y') ?></h4>
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
        setTimeout(function(){window.print()},3000);
    });
</script>