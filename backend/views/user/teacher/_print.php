<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;

?>
    <!-- title row -->
    <?php
   echo $this->render('/print/_header', [
       'userModel'=>$model,
       'locationModel'=>$model->userLocation->location,
]);
   ?>
      <!-- /.col -->
     <div class="col-sm-4 invoice-col">
        <b>Lessons</b><br>
        <br>
        <b><?php echo  $fromDate->format('F jS, Y') . ' to ' . $toDate->format('F jS, Y');?></b>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
       <div class="report-grid">
<?php echo $this->render('_time-voucher-content',['searchModel'=>$searchModel,'teacherLessonDataProvider' => $teacherLessonDataProvider]); ?>
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