<?php

namespace common\models;

use common\models\User;
use common\models\Address;
use common\models\PhoneNumber;
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

			/* @todo - recognize parent and associate, if billing email already exists
			 * 
			 */
			if (!empty($user)) {
				continue;
			}

			$user = new User();
			$user->email = $row['Billing Email Address'];
			$user->password = Yii::$app->security->generateRandomString(8);
			$user->save();

            $auth = Yii::$app->authManager;
        	$auth->assign($auth->getRole(User::ROLE_CUSTOMER), $user->getId());

			$userProfile = new UserProfile();
			$userProfile->user_id = $user->id;
			$userProfile->firstname = $row['Billing First Name'];
			$userProfile->lastname = $row['Billing Last Name'];
			$userProfile->save();

			$student = new Student();
			$student->first_name = $row['First Name'];
			$student->last_name = $row['Last Name'];
			$student->birth_date = $row['Date of Birth'];
			$student->customer_id = $user->id;
			$student->save();

			$address = new Address;
			$address->label = 'billing';

			$cityName = $row['Billing City'];
			$addressName = $row['Billing Address'];
			$pincodeName = $row['Billing Postal Code'];

			$address->address = $addressName;
			$city = City::findOne(['name' => $cityName]);

			if (empty($city)) {
				$city = new Cities;
				$city->name = $row['City'];
				$city->province_id = 1;
				$city->save();
			}

			$address->city_id = $city->id;
			$address->province_id = 1;
			$address->country_id = 1;
			$address->postal_code = $pincodeName;
			$address->save();

			$user->link('addresses', $address);

			if (!empty($row['Billing Home Tel'])) {
				$phoneNumber = $row['Billing Home Tel'];
				$phone = new PhoneNumber;
				$phone->number = $phoneNumber;
				$phone->label_id = PhoneNumber::LABEL_HOME;
				$phone->user_id = $user->id;
				$phone->save();
			}
			if (!empty($row['Billing Work Tel'])) {
				$phoneNumber = $row['Billing Work Tel'];
				$phone = new PhoneNumber;
				$phone->number = $phoneNumber;
				$phone->label_id = PhoneNumber::LABEL_WORK;
				$phone->user_id = $user->id;

				if (!empty($row['Billing Work Tel Ext.'])) {
					$phone->extension = $row['Billing Work Tel Ext.'];
				}

				$phone->save();
			}

			if (!empty($row['Billing Other Tel'])) {
				$phoneNumber = $row['Billing Other Tel'];
				$phone = new PhoneNumber;
				$phone->number = $phoneNumber;
				$phone->label_id = PhoneNumber::LABEL_OTHER;
				$phone->user_id = $user->id;
				$phone->save();
			}
		}
	}
}
