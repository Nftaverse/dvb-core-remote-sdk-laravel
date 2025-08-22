<?php

namespace DVB\Core\SDK\Tests\Unit\Exceptions;

use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\Exceptions\EmailExistsException;
use DVB\Core\SDK\Exceptions\InsufficientCreditException;
use DVB\Core\SDK\Exceptions\ValidationException;
use DVB\Core\SDK\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ExceptionsTest extends TestCase
{
    public function test_dvb_api_exception_can_be_instantiated()
    {
        $exception = new DvbApiException('API Error', 500);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('API Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function test_dvb_api_exception_can_be_created_with_data()
    {
        $errorData = ['field' => 'is required'];
        $exception = new DvbApiException('Bad request', 400, $errorData);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Bad request', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals($errorData, $exception->getErrorData());
    }

    public function test_email_exists_exception()
    {
        $exception = new EmailExistsException("Email already in use.");
        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals("Email already in use.", $exception->getMessage());
    }

    public function test_insufficient_credit_exception()
    {
        $exception = new InsufficientCreditException("Not enough credits to perform this action.");
        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals("Not enough credits to perform this action.", $exception->getMessage());
    }

    public function test_validation_exception()
    {
        $errors = ['field1' => ['The field1 field is required.']];
        $exception = new ValidationException("The given data was invalid.", 422, $errors);
        
        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals("The given data was invalid.", $exception->getMessage());
        $this->assertEquals($errors, $exception->getErrorData());
        $this->assertEquals(422, $exception->getCode());
    }
}
