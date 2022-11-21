<?php

namespace backend\models;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use Yii;

/**
 * Create user form.
 */
class UserImportForm extends Model
{
    public $file;

    private $model;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['file', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t('common', 'File'),
        ];
    }

    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->file = $model->file;

        return $this->model;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }

        return $this->model;
    }

    /**
     * Import users.
     *
     * @throws Exception
     */
    public function import()
    {
        if ($this->validate()) {
            $model = $this->getModel();

            return !$model->hasErrors();
        }

        return null;
    }
}
