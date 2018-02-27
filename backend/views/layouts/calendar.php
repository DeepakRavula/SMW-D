<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/admin/plugins/fullcalendar-week-view/fullcalendar-render.js?v=8"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<div id="fullcalendar-week-view" style="display: none">
    <div class="fullcalendar-week-view col-md-12">
        <div class="col-lg-2 pull-right">
            <?php echo '<label>Go to Date</label>'; ?>
            <input id="go-to-datepicker" type="text" value="<?=(new \DateTime())->format('d-m-Y')?>" class="form-control">
        </div>
        <div id="week-view-calendar"></div>
    </div>
</div>