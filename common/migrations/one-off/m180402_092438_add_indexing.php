<?php

use yii\db\Migration;

/**
 * Class m180402_092438_add_indexing
 */
class m180402_092438_add_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createIndex('tranctionId', 'invoice', 'transactionId');
	    $this->createIndex('user_id', 'invoice', 'user_id');
            $this->createIndex('location_id', 'invoice', 'location_id');
	    $this->createIndex('enrolmentId', 'invoice_item_enrolment', 'enrolmentId');
	    $this->createIndex('invoiceLineItemId', 'invoice_item_enrolment', 'invoiceLineItemId');
	    $this->createIndex('invoiceLineItemId', 'invoice_item_lesson', 'invoiceLineItemId');
	    $this->createIndex('lessonId', 'invoice_item_lesson', 'lessonId');
	    $this->createIndex('invoiceLineItemId', 'invoice_item_payment_cycle_lesson', 'invoiceLineItemId');
	    $this->createIndex('paymentCycleLessonId', 'invoice_item_payment_cycle_lesson', 'paymentCycleLessonId');
	    $this->createIndex('item_id', 'invoice_line_item', 'item_id');
	    $this->createIndex('invoice_id', 'invoice_line_item', 'invoice_id');
	    $this->createIndex('item_type_id', 'invoice_line_item', 'item_type_id');
	    $this->createIndex('invoiceLineItemId', 'invoice_line_item_discount', 'invoiceLineItemId');
            $this->createIndex('invoice_id', 'invoice_payment', 'invoice_id');
	    $this->createIndex('invoiceId', 'invoice_reverse', 'invoiceId');
	    $this->createIndex('reversedInvoiceId', 'invoice_reverse', 'reversedInvoiceId');
	    $this->createIndex('transactionId', 'payment', 'transactionId');
	    $this->createIndex('user_id', 'payment', 'user_id');
	    $this->createIndex('payment_method_id', 'payment', 'payment_method_id');
	    $this->createIndex('enrolmentId', 'payment_cycle', 'enrolmentId');
	    $this->createIndex('paymentCycleId', 'payment_cycle_lesson', 'paymentCycleId');
	    $this->createIndex('lessonId', 'payment_cycle_lesson', 'lessonId');
	    $this->createIndex('courseId', 'enrolment', 'courseId');
	    $this->createIndex('studentId', 'enrolment', 'studentId');
	    $this->createIndex('paymentFrequencyId', 'enrolment', 'paymentFrequencyId');
	    $this->createIndex('enrolmentId', 'enrolment_discount', 'enrolmentId');
	    $this->createIndex('programId', 'course', 'programId');
	    $this->createIndex('teacherId', 'course', 'teacherId');
	    $this->createIndex('locationId', 'course', 'locationId');
	    $this->createIndex('courseId', 'course_extra', 'courseId');
	    $this->createIndex('extraCourseId', 'course_extra', 'extraCourseId');
	    $this->createIndex('courseId', 'course_group', 'courseId');
	    $this->createIndex('courseId', 'course_program_rate', 'courseId');
	    $this->createIndex('courseId', 'course_schedule', 'courseId');
	    $this->createIndex('cityId', 'user_address', 'cityId');
	    $this->createIndex('userContactId', 'user_address', 'userContactId');
	    $this->createIndex('countryId', 'user_address', 'countryId');
	    $this->createIndex('provinceId', 'user_address', 'provinceId');
	    $this->createIndex('userId', 'user_contact', 'userId');
	    $this->createIndex('labelId', 'user_contact', 'labelId');
	    $this->createIndex('userContactId', 'user_email', 'userContactId');
	    $this->createIndex('userContactId', 'user_phone', 'userContactId');
	    $this->createIndex('user_id', 'user_token', 'user_id');
	    $this->createIndex('customer_id', 'student', 'customer_id');
	    $this->createIndex('studentId', 'exam_result', 'studentId');
	    $this->createIndex('user_id', 'blog', 'user_id');
	    $this->createIndex('province_id', 'city', 'province_id');
	    $this->createIndex('locationId', 'classroom', 'locationId');
	    $this->createIndex('customerId', 'customer_discount', 'customerId');
	    $this->createIndex('userId', 'customer_payment_preference', 'userId');
	    $this->createIndex('paymentMethodId', 'customer_payment_preference', 'paymentMethodId');
	    $this->createIndex('credit_payment_id', 'credit_usage', 'credit_payment_id');
	    $this->createIndex('debit_payment_id', 'credit_usage', 'debit_payment_id');
	    $this->createIndex('itemCategoryId', 'item', 'itemCategoryId');
	    $this->createIndex('locationId', 'item', 'locationId');
	    $this->createIndex('classroomId', 'lesson', 'classroomId');
	    $this->createIndex('lessonId', 'lesson_hierarchy', 'lessonId');
	    $this->createIndex('childLessonId', 'lesson_hierarchy', 'childLessonId');
	    $this->createIndex('lessonId', 'lesson_payment', 'lessonId');
	    $this->createIndex('paymentId', 'lesson_payment', 'paymentId');
	    $this->createIndex('enrolmentId', 'lesson_payment', 'enrolmentId');
	    $this->createIndex('lessonId', 'lesson_split_usage', 'lessonId');
	    $this->createIndex('extendedLessonId', 'lesson_split_usage', 'extendedLessonId');
	    $this->createIndex('city_id', 'location', 'city_id');
	    $this->createIndex('province_id', 'location', 'province_id');
	    $this->createIndex('country_id', 'location', 'country_id');
	    $this->createIndex('locationId', 'location_availability', 'locationId');
	    $this->createIndex('locationId', 'location_debt', 'locationId');
 	    $this->createIndex('logObjectId', 'log', 'logObjectId');
 	    $this->createIndex('logActivityId', 'log', 'logActivityId');
 	    $this->createIndex('locationId', 'log', 'locationId');
 	    $this->createIndex('logId', 'log_history', 'logId');
 	    $this->createIndex('instanceId', 'log_history', 'instanceId');
 	    $this->createIndex('instanceType', 'log_history', 'instanceType');
 	    $this->createIndex('logId', 'log_link', 'logId');
 	    $this->createIndex('tax_rate', 'province', 'tax_rate');
 	    $this->createIndex('country_id', 'province', 'country_id');
	    $this->createIndex('lessonId', 'private_lesson', 'lessonId');
	    $this->createIndex('invoiceId', 'proforma_payment_frequency', 'invoiceId');
	    $this->createIndex('paymentFrequencyId', 'proforma_payment_frequency', 'paymentFrequencyId');
	    $this->createIndex('teacher_id', 'qualification', 'teacher_id');
	    $this->createIndex('user_id', 'rbac_auth_assignment', 'user_id');
	    $this->createIndex('location_id', 'rbac_auth_item_child', 'location_id');
	    $this->createIndex('user_id', 'release_notes', 'user_id');
	    $this->createIndex('release_note_id', 'release_notes_read', 'release_note_id');
	    $this->createIndex('user_id', 'release_notes_read', 'user_id');
	    $this->createIndex('tax_type_id', 'tax_code', 'tax_type_id');
	    $this->createIndex('province_id', 'tax_code', 'province_id');
	    $this->createIndex('tax_type_id', 'tax_type_tax_status_assoc', 'tax_type_id');
	    $this->createIndex('tax_status_id', 'tax_type_tax_status_assoc', 'tax_status_id');
	    $this->createIndex('classroomId', 'teacher_room', 'classroomId');
	    $this->createIndex('teacherAvailabilityId', 'teacher_room', 'teacherAvailabilityId');
	    $this->createIndex('teacherId', 'teacher_unavailability', 'teacherId');
            $this->createIndex('locationId', 'timeline_event', 'locationId');
            $this->createIndex('timelineEventId', 'timeline_event_course', 'timelineEventId');
            $this->createIndex('courseId', 'timeline_event_course', 'courseId');
            $this->createIndex('timelineEventId', 'timeline_event_enrolment', 'timelineEventId');
            $this->createIndex('enrolmentId', 'timeline_event_enrolment', 'enrolmentId');
            $this->createIndex('timelineEventId', 'timeline_event_invoice', 'timelineEventId');
            $this->createIndex('invoiceId', 'timeline_event_invoice', 'invoiceId');
            $this->createIndex('timelineEventId', 'timeline_event_lesson', 'timelineEventId');
            $this->createIndex('lessonId', 'timeline_event_lesson', 'lessonId');
            $this->createIndex('timelineEventId', 'timeline_event_link', 'timelineEventId');
            $this->createIndex('timelineEventId', 'timeline_event_payment', 'timelineEventId');
            $this->createIndex('paymentId', 'timeline_event_payment', 'paymentId');
            $this->createIndex('timelineEventId', 'timeline_event_student', 'timelineEventId');
            $this->createIndex('studentId', 'timeline_event_student', 'studentId');
            $this->createIndex('timelineEventId', 'timeline_event_teacher', 'timelineEventId');
            $this->createIndex('teacherId', 'timeline_event_teacher', 'teacherId');
            $this->createIndex('timelineEventId', 'timeline_event_user', 'timelineEventId');
            $this->createIndex('userId', 'timeline_event_user', 'userId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180402_092438_add_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180402_092438_add_indexing cannot be reverted.\n";

        return false;
    }
    */
}
