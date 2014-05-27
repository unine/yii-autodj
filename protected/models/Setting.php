<?php

/**
 * This is the model class for table "setting".
 *
 * The followings are the available columns in table 'setting':
 * @property integer $id
 * @property string $mode
 * @property integer $pid
 * @property string $time_zone
 * @property integer $song_repeat
 * @property integer $artist_repeat
 * @property integer $album_repeat
 * @property string $input_type
 * @property string $input_host
 * @property string $input_port
 * @property string $input_user
 * @property string $input_pass
 * @property string $input_mount
 *
 * The followings are the available model relations:
 * @property Playlists $p
 * @property Stream[] $streams
 */
class Setting extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'setting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, song_repeat, artist_repeat, album_repeat', 'numerical', 'integerOnly'=>true),
			array('mode', 'length', 'max'=>50),
			array('time_zone', 'length', 'max'=>128),
			array('input_type', 'length', 'max'=>64),
			array('input_host, input_port, input_user, input_pass, input_mount', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, mode, pid, time_zone, song_repeat, artist_repeat, album_repeat, input_type, input_host, input_port, input_user, input_pass, input_mount', 'safe', 'on'=>'search'),
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
			'p' => array(self::BELONGS_TO, 'Playlists', 'pid'),
			'streams' => array(self::HAS_MANY, 'Stream', 'set_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'mode' => 'Mode',
			'pid' => 'Playlist',
			'time_zone' => 'Time Zone',
			'song_repeat' => 'Song Repeat',
			'artist_repeat' => 'Artist Repeat',
			'album_repeat' => 'Album Repeat',
			'input_type' => 'Type',
			'input_host' => 'Host',
			'input_port' => 'Port',
			'input_user' => 'Username',
			'input_pass' => 'Password',
			'input_mount' => 'Mount Point',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('mode',$this->mode,true);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('time_zone',$this->time_zone,true);
		$criteria->compare('song_repeat',$this->song_repeat);
		$criteria->compare('artist_repeat',$this->artist_repeat);
		$criteria->compare('album_repeat',$this->album_repeat);
		$criteria->compare('input_type',$this->input_type,true);
		$criteria->compare('input_host',$this->input_host,true);
		$criteria->compare('input_port',$this->input_port,true);
		$criteria->compare('input_user',$this->input_user,true);
		$criteria->compare('input_pass',$this->input_pass,true);
		$criteria->compare('input_mount',$this->input_mount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Setting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
