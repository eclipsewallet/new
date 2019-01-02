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


namespace Mirasvit\Indexer\Service;

use Mirasvit\Indexer\Api\Service\ValidatorServiceInterface;

class ValidatorService implements ValidatorServiceInterface
{
    /**
     * @var ValidatorService[]
     */
    private $validators;

    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        foreach ($this->validators as $validator) {
            $validator->validate();
        }

        return true;
    }
}