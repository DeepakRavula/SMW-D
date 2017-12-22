<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \Yii::$app->session->get('location_id')]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<div>
    <h3><strong>Royalty Free Items </strong></h3></div>
<?php echo $this->render('_royaltyfree', ['model' => $searchModel, 'royaltyFreeDataProvider' => $royaltyFreeDataProvider,]); ?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>