<?php

namespace common\models\query;
use common\models\Lesson;
/**
 * This is the ActiveQuery class for [[\common\models\GroupLesson]].
 *
 * @see \common\models\GroupLesson
 */
class GroupLessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\GroupLesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\GroupLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function completed() {
        $this->andFilterWhere(['<=', 'date', (new \DateTime())->format('Y-m-d')])
             ->andFilterWhere(['not',['status' => Lesson::STATUS_CANCELED]]);
		
		return $this;
	}

	public function scheduled() {
		$this->andFilterWhere(['>', 'date', (new \DateTime())->format('Y-m-d')])
             ->andFilterWhere(['not',['status' => Lesson::STATUS_CANCELED]]);
		
		return $this;
	}

	public function location($locationId) {
		$this->joinWith(['groupCourse gc' => function($query) use($locationId){
			$query->andFilterWhere(['gc.location_id' => $locationId]);
		}]);
		
		return $this;
	}
}
