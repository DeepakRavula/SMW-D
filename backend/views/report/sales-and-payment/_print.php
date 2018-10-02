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
    <h3><strong>Sales and Payment Report </strong></h3></div>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>