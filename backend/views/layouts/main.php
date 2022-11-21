<?php

/**
 * @var yii\web\View
 */
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->beginContent('@backend/views/layouts/common.php'); ?> 
    <div id="notification" style="display: none;" class="alert-danger alert fade in"></div>
    <div id="success-notification" style="display: none;" class="alert-success alert fade in"></div>
<?php echo $content ?>
<?= $this->render('modal-popup'); ?>
<?= $this->render('calendar'); ?>
<?php $this->endContent(); ?>
<body>
    <!-- <script type="text/javascript" src="https://assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
	FreshWidget.init("", {
		"queryString": "&widgetType=popup&helpdesk_ticket[requester]=<?= Html::encode(Yii::$app->user->identity->email); ?>&helpdesk_ticket[subject]=<?= Html::encode($this->title); ?>",
		"utf8": "✓", 
		"widgetType": "popup",
		"buttonType": "text",
		"buttonText": "Support",
		"buttonColor": "white",
		"buttonBg": "#006063",
		"alignment": "4",
		"offset": "-1500px", 
		"formHeight": "500px", 
		"url": "https://smw.freshdesk.com"} );
</script> -->
</body>
<script type="text/javascript">
$(document).ready(function(){
    $("form").on("afterValidate", function (event, messages, errorAttributes) {        
        if (errorAttributes.length > 0) {
            $('#notification').html("Form has some errors. Please fix and try again.").fadeIn().delay(8000).fadeOut();
        }
    });
    $(".release-notes .close").click(function(){
        $.ajax({
            url: "<?php echo Url::to(['release-notes/update-read-notes']); ?>",
            type: "POST",
            contentType: 'application/json',
            dataType: "json",
            data: JSON.stringify({
                "id": $( ".release-notes" ).attr("data-id")
            }),
            success: function(response) {
            },
            error: function(xhr) {
            }
        });
    });
    // Javascript to enable link to tab
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#'+url.split('#')[1]+'"]').tab('show') ;
    } 

    $('.grid-row-open').on('click','td',function (e) {        
        var url = $(this).closest('tr').data('url');
        if (e.target === this)
           location.href =  url;
    });

});

</script>




