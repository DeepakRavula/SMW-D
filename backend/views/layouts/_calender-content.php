<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use kartik\date\DatePicker;
?>

<div id="fullcalendar-week-view" class="col-md-12">
    <div class="col-lg-2 pull-right">
        <?php echo '<label>Go to Date</label>'; ?>
        <div id="week-view-calendar-go-to-date" class="input-group date">
			<input type="text" class="form-control" value=<?=(new \DateTime())->format('d-m-Y')?>>
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</div>
		</div>
    </div>
    <div id="week-view-calendar"></div>
</div>