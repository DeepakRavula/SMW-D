<?php

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="row">
    <div class="col-md-6">
        <?= Html::dropDownList('teacher', null, ArrayHelper::map($teachers, 
                'id', 'userProfile.fullName'), [ 'prompt' => 'Select Teacher',
                    'id' => 'bulk-action', 'class' => 'form-control',
                    'url' => Url::to(['teacher-substitute/index'])
        ])?>
    </div>

    <div class="col-md-12">
        <?=
        $this->render('//lesson/review/_listing', [
                'lessonDataProvider' => $lessonDataProvider,
                'conflicts' => $conflicts
        ]);
        ?>
    </div>
</div>