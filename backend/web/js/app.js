require(function(jq){
	$(function() {
		"use strict";

		//Make the dashboard widgets sortable Using jquery UI
		$(".connectedSortable").sortable({
			placeholder: "sort-highlight",
			connectWith: ".connectedSortable",
			handle: ".box-header, .nav-tabs",
			forcePlaceholderSize: true,
			zIndex: 999999
		}).disableSelection();
		$(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");
	});

$(function() {
    data = [{
        "start": "2015-06-20 15:00:00",
        "end": "2015-06-20 16:00:00",
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
});
