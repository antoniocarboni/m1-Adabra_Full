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
    const ENDPOINT = '/api/v1/catalog/product/update';

    protected $_virtualFields = array();

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

        // Must use this method due to 1.7 images on collection issue
        $imageType = Mage::helper('adabra_feed')->getImageType();
        $productImage = Mage::getResourceSingleton('catalog/product')
            ->getAttributeRawValue($product->getId(), $imageType, $this->getFeed()->getStoreId());

        if ($productImage && ($productImage != 'no_selection')) {
//            $imageSize = Mage::helper('adabra_feed')->getImageSize();
//            if ($imageSize) {
//                $imageUrl = Mage::helper('catalog/image')->init($product, $imageType)->resize($imageSize);
//            } else {
//                $imageUrl = Mage::getSingleton('catalog/product_media_config')->getMediaUrl($productImage);
//            }
            $imageUrl = Mage::getSingleton('catalog/product_media_config')->getMediaUrl($productImage);
        } else {
            $imageUrl = '';
        }

        // Find first category
        $mainCategoryId = Mage::helper('adabra_feed')->getFirstValidCategory($categoryIds, $this->getFeed()->getStore()->getStoreId());

        $productArray = array(
            'idProdotto' => $product->getSku(),
            'SKU' => $product->getSku(),
            'idCategoriaPrincipale' => $mainCategoryId,
            'categorieSecondarie' => $categoryIds,
            'linkNegozio' => $productUrl,
            'nome' => $product->getName(),
            'descrizione' => $product->getDescription(),
            'descrizioneBreve' => $product->getShortDescription() ?: $product->getName(),
            'prezzoSpedizione' => $this->getVirtualField($product, 'prezzo_spedizione'),
            'prezzoBase' => $this->_toCurrency($price, true),
            'prezzoFinale' => $this->_toCurrency($finalPrice, true),
            'valuta' => $this->getFeed()->getCurrency(),
            'fSpedizione' => $this->_toBoolean($shippable),
            'tempoSpedizione' => $this->getVirtualField($product, 'tempo_spedizione'),
            'immagine' => $imageUrl,
            'disponibilita' => $availability,
            'quantitaDisponibile' => floatval($qty),
            'condizione' => intval($this->getVirtualField($product, 'condizione')),
            'fPerAdulti' => $this->_toBoolean($this->getVirtualField($product, 'f_peradulti')),
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
            'idCatalogo' => $this->getFeed()->getAdabraCatalogId(),
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

    /**
     * Get virtual field
     * @param Mage_Catalog_Model_Product $product
     * @param $fieldName
     * @return string
     */
    public function getVirtualField(Mage_Catalog_Model_Product $product, $fieldName)
    {
        $fieldModel = $this->_getVirtualFieldModel($fieldName);
        return $fieldModel->getComputedValue($product);
    }

    /**
     * Get virtual field model
     * @param $fieldName
     * @return Adabra_Feed_Model_Source_Vfield
     */
    protected function _getVirtualFieldModel($fieldName)
    {
        if (!isset($this->_virtualFields[$fieldName])) {
            $this->_virtualFields[$fieldName] = Mage::getModel('adabra_feed/vfield')
                ->getCollection()
                ->addFieldToFilter('vfield_type', ['eq' => Adabra_Feed_Model_Source_Vfield_Type::TYPE_PRODUCT])
                ->addFieldToFilter('code', ['eq' => $fieldName])
                ->getFirstItem();
        }

        return $this->_virtualFields[$fieldName];
    }
}