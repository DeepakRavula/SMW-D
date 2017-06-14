<?php

/**
 * @var yii\web\View
 */
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

?>
<?php $this->beginContent('@backend/views/layouts/common.php'); ?> 
    <div id="notification" style="display: none;" class="alert-danger alert fade in"></div>
<?php echo $content ?>
<?php $this->endContent(); ?>
<body>
    <script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
    <script type="text/javascript">
        FreshWidget.init("", {"queryString": "&widgetType=popup&searchArea=no&helpdesk_ticket[requester]=<?= Html::encode(Yii::$app->user->identity->email); ?> &helpdesk_ticket[subject]=<?= Html::encode($this->title); ?>",
            "widgetType": "popup", "buttonType": "text", "buttonText": "Feedback",
            "buttonColor": "white", "buttonBg": "#E30018", "alignment": "1",
            "offset": "-1500px", "formHeight": "500px", "screenshot": "no",
            "url": "http://smw.freshdesk.com"});
    </script>
</body>
<script type="text/javascript">
$(document).ready(function(){
    $("form").on("afterValidate", function (event, messages, errorAttributes) {        
        if (errorAttributes.length > 0) {
            $('#notification').html("Form has some errors. Please fix and try again.").fadeIn();
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

    // With HTML5 history API, we can easily prevent scrolling!
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        if(history.pushState) {
            history.pushState(null, null, e.target.hash); 
        } else {
            window.location.hash = e.target.hash; //Polyfill for old browsers
        }
    });
	
    $('.grid-row-open').on('click','td',function (e) {        
        var url = $(this).closest('tr').data('url');
        if (e.target === this)
           location.href =  url;
    });

});

</script>




