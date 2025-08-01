<?php

namespace DVB\Core\SDK\Tests\Unit\DTOs;

use DVB\Core\SDK\DTOs\IpfsFileDataDTO;
use DVB\Core\SDK\DTOs\IpfsUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsFolderUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsStatsDTO;
use DVB\Core\SDK\DTOs\IpfsStatsResponseDTO;
use DVB\Core\SDK\Tests\TestCase;

class IpfsDtoTest extends TestCase
{
    public function test_ipfs_file_data_dto_can_be_created_from_array()
    {
        $data = [
            'name' => 'test.txt',
            'size' => 1024,
            'type' => 'text/plain',
        ];

        $fileData = IpfsFileDataDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFileDataDTO::class, $fileData);
        $this->assertEquals('test.txt', $fileData->name);
        $this->assertEquals(1024, $fileData->size);
        $this->assertEquals('text/plain', $fileData->type);
    }

    public function test_ipfs_file_data_dto_can_be_created_with_missing_fields()
    {
        $data = [
            'name' => 'test.txt',
        ];

        $fileData = IpfsFileDataDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFileDataDTO::class, $fileData);
        $this->assertEquals('test.txt', $fileData->name);
        $this->assertNull($fileData->size);
        $this->assertNull($fileData->type);
    }

    public function test_ipfs_upload_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmTest123',
                'url' => 'https://ipfs.example.com/QmTest123',
                'file' => [
                    'name' => 'test.txt',
                    'size' => 1024,
                    'type' => 'text/plain',
                ],
            ],
        ];

        $response = IpfsUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsUploadResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertEquals('QmTest123', $response->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmTest123', $response->data->url);
        $this->assertInstanceOf(IpfsFileDataDTO::class, $response->data->file);
        $this->assertEquals('test.txt', $response->data->file->name);
    }

    public function test_ipfs_upload_response_dto_can_handle_missing_file_data()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmTest123',
                'url' => 'https://ipfs.example.com/QmTest123',
            ],
        ];

        $response = IpfsUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsUploadResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertEquals('QmTest123', $response->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmTest123', $response->data->url);
        $this->assertNull($response->data->file);
    }

    public function test_ipfs_folder_upload_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmFolder123',
                'url' => 'https://ipfs.example.com/QmFolder123',
                'files' => [
                    [
                        'name' => 'file1.txt',
                        'size' => 1024,
                        'type' => 'text/plain',
                    ],
                    [
                        'name' => 'file2.jpg',
                        'size' => 2048,
                        'type' => 'image/jpeg',
                    ]
                ],
            ],
        ];

        $response = IpfsFolderUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFolderUploadResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertEquals('QmFolder123', $response->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmFolder123', $response->data->url);
        $this->assertIsArray($response->data->files);
        $this->assertCount(2, $response->data->files);
        $this->assertInstanceOf(IpfsFileDataDTO::class, $response->data->files[0]);
        $this->assertEquals('file1.txt', $response->data->files[0]->name);
    }

    public function test_ipfs_folder_upload_response_dto_can_handle_empty_files()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'cid' => 'QmFolder123',
                'url' => 'https://ipfs.example.com/QmFolder123',
                'files' => [],
            ],
        ];

        $response = IpfsFolderUploadResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsFolderUploadResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertEquals('QmFolder123', $response->data->cid);
        $this->assertEquals('https://ipfs.example.com/QmFolder123', $response->data->url);
        $this->assertIsArray($response->data->files);
        $this->assertCount(0, $response->data->files);
    }

    public function test_ipfs_stats_dto_can_be_created_from_array()
    {
        $data = [
            'totalUploads' => 100,
            'totalSize' => 1024000,
            'lastUpload' => '2023-01-01T00:00:00Z',
        ];

        $stats = IpfsStatsDTO::fromArray($data);

        $this->assertInstanceOf(IpfsStatsDTO::class, $stats);
        $this->assertEquals(100, $stats->totalUploads);
        $this->assertEquals(1024000, $stats->totalSize);
        $this->assertEquals('2023-01-01T00:00:00Z', $stats->lastUpload);
    }

    public function test_ipfs_stats_dto_can_be_created_with_missing_fields()
    {
        $data = [
            'totalUploads' => 100,
        ];

        $stats = IpfsStatsDTO::fromArray($data);

        $this->assertInstanceOf(IpfsStatsDTO::class, $stats);
        $this->assertEquals(100, $stats->totalUploads);
        $this->assertNull($stats->totalSize);
        $this->assertNull($stats->lastUpload);
    }

    public function test_ipfs_stats_response_dto_can_be_created_from_array()
    {
        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'totalUploads' => 100,
                'totalSize' => 1024000,
                'lastUpload' => '2023-01-01T00:00:00Z',
            ],
        ];

        $response = IpfsStatsResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsStatsResponseDTO::class, $response);
        $this->assertEquals(200, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertInstanceOf(IpfsStatsDTO::class, $response->data);
        $this->assertEquals(100, $response->data->totalUploads);
        $this->assertEquals(1024000, $response->data->totalSize);
    }

    public function test_ipfs_stats_response_dto_can_handle_null_data()
    {
        $data = [
            'code' => 404,
            'message' => 'Stats not found',
            'data' => null,
        ];

        $response = IpfsStatsResponseDTO::fromArray($data);

        $this->assertInstanceOf(IpfsStatsResponseDTO::class, $response);
        $this->assertEquals(404, $response->code);
        $this->assertEquals('Stats not found', $response->message);
        $this->assertNull($response->data);
    }
}