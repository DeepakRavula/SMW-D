<?php

use common\models\Course;
use common\models\UserProfile;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\User;
use common\models\Student;
?>
<style>
	.enrolment{    
		background-color: #fff;
	   border:1px solid #eaefe9;
	   font-size: 14px;
	}
	.panel{
		margin-bottom: 0px;
	}
	.enrolment-step {
		border-top: 1px solid #f2f2f2;
		color: #666;
		font-size: 14px;
		padding: 10px;
		position: relative;
	}
	.enrolment-step-number {
		border-radius: 50%;
		border: 1px solid #666;
		display: inline-block;
		font-size: 12px;
		height: 32px;
		margin-right: 26px;
		padding: 6px;
		text-align: center;
		width: 32px;
	}
	.enrolment-step-title{
		font-size: 18px;
		font-weight: 500;
		vertical-align: middle;
		display: inline-block;
		margin: 0px;
	}
	.enrolment-step-body{
		padding-left: 60px;
		padding-top: 30px;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div id="accordion" class="enrolment">
				<div class="panel enrolment-step">
					<div> <span class="enrolment-step-number">1</span>
						<h4 class="enrolment-step-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" > Program</a></h4>
					</div>
					<div id="collapseOne" class="collapse in">
						<div class="enrolment-step-body">
							<div class="row">
								<div class="col-lg-8">
									<div>
										<?=
										$this->render('_form-course', [
											'model' => new Course(),
										]);
										?>
									</div>
									<!-- /input-group -->
								</div>

								<!-- /.col-lg-6 -->
							</div>
							<a class="collapsed btn btn-default" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"> Next </a>
						</div>
					</div>
				</div>
				<div class="panel enrolment-step">
					<div role="tab" id="headingTwo"> <span class="enrolment-step-number">2</span>
						<h4 class="enrolment-step-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" > Customer </a> </h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse">
						<div class="enrolment-step-body">
							<div class="row">
								<?=
								$this->render('_form-customer', [
									'model' => new User(),
									'phoneModel' => new PhoneNumber(),
									'addressModel' => new Address(),
									'userProfile' => new UserProfile()
								]);
								?> 
							</div>
							<a class="collapsed btn btn-default" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"> Next </a>
						</div>
					</div>
				</div>
				<div class="panel enrolment-step">
					<div role="tab" id="headingThree"> <span class="enrolment-step-number">3</span>
						<h4 class="enrolment-step-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"  > Student </a> </h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse">
						<div class="enrolment-step-body">
							<div class="row">
								<div class="form-group">
									<?=
									$this->render('_form-student', [
										'model' => new Student(),
									]);
									?> 
								</div>
							</div>
							<a class="collapsed btn btn-default" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour"> Next </a>
						</div>
					</div>
				</div>
				<div class="panel enrolment-step">
					<div role="tab" id="headingFour"> <span class="enrolment-step-number">4</span>
						<h4 class="enrolment-step-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour"  > Preview </a> </h4>
					</div>
					<div id="collapseFour" class="panel-collapse collapse">
						<div class="checkout-step-body">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>