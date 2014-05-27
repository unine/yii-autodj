<?php

/**
 * This is the model class for table "shows".
 *
 * The followings are the available columns in table 'shows':
 * @property integer $id
 * @property string $name
 * @property string $text_color
 * @property string $bg_color
 * @property string $start_date
 * @property string $end_date
 * @property string $repeat_frq
 * @property string $repeat_end
 * @property string $exclude
 * @property integer $loop
 * @property integer $pid
 *
 * The followings are the available model relations:
 * @property QueueList[] $queueLists
 * @property Playlists $p
 */
class Shows extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shows';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('loop, pid', 'numerical', 'integerOnly'=>true),
			array('name, bg_color', 'length', 'max'=>255),
			array('text_color', 'length', 'max'=>7),
			array('start_date, end_date, repeat_frq, repeat_end, exclude', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, text_color, bg_color, start_date, end_date, repeat_frq, repeat_end, exclude, loop, pid', 'safe', 'on'=>'search'),
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
			'queueLists' => array(self::HAS_MANY, 'QueueList', 'sid'),
			'p' => array(self::BELONGS_TO, 'Playlists', 'pid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'text_color' => 'Text Color',
			'bg_color' => 'Bg Color',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'repeat_frq' => 'Repeat Frq',
			'repeat_end' => 'Repeat End',
			'exclude' => 'Exclude',
			'loop' => 'Loop',
			'pid' => 'Pid',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('text_color',$this->text_color,true);
		$criteria->compare('bg_color',$this->bg_color,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('repeat_frq',$this->repeat_frq,true);
		$criteria->compare('repeat_end',$this->repeat_end,true);
		$criteria->compare('exclude',$this->exclude,true);
		$criteria->compare('loop',$this->loop);
		$criteria->compare('pid',$this->pid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Shows the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
