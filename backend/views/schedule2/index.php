
<? $bundle = \common\assets\fullcalendar\FullCalendar::register($this);?>
<div id="calendar"></div>
<script>
$(function() {
    data = [{
        "start": "2017-01-01 15:00:00",
        "end": "2017-01-01 16:00:00",
        "title": "Java"
    }, {
        "start": "2015-06-20 15:00:00",
        "end": "2015-06-20 16:00:00",
        "title": "Java"
    }, {
        "start": "2015-06-20 15:00:00",
        "end": "2015-06-20 16:00:00",
        "title": "Java"
    }];

    Service = Backbone.Model.extend({
        defaults: {
            title: null,
            start: null,
            end: null
        }
    });
    ServiceCollection = Backbone.Collection.extend({
        model: Service,
    });
    App = Backbone.View.extend({
        initialize: function() {
            this.model = new Service();
        },
        render: function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay',
                    ignoreTimezone: false
                },
                selectable: true,
                selectHelper: true,
                editable: true,
                ignoreTimezone: false,
            });
            $('#calendar').fullCalendar("addEventSource", this.collection.toJSON());
        }
    });
    var myCollection = new ServiceCollection(data);
    new App({
        collection: myCollection
    }).render();
});
</script>