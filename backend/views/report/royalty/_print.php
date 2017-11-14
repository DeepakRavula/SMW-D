<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

use common\models\Location;

?>
<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<?php
   echo $this->render('/print/_print-header', [
       'locationModel'=>$model,
]);
   ?>
<div>
    <h3><strong>Royalty Items Report </strong></h3></div>
<?php
echo $this->render('_royalty', [
    'searchModel' => $searchModel,
    'invoiceTaxTotal' => $invoiceTaxTotal,
    'payments' => $payments,
    'royaltyPayment' => $royaltyPayment,
]);

?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>