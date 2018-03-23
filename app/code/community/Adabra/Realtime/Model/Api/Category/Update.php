<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Realtime_Model_Api_Category_Update extends Adabra_Realtime_Model_Api
{
    const ENDPOINT = '/api/v1/catalog/category/update';

    protected $_feed;

    /**
     * Set export feed
     * @param Adabra_Feed_Model_Feed $feed
     * @return $this
     */
    public function setFeed(Adabra_Feed_Model_Feed $feed)
    {
        $this->_feed = $feed;
        return $this;
    }

    /**
     * Get current feed
     * @return Adabra_Feed_Model_Feed
     */
    public function getFeed()
    {
        return $this->_feed;
    }


    private function _fetchRowData(Mage_Catalog_Model_Category $category)
    {
        $categoryArray = array(
            'idCategoria' => $category->getId(),
            'nomeCategoria' => $category->getName(),
            'fAttiva' => $this->_toBoolean($category->getIsActive())
        );

        return $categoryArray;
    }

    private function _prepareCategoriesData(Mage_Catalog_Model_Resource_Category_Collection $categoriesCollection)
    {
        $categoriesArray = array();

        foreach ($categoriesCollection->getItems() as $category) {
            $categoriesArray[] = $this->_fetchRowData($category);
        }

        return $categoriesArray;
    }

    public function send(Mage_Catalog_Model_Resource_Category_Collection $categoriesCollection, Adabra_Feed_Model_Feed $feed) {
        $this->setFeed($feed);

        $categoriesList = $this->_prepareCategoriesData($categoriesCollection);

        $payload = array(
            'apiKey'    => $this->_getHelper()->getApiKey(),
            'apiSecret' => $this->_getHelper()->getApiSecret(),
            'idCatalogo' => $this->getFeed()->getAdabraCatalogId(),
            'bulk'      => $categoriesList
        );

        return $this->_send($payload);
    }
}