<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;

?>
    <!-- title row -->
    <?php
   echo $this->render('/print/_report-header', [
       'userModel'=>$model,
       'locationModel'=>$model->userLocation->location,
]);
   ?>
<style>
        @media print{
            .report-h1{
                font-size:36px;
            }
            .report-h2{
                font-size:32px;
            }
	}
    </style>
      <!-- /.col -->
      <div class="col-md-12">
          <div class="report-h1"> Time Voucher For  <?php echo $model->publicIdentity.'     '; ?></div>
          
          <div class="report-h2"><?php echo  $fromDate->format('F jS, Y') . '  to   ' . $toDate->format('F jS, Y');?> </div>
      <!-- /.col -->
    <!-- /.row -->
      </div>
    <!-- Table row -->
    <div class="row">
	<div class="col-xs-12 table-responsive">
	    <div class="report-grid">
		<?php echo $this->render('_time-voucher-content',[
		    'searchModel' => $searchModel,
		    'teacherLessonDataProvider' => $teacherLessonDataProvider
		]); ?>
	    </div>
	    <!-- /.col -->
	</div>
    <!-- /.row -->
	<div class="boxed col-md-12 pull-right">
<div class="sign">
 Teacher Signature <span></span>
</div>
<div class="sign">
Authorizing Signature <span></span>
</div>
<div class="sign">
 Date <span></span>
</div>
</div>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>