<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */

$this->title = 'Edit Blog';
?>
<div class="blog-update p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
