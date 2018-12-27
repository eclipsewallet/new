<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Source\Type;

use Magento\Framework\Filesystem\DriverPool;

/**
 * Class Rest
 *
 * @package Firebear\ImportExport\Model\Source\Type
 */
class Rest extends AbstractType
{
    const JSON_FILENAME_EXTENSION = '.json';
    const XML_FILENAME_EXTENSION = '.xml';

    /**
     * @var string
     */
    protected $code = 'rest';

    /**
     * @var string
     */
    protected $fileName;

    /**
     * Download remote source file to temporary directory
     *
     * @return bool|strxing
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadSource()
    {
        if ($client = $this->_getSourceClient()) {
            if (!$this->fileName) {
                $fileName = $this->convertUrlToFilename($this->getData('request_url'));
                $filePath = $this->directory->getAbsolutePath($this->getImportPath() . '/' . $fileName);
                try {
                    $dirname = dirname($filePath);
                    if (!is_dir($dirname)) {
                        mkdir($dirname, 0775, true);
                    }
                } catch (\Exception $e) {
                    throw new  \Magento\Framework\Exception\LocalizedException(
                        __(
                            "Can't create local file /var/import/json'. Please check files permissions. "
                            . $e->getMessage()
                        )
                    );
                }

                $fileMetadata = $client->execute(
                    $this->getData('request_url'),
                    $this->getData('request_method'),
                    $this->convertToFormat($this->getData('request_body'))
                );
                if ($fileMetadata && false !== $fileMetadata->response) {
                    file_put_contents($filePath, $fileMetadata->response);
                    $this->fileName = $this->getImportPath() . '/' . $fileName;
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__("No content from API call, error: " . $fileMetadata->error));
                }
            }

            return $this->fileName;
        } else {
            throw new  \Magento\Framework\Exception\LocalizedException(__("Can't initialize %s client", $this->code));
        }
    }

    /**
     * @param $importImage
     * @param $imageSting
     *
     * @return array
     */
    public function importImage($importImage, $imageSting)
    {
        $imageExt = '.jpeg';
        $filePath = $this->directory->getAbsolutePath($this->getMediaImportPath() . $imageSting);
        $dirname = \dirname($filePath);
        if (!mkdir($dirname, 0775, true) && !is_dir($dirname)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dirname));
        }

        if (preg_match('/\bhttps?:\/\//i', $importImage, $matches)) {
            $url = str_replace($matches[0], '', $importImage);
        } else {
            $sourceFilePath = $this->getData($this->code . '_file_path');
            $sourceDir = \dirname($sourceFilePath);
            $url = $sourceDir . '/' . $importImage;
            if (preg_match('/\bhttps?:\/\//i', $url, $matches)) {
                $url = str_replace($matches[0], '', $url);
            }
        }
        if ($url) {
            $fileExistOnServer = 0;
            try {
                $driver = $this->getProperDriverCode($matches);
                $read = $this->readFactory->create($url, $driver);
                $fileExistOnServer = 1;
            } catch (\Exception $e) {

            }
            if ($fileExistOnServer) {
                try {
                    $this->directory->writeFile(
                        $filePath,
                        $read->readAll()
                    );
                    if (\function_exists('mime_content_type')) {
                        $imageExt = $this->getImageMimeType(mime_content_type($filePath));
                        $this->directory->renameFile(
                            $filePath,
                            $filePath . $imageExt
                        );
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return [true, $imageSting . $imageExt];
    }

    protected function getImageMimeType($mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                $ext = '.jpeg';
                break;
            case 'image/png':
                $ext = '.png';
                break;
            default:
                $ext = '.jpeg';
        }
        return $ext;
    }

    public function importImageCategory($importImage, $imageSting)
    {
        $filePath = $this->directory->getAbsolutePath('pub/media/catalog/category/' . $imageSting);
        $dirname = dirname($filePath);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0775, true);
        }
        if (preg_match('/\bhttps?:\/\//i', $importImage, $matches)) {
            $url = str_replace($matches[0], '', $importImage);
        } else {
            $sourceFilePath = $this->getData($this->code . '_file_path');
            $sourceDir = dirname($sourceFilePath);
            $url = $sourceDir . '/' . $importImage;
            if (preg_match('/\bhttps?:\/\//i', $url, $matches)) {
                $url = str_replace($matches[0], '', $url);
            }
        }
        if ($url) {
            try {
                $driver = $this->getProperDriverCode($matches);
                $read = $this->readFactory->create($url, $driver);
                $this->directory->writeFile(
                    $filePath,
                    $read->readAll()
                );
            } catch (\Exception $e) {

            }
        }

        return true;
    }

    /**
     * Check if remote file was modified since the last import
     *
     * @param int $timestamp
     *
     * @return bool|int
     */
    public function checkModified($timestamp)
    {
        return true;
    }

    /**
     * Prepare and return Driver client
     *
     * @return \RestClient
     */
    protected function _getSourceClient()
    {
        if (!$this->client) {
            $requestOption = $this->convertToFormat($this->getData('request_options'), 'json', 'object');
            $api = new \RestClient($requestOption);
            $this->client = $api;
        }

        return $this->client;
    }

    /**
     * @param string $data Json request body from Job's settings
     * @param null|string $type
     * @param null $format
     *
     * @return mixed json-structured data
     */
    public function convertToFormat($data, $type = null, $format = null)
    {
        $result = '';
        $type = $type ?: $this->getData('type_file');
        switch ($type) {
            case 'xml':
//                $data = trim(preg_replace('/\s+/', '', $data));
                $result = $data;
                break;
            case 'json':
//                $data = trim(preg_replace('/\s+/', '', $data));
                if ($format == 'object') {
                    $result = json_decode($data, true);
                } else {
                    $result = json_encode(json_decode($data, true));
                }
                break;
            default:
                // ToDo: make an exception and catch it further
                break;
        }

        return $result;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function convertUrlToFilename($url)
    {
        $parsedUrl = parse_url($url);
        $filename = str_replace('.', '_', $parsedUrl['host'])
            . str_replace('/', '_', $parsedUrl['path'])
            . constant("self::" . strtoupper($this->getData('type_file')) . "_FILENAME_EXTENSION");

        return $filename;
    }

    /**
     * @param $matches
     *
     * @return string
     */
    protected function getProperDriverCode($matches)
    {
        if (is_array($matches)) {
            return (false === strpos($matches[0], 'https'))
                ? DriverPool::HTTP
                : DriverPool::HTTPS;
        } else {
            return DriverPool::HTTP;
        }
    }
}
