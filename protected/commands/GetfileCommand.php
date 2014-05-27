<?php
class GetfileCommand extends CConsoleCommand 
{
    public function run($args)
    {
        $queue = Yii::app()->db->createCommand()->select('t.id,t.filepath,t.count_played,q.id as qid,q.ordering,q.fid as fid')
            ->from('files t')
            ->join('queue_list q', 'q.fid=t.id')
            ->order('q.ordering ASC')
            ->limit(3)
            ->queryAll();

        if(count($queue) < 3)
        {
            $cmd='php '.CONSOLE.' updatequeue';
            shell_exec($cmd);
        }

        if($queue)
        {
            $in_queued = Yii::app()->db->createCommand()->select('fid')
                ->from('queue_list')
                ->where('fid=:fid',array(':fid'=>$queue[0]['fid']))
                ->order('ordering ASC')
                ->queryAll();
            
            if(count($in_queued) > 1)
                $in_queued = 1;
            else
                $in_queued = 0;
            
            // this is very important don't delete it
            echo LIBRARY.$queue[0]['filepath'];
            $count = $queue[0]['count_played']+1;
            Yii::app()->db->createCommand()->delete('queue_list', 'id=:id', array(':id'=>$queue[0]['qid']));

            $played_time=date('Y-m-d H:i:s');

            Yii::app()->db->createCommand()->update('files', array(
                'count_played'=>$count,
                'in_queued'=>$in_queued,
                'last_played'=>$played_time,
            ), 'id=:id', array(':id'=>$queue[0]['id']));

            Yii::app()->db->createCommand()->insert('history', array(
                'fid'=>$queue[0]['id'],
                'played_time'=>$played_time,
            ));
        }
    }
}