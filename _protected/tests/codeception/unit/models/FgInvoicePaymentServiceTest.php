<?php
namespace tests\codeception\unit\models;

use app\models\FgInvoicePayment;
use app\services\FgInvoicePaymentService;
use tests\codeception\unit\DbTestCase;

class FgInvoicePaymentServiceTest extends DbTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FgInvoicePaymentService();
    }

    public function testGetWaybillsByContractReturnsEmptyArrayWhenContractNotFound()
    {
        $result = $this->service->getWaybillsByContract(999999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetCustomerByContractReturnsNullWhenNotFound()
    {
        $result = $this->service->getCustomerByContract(999999);
        $this->assertNull($result);
    }

    public function testSaveReturnsFalseWhenModelInvalid()
    {
        $model = new FgInvoicePayment();
        // no required fields set → validate() will fail
        $result = $this->service->save($model, []);
        $this->assertFalse($result);
    }
}
