 <div class="invoice-view">
       <div class="row">
      <div class="col-md-12">
        <h2 class="page-header">
          <span class="logo-lg"><img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" /></span>
          <?php if (!empty($invoiceModel) ): ?>
          <small class="pull-right"><?= Yii::$app->formatter->asDate($invoiceModel->date); ?></small>
          <?php else: ?>
          <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
         <?php endif; ?>
        </h2>
      </div>
       </div>
        <div class="row">
      <div class="col-md-6 invoice-col">
          <div class="invoice-print-address">
        From
        <address>
          <b>Arcadia Music Academy ( <?= $locationModel->name; ?> )</b><br>
          <?php if (!empty($locationModel->address)): ?>
              <?= $locationModel->address; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($locationModel->city_id)): ?>
              <?= $locationModel->city->name; ?>,
          <?php endif; ?>        
          <?php if (!empty($locationModel->province_id)): ?>
              <?= $locationModel->province->name; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($locationModel->postal_code)): ?>
              <?= $locationModel->postal_code; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($locationModel->phone_number)): ?>
              <?= $locationModel->phone_number ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($locationModel->email)): ?>
              <?= $locationModel->email ?>
          <?php endif; ?>
          <br/>
          www.arcadiamusicacademy.com
        </address>
          </div>
      </div>
      <!-- /.col -->
      <div class="col-md-4 invoice-col">
          <div class="invoice-print-address">
       
        <?php if(!empty($userModel)) : ?>
               To
        <address>
          <strong><?php echo isset($userModel->publicIdentity) ? $userModel->publicIdentity : null?></strong><br>
          <?php
          $addresses = $userModel->addresses;
          if(!empty($userModel->primaryAddress))
          {
           $primaryAddress = $userModel->primaryAddress;   
          }
          
          $phoneNumber = $userModel->phoneNumber;
          ?>
          <?php if (!empty($primaryAddress->address)) : ?>
              <?= $primaryAddress->address; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($primaryAddress->city->name)) : ?>
              <?= $primaryAddress->city->name; ?>,
          <?php endif; ?>  
          <?php if (!empty($primaryAddress->province->name)) : ?>
              <?= $primaryAddress->province->name; echo '<br/>'; ?>
          <?php endif; ?>  
          <?php if (!empty($primaryAddress->postal_code)) : ?>
              <?= $primaryAddress->postal_code; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($phoneNumber)) : ?>
              <?php echo $phoneNumber->number; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($model->user->email)): ?>
              <?php echo $model->user->email; echo '<br/>'; ?>
          <?php endif; ?>
          <?php endif; ?>
        </address>
      </div>
      </div>
      <?php if(!empty($invoiceModel)):?>
      <div class="col-md-2 invoice-col">
        <b><?= $model->getInvoiceNumber();?></b><br>
        <br>
        <b>Date:</b><?= Yii::$app->formatter->asDate($model->date); ?> <br>
        <b>Status:</b>  <?= $model->getStatus(); ?><br>
        <?php if (!empty($model->dueDate)) : ?>
        <b>Due Date:</b><?= Yii::$app->formatter->asDate($model->dueDate);?>
           <?php endif; ?>
        <?php endif; ?>
      </div>
      <!-- /.col -->
    </div>
     <?php if(!empty($courseModel)):?>
      <div class="col-md-2 invoice-col">
        <b><?= $model->getInvoiceNumber();?></b><br>
        <br>
        <b>Date:</b><?= Yii::$app->formatter->asDate($model->date); ?> <br>
        <b>Status:</b>  <?= $model->getStatus(); ?><br>
        <?php if (!empty($model->dueDate)) : ?>
        <b>Due Date:</b><?= Yii::$app->formatter->asDate($model->dueDate);?>
           <?php endif; ?>
        <?php endif; ?>
      </div>
      <!-- /.col -->
    </div>
      <!-- /.col -->
    </div><?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

