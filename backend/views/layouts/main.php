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
<script>
    $("form").on("afterValidate", function (event, messages) {
        $('#notification').html("Form has some errors. Please fix and try again.").fadeIn();
    });
</script>
