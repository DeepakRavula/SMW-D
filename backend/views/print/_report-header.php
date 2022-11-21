<div class="invoice-view">
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-header">
                <span class="logo-lg"><img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" /></span>   
                <span class="pull-right invoice-address right-align">
                    <small>
                    <b>Arcadia Academy of Music ( <?= $locationModel->name; ?> )</b><br>
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
                    </small>
                </span>
                </h2>
        </div>
    </div>
    
    </div>