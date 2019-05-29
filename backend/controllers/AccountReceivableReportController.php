<?php

namespace backend\controllers;

use Yii;
use common\models\Blog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Invoice;
use common\models\User;
use common\models\Lesson;
use common\models\Payment;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

class AccountReceivableReportController extends \common\components\controllers\BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['create','update','delete'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['manageAccountReceivableReport'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Blog models.
     *
     * @return mixed
     */
   public function actionIndex()
    {
       
    }

    public function actionView($id)
    {
       $model =  User::findOne($id);
        return $this->render('_detail-view', [
            'outstandingInvoice' => $this->getOutstandingInvoice($id),
            'prePaidLessons' => $this->getPrePaidLessons($id),
            'unUsedCredits' => $this->getAvailableCredit($id),
            'model' => $model,
        ]);
    }
    /**
     * Lists all Blog models.
     *
     * @return mixed
     */
    /**
     * Displays a single Blog model.
     *
     * @param string $id
     *
     * @return mixed
     */
  
    /**
     * Finds the Blog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Blog the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */


    protected function getOutstandingInvoice($id)
    {
        $outstandingInvoice = Invoice::find()
                ->customer($id)
                ->invoice()
                ->andWhere(['>', 'invoice.balance', 0.0])
                ->notDeleted();
        return new ActiveDataProvider([
            'query' => $outstandingInvoice,
            'sort' => ['defaultOrder' => ['date' => SORT_ASC]],
            'pagination' => false,
        ]);
    }

    protected function getPrePaidLessons($id)
    {
        $invoicedLessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->customer($id)
            ->invoiced();
        $prePaidLessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->joinWith(['lessonPayments' => function ($query) {
                $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
            }])
            ->customer($id)
            ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
            ->andWhere(['invoiced_lesson.id' => null]);

            return new ActiveDataProvider([
                'query' => $prePaidLessons,
                'sort' => ['defaultOrder' => ['date' => SORT_ASC]],
                'pagination' => false,
            ]);
    }

    public function getCustomerCreditInvoices($customerId)
    {
        return Invoice::find()
            ->notDeleted()
            ->invoiceCredit($customerId)
            ->all();
    }

    public function getAvailableCredit($customerId)
    {
        $invoiceCredits = $this->getCustomerCreditInvoices($customerId);
        $results = [];
        $amount = 0;
        $paymentCredits = $this->getCustomerPayments($customerId);
        
        if ($invoiceCredits) {
            foreach ($invoiceCredits as $invoiceCredit) {
                $results[] = [
                    'id' => $invoiceCredit->id,
                    'type' => 'Invoice Credit',
                    'reference' => $invoiceCredit->getInvoiceNumber(),
                    'amount' => round(abs($invoiceCredit->balance), 2)
                ];
            }
        }

        if ($paymentCredits) {
            foreach ($paymentCredits as $paymentCredit) {
                if ($paymentCredit->hasCredit()) {
                    $results[] = [
                        'id' => $paymentCredit->id,
                        'type' => 'Payment Credit',
                        'reference' => $paymentCredit->reference,
                        'amount' => round($paymentCredit->creditAmount, 2)
                    ];
                }
            }
        }
        
        $creditDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['id', 'type', 'reference', 'amount']
            ],
            'pagination' => false
        ]);
        return $creditDataProvider;
    }
    
    public function getCustomerPayments($customerId)
    {
        return Payment::find()
            ->notDeleted()
            ->exceptAutoPayments()
            ->customer($customerId)
            ->orderBy(['payment.id' => SORT_ASC])
            ->all();
    }
}
