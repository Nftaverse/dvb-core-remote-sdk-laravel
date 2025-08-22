<?php

namespace DVB\Core\SDK\Enums;

enum WebhookType: string
{
    case deploy_collection = 'DEPLOY_COLLECTION';
    case deploy_owned_collection = 'DEPLOY_OWNED_COLLECTION';
    case mint_nft = 'MINT_NFT';
    case transfer_nft = 'TRANSFER_NFT';
    case sale_nft = 'SALE_NFT';
    case buy_nft = 'BUY_NFT';
    case bid_nft = 'BID_NFT';
    case take_bid_nft = 'TAKE_BID_NFT';
    case cancel_nft_order = 'CANCEL_NFT_ORDER';
    case user_profile_update = 'USER_PROFILE_UPDATE';
    case pay_created = 'PAY_CREATED';
    case pay_completed = 'PAY_COMPLETED';
    case pay_failed = 'PAY_FAILED';
    case pay_cancelled = 'PAY_CANCELLED';
    
    /**
     * Get all available webhook types as an array of strings
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    }