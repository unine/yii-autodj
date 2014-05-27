<?php
Yii::import('application.vendor.*');
require_once('getid3/getid3.php');
require_once('getid3/write.php');

class LibraryController extends Controller
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
                        'actions'=>array('list','import','addtag','removetag','delete','view','update'), 
                        'users'=>array('@'), 
                    ), 
                    array('deny',  // deny all users 
                        'users'=>array('*'), 
                    ), 
            ); 
        } 

    public function actionList()
    {
        $model=new Files('search'); 
        $model->unsetAttributes();
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);
        }
        
        if(isset($_GET['Files'])) 
            $model->attributes=$_GET['Files']; 

        $tags = CHtml::listData(Tags::model()->findAll(array('order'=>'name')), 'id', 'name');
        $this->render('list',array( 
            'model'=>$model,
            'tags'=>$tags
        ));
    }

    public function actionImport()
    {
        $status = shell_exec('php '.CONSOLE.' import');
        if($status == "Done!")
            echo 'Complete';
    }

    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        $criteria=new CDbCriteria;
        $criteria->order='name ASC';
        $taglist = CHtml::listData(Tags::model()->findAll($criteria), 'id', 'name');

        $selected=array();
        foreach ($model->tags0 as $key => $item) {
            $selected[]=$item->id;
        }

        if(isset($_POST['Files']))
        {
            $model->attributes=$_POST['Files'];

            Yii::app()->db->createCommand('DELETE FROM file_tag WHERE fid = '.$model->id.'')->execute();
            if(isset($_POST['tags_list']))
            {
                $tags = $_POST['tags_list'];
                if(count($tags) > 0)
                {
                    foreach ($tags as $item)
                    {
                        $model->tags0=$item;
                        $model->saveRelated(array('tags0'=> array('append' => true)));
                    }
                }
            }

            if($model->save())
            {
                $Filename = LIBRARY.$model->filepath;

                $tagwriter = new getid3_writetags;
                $tagwriter->filename       = $Filename;
                $tagwriter->tagformats     = array('id3v2.3','id3v2.4');
                $tagwriter->overwrite_tags = true;
                $tagwriter->tag_encoding   = 'UTF-8';

                $TagData['title'][] = trim($model->track_title);
                $TagData['artist'][] = trim($model->artist_name);
                $TagData['album'][] = trim($model->album_title);

                $tagwriter->tag_data = $TagData;
                if ($tagwriter->WriteTags())
                {
                    $this->redirect(array('list'));
                }
                else
                {
                    $tag_error = '';
                    foreach ($tagwriter->errors as $i => $error) {
                        if($i != 0)
                            $tag_error .= '<br />';
                        $tag_error .= $error;
                    }
                    Yii::app()->user->setFlash('error', "<strong>".$tag_error."</strong>");
                    $this->refresh();
                }
            }
        }

        $this->render('update',array(
            'model'=>$model,
            'tagslist'=>$taglist,
            'selected'=>$selected,
        ));
    }

    public function actionUpload()
    {
        $model=new Files('search'); 
            $model->unsetAttributes();  // clear any default values 
            if(isset($_GET['Files'])) 
                $model->attributes=$_GET['Files']; 

            $this->render('upload',array( 
                'model'=>$model, 
            ));
    }

    public function actionUploadFiles()
    {
        Yii::import("ext.EAjaxUpload.qqFileUploader");
        
        $folder='files/';// folder for uploaded files
        $allowedExtensions = array("mp3");
        $sizeLimit = 20 * 1024 * 1024;
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($folder);
        if(array_key_exists('success', $result) && $result['success'] === true)
        {
            $fileSysname=$result['filename'];//GETTING FILE SYSTEM NAME
            $fileName = $result['filerealname'];//GETTING FILE NAME
                        
            $filePath = $folder.$fileSysname;
            $songInfo = new getID3;
            $ThisSongInfo = $songInfo->analyze($filePath);
            
            $artist = $title = $album = '';
            if(array_key_exists('tags', $ThisSongInfo) && array_key_exists('id3v2', $ThisSongInfo['tags']))
            {
                if(array_key_exists('artist', $ThisSongInfo['tags']['id3v2']))
                    $artist = trim($ThisSongInfo['tags']['id3v2']['artist'][0]);
                if(array_key_exists('title', $ThisSongInfo['tags']['id3v2']))
                    $title = trim($ThisSongInfo['tags']['id3v2']['title'][0]);
                if(array_key_exists('album', $ThisSongInfo['tags']['id3v2']))
                    $album = trim($ThisSongInfo['tags']['id3v2']['album'][0]);
            }

            $length = trim($ThisSongInfo['playtime_seconds']);

            $Filename = LIBRARY.DIRECTORY_SEPARATOR.$fileSysname;

            $tagwriter = new getid3_writetags;
            $tagwriter->filename       = $Filename;
            $tagwriter->tagformats     = array('id3v2.3','id3v2.4');
            $tagwriter->overwrite_tags = true;
            $tagwriter->tag_encoding   = 'UTF-8';

            $TagData['title'][] = $title;
            $TagData['artist'][] = $artist;
            $TagData['album'][] = $album;
            
            $tagwriter->tag_data = $TagData;
            $tagwriter->WriteTags();

            $files=new Files;
            $files->filepath = $fileSysname;
            $files->filename = $fileName;
            $files->track_title = $title;
            $files->artist_name = $artist;
            $files->album_title = $album;
            $files->duration = $length;
            $files->save();
        }
        $return = htmlspecialchars(json_encode($result), ENT_NOQUOTES);

        echo $return;
    }

    public function actionDelete($id)
    {
        $model=$this->loadModel($id);
                
        $filename=LIBRARY.'/'.$model->filepath;
        if(unlink($filename))
        {
            $model->delete();
        }else{
            throw new CHttpException(400,'Error can not delete this file. Please try agian later or contact support team.');
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('list'));
    }

    public function actionAddtag()
        {
                $files = $_POST['files_select'];
                $tag = $_POST['tag_select'];

                if(count($files)>0)
                {
                        foreach($files as $item)
                        {
                                $model=$this->loadModel($item);
                                $model->tags0=$tag;
                                $count=Yii::app()->db->createCommand('SELECT COUNT(*) FROM file_tag WHERE fid = '.$model->id.' AND tid = '.$model->tags0.'')->queryScalar();

                                if(!$count) {
                                        if($model->saveRelated(array('tags0'=>array('append'=>true))))
                                                echo 'ok';
                                        else
                                                throw new Exception("Sorry",500);
                                }else { echo 'ok'; }
                        }
                }
        }

        public function actionRemovetag()
        {
                $files = $_POST['files_select'];
                $tag = $_POST['tag_select'];
                
                if(count($files)>0)
                {
                        foreach($files as $item)
                        {
                                $count=Yii::app()->db->createCommand('SELECT COUNT(*) FROM file_tag WHERE fid = '.$item.' AND tid = '.$tag.'')->queryScalar();
                                
                                if($count)
                                        Yii::app()->db->createCommand('DELETE FROM file_tag WHERE fid = '.$item.' AND tid = '.$tag.'')->execute();
                        }
                }
                echo 'ok'; 
        }

        protected function gridTags($data,$row)
        {
                $model=$this->loadModel($data->id);
                
                return $this->renderPartial('_tags',array('model'=>$model),true); 
        }

    public function loadModel($id) 
        { 
            $model=Files::model()->findByPk($id); 
            if($model===null) 
                    throw new CHttpException(404,'The requested page does not exist.'); 
            return $model; 
        }
}