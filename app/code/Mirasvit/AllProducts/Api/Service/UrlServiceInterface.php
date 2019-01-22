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
 * @package   mirasvit/module-navigation
 * @version   1.0.54
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\AllProducts\Api\Service;

interface UrlServiceInterface
{
    const IS_CORRECT_URL = 'is_all_products_url';

    /**
     * @return string
     */
    public function getBaseRoute();

    /**
     * @param string $pathInfo
     * @return bool|DataObject
     */
    public function match($pathInfo);

}