<?php

namespace Marvelic\Job\Ui\Component\Listing\Column\ExportSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => '-- Please Select --',
                'value' => ''
            ],
            [
                'label' => 'File',
                'value' => 'file'
            ],
            [
                'label' => 'FTP',
                'value' => 'ftp'
            ],
            [
                'label' => 'SFTP',
                'value' => 'sftp'
            ]
        ];

        return $options;
    }

    public function toArray(){
        $options        = $this->toOptionArray();
        $resultOptions  = [];

        foreach ($options as $option) {
            $resultOptions[$option['value']] = $option['label'];
        }

        return $resultOptions;
    }
}
