<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Deck */

?>
<div class="deck-view">
    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'firstName',
            'lastName',
            'email',
            'address',
            'city',
            'province',
            'postalCode',
            'country',
            'homeTel',
            'otherTel',
            'birthDate:date',
            'billingFirstName',
            'billingLastName',
            'billingEmail',
            'billingAddress',
            'billingCity',
            'billingProvince',
            'billingPostalCode',
            'billingCountry',
            'billingHomeTel',
            'billingOtherTel',
            'billingWorkTel',
            'billingWorkTelExt',
            'openingBalance',
            'notes:raw'
        ],
    ])
    ?>

</div>
