<?php

namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log_activity".
 *
 * @property integer $id
 * @property string $name
 */
class LogActivity extends \yii\db\ActiveRecord
{
     const TYPE_CREATE = 'create';
     const TYPE_EDIT   = 'edit';
     const TYPE_UPDATE = 'update';
     const TYPE_DELETE = 'delete';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_activity';
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
