<?php

use common\models\Program;
use backend\models\search\ProgramSearch;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = 'Programs';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'new-program']);

$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
 ]);
?>
<div class="m-b-10">
</div>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="row">
    <div class="col-md-12">
        <?php
        echo $this->render('_index-private',
            [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        ?>
    </div>
</div>
<div>
<div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Program</h4>',
    'id' => 'program-modal',
]);
?>
<div id="program-content"></div>
<?php Modal::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.action-button,#private-program-grid tbody > tr',function () {
            var programId = $(this).data('key');
            if (programId === undefined) {
                var customUrl = '<?= Url::to(['program/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['program/update']); ?>?id=' + programId;
            }
            $.ajax({
                url: customUrl,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#program-content').html(response.data);
                        $('#program-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#program-form', function () {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status) {
                        $.pjax.reload({container: '#program-listing', timeout: 6000});
                        $('#program-modal').modal('hide');
                    } else {
                        $('#error-notification').html(response.message).fadeIn().delay(8000).fadeOut();
                        $('#program-modal').modal('hide');
                    }

                }
            });
            return false;
        });
        $(document).on('click', '.program-cancel', function () {
            $('#program-modal').modal('hide');
            return false;
        });
    });
</script>
