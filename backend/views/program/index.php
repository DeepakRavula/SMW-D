<?php

use common\models\Program;
use yii\helpers\Url;
use yii\helpers\Html;

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
        echo $this->render(
    '_index-private',
            [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        ?>
    </div>
</div>
<?php echo Html::hiddenInput('name',Program::TYPE_PRIVATE_PROGRAM,array('id'=>'program-type')); ?>
<script>
	$(document).on('click', '.action-button,#program-listing  tbody > tr', function () {
	    var type=$('#program-type').val();
            var programId = $(this).data('key');
            if (!programId) {
                    var customUrl = '<?= Url::to(['program/create']); ?>?type=' + type;
            } else {
                var customUrl = '<?= Url::to(['program/update']); ?>?id=' + programId;
                var url = '<?= Url::to(['program/delete']); ?>?id=' + programId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action",url);
            }
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Program</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
	    $(document).on('click', '.private', function() {
	        var privateprogram= <?= Program::TYPE_PRIVATE_PROGRAM ?>;
            var type=$('#program-type').val(privateprogram);
		    $(".group").removeClass('active');		
		    $(".private").addClass('active');	
    	});
	    $(document).on('click', '.group', function() {
	        var groupprogram= <?= Program::TYPE_GROUP_PROGRAM ?>;
            var type=$('#program-type').val(groupprogram);
		    $(".private").removeClass('active');	
		    $(".group").addClass('active');	
	    });
	   $(document).on('click', '.group, .private', function() {
            var type = $('#program-type').attr('value');
            var showAllPrograms = $("#programsearch-showallprograms").is(":checked");
            var params = $.param({'ProgramSearch[type]': type, 'ProgramSearch[showAllPrograms]': showAllPrograms | 0});
            var url = "<?php echo Url::to(['program/index']); ?>?" + params;
            $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  
            return false;
       });
       $("#programsearch-showallprograms").on("change", function () {
           var showAllPrograms = $(this).is(":checked");
           var type=$('#program-type').val();
           var params = $.param({'ProgramSearch[type]': type, 'ProgramSearch[showAllPrograms]': showAllPrograms | 0});
           var url = "<?php echo Url::to(['program/index']); ?>?" + params;
           $.pjax.reload({url: url, container: "#program-listing", replace: false, timeout: 4000});  //Reload GridView
            return false;
       });
</script>
