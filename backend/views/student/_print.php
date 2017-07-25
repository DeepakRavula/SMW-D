<?php

use common\models\Location;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<?php $location = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<h3><strong>Student's List for <?= $location->name;?> Location</strong></h3></div>
<?php echo $this->render('_index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>