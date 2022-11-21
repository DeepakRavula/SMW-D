<?php

use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Import {modelClass}', [
    'modelClass' => 'User',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Import'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id = "alert_placeholder"></div>
<div class="user-import-form p-10 pull-left">
<?php echo \trntv\filekit\widget\Upload::widget([
    'model' => $model,
    'attribute' => 'file',
    'url' => ['upload'],
    'sortable' => true,
    'maxFileSize' => 10 * 1024 * 1024, // 10Mb
    //'minFileSize' => 1 * 1024 * 1024, // 1Mb
    'maxNumberOfFiles' => 3, // default 1,
    'clientOptions' => [
        'start' => new JsExpression('UserImport.onStart'),
        'done' => new JsExpression('UserImport.onDone')
    ],
]); ?>
    <div id="user-import-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div id="import-log" class="p-t-10 pull-left">

</div>
<div class="clearfix"></div>
<script type="text/javascript">

bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
    $('#alert_placeholder').html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>'+message+'</span></div>')
}
    var UserImport = {
        onDone : function(e, data) {
            $('#user-import-spinner').hide();
            $.each(data.result.errors, function(index, val){
                var error = $("<div/>").html(val);
                $('#import-log').append(error);
            });
            var importMessage = '<strong>Import complete</strong>';
            var studentMessage = data.result.studentCount + (' students imported out of ') + data.result.totalRows;
            var customerMessage = data.result.customerCount + (' customers created');
            $('#import-log').append(importMessage);
            var student = $("<div/>").html(studentMessage);
            $('#import-log').append(student);
            $('#import-log').append(customerMessage);
            bootstrap_alert.success('User data imported successfully');
        },
        onStart : function(e, data) {
            $('#user-import-spinner').show();
        }
    }
</script>
