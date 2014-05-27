<?php
class UpdatequeueCommand extends CConsoleCommand {
        public function run($args) 
        {
                $setting = Setting::model()->findByPk(1);
                $queue = Yii::app()->db->createCommand()
                ->select('*')
                ->from('queue_list t')
                ->order('t.ordering asc')
                ->queryAll();

                $count_q = count($queue);
                $start_order = 1;
                $sid = null;
                $song = array();

                if($count_q > 0)
                {
                        $start_order = $queue[$count_q-1]['ordering']+1;
                        $qshow = $queue[$count_q-1]['sid'];
                }

                if($setting->mode == 'Schedule')
                {
                        $change_show = false;
                        $show = $this->currentShow();
                        if($show != null && $qshow != $show->id)
                        {
                                if($count_q > 0)
                                {
                                        QueueList::model()->deleteAll();
                                        $count_q = 0;
                                        $change_show = true;
                                }

                                if($change_show || ($count_q < 3 && $show->loop == 1))
                                {
                                        $playlist = Playlists::model()->findByPk($show->pid);
                                        $song = $this->convertPlaylist($playlist);
                                        $sid = $show->id;
                                }
                        }
                }

                if($setting->mode == 'Playlist' && $setting->pid != null && $count_q < 3)
                {
                        $playlist = Playlists::model()->findByPk($setting->pid);
                        $song = $this->convertPlaylist($playlist);
                }

                if(($setting->mode == 'Random' || count($song) === 0) && $count_q < 3)
                {
                        $song = $this->randomSong($setting->song_repeat);
                }

                if(count($song) != 0)
                {
                        foreach ($song as $v)
                        {
                                $file_exist = Files::model()->findByPk($v['id']);

                                if($file_exist)
                                {
                                        $queue=new QueueList;
                                        $queue->fid=$file_exist->id;
                                        $queue->sid=$sid;
                                        $queue->ordering=$start_order;
                                        if($queue->save())
                                                $start_order++;

                                        $file_exist->in_queued = 1;
                                        $file_exist->save();
                                }
                        }
                }
        }

        protected function randomSong($s = 0)
        {
                $none_music = Yii::app()->db->createCommand()
                                ->select('t.id')
                                ->from('files t')
                                ->join('file_tag a', 'a.fid=t.id')
                                ->join('tags b', 'a.tid=b.id')
                                ->where('b.type="None Music"')
                                ->queryAll();
                $none_music = $this->convertToArray($none_music);

                $tracks_1 = Yii::app()->db->createCommand()
                                ->select('t.id,t.last_played')
                                ->from('files t')
                                ->where(array('and', 't.last_played = "0000-00-00 00:00:00"', 't.in_queued = 0', array('not in', 't.id', $none_music)))
                                ->order('RAND()')
                                ->limit(6)
                                ->queryAll();
                $tracks_2 = Yii::app()->db->createCommand()
                                ->select('t.id,t.last_played')
                                ->from('files t')
                                ->where(array('and', 't.last_played != "0000-00-00 00:00:00"', 't.last_played < "'.date('Y-m-d H:i:s', strtotime("$s minute ago")).'"', 't.in_queued = 0', array('not in', 't.id', $none_music)))
                                ->order('RAND()')
                                ->limit(20)
                                ->queryAll();

                $song = array_merge($tracks_1,$tracks_2);
                if(count($song) === 0)
                {
                        $song = Yii::app()->db->createCommand()
                                ->select('t.id,t.last_played')
                                ->from('files t')
                                ->where(array('and', 't.in_queued = 0', array('not in', 't.id', $none_music)))
                                ->order('RAND()')
                                ->limit(10)
                                ->queryAll();
                }

                shuffle($song);
                return $song;
        }

        protected function convertPlaylist($playlist,$s=0)
        {
            $items = unserialize($playlist->items);
            $normal_i = array();
            if($items)
            {
                foreach ($items as $item)
                {
                        $item = explode('-', $item);
                        if($item[0] == 'song')
                        {
                                $normal_i[]['id'] = $item[1];
                        }
                        elseif($item[0] == 'tags')
                        {
                            $id =$item[1];
                            $tag = Tags::model()->findByPk($id);
                            if($tag)
                            {
                                if($tag->type == 'None Music')
                                {
                                    $track = Yii::app()->db->createCommand()
                                            ->select('t.id,t.last_played')
                                            ->from('files t')
                                            ->join('file_tag ta', 'ta.fid=t.id')
                                            ->where('ta.tid=:id', array(':id'=>$id))
                                            ->order('RAND()')
                                            ->queryRow();
                                    $normal_i[]['id'] = $track['id'];
                                }
                                elseif($tag->type == "Music")
                                {
                                    $arr = $this->convertToArray($normal_i);
                                    $track = Yii::app()->db->createCommand()
                                            ->select('t.id,t.last_played')
                                            ->from('files t')
                                            ->join('file_tag ta', 'ta.fid=t.id')
                                            ->where(array('and', 'ta.tid=:id', array('not in', 't.id',$arr), 't.last_played < "'.date('Y-m-d H:i:s', strtotime("$s minute ago")).'"', 't.in_queued = 0'), array(':id'=>$id))
                                            ->order('RAND()')
                                            ->queryRow();
                                    $normal_i[]['id'] = $track['id'];
                                }
                            }
                        }
                }
            }
            return $normal_i;
        }

        protected function convertToArray($model)
        {
                $arr = array();
                foreach ($model as $key => $item)
                {
                        $arr[] = $item['id'];
                }
                return $arr;
        }

        protected function currentShow()
        {
            $now = date('Y-m-d H:i:s');
            $nowtime = date('H:i:s');
            $today = date('w');
            switch ($today) {
                case 0:
                        $today = 's';
                        break;
                case 1:
                        $today = 'm';
                        break;
                case 2:
                        $today = 'tu';
                        break;
                case 3:
                        $today = 'w';
                        break;
                case 4:
                        $today = 'th';
                        break;
                case 5:
                        $today = 'f';
                        break;
                case 6:
                        $today = 'sa';
                        break;
            }

            $criteria = new CDbCriteria;
            $criteria->condition = 'pid IS NOT NULL';
            $shows = Shows::model()->findAll($criteria);

            foreach ($shows as $show)
            {
                if($show->start_date <= $now && $show->end_date >= $now)
                {
                        return $show;
                }
                elseif($show->repeat_frq !== null && ($show->repeat_end == null || $show->repeat_end >= $now))
                {
                    $repeat = unserialize($show->repeat_frq);
                    $start = date('H:i:s', strtotime($show->start_date));
                    $end = date('H:i:s', strtotime($show->end_date));
                    if(in_array($today, $repeat) && $nowtime >= $start && $nowtime <= $end)
                            return $show;
                }
            }

            return null;
        }

}