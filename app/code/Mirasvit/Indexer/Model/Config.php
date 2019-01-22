<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-indexer
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Indexer\Model;

use Magento\Variable\Model\VariableFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class Config
{
    /**
     * @var VariableFactory
     */
    private $variableFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        VariableFactory $variableFactory,
        DateTime $dateTime,
        Filesystem $filesystem
    ) {
        $this->variableFactory = $variableFactory;
        $this->dateTime = $dateTime;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getLastRunTime()
    {
        $variable = $this->variableFactory->create()
            ->loadByCode('indexer_last_run');

        return $variable->getData('html_value');
    }

    /**
     * @return $this
     */
    public function updateLastRun()
    {
        $variable = $this->variableFactory->create()
            ->loadByCode('indexer_last_run');

        $variable->setCode('indexer_last_run')
            ->setData('html_value', $this->dateTime->gmtDate())
            ->save();

        return $this;
    }


    /**
     * @return string
     */
    public function getTmpPath()
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::TMP)->getAbsolutePath();

        if (!file_exists($path)) {
            @mkdir($path);
        }

        return $path;
    }
}