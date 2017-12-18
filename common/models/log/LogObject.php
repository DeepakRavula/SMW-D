<?php
namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log_object".
 *
 * @property integer $id
 * @property string $name
 */
class LogObject extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_object';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['name'], 'required'],
                [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
