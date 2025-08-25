<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\UserResponseDTO;
use DVB\Core\SDK\DTOs\UserNftResponseDTO;

class UserClient extends DvbBaseClient
{
    /**
     * Create a new user.
     *
     * @param string $email
     * @param string|null $name
     * @param string|null $phone
     * @return UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function createUser(string $email, ?string $name = null, ?string $phone = null): UserResponseDTO
    {
        $query = ['email' => $email];
        if ($name !== null) {
            $query['name'] = $name;
        }
        if ($phone !== null) {
            $query['phone'] = $phone;
        }
        $response = $this->post('user', [], $query);
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Get a user by their identifier (UID, email, phone, or wallet address).
     *
     * @param string $identifier
     * @return UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getUser(string $identifier): UserResponseDTO
    {
        $response = $this->get("user/{$identifier}");
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Get NFTs owned by a user.
     *
     * @param string $uid
     * @param int $chainId
     * @param string|null $collectionAddress
     * @param string|null $cursor
     * @return UserNftResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getNftsByUser(string $uid, int $chainId, ?string $collectionAddress = null, ?string $cursor = null): UserNftResponseDTO
    {
        $query = ['chain_id' => $chainId];
        if ($collectionAddress !== null) {
            $query['collection_address'] = $collectionAddress;
        }
        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }
        $response = $this->get("user/{$uid}/nft", $query);
        return UserNftResponseDTO::fromArray($response);
    }

    /**
     * Get user profile.
     *
     * @return \DVB\Core\SDK\DTOs\UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getProfile(): UserResponseDTO
    {
        $response = $this->get('profile');
        return UserResponseDTO::fromArray($response);
    }

    /**
     * Query user by field and value.
     *
     * @param string $field
     * @param string $value
     * @return \DVB\Core\SDK\DTOs\UserResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function queryUser(string $field, string $value): UserResponseDTO
    {
        $response = $this->post('query/user', [
            'field' => $field,
            'value' => $value,
        ]);
        return UserResponseDTO::fromArray($response);
    }
}