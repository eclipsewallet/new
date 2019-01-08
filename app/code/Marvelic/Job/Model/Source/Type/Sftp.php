<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/12/2018
 * Time: 16:13
 */

namespace Marvelic\Job\Model\Source\Type;

class Sftp
{
    const SFTP_SOURCE = 'Marvelic\Job\Model\Filesystem\Io\Sftp';

    /**
     * @var string
     */
    protected $code = 'sftp';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * Sftp constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Framework\Filesystem\DirectoryList $dir)
    {
        $this->_objectManager   = $objectManager;
        $this->_dir             = $dir;
    }

    /**
     * @param $model
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function run(array $args = [])
    {
        $ioSftp         = $this->_objectManager->create('Marvelic\Job\Model\Filesystem\Io\Sftp');
        $arrDataSftp    = [
            'host'  => $args['host'],
            'port'  => (isset($args['port'])) ? $args['port'] : null,
            'username'  => $args['username'],
            'password'  => $args['password']
        ];

        $ioSftp->open($arrDataSftp);
        $dataFolder = explode('/', $args['file_path']);

        foreach ($dataFolder as $folderName) {
            if (!$folderName == ''){
                $exist = $ioSftp->cd($folderName);
                if ($exist == false){
                    $ioSftp->mkdir($folderName, 0777, false);
                    $ioSftp->cd($folderName);
                }
            }
        }

        $fileSource = $args['file_source'];
        $sttUpload  = $ioSftp->write( $ioSftp->pwd().'/'.basename($fileSource) , $fileSource);

        $ioSftp->close();
    }
}
