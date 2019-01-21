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



namespace Mirasvit\Indexer\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Magento\Indexer\Model\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends Command
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        Processor $processor,
        State $state,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct();

        $this->processor = $processor;
        $this->state = $state;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:indexer:cron')
            ->setDescription('Run indexer cron jobs')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        } catch (\Exception $e) {
            # some modules may already set area code
        }

        $jobs = [
            'Mirasvit\Indexer\Cron\ReindexScheduled',
            'Magento\Indexer\Cron\ReindexAllInvalid',
            'Magento\Indexer\Cron\UpdateMview',
            'Magento\Indexer\Cron\ClearChangelog',
            'Mirasvit\Indexer\Cron\Validate',
        ];

        foreach ($jobs as $className) {
            $output->write("Run job $className...");
            $job = $this->objectManager->create($className);
            $job->execute();

            $output->writeln("<info>done</info>");
        }
    }
}
