<?php

namespace common\models;

use League\Csv\Reader;
use yii\base\Model;
use Yii;

/**
 * Create user form.
 */
class UserImport extends Model
{
    public $file;
    public $path;
    
    const DEFAULT_OPENING_BALANCE = 0;
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
        $fileHandle = fopen(Yii::getAlias('@storage') . '/web/source/' . $this->path, "r");
        $csvFixed = [];
        while (!feof($fileHandle)) {
            $line = fgets($fileHandle);
            if (preg_match_all('/(?<!,)"(?!,)/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                $newLine = $line;
                foreach ($matches[0] as $match) {
                    if ($match !== current($matches[0]) && $match !== end($matches[0])) {
                        $newLine = substr_replace($line, '"', $match[1], 0);
                    }
                }
                $csvFixed[] = $newLine;
            }
        }
        fclose($fileHandle);
        unlink(Yii::getAlias('@storage') . '/web/source/' . $this->path);
        $fp = fopen(Yii::getAlias('@storage') . '/web/source/' . $this->path, 'w');
        foreach ($csvFixed as $line) {
            fputs($fp, $line);
        }
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $rows = $this->parseCSV();
        $errors = [];
        $successCount = 0;
        $studentCount = 0;
        $customerCount = 0;
        foreach ($rows as $i => $row) {
            if (empty($row['Billing Home Tel'])) {
                continue;
            }

            $user = User::find()
                ->joinWith(['userContacts' => function ($query) use ($row) {
                    $query->joinWith(['phone' => function ($query) use ($row) {
                        $query->andWhere(['number' => $row['Billing Home Tel']]);
                    }]);
                }])
                ->notDeleted()
                ->one();

            if (!empty($user)) {
                $student = new Student();
                $student->first_name = $row['First Name'];
                $student->last_name = $row['Last Name'];
                if (!empty($row['Date of Birth'])) {
                    $birthDate = \DateTime::createFromFormat('d/m/Y', $row['Date of Birth']);
                    $student->birth_date = $birthDate->format('M d,Y');
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
                $user->password = Yii::$app->security->generateRandomString(8);
                $user->status = User::STATUS_ACTIVE;
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
                $userLocationModel->location_id = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                $userLocationModel->save();

                $auth = Yii::$app->authManager;
                $auth->assign($auth->getRole(User::ROLE_CUSTOMER), $user->getId());

                $student = new Student();
                $student->first_name = $row['First Name'];
                $student->last_name = $row['Last Name'];
                if (!empty($row['Date of Birth'])) {
                    $birthDate = \DateTime::createFromFormat('d/m/Y', $row['Date of Birth']);
                    $student->birth_date = $birthDate->format('M d,Y');
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
                if (!empty($row['Email Address'])) {
                    $userEmail = new UserEmail();
                    if (!$userEmail->validate(['email'])) {
                        $errors[] = 'Error on Line '.($i + 2).': Invalid Email address. Skipping email address for customer named, "'.$row['Billing First Name'].'"';
                    } else {
                        $userContact = new UserContact();
                        $userContact->userId = $user->id;
                        $userContact->isPrimary = true;
                        $userContact->labelId = Label::LABEL_HOME;
                        $userContact->save();

                        $userEmail->userContactId = $userContact->id;
                        $userEmail->email = $row['Email Address'];
                        $userEmail->save();
                    }
                }
                
                $cityName = $row['Billing City'];
                $addressName = $row['Billing Address'];
                $pincodeName = $row['Billing Postal Code'];
                
                if (empty($addressName)) {
                    $errors[] = 'Error on Line '.($i + 2).': Address is missing. Skipping  address for customer named, "'.$row['Billing First Name'].'"';
                } else {
                    $city = City::findOne(['name' => $cityName]);

                    if (empty($city)) {
                        $city = new City();
                        $city->name = $row['City'];
                        $city->province_id = 1;
                        $city->save();
                    }
                    $userContact = new UserContact();
                    $userContact->userId = $user->id;
                    $userContact->isPrimary = true;
                    $userContact->labelId = Label::LABEL_HOME;
                    $userContact->save();

                    $address = new UserAddress();
                    $address->userContactId = $userContact->id;
                    $address->address = $addressName;
                    $address->cityId = $city->id;
                    $address->provinceId = 1;
                    $address->countryId = 1;
                    $address->postalCode = $pincodeName;
                    $address->save();
                }
                if (!empty($row['Billing Home Tel'])) {
                    $phoneNumber = $row['Billing Home Tel'];
                    $userContact = new UserContact();
                    $userContact->userId = $user->id;
                    $userContact->isPrimary = true;
                    $userContact->labelId = Label::LABEL_HOME;
                    $userContact->save();
                    $phone = new UserPhone();
                    $phone->number = $phoneNumber;
                    $phone->userContactId = $userContact->id;
                    $phone->save();
                }
                if (!empty($row['Billing Work Tel'])) {
                    $phoneNumber = $row['Billing Work Tel'];
                    $userContact = new UserContact();
                    $userContact->userId = $user->id;
                    $userContact->isPrimary = false;
                    $userContact->labelId = Label::LABEL_WORK;
                    $userContact->save();
                    $phone = new UserPhone();
                    $phone->userContactId = $userContact->id;
                    $phone->number = $phoneNumber;
                    if (!empty($row['Billing Work Tel Ext.'])) {
                        $phone->extension = $row['Billing Work Tel Ext.'];
                    }
                    $phone->save();
                }

                if (!empty($row['Billing Other Tel'])) {
                    $phoneNumber = $row['Billing Other Tel'];
                    $userContact = new UserContact();
                    $userContact->userId = $user->id;
                    $userContact->isPrimary = false;
                    $userContact->labelId = Label::LABEL_WORK;
                    $userContact->save();
                    $phone = new UserPhone();
                    $phone->userContactId = $userContact->id;
                    $phone->number = $phoneNumber;
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
        $response = [
            'successCount' => $successCount,
            'studentCount' => $studentCount,
            'customerCount' => $customerCount,
            'errors' => $errors,
            'totalRows' => count($rows),
        ];
        return $response;
    }
    
    public function StudentCsv($row, $student)
    {
        $studentCsv = new StudentCsv();
        $studentCsv->studentId = $student->id;
	    $studentCsv->openingBalance = self::DEFAULT_OPENING_BALANCE;
        if (!empty($row['Balance To Date'])) {
            $studentCsv->openingBalance = $row['Balance To Date'];
        }
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
        if (!empty($row['Date of Birth'])) {
            $birthDate = \DateTime::createFromFormat('d/m/Y', $row['Date of Birth']);
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
        $studentCsv->notes =json_encode($row['Comments']);
        $studentCsv->save();
    }
}
