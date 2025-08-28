<?php

namespace DVB\Core\SDK\DTOs;

class UserDTO
{
    public string $uid;
    public string $name;
    public ?string $email;
    public ?string $avatar;
    public ?int $credit_web_three;
    public ?int $credit_otp;
    /** @var WalletDTO[]|null */
    public ?array $wallet;

    /**
     * @param WalletDTO[]|null $wallet
     */
    public function __construct(
        string $uid,
        string $name,
        ?string $email,
        ?string $avatar = null,
        ?int $credit_web_three = null,
        ?int $credit_otp = null,
        ?array $wallet = null
    ) {
        $this->uid = $uid;
        $this->name = $name;
        $this->email = $email;
        $this->avatar = $avatar;
        $this->credit_web_three = $credit_web_three;
        $this->credit_otp = $credit_otp;
        $this->wallet = $wallet;
    }

    public static function fromArray(array $data): self
    {
        $wallet = isset($data['wallet']) && is_array($data['wallet'])
            ? array_map(fn($walletData) => WalletDTO::fromArray($walletData), $data['wallet'])
            : null;

        return new self(
            $data['uid'] ?? '',
            $data['name'] ?? '',
            $data['email'] ?? null,
            $data['avatar'] ?? null,
            $data['credit_web_three'] ?? null,
            $data['credit_otp'] ?? null,
            $wallet,
        );
    }
}