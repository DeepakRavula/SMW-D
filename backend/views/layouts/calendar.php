<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/admin/plugins/fullcalendar-week-view/fullcalendar-render.js?v=1"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<?= $this->render('/lesson/_color-code');?>

<div id="fullcalendar-week-view">
    <div class="col-lg-2 pull-right">
        <label>Go to Date</label>
        <div id="go-to-datepicker" class="input-append date">
            <input id="fullcalendar-week-view-go-to-datepicker" class="form-control" readonly="true" type="text" value="<?=(new \DateTime())->format('M-d-Y')?>"><span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>
    <div id="week-view-calendar"></div>
</div>