<?php

use yii\db\Migration;
use common\models\UserEmail;
use common\models\UserContact;
use common\models\Address;
use common\models\UserAddress;
use common\models\PhoneNumber;
use common\models\UserPhone;

class m171024_071534_user_contact extends Migration
{
    public function up()
    {
		$emails = UserEmail::find()->all();
		foreach ($emails as $email) {
			$userContact = new UserContact();
			$userContact->userId = $email->userId;
			$userContact->isPrimary = $email->isPrimary;
			$userContact->labelId = $email->labelId;
			$userContact->save();
			$email->updateAttributes([
				'userContactId' => $userContact->id
			]);
		}
		$this->dropColumn('user_email', 'userId');
		$this->dropColumn('user_email', 'labelId');
		$this->dropColumn('user_email', 'isPrimary');

		$phones = PhoneNumber::find()->all();
		foreach($phones as $phone) {
			$userContact = new UserContact();
			$userContact->userId = $phone->user_id;
			$userContact->isPrimary = $phone->is_primary;
			$userContact->labelId = $phone->label_id;
			$userContact->save();	

			$userPhone = new UserPhone();
			$userPhone->number = $phone->number;
			$userPhone->extension = $phone->extension;
			$userPhone->userContactId = $userContact->id;
			$userPhone->save();
		}
		$this->dropTable('phone_number');

		$this->delete('label', [
			'id' => 3
		]);
		$this->insert('label', [
			'id' => 3,
			'name' => 'Billing',
			'userAdded' => false
		]);
		$addresses = Address::find()->all();
		foreach ($addresses as $address) {
			$userContact = new UserContact();
			$userContact->userId = $address->userAddress->user_id;
			$userContact->isPrimary = $address->is_primary;
			$userContact->labelId = $address->getLabel();
			$userContact->save();	
			
			$userAddress = new UserAddress();
			$userAddress->userContactId = $userContact->id;
			$userAddress->address = $address->address;
			$userAddress->cityId = $address->city_id;
			$userAddress->provinceId = $address->province_id;
			$userAddress->postalCode = $address->postal_code;
			$userAddress->countryId = $address->country_id;
			$userAddress->save();
			print_r($userAddress->getErrors());die;
				
		}
		$this->dropColumn('user_address', 'user_id');
		$this->dropColumn('user_address', 'address_id');
		$this->dropTable('address');
    }

    public function down()
    {
        echo "m171024_071534_user_contact cannot be reverted.\n";

        return false;
    }
}
