<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach($roles as $name => $description){
	$role = $name;
}
?>
<div class="location-view">
    <?php if($role === User::ROLE_ADMINISTRATOR):?>
    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php endif;?>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'address',
            'city.name',
            'province.name',
            'postal_code',
            'country.name',
        ],
    ]) ?>

</div>
