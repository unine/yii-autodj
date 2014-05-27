<?php

/**
 * This is the model class for table "files".
 *
 * The followings are the available columns in table 'files':
 * @property integer $id
 * @property string $filepath
 * @property string $filename
 * @property string $track_title
 * @property string $artist_name
 * @property string $album_title
 * @property double $duration
 * @property integer $count_played
 * @property integer $in_queued
 * @property string $last_played
 * @property string $modified_time
 *
 * The followings are the available model relations:
 * @property tags0[] $tags0
 * @property History[] $histories
 * @property QueueList[] $queueLists
 */
class Files extends CActiveRecord
{
    public $filter_tags;
    
    public function tableName()
    {
        return 'files';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('filepath, filename', 'required'),
            array('count_played, in_queued', 'numerical', 'integerOnly'=>true),
            array('duration', 'numerical'),
            array('track_title, artist_name, album_title', 'length', 'max'=>512),
            array('last_played, modified_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, filepath, filename, track_title, artist_name, album_title, duration, count_played, in_queued, last_played, modified_time', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'tags0' => array(self::MANY_MANY, 'Tags', 'file_tag(fid, tid)'),
            'histories' => array(self::HAS_MANY, 'History', 'fid'),
            'queueLists' => array(self::HAS_MANY, 'QueueList', 'fid'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'filepath' => 'Filepath',
            'filename' => 'Filename',
            'track_title' => 'Title',
            'artist_name' => 'Artist',
            'album_title' => 'Album',
            'duration' => 'Duration',
            'count_played' => 'Count Played',
            'in_queued' => 'In Queued',
            'last_played' => 'Last Played',
            'modified_time' => 'Modified Time',
        );
    }

    public function search()
    {
        $criteria=new CDbCriteria;
        if($this->filter_tags!='')
        {
            $criteria->with = array('tags0');
            $criteria->together=true;
            $criteria->compare('tags0.name', $this->filter_tags, true);                        
        }

        $criteria->compare('id',$this->id);
        $criteria->compare('filepath',$this->filepath,true);
        $criteria->compare('filename',$this->filename,true);
        $criteria->compare('track_title',$this->track_title,true);
        $criteria->compare('artist_name',$this->artist_name,true);
        $criteria->compare('album_title',$this->album_title,true);
        $criteria->compare('duration',$this->duration);
        $criteria->compare('count_played',$this->count_played);
        $criteria->compare('in_queued',$this->in_queued);
        $criteria->compare('last_played',$this->last_played,true);
        $criteria->compare('modified_time',$this->modified_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                    'pageSize'=>Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Files the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors(){
        return array('ESaveRelatedBehavior' => array(
            'class' => 'application.components.ESaveRelatedBehavior')
         );
    }
}
