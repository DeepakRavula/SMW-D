<?php

use common\models\Program;
use backend\models\search\ProgramSearch;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Programs';
?>

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="nav-tabs-custom">
<?php 

$indexProgram = $this->render('_index-program', [
    'model' => $model,
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private Programs',
            'content' => (int) $searchModel->type === Program::TYPE_PRIVATE_PROGRAM ? $indexProgram : null,
            'url' => ['/program/index', 'ProgramSearch[type]' => Program::TYPE_PRIVATE_PROGRAM],
            'active' => (int) $searchModel->type === Program::TYPE_PRIVATE_PROGRAM,
        ],
        [
            'label' => 'Group Programs',
            'content' => (int) $searchModel->type === Program::TYPE_GROUP_PROGRAM ? $indexProgram : null,
            'url' => ['/program/index', 'ProgramSearch[type]' => Program::TYPE_GROUP_PROGRAM],
            'active' => (int) $searchModel->type === Program::TYPE_GROUP_PROGRAM,
        ],
    ],
]); ?>
<div class="clearfix"></div>
</div>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Program</h4>',
        'id' => 'program-modal',
    ]); ?>
<div id="program-content"></div>
 <?php  Modal::end(); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#add-program, #program-listing  tbody > tr', function () {
            var programId = $(this).data('key');
            if (programId === undefined) {
                var customUrl = '<?= Url::to(['program/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['program/update']); ?>?id=' + programId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
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
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
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
