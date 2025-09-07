<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\IpfsFileDataDTO;
use DVB\Core\SDK\DTOs\IpfsFolderUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsStatsResponseDTO;
use DVB\Core\SDK\DTOs\IpfsUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsJsonUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsUsageStatsDTO;
use DVB\Core\SDK\Tests\TestCase;

class IpfsDtoTest extends TestCase
{
    public function test_ipfs_file_data_dto_from_array()
    {
        $data = [
            'cid' => 'bafktestcid',
            'url' => 'ipfs://bafktestcid',
            'http_url' => 'https://ipfs.io/ipfs/bafktestcid',
            'size' => 12345,
            'mime_type' => 'image/png',
            'name' => 'image.png',
            'cid_path' => 'bafyfolder/image.png',
            'cdn_url' => 'https://cdn.example.com/bafyfolder/image.png',
        ];

        $dto = IpfsFileDataDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFileDataDTO::class, $dto);
        $this->assertEquals('bafktestcid', $dto->cid);
        $this->assertEquals(12345, $dto->size);
        $this->assertEquals('image.png', $dto->name);
    }

    public function test_ipfs_upload_response_dto_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'cid' => 'bafktestcid',
            'url' => 'https://ipfs.example.com/bafktestcid',
        ];

        $dto = IpfsUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsUploadResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\IpfsFileDTO::class, $dto->data);
        $this->assertEquals('bafktestcid', $dto->data->cid);
    }

    public function test_ipfs_json_upload_response_dto_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'cid' => 'bafkjsoncid',
            'url' => 'https://ipfs.example.com/bafkjsoncid',
        ];

        $dto = IpfsJsonUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsJsonUploadResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\IpfsFileDTO::class, $dto->data);
        $this->assertEquals('bafkjsoncid', $dto->data->cid);
    }

    public function test_ipfs_json_upload_response_dto_with_null_data()
    {
        $data = [
            'code' => 400,
            'message' => 'Bad Request',
        ];

        $dto = IpfsJsonUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsJsonUploadResponseDTO::class, $dto);
        $this->assertEquals(400, $dto->code);
        $this->assertEquals('Bad Request', $dto->message);
        $this->assertNull($dto->data);
    }

    public function test_ipfs_folder_upload_response_dto_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'folder_cid' => 'bafyfolder',
            'folder_url' => 'https://ipfs.example.com/bafyfolder',
        ];

        $dto = IpfsFolderUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFolderUploadResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\IpfsFolderDTO::class, $dto->data);
        $this->assertEquals('bafyfolder', $dto->data->cid);
    }

    public function test_ipfs_usage_stats_dto_from_array()
    {
        $data = [
            'total_uploads' => 150,
            'total_storage' => 25600000,
            'uploads_by_type' => [
                'raw' => 100,
                'json' => 40,
                'folder' => 10,
            ],
        ];

        $dto = IpfsUsageStatsDTO::fromArray($data);

        $this->assertInstanceOf(IpfsUsageStatsDTO::class, $dto);
        $this->assertEquals(150, $dto->total_uploads);
        $this->assertEquals(25600000, $dto->total_storage);
        $this->assertEquals(100, $dto->uploads_by_type->raw);
    }

    public function test_ipfs_stats_response_dto_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'user_stats' => [
                'total_uploads' => 150,
                'total_storage' => 25600000,
                'uploads_by_type' => [
                    'raw' => 100,
                    'json' => 40,
                    'folder' => 10,
                ],
            ]
        ];

        $dto = IpfsStatsResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsStatsResponseDTO::class, $dto);
        $this->assertEquals(200, $dto->code);
        $this->assertInstanceOf(\DVB\Core\SDK\DTOs\IpfsStatsDTO::class, $dto->data);
        $this->assertEquals(150, $dto->data->totalUploads);
    }
}
