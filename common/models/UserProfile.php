<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $user_id
 * @property int $locale
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $picture
 * @property string $avatar
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property int $gender
 * @property User $user
 */
class UserProfile extends ActiveRecord
{
    /**
     * @var
     */
    const EVENT_USER_CREATE='userCreate';
    public $picture;
    public $email;
    public $loggedUser;
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname'], 'required'],
            [['user_id'], 'integer'],
            [['firstname', 'lastname', 'avatar_path', 'avatar_base_url'], 'string', 'max' => 255],
            [['firstname', 'lastname'], 'trim'],
            ['picture', 'safe'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('backend', 'This email has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
                },
            ],
            [['birthDate'], 'date', 'format' => 'M d, Y', 'message' => 'Date format should be in M d, Y format'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('common', 'User ID'),
            'firstname' => Yii::t('common', 'First Name'),
            'lastname' => Yii::t('common', 'Last Name'),
            'picture' => Yii::t('common', 'Picture'),
            'birthDate' => 'Birth Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['teacherId' => 'user_id']);
    }

    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['teacherId' => 'user_id']);
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        if ($this->firstname || $this->lastname) {
            return implode(' ', [$this->firstname, $this->lastname]);
        }

        return null;
    }

    /**
     * @param null $default
     *
     * @return bool|null|string
     */
    public function getAvatar($default = null)
    {
        return $this->avatar_path
            ? Yii::getAlias($this->avatar_base_url.'/'.$this->avatar_path)
            : $default;
    }
    
    public function beforeSave($insert)
    {
        if (!empty($this->birthDate)) {
            $birthDate = new \DateTime($this->birthDate);
            $this->birthDate = $birthDate->format('Y-m-d');
        }
        return parent::beforeSave($insert);
    }

    public function setModel($model)
    {
        $this->firstname = $model->firstname;
        $this->lastname = $model->lastname;
        return $this;
    }
}
