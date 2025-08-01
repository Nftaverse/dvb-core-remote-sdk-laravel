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
    public function test_dvb_api_exception_can_be_created_from_guzzle_exception()
    {
        $request = new Request('GET', 'test');
        $guzzleException = new RequestException('Error Communicating with Server', $request);
        $exception = DvbApiException::fromGuzzleException($guzzleException);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Error Communicating with Server', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_dvb_api_exception_can_be_created_with_response()
    {
        $request = new Request('GET', 'test');
        $response = new Response(400, [], json_encode(['message' => 'Bad request', 'errors' => ['field' => 'is required']]));
        $guzzleException = new RequestException('Client error', $request, $response);
        $exception = DvbApiException::fromGuzzleException($guzzleException);

        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals('Bad request', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals(['field' => 'is required'], $exception->getErrors());
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
        $exception = new ValidationException("The given data was invalid.", 422, null, $errors);
        
        $this->assertInstanceOf(DvbApiException::class, $exception);
        $this->assertEquals("The given data was invalid.", $exception->getMessage());
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(422, $exception->getCode());
    }
}
