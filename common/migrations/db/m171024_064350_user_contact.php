<?php

use yii\db\Migration;

class m171024_064350_user_contact extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('user_contact');
        if ($tableSchema == null) {
            $this->createTable('user_contact', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'isPrimary' => $this->boolean()->notNull(),
                'labelId' => $this->integer()->notNull()
            ]);
        }
        $userPhone = Yii::$app->db->schema->getTableSchema('user_phone');
        if ($userPhone == null) {
            $this->createTable('user_phone', [
                'id' => $this->primaryKey(),
                'userContactId' => $this->integer()->notNull(),
                'number' => $this->char(15)->notNull(),
                'extension' => $this->integer()
            ]);
        }
        $this->addColumn(
            'user_email',
            'userContactId',
            $this->integer()->after('id')
        );
        $this->addColumn(
            'user_address',
            'userContactId',
            $this->integer()->after('id')
        );
        $this->addColumn(
            'user_address',
            'address',
            $this->string()->after('userContactId')
        );
        $this->addColumn(
            'user_address',
            'cityId',
            $this->integer()->after('address')
        );
        $this->addColumn(
            'user_address',
            'countryId',
            $this->integer()->after('cityId')
        );
        $this->addColumn(
            'user_address',
            'provinceId',
            $this->integer()->after('countryId')
        );
        $this->addColumn(
            'user_address',
            'postalCode',
            $this->string()->after('countryId')
        );
    }

    public function down()
    {
        echo "m171024_064350_user_contact cannot be reverted.\n";

        return false;
    }
}
