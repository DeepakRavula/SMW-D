<?php

use yii\db\Migration;
use common\models\CustomerRecurringPayment;
use common\models\CustomerRecurringPaymentEnrolment;
use common\models\CustomerPaymentPreference;
use common\models\Enrolment;
use common\models\User;

/**
 * Class m190410_052103_customer_payment_preference_migration
 */
class m190410_052103_customer_payment_preference_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function safeUp()
    {
        $customerPaymentPreferences = CustomerPaymentPreference::find()->notDeleted()->all();
        foreach ($customerPaymentPreferences as $customerPaymentPreference) {
            $enrolments = Enrolment::find()->privateProgram()->isRegular()->customer($customerPaymentPreference->userId)->all();
            $customerRecurringPayment = new CustomerRecurringPayment();
            $customerRecurringPayment->customerId = $customerPaymentPreference->userId;
            $customerRecurringPayment->entryDay = 12;
            $customerRecurringPayment->paymentDay = $customerPaymentPreference->dayOfMonth;
            $customerRecurringPayment->paymentMethodId = $customerPaymentPreference->paymentMethodId;
            $enrolmentsCount = $customerPaymentPreference->customer->getEnrolmentsCount();
            if ($enrolmentsCount == 1) {
                $customerRecurringPayment->paymentFrequencyId = $customerPaymentPreference->customer->enrolment->paymentFrequencyId;
            } else {
                $customerRecurringPayment->paymentFrequencyId = 1;
            }
            $customerRecurringPayment->expiryDate = $customerPaymentPreference->expiryDate;
            $customerRecurringPayment->amount = $customerPaymentPreference->customer->getPrivateLessonsDue($customerPaymentPreference->customer->id);
            $customerRecurringPayment->isRecurringPaymentEnabled = $customerPaymentPreference->isPreferredPaymentEnabled;
            if ($customerRecurringPayment->save()) {
                foreach ($enrolments as $enrolment) {
                    $customerRecurringPaymentEnrolment = new CustomerRecurringPaymentEnrolment();
                    $customerRecurringPaymentEnrolment->enrolmentId = $enrolment->id;
                    $customerRecurringPaymentEnrolment->customerRecurringPaymentId = $customerRecurringPayment->id;
                    $customerRecurringPaymentEnrolment->save();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190410_052103_customer_payment_preference_migration cannot be reverted.\n";

        return false;
    }
}
