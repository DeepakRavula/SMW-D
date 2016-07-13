<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = 'Edit Student';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<style>
	.form-well-smw{
		margin-bottom: 0;
		background: #fff;
	}
</style>
<div class="student-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'customer' => $model->customer,
    ]) ?>

</div>