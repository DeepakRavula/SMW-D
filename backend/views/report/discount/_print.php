<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Location;

?>
<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-header">
            <span class="logo-lg"><b>Arcadia</b>SMW</span>
            <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6 invoice-col">
        <div class="invoice-print-address">
            <address>
                <strong> Arcadia Music Academy ( <?= $model->name; ?> )</strong><br/>
                <?php if (!empty($model->address)): ?>
                    <?= $model->address ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->city_id)): ?>
                    <?= $model->city->name ?>
                <?php endif; ?>
                <?php if (!empty($model->province_id)): ?>
                    <?= ', ' . $model->province->name ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->postal_code)): ?>
                    <?= $model->postal_code ?><br/>
                <?php endif; ?>    
                <?php if (!empty($model->phone_number)): ?>
                    <?= $model->phone_number ?>
                <?php endif; ?>
                <br/>
                www.arcadiamusicacademy.com
            </address>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div>
<h3><strong>Discount Report </strong></h3></div>
<div><h3><?= $searchModel->fromDate->format('F jS, Y') . ' to ' . $searchModel->toDate->format('F jS, Y');?></h3></div>
<?php echo $this->render('_discount', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
    $(document).ready(function(){
        window.print();
    });
</script>