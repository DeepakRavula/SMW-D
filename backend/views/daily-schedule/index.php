<?php

$this->title = 'Daily Schedule';
?>

<div class="payments-index p-10">
    <?php echo $this->render('_publiclist', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
	
</div>
