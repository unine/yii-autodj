<?php

class ScheduleController extends Controller
{
	public $defaultAction = 'list';

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
	                	'actions'=>array('calendar','shows','create','update','resize','delete'), 
	                	'users'=>array('@'), 
	            	), 
	            	array('deny',  // deny all users 
	                	'users'=>array('*'), 
	            	), 
	        ); 
    	} 

	public function actionCalendar()
	{
		$this->render('calendar');
	}

	public function actionCreate() 
	{ 
		$model=new Shows; 

		$model->start_date = (isset($_GET['start']))?date('Y-m-d H:i:s',$_GET['start']):date('Y-m-d H:i:s');
		$model->end_date = (isset($_GET['end']))?date('Y-m-d H:i:s',$_GET['end']):date('Y-m-d H:i:s');
		$playlists=CHtml::listdata(Playlists::model()->findAll(),'id','name');

		if(isset($_POST['Shows'])) 
	        {
	            	$model->attributes=$_POST['Shows'];
	            	if(!empty($model->repeat_frq)) 
                                $model->repeat_frq=serialize($model->repeat_frq); 
                        else
                                $model->repeat_frq=null; 
  
                        if(empty($model->repeat_end)) 
                                $model->repeat_end=null;

	            	if($model->save()) 
	                	$this->redirect(array('calendar')); 
	        }

	        $this->render('form',array( 
	            'model'=>$model,
	            'playlists'=>$playlists
	        )); 
	}

	public function actionUpdate($id)
        {
                $model=Shows::model()->findByPk($id);
                if($model->repeat_frq != null)
                        $model->repeat_frq = unserialize($model->repeat_frq);

                $playlists=CHtml::listdata(Playlists::model()->findAll(),'id','name');

                if(isset($_POST['Shows']))
                {
                        $model->attributes=$_POST['Shows'];
                        if(!empty($model->repeat_frq))
                                $model->repeat_frq=serialize($model->repeat_frq);
                        else
                                $model->repeat_frq=null;

                        if(empty($model->repeat_end))
                                $model->repeat_end=null;

                        if($model->save())
                                $this->redirect(array('calendar'));
                }

                $this->render('form',array(
                        'model'=>$model,
                        'playlists'=>$playlists
                ));
        }

        public function actionResize($id)
        {
                $model=Shows::model()->findByPk($id);

                $model->start_date = $_POST['start_date'];
                $model->end_date = $_POST['end_date'];

                if($model->save())
                        echo 'ok';
                else
                        throw new Exception("Sorry",500);
        }

        public function actionDelete($id)
        {
        	Shows::model()->findByPk($id)->delete();

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('calendar'));
        }
	public function actionShows()
	{
		$start = date('Y-m-d 00:00:00', $_GET['start']);
		$end = date('Y-m-d 00:00:00', $_GET['end']);
		$shows = array();

		// one time show
		$criteria = new CDbCriteria;
		$criteria->condition = 'repeat_frq is null AND 
					start_date >= :start AND 
					start_date <= :end';
		$criteria->params = array(':start'=>$start,':end'=>$end);
		$models = Shows::model()->findAll($criteria);
		if(!empty($models))
		{
			foreach ($models as $item) {
				$shows[] = $this->convertShow($item,$item->start_date,$item->end_date);
			}
		}

		// repeat show
		$criteria = new CDbCriteria;
		$criteria->condition = 'repeat_frq is not null';
		$models = Shows::model()->findAll($criteria);
		if(!empty($models))
		{
			foreach ($models as $item) {
				$repeat = unserialize($item->repeat_frq);
				$start_time = explode(' ', $item->start_date);
				$end_time = explode(' ', $item->end_date);
				foreach ($repeat as $re_date) 
				{
					$endTmp = $end;
					switch($re_date)
					{
						case "s":
							$s = 'next sunday';
							break;
                                                case "m":
                                                        $s = 'next monday';
                                                        break;
                                                case "tu":
                                                        $s = 'next tuesday';
                                                        break;
                                                case "w":
                                                        $s = 'next wednesday';
                                                        break;
                                                case "th":
                                                        $s = 'next thursday';
                                                        break;
                                                case "f":
                                                        $s = 'next friday';
                                                        break;
                                                case "sa":
                                                        $s = 'next saturday';
                                                        break;
                                        }
                                        if($item->repeat_end !== null && $item->repeat_end <= $end)
                                        	$endTmp = $item->repeat_end;
                                        $day_repeat_array = $this->repeatdate($item, $start_time, $end_time, $start, $endTmp, $s);
                                        $shows = array_merge($day_repeat_array,$shows);
				}
				$shows[] = $this->convertShow($item,$item->start_date,$item->end_date);
			}
		}
		echo CJSON::encode($shows);
	}

        protected function repeatdate($model,$start,$end,$rang_start,$rang_end,$s)
        {
                $date_repeat = array();
                $rows = array();
                $dateTmp = $start[0];
                while($dateTmp <= $rang_end)
                {
                    	$date_repeat['start_date'] = date('Y-m-d '.$start[1],strtotime($dateTmp));
                    	$date_repeat['end_date'] = date('Y-m-d '.$end[1],strtotime($dateTmp));
                	
                	if($dateTmp > $start[0])
                    		$rows[] = $this->convertShow($model,$date_repeat['start_date'],$date_repeat['end_date']);
                    	$dateTmp = date('Y-m-d '.$start[1],strtotime($s, strtotime($dateTmp)));
                }
                
                return $rows;
        }

	protected function convertShow($model,$start,$end)
        {
                $row = array();
                $row['id'] = CHtml::value($model, 'id');
                $row['title'] = CHtml::value($model, 'name');
                $row['allDay'] = false;
                $row['start'] = $start;
                $row['end'] = $end;
                $row['color'] = CHtml::value($model, 'bg_color');
                $row['textColor'] = CHtml::value($model, 'text_color');

                return $row;
        }

}