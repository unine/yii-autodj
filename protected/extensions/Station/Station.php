<?php
class Station extends CWidget
{
    public $previous = null;
    public $nowplaying = null;
    public $next = null;
    public $online = false;

    public function init()
    {
        Yii::app()->clientScript->registerScript('clock',"
            var serverDate = new Date();
            serverDate.setTime(".round(microtime(true) * 1000).");

            var seconds = serverDate.getSeconds();
            var minutes = serverDate.getMinutes();
            var hours = serverDate.getHours();

            setInterval( function() {                
                $('.sec').html(( seconds < 10 ? '0' : '' ) + seconds);
                $('.min').html(( minutes < 10 ? '0' : '' ) + minutes);
                $('.hours').html(( hours < 10 ? '0' : '' ) + hours);
                seconds++;
                if(seconds > 59)
                {
                    minutes++;
                    seconds = 0;
                }
                
                if(minutes > 59)
                {
                    hours++;
                    minutes = 0;
                }

                if(hours > 23)
                    hours = 0;

            },1000);
        ");

        Yii::app()->clientScript->registerScript('refreshqueue',"
            setInterval(function(){
                $.ajax({
                    'url':'".Yii::app()->createUrl("dashboard/playupdate")."',
                    'type':'POST',
                    'async':false,
                    'dataType':'json',
                    'success':function(data){
                        var old_now = $('#current-title').text();
                        var new_now = data[0].title+' - '+data[0].artist;
                        var new_now = 'Nothing Play';
                        if(data[0].title != '' && data[0].artist != '')
                            new_now = data[0].title+' - '+data[0].artist;
                        var old_prev = $('#previous-title').text();
                        var new_prev = data[1].title+' - '+data[1].artist;
                        var old_next = $('#next-title').text();
                        var new_next = data[2].title+' - '+data[2].artist;

                        if(old_now != new_now)
                        {
                            $.fn.yiiGridView.update('queue-grid');
                            $('#current-title').fadeOut('slow', function(){
                                $(this).text(new_now);
                                $(this).fadeIn();
                            });
                            $('#previous-title').fadeOut('slow', function(){
                                $(this).text(new_prev);
                                $(this).fadeIn();
                            });
                        }
                        if(old_next != new_next)
                        {
                            $('#next-title').fadeOut('slow', function(){
                                $(this).text(new_next);
                                $(this).fadeIn();
                            });
                        }
                    }
                });
            },5000);
        ");

        Yii::app()->clientScript->registerScript('on_off',"
            $('#on-air-info').on('click', function(){
                if($(this).hasClass('on'))
                {
                    // switch off
                    $.ajax({
                            'url':'".Yii::app()->createUrl('//dashboard/stop')."', 
                            'success':function(data){
                                $('#on-air-info').removeClass('on').addClass('off');
                            },
                            'error': function(request, status, error){
                                alert('We are unable to stop program at this time.  Please try again in a few minutes.');
                            }
                    });
                }
                else
                {
                    // switch on
                    $.ajax({
                            'url':'".Yii::app()->createUrl('//dashboard/start')."', 
                            'success':function(data){
                                $('#on-air-info').addClass('on').removeClass('off');
                            },
                            'error': function(request, status, error){
                                alert('We are unable to start program at this time.  Please try again in a few minutes.');
                            }
                    });
                }
            });
        ");

        $pid = shell_exec('cat /var/run/liquidsoap/radio.pid');
        if(trim($pid) != '')
        {
            $ps = shell_exec("ps -o cmd= $pid");
            if(trim($ps) != '')
                $this->online = true;
        }

        $history = History::model()->findAll(array('order'=>'played_time desc','limit'=>2));
        if(!empty($history))
        {
            foreach ($history as $item) {
                if($this->previous === null)
                {
                    $duration = ceil($item->f->duration);
                    $song_end = date('Y-m-d H:i:s', strtotime("+$duration second",strtotime($item->f->last_played)));
                    
                    if($song_end >= date('Y-m-d H:i:s'))
                        $this->nowplaying = $item->f;
                    else
                        $this->previous = $item->f;
                }
            }
        }

        $next = QueueList::model()->find(array('order'=>'ordering asc'));
        if($next !== null)
            $this->next = $next->f;
    }

    public function run()
    {
        $this->render('nowplaying');
    }
    
}