<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[Invoice]].
 *
 * @see Invoice
 */
class InvoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Invoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Invoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
	public function location($locationId) {
		$this->joinWith(['lineItems' => function($query) use($locationId) {
			$query->joinWith(['lesson'=> function($query) use($locationId){ 
				$query->joinWith(['enrolment' => function($query) use($locationId) {
					$query->andFilterWhere(['enrolment.location_id' => $locationId]);
				}]);
			}]);
		}]);
		return $this;
	}

	public function student($id) {
		$this->joinWith(['lineItems li'=>function($query) use($id){
			$query->joinWith(['lesson l'=>function($query) use($id){	
				$query->joinWith(['enrolment e'=>function($query) use($id){
					$query->joinWith('student s')
						->where(['s.customer_id' => $id]);
					}]);
				}]);
			}]);
		return $this;
	}
}
