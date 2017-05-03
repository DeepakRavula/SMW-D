<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "exam_result".
 *
 * @property string $id
 * @property string $studentId
 * @property string $date
 * @property integer $mark
 * @property integer $level
 * @property string $program
 * @property string $type
 * @property string $teacherId
 */
class ExamResult extends \yii\db\ActiveRecord
{
    
    const EVENT_CREATE = 'event-create';
    const EVENT_UPDATE = 'event-update';
    const EVENT_DELETE = 'event-delete';
    /**
     * @inheritdoc
     */
    public $userName;
    public static function tableName()
    {
        return 'exam_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mark', 'level', 'programId', 'teacherId'], 'required'],
            [['studentId', 'mark', 'teacherId', 'programId'], 'integer'],
            [['date'], 'safe'],
            [['type'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'date' => 'Exam Date',
            'mark' => 'Mark',
            'level' => 'Level',
            'programId' => 'Program',
            'type' => 'Type',
            'teacherId' => 'Teacher',
        ];
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId']);
    }
	

	public function beforeSave($insert)
	{
		$this->date = (new \DateTime($this->date))->format('Y-m-d H:i:s');
		
		return parent::beforeSave($insert);
	}

        
        
        public function afterSave($insert, $changedAttributes) {
		if($insert) {
			$this->trigger(self::EVENT_CREATE);
		} 
                
                if($changedAttributes) {
			$this->trigger(self::EVENT_UPDATE);
		} 
		return parent::afterSave($insert, $changedAttributes);
	}
        
        
        
        
        
        }
