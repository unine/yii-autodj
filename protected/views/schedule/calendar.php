<?php
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/fullcalendar/fullcalendar.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/fullcalendar/fullcalendar.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/fullcalendar/bootstrap-fullcalendar.css');
Yii::app()->clientScript->registerScript('fullcalendar_init',"
	var calendar = $('#calendar').fullCalendar({
		header: {
				left: 'prev,today,next',
				center: 'title',
				right: 'month,agendaWeek'
			},
		defaultView: 'month',
		allDaySlot: false,
		slotMinutes: 15,
		axisFormat: 'HH:mm',
		defaultEventMinutes: 60,
		timeFormat: 'H:mm{ - H:mm}',
		selectable: true,
		selectHelper: true,
		editable: true,
		buttonText: {
	        prev: '&lt;',
	        next: '&gt;',
	        today: 'Today'
	    },
		events: '".$this->createUrl('schedule/shows')."',
		select: function(start, end, allDay)
		{
			var event = new Object();
			    event.start = start;
			    event.end = end;
			var start = event.start.getTime();
			    start = Math.round(start/1000);
			var end = event.end.getTime();
			    end = Math.round(end/1000);
			    end = end + 3600;
			if(!isOverlapping(event) && !isInpast(event))
				window.location.href = '".Yii::app()->request->baseUrl."/schedule/create?start='+start+'&end='+end+'';
		},
		eventClick: function(calEvent, jsEvent, view)
            	{
            		window.location.href = '".Yii::app()->request->baseUrl."/schedule/update?id='+calEvent.id+'';
            	},
            	eventResize: function(event,dayDelta,minuteDelta,revertFunc)
	    	{
			if(isOverlapping(event))
			{
			    	revertFunc();
			}
			else
			{
	                startdate = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm');                    
			    	enddate = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm');
			    	jQuery.ajax({
				    	'type':'POST',
				    	'url':'".Yii::app()->request->baseUrl."/schedule/resize?id='+event.id,
				    	'cache':false,
				    	'data':{'start_date':startdate,'end_date':enddate},
			    	});
			}
            	},
            	eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc)
            	{
			if(isOverlapping(event))
			{
			    	revertFunc();
			}
			else
			{
					startdate = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm');                    
					enddate = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm');
					jQuery.ajax({
						'type':'POST',
						'url':'".Yii::app()->request->baseUrl."/schedule/resize?id='+event.id,
						'cache':false,
						'data':{'start_date':startdate,'end_date':enddate},
					});
			}
            	}
	});
");
Yii::app()->clientScript->registerScript('isOverlapping',"
function isOverlapping(event)
{
    	var array = calendar.fullCalendar('clientEvents');
    	for(i in array)
    	{
        	if(array[i].id != event.id)
        	{
            		if(!(array[i].start >= event.end || array[i].end <= event.start))
            		{
                		return true;
            		}
        	}
    	}
    	return false;
}
");
Yii::app()->clientScript->registerScript('isInpast',"
function isInpast(event)
{
    	var today = new Date();
            today.setHours(0,0,0,0);
    	var eventday = new Date(event.start);
            eventday.setHours(0,0,0,0);
    	if(event.start < today)
    	{
        	return true;
    	}else{
        	return false;
    	}
}
");
?>
<div class="content-block">
	<div id='calendar'></div>
</div>