<?php

class PlaylistsController extends Controller
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
                'actions'=>array('list','create','update','delete'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionCreate($string='')
    {
        $model=new Playlists;

        $tags=Tags::model()->findAll();

        $criteria=new CDbCriteria;
        $criteria->with=array('tags0');
        if( strlen( $string ) > 0 )
        {
            $criteria->together=true;
            $criteria->condition='(t.track_title like "%'.$string.'%" or t.artist_name like "%'.$string.'%" or t.filename like "%'.$string.'%" or tags0.name like "%'.$string.'%")';
        }
        $files=new CActiveDataProvider('Files',array('criteria'=>$criteria,'pagination'=>array('PageSize'=>25)));
        $newitems = array();

        if(isset($_POST['Playlists']))
        {
            $model->attributes=$_POST['Playlists'];
            if(isset($_POST['itemlist']))
                            $model->items=serialize($_POST['itemlist']);
                    else
                        $model->items=null;

            if($model->save())
                $this->redirect(array('list'));
        }

        $this->render('form',array(
            'model'=>$model,
            'itemlist'=>$newitems,
            'tags'=>$tags,
            'files'=>$files,
        ));
    }

    public function actionUpdate($id,$string='')
    {
        $model=$this->loadModel($id);

   	 $tags=Tags::model()->findAll();
         $criteria=new CDbCriteria;
         $criteria->with=array('tags0');
         if( strlen( $string ) > 0 )
         {
        	 $criteria->together=true;
                 $criteria->condition='(t.track_title like "%'.$string.'%" or t.artist_name like "%'.$string.'%" or t.filename like "%'.$string.'%" or tags0.name like "%'.$string.'%")';
       	 }
        $files=new CActiveDataProvider('Files',array('criteria'=>$criteria,'pagination'=>array('PageSize'=>25)));
        $newitems = array();
        if($model->items != '')
            $items = unserialize($model->items);

        if (!empty($items))
        {
                    foreach ($items as $key => $item) {
                        $source=explode('-', $item);
                        $type=$source[0];
                        $valueID=$source[1];
                        switch ($type) {
                            case 'tags':
                                $tag=Tags::model()->findByPk($valueID);
				if($tag != null)
				{
					$newitem['name']=$tag->name;
                                	$newitem['style']='style="color:'.$tag->text_color.';background-color:'.$tag->bg_color.';border-left:none;border-right:none;"';
                                	$newitem['itemId']='tags-'.$tag->id;
                                	$newitem['type']='tags-type';
				}
				//else
				//{
				//	$newitem['name']=$newitem['style']=$newitem['itemId']=$newitem['type']='';
				//}
                                break;
                            case 'song':
                                $track=Files::model()->findByPk($valueID);
                                if($track)
                                {
                                    $artist=trim($track->artist_name);
                                    $songname=trim($track->track_title);
                                    if($songname == '')
                                        $songname = 'unknow song';

                                    if($artist != '')
                                        $songname .= ' - '.$artist;

                                    $newitem['name']=$songname;
                                    $newitem['style']='';
                                    $newitem['itemId']='song-'.$track->id;
                                    $newitem['type']='song-type';
                                }
                                break;
                        }
                        if(isset($newitem['itemId']))
                    $newitems[]=$newitem;
                    }       
        }
    
        if(isset($_POST['Playlists']))
        {
            $model->attributes=$_POST['Playlists'];
            if(isset($_POST['itemlist']))
                            $model->items=serialize($_POST['itemlist']);
                    else
                        $model->items=null;
            
            if($model->save())
                $this->redirect(array('list'));
        }

        $this->render('form',array(
            'model'=>$model,
            'itemlist'=>$newitems,
            'tags'=>$tags,
            'files'=>$files,
        ));
    }

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionList()
    {
        $model=new Playlists('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Playlists']))
            $model->attributes=$_GET['Playlists'];

        $this->render('list',array(
            'model'=>$model,
        ));
    }

    public function loadModel($id)
    {
        $model=Playlists::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='playlists-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    protected function gridTags($data,$row)
    {
        $model=Files::model()->findByPk($data->id);

        return $this->renderPartial('_tags',array('model'=>$model),true); 
    }
}
