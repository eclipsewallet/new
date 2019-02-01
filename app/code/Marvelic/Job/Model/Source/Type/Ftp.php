<?php

namespace Marvelic\Job\Model\Source\Type;

class Ftp
{
    /**
     * @var string
     */
    protected $code = 'ftp';

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_dir     = $dir;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $args
     * @return bool
     */
    public function run(array $args = [])
    {
        try {
            $arrDataFtp = [
                'host'      => $args['host'],
                'port'      => $args['port'],
                'user'      => $args['user'],
                'password'  => $args['password']
            ];

            $ioFtp         = $this->_objectManager->create('Marvelic\Job\Model\Filesystem\Io\Ftp');
            $ioFtp->open($arrDataFtp);
            $dataFolder = explode('/', $args['file_path']);

            foreach ($dataFolder as $folderName) {
                if (!$folderName == '') {
                    $exist = $ioFtp->cd($folderName);
                    if ($exist == false) {
                        $ioFtp->mkdir($folderName, 0777, false);
                        $ioFtp->cd($folderName);
                    }
                }
            }

            $fileSource = $args['file_source'];
            $sttUpload  = $ioFtp->write($ioFtp->pwd() . '/' . basename($fileSource), $fileSource);
            $ioFtp->close();
        } catch (\Exception $e) {
            $errors[] = __('Folder for import / export don\'t have enough permissions! Please set 775');
        }

        return $sttUpload;
    }
}
