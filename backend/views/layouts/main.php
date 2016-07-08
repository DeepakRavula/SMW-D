<?php
/**
 * @var $this yii\web\View
 */
use common\models\User;
use yii\helpers\Html;
?>
<?php $this->beginContent('@backend/views/layouts/common.php'); ?> 
<div class="box"> 
    <div id="notification" style="display: none;" class="alert-danger alert fade in"></div>
    <div class="box-body">
        <?php echo $content ?>
    </div>
</div> 
<?php $this->endContent(); ?>
<body>
<script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
    FreshWidget.init("", {"queryString": "&helpdesk_ticket[requester]=<?= Html::encode( Yii::$app->user->identity->email);?> &helpdesk_ticket[subject]=<?= Html::encode($this->title);?>",
"widgetType": "popup", "buttonType": "text", "buttonText": "Feedback", 
"buttonColor": "white", "buttonBg": "#E30018", "alignment": "2",
 "offset": "260px", "formHeight": "500px","screenshot": "no", 
 "captcha": "yes","url": "http://smw.freshdesk.com"} );
</script>
</body>
<script>
    $("form").on("afterValidate", function (event, messages) {
        $('#notification').html("Form has some errors. Please fix and try again.").fadeIn();
    });
</script>
