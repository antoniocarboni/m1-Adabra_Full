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

class Adabra_Realtime_Model_Api_Product_Update extends Adabra_Realtime_Model_Api
{
    const ENDPOINT = 'https://staging.marketingspray.com/api/v1/catalog/product/add';

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

    /**
     * Get stock qty
     * @param Mage_Catalog_Model_Product $product
     * @return float|boolean
     */
    protected function _getStockQty(Mage_Catalog_Model_Product $product)
    {
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $useStock = $product->getUseConfigManageStock() ? $manageStock : $product->getManageStock();

        if ($useStock && !$product->getIsInStock()) {
            return false;
        }

        return $useStock ? $product->getQty() : 999999;
    }

    /**
     * Get category name by id
     * @param $categoryId
     * @return string
     */
    protected function _getCategoryName($categoryId)
    {
        if (!isset($this->_categoryNames[$categoryId])) {
            $this->_categoryNames[$categoryId] = Mage::getModel('catalog/category')->load($categoryId)->getName();
        }

        return $this->_categoryNames[$categoryId];
    }

    private function _fetchRowData(Mage_Catalog_Model_Product $product)
    {
        $productUrl = $product->getProductUrl();

        $price = $product->getPrice();
        $finalPrice = Mage::getSingleton('catalogrule/rule')->calcProductPriceRule($product, $product->getFinalPrice());
        if ($finalPrice == 0) {
            $finalPrice = $product->getFinalPrice();
        }

        $shippable = !in_array($product->getTypeId(), array(
            Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
        ));

        $availability = $product->isSaleable() ? '1' : '0';

        $qty = $this->_getStockQty($product);

        $isVisible = in_array(
                $product->getVisibility(),
                array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                )
            ) && ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $categories = array();
        $categoryIds = $product->getCategoryIds();

        if (count($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $categories[] = $this->_getCategoryName($categoryId);
            }
        }

        // Find first category
        $mainCategoryId = Mage::helper('adabra_feed')->getFirstValidCategory($categoryIds, $this->getFeed()->getStore()->getStoreId());

        $productArray = array(
            'idProdotto' => $product->getSku(),
            'idCatalogo' => $this->getFeed()->getAdabraCatalogId(),
            'idCategoriaPrincipale' => $mainCategoryId,
            'linkNegozio' => $productUrl,
            'nome' => $product->getName(),
            'descrizioneBreve' => $product->getDescription(),
            'prezzoSpedizione' => '',  // TODO: virtual_field
            'prezzoBase' => $this->_toCurrency($price, true),
            'prezzoFinale' => $this->_toCurrency($finalPrice, true),
            'valuta' =>'EU', // TODO: prendere il dato dallo store
            'fSpedizione' => $this->_toBoolean($shippable),
            'tempoSpedizione' => 5, // TODO: virtual_field
            'immagine' => '', // TODO: prendere il dato dallo store
            'disponibilita' => $availability,
            'quantitaDisponibile' => floatval($qty),
            'condizione' => 0, // TODO: virtual_field
            'fPerAdulti' => false, // TODO: virtual_field
            'fAttivo' => $this->_toBoolean($isVisible),
            'upSert' => true
        );

        return $productArray;
    }
    
    private function _prepareProductsData(Mage_Catalog_Model_Resource_Product_Collection $produtsCollection)
    {
        $productsArray = array();

        foreach ($produtsCollection->getItems() as $product) {
            $productsArray[] = $this->_fetchRowData($product);
        }

        return $productsArray;
    }

    public function send(Mage_Catalog_Model_Resource_Product_Collection $produtsCollection, Adabra_Feed_Model_Feed $feed) {
        $this->setFeed($feed);

        $productsList = $this->_prepareProductsData($produtsCollection);

        $payload = array(
            'apiKey'    => $this->_getHelper()->getApiKey(),
            'apiSecret' => $this->_getHelper()->getApiSecret(),
            'bulk'      => $productsList
        );

        return $this->_send($payload);
    }

    /**
     * Convert value to currency
     * @param $val
     * @param $currencyConvert
     * @return string
     */
    protected function _toCurrency($val, $currencyConvert = false)
    {
        if ($currencyConvert) {
            $baseCurrency = $this->getFeed()->getStore()->getBaseCurrencyCode();
            $val = Mage::helper('directory')->currencyConvert($val, $baseCurrency, $this->getFeed()->getCurrency());
        }

        return number_format($val, 4, '.', '');
    }
}