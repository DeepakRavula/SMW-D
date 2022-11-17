<?php

use yii\db\Migration;
use common\models\Blog;
use common\models\CalendarEventColor;
use common\models\log\LogHistory;
use common\models\log\Log;
use common\models\log\LogActivity;
use common\models\log\LogObject;
use common\models\log\LogLink;
use common\models\Student;
use common\models\UserAddress;
use common\models\UserProfile;
use common\models\UserEmail;
use common\models\TextTemplate;
use common\models\TaxType;
use common\models\TaxStatus;
use common\models\StudentCsv;
use common\models\City;
use common\models\Country;
use common\models\Province;
use common\models\Program;
use common\models\PaymentMethod;
use common\models\PaymentFrequency;
use common\models\Note;
use common\models\Location;
use common\models\Label;
use common\models\ItemType;
use common\models\ItemCategory;
use common\models\Item;
use common\models\InvoiceLineItem;
use common\models\Invoice;
use common\models\Holiday;
use common\models\ExamResult;
use common\models\ClassroomUnavailability;
use common\models\Classroom;

class m180113_061641_trim_space extends Migration
{
    public function up()
    {
        $blogs = Blog::find()->all();
        foreach ($blogs as $blog) {
            $blog->updateAttributes([
                'title' => trim($blog->title),
                'content' => trim($blog->content)
            ]);
        }
        $calendarEvents = CalendarEventColor::find()->all();
        foreach ($calendarEvents as $calendarEvent) {
            $calendarEvent->updateAttributes([
                'name' => trim($calendarEvent->name),
                'code' => trim($calendarEvent->code),
                'cssClass' => trim($calendarEvent->cssClass)
            ]);
        }
        $cities = City::find()->all();
        foreach ($cities as $city) {
            $city->updateAttributes([
                'name' => trim($city->name),
            ]);
        }
        $classrooms = Classroom::find()->all();
        foreach ($classrooms as $classroom) {
            $classroom->updateAttributes([
                'name' => trim($classroom->name),
            ]);
        }
        $classroomUnavailabilities = ClassroomUnavailability::find()->all();
        foreach ($classroomUnavailabilities as $classroomUnavailability) {
            $classroomUnavailability->updateAttributes([
                'reason' => trim($classroomUnavailability->reason),
            ]);
        }
        $countries = Country::find()->all();
        foreach ($countries as $country) {
            $country->updateAttributes([
                'name' => trim($country->name),
            ]);
        }
        $examResults = ExamResult::find()->all();
        foreach ($examResults as $examResult) {
            $examResult->updateAttributes([
                'level' => trim($examResult->level),
                'type' => trim($examResult->type),
            ]);
        }
        $holidays = Holiday::find()->all();
        foreach ($holidays as $holiday) {
            $holiday->updateAttributes([
                'description' => trim($holiday->description),
            ]);
        }
        $invoices = Invoice::find()->all();
        foreach ($invoices as $invoice) {
            $invoice->updateAttributes([
                'reminderNotes' => trim($invoice->reminderNotes),
            ]);
        }
        $lineItems = InvoiceLineItem::find()->all();
        foreach ($lineItems as $lineItem) {
            $lineItem->updateAttributes([
                'description' => trim($lineItem->description),
            ]);
        }
        $items = Item::find()->all();
        foreach ($items as $item) {
            $item->updateAttributes([
                'description' => trim($item->description),
                'code' => trim($item->code),
            ]);
        }
        $itemCategories = ItemCategory::find()->all();
        foreach ($itemCategories as $itemCategory) {
            $itemCategory->updateAttributes([
                'name' => trim($itemCategory->name),
            ]);
        }
        $itemTypes = ItemType::find()->all();
        foreach ($itemTypes as $itemType) {
            $itemType->updateAttributes([
                'name' => trim($itemType->name),
            ]);
        }
        $labels = Label::find()->all();
        foreach ($labels as $label) {
            $label->updateAttributes([
                'name' => trim($label->name),
            ]);
        }
        $notes = Note::find()->all();
        foreach ($notes as $note) {
            $note->updateAttributes([
                'content' => trim($note->content),
            ]);
        }
        $locations = Location::find()->all();
        foreach ($locations as $location) {
            $location->updateAttributes([
                'name' => trim($location->name),
                'address' => trim($location->address),
                'postal_code' => trim($location->postal_code),
                'slug' => trim($location->slug),
                'email' => trim($location->email),
            ]);
        }
        $paymentMethods = PaymentMethod::find()->all();
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethod->updateAttributes([
                'name' => trim($paymentMethod->name),
            ]);
        }
        $paymentFrequencies = PaymentFrequency::find()->all();
        foreach ($paymentFrequencies as $paymentFrequency) {
            $paymentFrequency->updateAttributes([
                'name' => trim($paymentFrequency->name),
            ]);
        }
        $programs = Program::find()->all();
        foreach ($programs as $program) {
            $program->updateAttributes([
                'name' => trim($program->name),
            ]);
        }
        $provinces = Province::find()->all();
        foreach ($provinces as $province) {
            $province->updateAttributes([
                'name' => trim($province->name),
            ]);
        }
        $students = Student::find()->all();
        foreach ($students as $student) {
            $student->updateAttributes([
                'first_name' => trim($student->first_name),
                'last_name' => trim($student->last_name),
            ]);
        }
        $studentCsvs = StudentCsv::find()->all();
        foreach ($studentCsvs as $studentCsv) {
            $studentCsv->updateAttributes([
                'city' => trim($studentCsv->city),
                'province' => trim($studentCsv->province),
                'postalCode' => trim($studentCsv->postalCode),
                'country' => trim($studentCsv->country),
                'billingFirstName' => trim($studentCsv->billingFirstName),
                'billingLastName' => trim($studentCsv->billingLastName),
                'billingEmail' => trim($studentCsv->billingEmail),
                'billingCity' => trim($studentCsv->billingCity),
                'billingProvince' => trim($studentCsv->billingProvince),
                'billingPostalCode' => trim($studentCsv->billingPostalCode),
                'billingCountry' => trim($studentCsv->billingCountry),
                'homeTel' => trim($studentCsv->homeTel),
                'otherTel' => trim($studentCsv->otherTel),
                'billingHomeTel' => trim($studentCsv->billingHomeTel),
                'billingOtherTel' => trim($studentCsv->billingOtherTel),
                'billingWorkTel' => trim($studentCsv->billingWorkTel),
                'firstName' => trim($studentCsv->firstName),
                'lastName' => trim($studentCsv->lastName),
                'address' => trim($studentCsv->address),
                'billingAddress' => trim($studentCsv->billingAddress),
            ]);
        }
        $taxStatuses = TaxStatus::find()->all();
        foreach ($taxStatuses as $taxStatus) {
            $taxStatus->updateAttributes([
                'name' => trim($taxStatus->name),
            ]);
        }
        $taxTypes = TaxType::find()->all();
        foreach ($taxTypes as $taxType) {
            $taxType->updateAttributes([
                'name' => trim($taxType->name),
            ]);
        }
        $textTemplates = TextTemplate::find()->all();
        foreach ($textTemplates as $textTemplate) {
            $textTemplate->updateAttributes([
                'message' => trim($textTemplate->message),
            ]);
        }
        $userAddresses = UserAddress::find()->all();
        foreach ($userAddresses as $userAddress) {
            $userAddress->updateAttributes([
                'address' => trim($userAddress->address),
                'postalCode' => trim($userAddress->postalCode),
            ]);
        }
        $userEmails = UserEmail::find()->all();
        foreach ($userEmails as $userEmail) {
            $userEmail->updateAttributes([
                'email' => trim($userEmail->email),
            ]);
        }
        $userProfiles = UserProfile::find()->all();
        foreach ($userProfiles as $userProfile) {
            $userProfile->updateAttributes([
                'firstname' => trim($userProfile->firstname),
                'lastname' => trim($userProfile->lastname),
            ]);
        }
        $logs = Log::find()->all();
        foreach ($logs as $log) {
            $log->updateAttributes([
                'message' => trim($log->message),
            ]);
        }
        $logActivities = LogActivity::find()->all();
        foreach ($logActivities as $logActivity) {
            $logActivity->updateAttributes([
                'name' => trim($logActivity->name),
            ]);
        }
        $logHistories = LogHistory::find()->all();
        foreach ($logHistories as $logHistory) {
            $logHistory->updateAttributes([
                'instanceType' => trim($logHistory->instanceType),
            ]);
        }
        $logLinks = LogLink::find()->all();
        foreach ($logLinks as $logLink) {
            $logLink->updateAttributes([
                'index' => trim($logLink->index),
                'baseUrl' => trim($logLink->baseUrl),
                'path' => trim($logLink->path),
            ]);
        }
        $logObjects = LogObject::find()->all();
        foreach ($logObjects as $logObject) {
            $logObject->updateAttributes([
                'name' => trim($logObject->name),
            ]);
        }
    }

    public function down()
    {
        echo "m180113_061641_trim_space cannot be reverted.\n";

        return false;
    }
}
