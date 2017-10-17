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
    <div class="col-md-5">
        <?= Html::dropDownList('teacher', null, ArrayHelper::map($teachers, 
                'id', 'userProfile.fullName'), [ 'prompt' => 'Select Substitute Teacher',
                    'id' => 'teacher-drop', 'class' => 'form-control'])
        ?>
    </div>

    <div class="col-md-12">
        <?=
        $this->render('//lesson/review/_listing', [
                'lessonDataProvider' => $lessonDataProvider,
                'conflicts' => $conflicts,
                'conflictedLessonIds' => $conflictedLessonIds,
                'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
        ]);
        ?>
    </div>
</div>

<script>
    $(document).on('change', '#teacher-drop', function () {
        var selectedValue = $(this).val();debugger;
        if (selectedValue) {
            var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
            var params = $.param({ ids: lessonIds, teacherId: selectedValue });
            $.ajax({
                url    : '<?= Url::to(['teacher-substitute/index']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if(response.status)
                    {
                        $.pjax.reload({container: '#review-lesson-listing', timeout: 6000});
                    }
                }
            });
            return false;
        }
    });
</script>