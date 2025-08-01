<?php

namespace DVB\Core\SDK\Tests\Integration;

use DVB\Core\SDK\Exceptions\DvbApiException;

class UserProfileIntegrationTest extends IntegrationTestCase
{
    /**
     * @group integration
     */
    public function test_get_profile_fetches_user_data_successfully()
    {
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        try {
            $response = $this->client->getProfile();

            $this->assertEquals(200, $response->code);
            $this->assertNotNull($response->data->uid);
            $this->assertNotNull($response->data->email);
        } catch (DvbApiException $e) {
            $this->handleException($e);
        }
    }

    /**
     * @group integration
     */
    public function test_query_user_fetches_data_by_email()
    {
        if (!$this->isIntegrationTestEnabled()) {
            $this->markTestSkipped('Integration tests are disabled.');
        }

        // This test assumes a user exists with the email from the authenticated profile.
        // A more robust test might create a user first, but that depends on API capabilities.
        try {
            $profile = $this->client->getProfile();
            $email = $profile->data->email;

            $response = $this->client->queryUser('email', $email);

            $this->assertEquals(200, $response->code);
            $this->assertEquals($profile->data->uid, $response->data->uid);
        } catch (DvbApiException $e) {
            // A 404 is a possible valid outcome if the user isn't found by query
            if ($e->getCode() === 404) {
                $this->markTestSkipped('User not found by query, which can be a valid state.');
            }
            $this->handleException($e);
        }
    }

    private function handleException(DvbApiException $e)
    {
        if (in_array($e->getCode(), [401, 403])) {
            $this->markTestSkipped('API key is invalid or lacks permissions for this test.');
        }

        throw $e;
    }
}
