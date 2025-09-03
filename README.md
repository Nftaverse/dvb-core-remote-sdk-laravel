# DVB Core Remote SDK

A comprehensive PHP SDK for interacting with the DVB Core Remote API. This SDK provides easy integration with DVB services for managing digital assets, collections, users, payments, and more.

## Requirements

- PHP 8.2 or higher
- Laravel 10.x, 11.x, 12.x
- Composer

## Installation

You can install the package via composer:

```bash
composer require dvb/dvb-core-remote-sdk
```

## Configuration

Add your DVB API credentials to your `config/services.php` file:

```php
'dvb' => [
    'api_key' => env('DVB_API_KEY'),
    'domain' => env('DVB_API_DOMAIN', 'https://dev-epoch.nft-investment.io'),
    'protocol' => env('DVB_API_PROTOCOL', 'https'),
],
```

Then add the environment variables to your `.env` file:

```env
DVB_API_KEY=your_api_key_here
DVB_API_DOMAIN=dev-epoch.nft-investment.io
DVB_API_PROTOCOL=https
```

## Usage

### Basic Usage

```php
use DVB\Core\SDK\DvbApiClient;

// Create a new client instance from Laravel's service container (uses config/services.php)
$client = app(DvbApiClient::class);

// Or, create a new client instance manually
$client = DvbApiClient::newClient('your_api_key', 'dev-epoch.nft-investment.io');

// You can also set the credentials after instantiation
$client = new DvbApiClient();
$client->setApiKey('your_api_key')
       ->setBaseDomain('dev-epoch.nft-investment.io');

// Get user profile
$profile = $client->getProfile();

// Query user by field
$user = $client->queryUser('email', 'user@example.com');

// Get user permissions
$permissions = $client->getPermissions();

// Check specific permissions
$hasPermission = $client->checkPermission(['admin', 'editor']);
```

### Working with Collections and NFTs

```php
// Get collection events
$events = $client->getCollectionEvents('0xcontract_address', 1);

// Check collection
$collectionCheck = $client->checkCollection(1, '0xcontract_address', '0xuser_address');

// Get collection details
$collectionDetails = $client->getCollectionDetails('0xcontract_address', 1);

// Deploy a new collection (image is now required)
$imageResource = fopen('/path/to/image.png', 'r');
$deployRequest = new DeployCollectionRequestDTO(
    chainId: 1,
    ownerAddress: '0xowner_address',
    name: 'My Collection',
    quantity: 100,
    enableFlexibleMint: true,
    enableSoulbound: false,
    imageResource: $imageResource, // Image is now a required parameter
    description: 'My collection description',
    symbol: 'MC',
    imageUrl: 'https://example.com/image.jpg',
    contractMetadataUrl: 'https://example.com/metadata',
    contractBaseUrl: 'https://example.com/base',
    team: [['name' => 'Team Member 1'], ['name' => 'Team Member 2']],
    roadmap: [['phase' => 'Phase 1', 'description' => 'Initial release']],
    enableOwnerSignature: true,
    royalty: 5,
    receiveRoyaltyAddress: '0xroyalty_address',
    enableParentContract: false,
    enableBlind: true,
    blindName: 'Blind Collection',
    blindDescription: 'Blind collection description',
    blindMetadataBaseUri: 'https://example.com/blind',
    // For blind image: blindImageResource: $blindImageResource,
    blindImageUrl: 'https://example.com/blind.jpg'
);
$deployedCollection = $client->deployCollection($deployRequest);

// Don't forget to close the file resource when done
fclose($imageResource);

// Mint NFT in collection
$mintRequest = new MintNftRequestDTO(
    chainId: 1,
    address: '0xcontract_address',
    toAddress: '0xrecipient_address',
    amount: 1,
    reference: 'mint-ref-001'
);
$mintResult = $client->mintNft($mintRequest);

// Get collection details
$collectionDetails = $client->getCollectionDetails('0xcontract_address', 1);

// Mint NFT in collection
$mintRequest = new MintNftRequestDTO(
    chainId: 1,
    address: '0xcontract_address',
    toAddress: '0xrecipient_address',
    amount: 1,
    reference: 'mint-ref-001'
);
$mintResult = $client->mintNft($mintRequest);

// Get NFTs by contract with pagination
$nfts = $client->getNftsByContract('0xcontract_address', 1);

// Get NFT metadata
$metadata = $client->getNftMetadata('0xcontract_address', '1', 1);

// Get NFT detail
$nftDetail = $client->getNftDetail('0xcontract_address', '1', 1);

// Get NFT job details
$jobDetails = $client->getNftJobDetails('job_id');

// Transfer NFT ownership
$transferRequest = new TransferNftRequestDTO(
    chainId: 1,
    toAddress: '0xnew_owner_address'
);
$transferResult = $client->transferNft('0xcontract_address', '1', $transferRequest);

// Create NFT event
$event = $client->createNftEvent([
    'contractAddress' => '0xcontract_address',
    'chainId' => 1,
    'eventType' => 'mint',
    'eventData' => ['tokenId' => '1']
]);
```

### Working with Webhooks

```php
// Get all webhooks
$webhooks = $client->getWebhooks();

// Create a webhook
$webhook = $client->createWebhook(
    'https://your-domain.com/webhook',
    'collection_events',
    'My Webhook',
    '0xcontract_address',
    '1'
);

// Get webhook by ID
$webhook = $client->getWebhook('webhook_id');

// Delete webhook
$deleted = $client->deleteWebhook('webhook_id');
```

### Working with Payments

```php
// Create payment request
$payment = $client->createPaymentRequest([
    'amount' => 100.00,
    'currency' => 'USD',
    'description' => 'Product purchase'
]);

// Get payment request
$paymentDetails = $client->getPaymentRequest('payment_id');

// Get payment gateway
$gateway = $client->getPaymentGateway('gateway_id');

// Get payment methods
$methods = $client->getPaymentMethods();
```

### Working with Networks

```php
// Get all networks
$networks = $client->getNetworks();

// Get network detail
$networkDetail = $client->getNetworkDetail(1);
```

### Working with IPFS

```php
// Upload file to IPFS
$file = $client->uploadFileToIpfs($fileResource);

// Upload folder to IPFS
$folder = $client->uploadFolderToIpfs([$file1, $file2]);

// Upload JSON to IPFS
$json = $client->uploadJsonToIpfs(['key' => 'value']);

// Get IPFS stats
$stats = $client->getIpfsStats();
```

### Pagination

The SDK provides built-in pagination support for endpoints that return paginated data:

```php
// Using the paginate method
$paginator = $client->paginate('getNftsByContract', ['0xcontract_address', 1]);

while ($paginator->hasNext()) {
    $paginator->next();
    $response = $paginator->current();
    
    // Process the current page of items
    foreach ($response->getItems() as $nft) {
        // Handle each NFT
    }
}

// Or get all items at once
$allItems = $paginator->getAllItems();
```

## Error Handling

The SDK throws specific exceptions for different error scenarios:

```php
use DVB\Core\SDK\Exceptions\DvbApiException;
use DVB\Core\SDK\Exceptions\InsufficientCreditException;
use DVB\Core\SDK\Exceptions\EmailExistsException;
use DVB\Core\SDK\Exceptions\ValidationException;

try {
    $client->getProfile();
} catch (InsufficientCreditException $e) {
    // Handle insufficient credit
} catch (EmailExistsException $e) {
    // Handle email duplication
} catch (ValidationException $e) {
    // Handle validation errors
} catch (DvbApiException $e) {
    // Handle other API errors
}
```

## Testing

To run the tests:

```bash
composer test
```