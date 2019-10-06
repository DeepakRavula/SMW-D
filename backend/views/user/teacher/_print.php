<?php

use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;

?>
    <!-- title row -->
      <!-- /.col -->
      <div class="col-md-12">
            <h3> Time Voucher For  <?php echo $model->publicIdentity.'     '; ?></h3>
            <?php if ($fromDate->format('F jS, Y') === $toDate->format('F jS, Y')): ?>
            <h4><?=  $toDate->format('F jS, Y'); ?></h4>
            <?php else: ?>
            <h4><?=  $fromDate->format('F jS, Y'); ?> to <?= $toDate->format('F jS, Y') ?></h4>
            <?php endif; ?>  
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
      setTimeout(function(){
            window.print();
}, 1500)
    });
</script>