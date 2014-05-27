<?php

class DashboardController extends Controller
{
    public $defaultAction = 'nowplaying';

    public function filters()
    {
        return array( 
                'accessControl', // perform access control for CRUD operations 
                'postOnly + delete', // we only allow deletion via POST request 
            );
    }

    public function accessRules() 
    { 
        return array( 
            array('allow', // allow authenticated user to perform 'create' and 'update' actions 
                'actions'=>array('nowplaying','playupdate','neworder','addQueue','delete','start','stop','reload'), 
                'users'=>array('@'), 
            ),
            array('deny',  // deny all users 
                'users'=>array('*'), 
            ), 
        ); 
    }

    public function actionreload()
    {
        $queue = QueueList::model()->findAll();
        foreach ($queue as $files) {
            $files->f->in_queued = 0;
            $files->f->save();
        }
        QueueList::model()->deleteAll();
        $rs = shell_exec('php '.CONSOLE.' updatequeue');
    }

    public function actionStart(){
        $cmd = shell_exec('sudo /etc/init.d/liquidsoap start');
    }

    public function actionStop(){
        $cmd = shell_exec('sudo /etc/init.d/liquidsoap stop');
    }

    public function actionNowplaying($string='')
    {
        $status = 'offline';
        $pid = shell_exec('cat /var/run/liquidsoap/radio.pid');
        if(trim($pid) != '')
        {
            $ps = shell_exec("ps -o cmd= $pid");
            if(trim($ps) != '')
                $status = 'online';
        }

        $nowplaying = null;
        $previous = null;

        $criteria = new CDbCriteria;
        $criteria->order = 'played_time desc';
        $last_play = History::model()->find($criteria);
        if($last_play && $status == 'online')
        {
            $duration = ceil($last_play->f->duration);
            $song_end = date('Y-m-d H:i:s', strtotime("+$duration second",strtotime($last_play->f->last_played)));
            
            if($song_end >= date('Y-m-d H:i:s'))
                $nowplaying = $last_play->f;
            else
                $previous = $last_play->f;
        }

        if($previous === null)
        {           
            $criteria->offset = 1;
            $previous_play = History::model()->find($criteria);
            if($previous_play)
                $previous = $previous_play->f;
        }

        $queue = new CActiveDataProvider('QueueList',array(
                'criteria'=>array(
                    'order'=>'ordering ASC',
                ),
                'pagination'=>array('pageSize'=>100),
            ));

        $criteria=new CDbCriteria;
        $criteria->with=array('tags0');
        if( strlen( $string ) > 0 )
        {
            $criteria->together=true;
            $criteria->condition='(t.track_title like "%'.$string.'%" or t.artist_name like "%'.$string.'%" or t.filename like "%'.$string.'%" or tags0.name like "%'.$string.'%")';
        }
        $criteria->order='RAND()';
        $files=new CActiveDataProvider('Files',array('criteria'=>$criteria,'pagination'=>array('PageSize'=>20)));

        $this->render('nowplaying',array(
                'nowplaying'=>$nowplaying,
                'previous'=>$previous,
                'status'=>$status,
                'queue'=>$queue,
                'files'=>$files,
            ));
    }

    public function actionPlayupdate()
    {
        $status = 'offline';
        $pid = shell_exec('cat /var/run/liquidsoap/radio.pid');
        if(trim($pid) != '')
        {
            $ps = shell_exec("ps -o cmd= $pid");
            if(trim($ps) != '')
                $status = 'online';
        }

        $update = array();
        $next = array(
                'title'=>'',
                'artist'=>'',
            );
        $now = array(
                'title'=>'',
                'artist'=>'',
            );
        $prev = null;

        $criteria = new CDbCriteria;
        $criteria->order = 'played_time desc';
        $criteria->limit = 2;
        $model = History::model()->findAll($criteria);

        if(!empty($model))
        {
            foreach ($model as $key => $item)
            {
                if($prev === null)
                {
                    $duration = ceil($item->f->duration);
                    $song_end = date('Y-m-d H:i:s', strtotime("+$duration second",strtotime($item->f->last_played)));

                    if($song_end >= date('Y-m-d H:i:s') && $status == 'online' && $key == 0)
                    {
                        $now['title'] = $item->f->track_title;
                        $now['artist'] = $item->f->artist_name;
                    }
                    else
                    {
                        $prev['title'] =  $item->f->track_title;
                        $prev['artist'] =  $item->f->artist_name;
                    }
                }
            }
        }

        $criteria = new CDbCriteria;
        $criteria->order = 'ordering asc';
        $next_song = QueueList::model()->find($criteria);

        if($next_song)
        {
            $next['title'] =  $next_song->f->track_title;
            $next['artist'] =  $next_song->f->artist_name;
        }

        $update[] = $now;
        $update[] = $prev;
        $update[] = $next;
        echo json_encode($update);
    }

    public function actionNeworder()
    {
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $i = 0;
            foreach ($_POST['items'] as $item) {
                $queue = QueueList::model()->findByPk($item);
                $queue->ordering = $i;
                $queue->save();
                $i++;
            }
        }
    }

    public function actionAddQueue()
    {
        if (isset($_POST['fid']))
        {
            $criteria = new CDbCriteria;
            $criteria->order = 'ordering desc';
            $last_queue = QueueList::model()->find($criteria);

            $queue = new QueueList;
            $queue->fid = $_POST['fid'];
            $queue->sid = $last_queue->sid;
            $queue->ordering = $last_queue->ordering+1;
            $queue->save();
        }
    }

    public function actionDelete($id)
    {
        $queue = QueueList::model()->findByPk($id);
        $q_f = QueueList::model()->findAllByAttributes(array('fid'=>$queue->fid));

        if(count($q_f) === 1)
        {
            $queue->f->in_queued = 0;
            $queue->f->save();
        }
        $queue->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('nowplaying'));
    }

    protected function gridTags($data,$row)
    {
        $model=Files::model()->findByPk($data->id);

        return $this->renderPartial('_tags',array('model'=>$model),true); 
    }

}