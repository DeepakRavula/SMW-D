<div class="btn-group-sm">
    <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
    <ul class="dropdown-menu dropdown-menu-right">
        <?php if (!$model->isInvoice()) : ?>
            <?php if ($model->canPost()) : ?>
                <li><a id="post" href="#">Post</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Post</a></li>
            <?php endif; ?>
            <?php if ($model->canPost() && $model->canDistributeCredits()) : ?>
                <li><a id="post-distriute" href="#">Post & Distribute</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Post & Distribute</a></li>
            <?php endif; ?>
            <?php if ($model->canDistributeCredits()) : ?>
                <li><a id="distriute" href="#">Distribute Funds to Lessons</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Distribute Funds to Lessons</a></li>
            <?php endif; ?>
            <?php if (!$model->canRetractCredits()) : ?>
                <li><a class="multiselect-disable" href="#">Retract Funds From Lessons</a></li>
            <?php else : ?>
                <li><a id="retract" href="#">Retract Funds From Lessons</a></li>
            <?php endif; ?>
            <?php if ($model->canUnpost() && ($loggedUser->isAdmin() || $loggedUser->isOwner())) : ?>
                <li><a id="un-post" href="#">Un-post</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Un-post</a></li>
            <?php endif; ?>
        <?php else : ?>
            <?php if (!$model->isVoid) : ?>
                <li><a id="void" href="#">Void</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Void</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>