<?php

namespace common\models;

use yii\base\Model;
use Yii;
/**
 * Create user form.
 */
class UserImport extends Model
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t('common', 'File'),
        ];
    }

    private function parseCSV()
    {
        $rows = $fields = [];
        $i = 0;
        ini_set('auto_detect_line_endings', '1');
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
                ++$i;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        return $rows;
    }

    public function import()
    {
        $rows = $this->parseCSV();
        $errors = [];
        $successCount = 0;
        $studentCount = 0;
        $customerCount = 0;
        set_time_limit(1000);
        foreach ($rows as $i => $row) {
            if (empty($row['Billing Home Tel'])) {
                continue;
            }

            $user = User::find()
                ->joinWith(['phoneNumber' => function ($query) use ($row) {
                    $query->where(['number' => $row['Billing Home Tel']]);
                }])
                ->one();

            if (!empty($user)) {
                $student = new Student();
                $student->first_name = $row['First Name'];
                $student->last_name = $row['Last Name'];
				if(!empty($row['Date of Birth'])) {
	                $birthDate = \DateTime::createFromFormat('n/j/Y', $row['Date of Birth']);
    	            $student->birth_date = $birthDate->format('d-m-Y');
				}
                $student->customer_id = $user->id;
                $student->status = Student::STATUS_ACTIVE;

                if (!$student->validate(['birth_date'])) {
                    $student->birth_date = null;
                    $errors[] = 'Error on Line '.($i + 2).': Incorrect Date format. Skipping DOB for student named, "'.$student->first_name.'"';
                }

                if ($student->save()) {
					$this->StudentCsv($row, $student);
					
                    ++$studentCount;
                    continue;
                }
            }

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $user = new User();
                $user->email = $row['Email Address'];
                $user->password = Yii::$app->security->generateRandomString(8);
                $user->status = User::STATUS_ACTIVE;
                if (!$user->validate(['email'])) {
                    $user->email = null;
                    $errors[] = 'Error on Line '.($i + 2).': Invalid Email address. Skipping email address for customer named, "'.$row['Billing First Name'].'"';
                }
                if ($user->save()) {
                    ++$customerCount;
                }
                $userProfile = new UserProfile();
                $userProfile->user_id = $user->id;
                $userProfile->firstname = $row['Billing First Name'];
                $userProfile->lastname = $row['Billing Last Name'];
                $userProfile->save();

                $userLocationModel = new UserLocation();
                $userLocationModel->user_id = $user->id;
                $userLocationModel->location_id = Yii::$app->session->get('location_id');
                $userLocationModel->save();

                $auth = Yii::$app->authManager;
                $auth->assign($auth->getRole(User::ROLE_CUSTOMER), $user->getId());

                $student = new Student();
                $student->first_name = $row['First Name'];
                $student->last_name = $row['Last Name'];
				if(!empty($row['Date of Birth'])) {
                	$birthDate = \DateTime::createFromFormat('n/j/Y', $row['Date of Birth']);
	                $student->birth_date = $birthDate->format('d-m-Y');
				}
                $student->customer_id = $user->id;
                $student->status = Student::STATUS_ACTIVE;

                if (!$student->validate(['birth_date'])) {
                    $student->birth_date = null;
                    $errors[] = 'Error on Line '.($i + 2).': Incorrect Date format. Skipping DOB for student named, "'.$student->first_name.'"';
                }

                if ($student->save()) {
					$this->StudentCsv($row, $student);
					
                    ++$studentCount;
                }

                $address = new Address();
                $address->label = 'Billing';

                $cityName = $row['Billing City'];
                $addressName = $row['Billing Address'];
                $pincodeName = $row['Billing Postal Code'];

                $address->address = $addressName;
                $city = City::findOne(['name' => $cityName]);

                if (empty($city)) {
                    $city = new City();
                    $city->name = $row['City'];
                    $city->province_id = 1;
                    $city->save();
                }

                $address->city_id = $city->id;
                $address->province_id = 1;
                $address->country_id = 1;
                $address->postal_code = $pincodeName;
                if (!$address->validate(['address'])) {
                    $address->address = null;
                    $errors[] = 'Error on Line '.($i + 2).': Address is missing. Skipping  address for customer named, "'.$row['Billing First Name'].'"';
                }
                $address->save();

                $user->link('addresses', $address);

                if (!empty($row['Billing Home Tel'])) {
                    $phoneNumber = $row['Billing Home Tel'];
                    $phone = new PhoneNumber();
                    $phone->number = $phoneNumber;
                    $phone->label_id = PhoneNumber::LABEL_HOME;
                    $phone->user_id = $user->id;
                    $phone->save();
                }
                if (!empty($row['Billing Work Tel'])) {
                    $phoneNumber = $row['Billing Work Tel'];
                    $phone = new PhoneNumber();
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
                    $phone = new PhoneNumber();
                    $phone->number = $phoneNumber;
                    $phone->label_id = PhoneNumber::LABEL_OTHER;
                    $phone->user_id = $user->id;

                    if (!empty($row['Billing Other Tel Ext.'])) {
                        $phone->extension = $row['Billing Other Tel Ext.'];
                    }

                    $phone->save();
                }

                $transaction->commit();
                ++$successCount;
            } catch (\Exception $e) {
                $transaction->rollBack();
                $errors[] = 'Error on Line '.($i + 2).': '.$e->getMessage();
            }
        }

        return [
            'successCount' => $successCount,
            'studentCount' => $studentCount,
            'customerCount' => $customerCount,
            'errors' => $errors,
            'totalRows' => count($rows),
        ];
    }

	public function StudentCsv($row, $student) 
	{
		$studentCsv = new StudentCsv();
		$studentCsv->studentId = $student->id;
		$studentCsv->firstName = $row['First Name']; 
		$studentCsv->lastName = $row['Last Name'];
		$studentCsv->email = $row['Email Address'];
		$studentCsv->address = $row['Address'];
		$studentCsv->city = $row['City'];
		$studentCsv->province = $row['Province'];
		$studentCsv->postalCode = $row['Postal Code'];
		$studentCsv->country = $row['Country'];
		$studentCsv->homeTel = $row['Home Tel'];
		$studentCsv->otherTel = $row['Other Tel'];
		if(!empty($row['Date of Birth'])) {
			$birthDate = \DateTime::createFromFormat('n/j/Y', $row['Date of Birth']);
			$studentCsv->birthDate = $birthDate->format('Y-m-d');
		}
		$studentCsv->billingFirstName = $row['Billing First Name'];
		$studentCsv->billingLastName = $row['Billing Last Name'];
		$studentCsv->billingEmail = $row['Billing Email Address'];
		$studentCsv->billingAddress = $row['Billing Address'];
		$studentCsv->billingCity = $row['Billing City'];
		$studentCsv->billingProvince = $row['Billing Province'];
		$studentCsv->billingPostalCode = $row['Billing Postal Code'];
		$studentCsv->billingCountry = $row['Billing Country'];
		$studentCsv->billingHomeTel = $row['Billing Home Tel'];
		$studentCsv->billingOtherTel = $row['Billing Other Tel'];
		$studentCsv->billingWorkTel = $row['Billing Work Tel'];
		$studentCsv->billingWorkTelExt = $row['Billing Work Tel Ext.'];
		$studentCsv->save();
	}
}
