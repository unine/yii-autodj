<?php

/**
 * This is the model class for table "stream".
 *
 * The followings are the available columns in table 'stream':
 * @property integer $id
 * @property integer $active
 * @property string $protocol
 * @property string $type
 * @property integer $bitrate
 * @property string $host
 * @property string $port
 * @property string $mount
 * @property string $name
 * @property string $description
 * @property string $url
 * @property integer $set_id
 * @property string $username
 * @property string $password
 * @property string $admin_user
 * @property string $admin_pass
 *
 * The followings are the available model relations:
 * @property Setting $set
 */
class Stream extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'stream';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('host, port, password', 'required'),
			array('active, bitrate, set_id', 'numerical', 'integerOnly'=>true),
			array('protocol, type', 'length', 'max'=>20),
			array('host, name, url, username, password, admin_user, admin_pass', 'length', 'max'=>255),
			array('port', 'length', 'max'=>10),
			array('mount', 'length', 'max'=>64),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, active, protocol, type, bitrate, host, port, mount, name, description, url, set_id, username, password, admin_user, admin_pass', 'safe', 'on'=>'search'),
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
			'set' => array(self::BELONGS_TO, 'Setting', 'set_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'active' => 'Enabled',
			'protocol' => 'Service Type',
			'type' => 'Stream Type',
			'bitrate' => 'Bitrate',
			'host' => 'Host',
			'port' => 'Port',
			'mount' => 'Mount',
			'name' => 'Name',
			'description' => 'Description',
			'url' => 'URL',
			'set_id' => 'Set',
			'username' => 'Username',
			'password' => 'Password',
			'admin_user' => 'Admin User',
			'admin_pass' => 'Admin Password',
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
		$criteria->compare('active',$this->active);
		$criteria->compare('protocol',$this->protocol,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('bitrate',$this->bitrate);
		$criteria->compare('host',$this->host,true);
		$criteria->compare('port',$this->port,true);
		$criteria->compare('mount',$this->mount,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('set_id',$this->set_id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('admin_user',$this->admin_user,true);
		$criteria->compare('admin_pass',$this->admin_pass,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Stream the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
