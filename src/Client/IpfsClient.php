<?php

namespace DVB\Core\SDK\Client;

use DVB\Core\SDK\DTOs\IpfsUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsFolderUploadResponseDTO;
use DVB\Core\SDK\DTOs\IpfsStatsResponseDTO;

class IpfsClient extends DvbBaseClient
{
    /**
     * Upload file to IPFS.
     *
     * @param resource $fileResource
     * @param bool $toCdn
     * @return IpfsUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadFileToIpfs($fileResource, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->request('POST', 'ipfs/upload-file', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $fileResource,
                ],
                [
                    'name'     => 'to_cdn',
                    'contents' => $toCdn ? '1' : '0',
                ],
            ],
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload folder to IPFS.
     *
     * @param array $files An array of file resources.
     * @param bool $toCdn
     * @return IpfsFolderUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadFolderToIpfs(array $files, bool $toCdn = true): IpfsFolderUploadResponseDTO
    {
        $multipart = [];
        foreach ($files as $file) {
            $multipart[] = [
                'name'     => 'files[]',
                'contents' => $file,
            ];
        }

        $multipart[] = [
            'name'     => 'to_cdn',
            'contents' => $toCdn ? 'true' : 'false',
        ];

        $response = $this->request('POST', 'ipfs/upload-files-to-folder', [
            'multipart' => $multipart,
        ]);
        return IpfsFolderUploadResponseDTO::fromArray($response);
    }

    /**
     * Upload JSON to IPFS.
     *
     * @param array $jsonData
     * @param bool $toCdn
     * @return IpfsUploadResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function uploadJsonToIpfs(array $jsonData, bool $toCdn = true): IpfsUploadResponseDTO
    {
        $response = $this->post('ipfs/upload-json', [
            'json'   => $jsonData,
            'to_cdn' => $toCdn,
        ]);
        return IpfsUploadResponseDTO::fromArray($response);
    }

    /**
     * Get IPFS stats.
     *
     * @return IpfsStatsResponseDTO
     * @throws \DVB\Core\SDK\Exceptions\DvbApiException
     */
    public function getIpfsStats(): IpfsStatsResponseDTO
    {
        $response = $this->get('ipfs/upload-stats');
        return IpfsStatsResponseDTO::fromArray($response);
    }
}