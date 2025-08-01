<?php

namespace DVB\Core\SDK\Tests\Unit\Exceptions;

use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\Exceptions\InsufficientCreditException;
use DVB\Core\SDK\Exceptions\EmailExistsException;
use DVB\Core\SDK\Exceptions\ValidationException;
use DVB\Core\SDK\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function test_dvb_api_exception_can_be_created()
    {
        $exception = new DvbApiException('Test error', 500, ['error' => 'details']);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(500, $exception->getErrorCode());
        $this->assertEquals(['error' => 'details'], $exception->getErrorData());
    }

    public function test_dvb_api_exception_can_be_created_with_null_values()
    {
        $exception = new DvbApiException('Test error');

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode()); // Default code when not provided
        $this->assertNull($exception->getErrorCode());
        $this->assertNull($exception->getErrorData());
    }

    public function test_dvb_api_exception_can_store_previous_exception()
    {
        $previous = new \Exception('Previous error');
        $exception = new DvbApiException('Test error', 500, ['error' => 'details'], $previous);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(500, $exception->getErrorCode());
        $this->assertEquals($previous, $exception->getPreviousException());
    }

    public function test_dvb_api_exception_handles_empty_error_data()
    {
        $exception = new DvbApiException('Test error', 500, []);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(500, $exception->getErrorCode());
        $this->assertEquals([], $exception->getErrorData());
    }

    public function test_insufficient_credit_exception_can_be_created()
    {
        $exception = new InsufficientCreditException();

        $this->assertInstanceOf(InsufficientCreditException::class, $exception);
        $this->assertEquals('Insufficient credit', $exception->getMessage());
    }

    public function test_insufficient_credit_exception_inherits_from_dvb_api_exception()
    {
        $exception = new InsufficientCreditException();

        $this->assertInstanceOf(DvbApiException::class, $exception);
    }

    public function test_email_exists_exception_can_be_created()
    {
        $exception = new EmailExistsException();

        $this->assertInstanceOf(EmailExistsException::class, $exception);
        $this->assertEquals('Email already exists', $exception->getMessage());
    }

    public function test_email_exists_exception_inherits_from_dvb_api_exception()
    {
        $exception = new EmailExistsException();

        $this->assertInstanceOf(DvbApiException::class, $exception);
    }

    public function test_validation_exception_can_be_created()
    {
        $exception = new ValidationException();

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertEquals('Validation failed', $exception->getMessage());
    }

    public function test_validation_exception_inherits_from_dvb_api_exception()
    {
        $exception = new ValidationException();

        $this->assertInstanceOf(DvbApiException::class, $exception);
    }

    public function test_validation_exception_can_be_created_with_custom_message()
    {
        $exception = new ValidationException('Custom validation error');

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertEquals('Custom validation error', $exception->getMessage());
    }

    public function test_insufficient_credit_exception_can_be_created_with_custom_message()
    {
        $exception = new InsufficientCreditException('Custom credit error');

        $this->assertInstanceOf(InsufficientCreditException::class, $exception);
        $this->assertEquals('Custom credit error', $exception->getMessage());
    }

    public function test_email_exists_exception_can_be_created_with_custom_message()
    {
        $exception = new EmailExistsException('Custom email error');

        $this->assertInstanceOf(EmailExistsException::class, $exception);
        $this->assertEquals('Custom email error', $exception->getMessage());
    }
}