<?php
/**
 * @var $this yii\web\View
 */
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
	FreshWidget.init("", {"queryString": "&widgetType=popup&screenshot=no&captcha=yes", "utf8": "✓", "widgetType": "popup", "buttonType": "text", "buttonText": "Feedback", "buttonColor": "white", "buttonBg": "#E30018", "alignment": "2", "offset": "260px", "formHeight": "500px", "screenshot": "no", "captcha": "yes", "url": "https://smw.freshdesk.com"} );
</script>
</body>
<script>
    $("form").on("afterValidate", function (event, messages) {
        $('#notification').html("Form has some errors. Please fix and try again.").fadeIn();
    });
</script>
