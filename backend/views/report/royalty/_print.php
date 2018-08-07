<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<?php
   echo $this->render('/print/_header', [
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
    'royaltyFreeAmount' => $royaltyFreeAmount,
    'giftCardPayments' => $giftCardPayments,
]);

?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>