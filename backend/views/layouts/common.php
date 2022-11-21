<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use yii\helpers\ArrayHelper;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
    <nav class="navbar navbar-inverse navbar-fixed-top">
       <?= $this->render('_header'); ?>
   </nav>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
              <?= $this->render('_left-menu', [
                  
              ]); ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php if (isset($this->params['goback'])) : ?>  
                    <div class="pull-left go-back">
                        <?php echo $this->params['goback']; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($this->params['label'])) : ?>  
                    <div class="pull-left course-icon m-r-10">
                        <?php echo $this->params['label']; ?>
                    </div>
					<?php else: ?>
                        <?php echo $this->params['label'] = $this->title; ?>
                    <?php endif; ?> 
                    <?php if (isset($this->params['action-button'])) : ?>
                        <div class="pull-right action-button">
                            <?php echo $this->params['action-button']; ?>
                        </div>
                    <?php endif; ?>
					 <?php if (isset($this->params['show-all'])) : ?>
                        <div class="pull-right" style="margin-bottom:-10px">
                            <?php echo $this->params['show-all']; ?>
                        </div>
                    <?php endif; ?> 
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')):?>
					<div class="col-md-2"></div>
					<div class="col-md-7">
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ])?>
					</div>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->           
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>

<script>
    $(".filters").on("click", function() {
        return false;
    });
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
</script>