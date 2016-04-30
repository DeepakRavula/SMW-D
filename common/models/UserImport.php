<?php

namespace common\models;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserImport extends Model {

	public $file;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'file' => Yii::t('common', 'File'),
		];
	}

	private function parseCSV() {
		$rows = $fields = [];
		$i = 0;

		ini_set("auto_detect_line_endings", "1");
		$handle = $this->file->readStream();
		if ($handle) {
			while (($row = fgetcsv($handle, 4096)) !== false) {
				if (empty($fields)) {
					$fields = $row;
					continue;
				}
				foreach ($row as $k => $value) {
					$rows[$i][$fields[$k]] = $value;
				}
				$i++;
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
		return $rows;
	}

	public function import() {
		$rows = $this->parseCSV();
		foreach ($rows as $row) {

			$user = User::findOne(['email' => $row['Billing Email Address']]);

			if (!empty($user)) { //user already exists, so skip
				continue;
			}

			$user = new User();
			$user->email = $row['Billing Email Address'];
			$user->save();

			$userProfile = new UserProfile();
			$userProfile->user_id = $user->id;
			$userProfile->firstname = $row['Billing First Name'];
			$userProfile->lastname = $row['Billing Last Name'];
			$userProfile->save();
			$adultUser = false;

			$student = new Student();
			$student->first_name = $row['First Name'];
			$student->last_name = $row['Last Name'];
			$student->birth_date = $row['Date of Birth'];
			$student->customer_id = $user->id;
			$student->save();

			continue;

			$address = new Addresses;
			$address->label = 'billing';

			$cityName = $row['Billing City'];
			$addressName = $row['Billing Address'];
			$pincodeName = $row['Billing Postal Code'];

			$address->address = $addressName;
			$city = Cities::model()->findByAttributes(['name' => $cityName]);

			if (empty($city)) {
				$city = new Cities;
				$city->name = $row['City'];
				$city->province_id = 1;
			}

			$address->city_id = $city->id;
			$address->province_id = 1;
			$address->country_id = 1;
			$address->postal_code = $pincodeName;
			$address->save();

			if (!empty($row['Billing Home Tel'])) {
				$phoneNumber = $row['Billing Home Tel'];
				$phone = new PhoneNumbers;
				$phone->number = $phoneNumber;
				$phone->label_id = Users::LABEL_HOME;
				$phone->user_id = $adultUser ? $student->id : $parent->id;
				$phone->save();
			}
			if (!empty($row['Billing Work Tel'])) {
				$phoneNumber = $row['Billing Work Tel'];
				$phone = new PhoneNumbers;
				$phone->number = $phoneNumber;
				$phone->label_id = Users::LABEL_WORK;
				$phone->user_id = $adultUser ? $student->id : $parent->id;

				if (!empty($row['Billing Work Tel Ext.'])) {
					$phone->extension = $row['Billing Work Tel Ext.'];
				}

				$phone->save();
			}

			if (!empty($row['Billing Other Tel'])) {
				$phoneNumber = $row['Billing Other Tel'];
				$phone = new PhoneNumbers;
				$phone->number = $phoneNumber;
				$phone->label_id = Users::LABEL_OTHER;
				$phone->user_id = $adultUser ? $student->id : $parent->id;
				$phone->save();
			}

			$userAddressAssoc = new UserAddress;
			$userAddressAssoc->user_id = $adultUser ? $student->id : $parent->id;
			$userAddressAssoc->address_id = $address->id;
			$userAddressAssoc->save();
		}
	}

}
