<?php

/**
 * TechDivision\Import\Category\Observers\CategoryUpdateObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\ColumnKeys;

/**
 * Observer that add's/update's the category itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryUpdateObserver extends CategoryObserver
{

    /**
     * Initialize the category with the passed attributes and returns an instance.
     *
     * @param array $attr The category attributes
     *
     * @return array The initialized category
     */
    protected function initializeCategory(array $attr)
    {

        // load the path of the category that has to be initialized
        $path = $this->getValue(ColumnKeys::PATH);
        // prepare the store view code
        $this->prepareStoreViewCode();
        // load ID of the actual store view
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        try {
            // try to load the category and the entity with the passed path
            $category = $this->getCategoryByPkAndStoreId($this->mapPath($path), $storeId);
            // load the category entity itself
            $entity = $this->loadCategory($this->getPrimaryKey($category));
            // merge it with the attributes, if we can find it
            return $this->mergeEntity($entity, $attr);
        } catch (\Exception $e) {
            $this->getSystemLogger()->debug(sprintf('Can\'t load category with path %s', $path));
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Return's the primary key of the category.
     *
     * @param array $category The category
     *
     * @return integer The primary key
     */
    protected function getPrimaryKey($category)
    {
        return $category[$this->getPkMemberName()];
    }

    /**
     * Returns the category with the passed primary key and the attribute values for the passed store ID.
     *
     * @param string  $pk      The primary key of the category to return
     * @param integer $storeId The store ID of the category values
     *
     * @return array|null The category data
     */
    protected function getCategoryByPkAndStoreId($pk, $storeId)
    {
        return $this->getCategoryBunchProcessor()->getCategoryByPkAndStoreId($pk, $storeId);
    }
}
