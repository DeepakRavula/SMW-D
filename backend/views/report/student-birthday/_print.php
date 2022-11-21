<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<?php echo $this->render('_birthday', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,'model'=>$model]); ?>

<script>
	$(document).ready(function(){
		setTimeout(function(){
            window.print();
}, 1500)
	});
</script>