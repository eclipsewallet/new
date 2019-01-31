<?php

namespace Marvelic\Job\Model\Source\Type;

class Ftp
{
    /**
     * @var string
     */
    protected $code = 'ftp';

    protected $_dir;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Firebear\ImportExport\Model\Filesystem\Io\Ftp
     */
    protected $ftp;

    /**
     * Ftp constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Firebear\ImportExport\Model\Filesystem\File\ReadFactory $readFactory
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Framework\Filesystem\File\WriteFactory $fileWrite
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param \Firebear\ImportExport\Model\Source\Factory $factory
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Marvelic\Job\Model\Filesystem\Io\Ftp $ftp
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        \Marvelic\Job\Model\Filesystem\Io\Ftp $ftp
    ) {
        $this->_dir     = $dir;
        $this->file     = $file;
        $this->ftp      = $ftp;
    }

    /**
     * @param array $args
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function run(array $args = [])
    {
        $arrDataFtp = [
            'host'      => $args['host'],
            'user'      => $args['user'],
            'password'  => $args['password']
        ];

        try {
            $this->ftp->open($arrDataFtp);
            $dataFolder = explode('/', $args['file_path']);

            foreach ($dataFolder as $folderName) {
                if (!$folderName == '') {
                    $exist = $this->ftp->cd($folderName);
                    if ($exist == false) {
                        $this->ftp->mkdir($folderName, 0777, false);
                        $this->ftp->cd($folderName);
                    }
                }
            }

            $fileSource = $args['file_source'];
            $sttUpload  = $this->ftp->write($this->ftp->pwd() . '/' . basename($fileSource), $fileSource);

            $this->ftp->close();
        } catch (\Exception $e) {
            $result = false;
            $errors[] = __('Folder for import / export don\'t have enough permissions! Please set 775');
        }

    }
}
