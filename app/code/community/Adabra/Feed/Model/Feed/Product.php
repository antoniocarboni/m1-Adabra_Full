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
 * @category   Adabra
 * @package    Adabra_Feed
 * @copyright  Copyright (c) 2017 Skeeller srl / MageSpecialist (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Feed_Model_Feed_Product extends Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = 'product';
    protected $_exportName = 'products';

    protected $_categoryNames = array();
    protected $_virtualFields = array();

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

    /**
     * Get headers
     * @return array
     */
    protected function _getHeaders()
    {
        return array(
            'id_cli_prodotto',
            'id_cli_categoria',
            'link_negozio',
            'nome',
            'descrizione_breve',
            'descrizione',
            'brand',
            'modello',
            'prezzo_spedizione',
            'prezzo_base',
            'prezzo_finale',
            'valuta',
            'info_pagamento',
            'f_spedizione',
            'tempo_spedizione',
            'info_spedizione',
            'immagine',
            'fine_validita',
            'disponibilita',
            'quantita_disponibile',
            'disponibile_dal',
            'priorita',
            'condizione',
            'f_peradulti',
            'f_attivo',
            'SKU',
            'GTIN',
            'UPC',
            'EAN',
            'ISBN',
            'ASIN',
            'PZN',
            'CNET',
            'MUZEID',
            'MPN',
            'correlati',
            'tags',
            'categorie',
            'categorie_id',
        );
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
     * Get product children ids
     * @param Mage_Catalog_Model_Product $product
     * @return array|null
     */
    protected function _getProductChildrenIds(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $res = Mage::getResourceSingleton('catalog/product_type_configurable')
                ->getChildrenIds($product->getId());

        } else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            $res = Mage::getResourceSingleton('catalog/product_link')
                ->getChildrenIds($product->getId(), Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

        } else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $res = Mage::getResourceSingleton('bundle/selection')
                ->getChildrenIds($product->getId(), true);
        } else {
            $res = array();
        }

        // Flatten children
        $out = array();
        foreach ($res as $i) {
            $out = array_merge($out, $i);
        }

        return array_unique($out);
    }

    protected function _getCustomTagsList(Mage_Catalog_Model_Product $product)
    {
        $tagList = [];
        $tagsListArray = Mage::helper('adabra_feed')->getCustomTagsList();
        foreach ($tagsListArray as $tag) {
            if($product->getData($tag) || $product->getAttributeText($tag)) {
                if($product->getAttributeText($tag) === false) {
                    $tagList[] = $product->getData($tag);
                } else {
                    if($product->getResource()->getAttribute($tag)->getFrontendInput() == 'boolean') {
                        if($product->getAttributeText($tag) == 'Yes') {
                            $tagList[] = $tag;
                        }
                    } else {
                        $tagList[] = $product->getAttributeText($tag);
                    }

                }
//                $currentAttribute = $product->getResource()->getAttribute($tag);
//
            }
        }
        $tagListStr = implode("|",$tagList);
        return $tagListStr;
    }

    /**
     * Get stock sum for children products
     * @param Mage_Catalog_Model_Product $product
     * @return float|null
     */
    protected function _getStockSum(Mage_Catalog_Model_Product $product)
    {
        $childrenIds = $this->_getProductChildrenIds($product);
        if (!count($childrenIds)) {
            return 0;
        }

        $resource = Mage::getSingleton('core/resource');

        $stockTable = $resource->getTableName('cataloginventory/stock_item');
        $coreRead = $resource->getConnection('core_read');

        // Check for non-managed stock children
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);

        $conditions = array();
        $conditions[] = 'product_id IN (' . implode(', ', $childrenIds) . ')';
        if ($manageStock) {
            $conditions[] = '(use_config_manage_stock=0 AND manage_stock=0)';
        } else {
            $conditions[] = '(use_config_manage_stock=1 OR manage_stock=0)';
        }

        $qry = $coreRead->select()
            ->from($stockTable, 'product_id')
            ->where('(' . implode(') AND (', $conditions) . ')')
            ->limit(1);

        if ($coreRead->fetchOne($qry)) {
            return 9999;
        }

        // Sum children qty (here we have all children stock managed
        $conditions = array();
        $conditions[] = 'product_id IN (' . implode(', ', $childrenIds) . ')';
        $conditions[] = 'is_in_stock=1';
        $qry = $coreRead->select()
            ->from($stockTable, 'SUM(qty) as total_qty')
            ->where('(' . implode(') AND (', $conditions) . ')')
            ->limit(1);

        return $coreRead->fetchOne($qry);
    }

    /**
     * Get feed row
     * @param Varien_Object $entity
     * @return array
     */
    protected function _getFeedRow(Varien_Object $entity)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $entity;

        $categories = array();
        $categoryIds = $product->getCategoryIds();

        if (count($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $categories[] = $this->_getCategoryName($categoryId);
            }
        } else {
            $categoryIds = array(Adabra_Feed_Model_Feed_Category::FAKE_CATEGORY_ID);
            $categories = array(Adabra_Feed_Model_Feed_Category::FAKE_CATEGORY_NAME);
        }

        $shippable = !in_array($product->getTypeId(), array(
            Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
        ));

        // Must use this method due to 1.7 images on collection issue
        $imageType = Mage::helper('adabra_feed')->getImageType();
        $productImage = Mage::getResourceSingleton('catalog/product')
            ->getAttributeRawValue($product->getId(), $imageType, $this->getStoreId());

        if ($productImage && ($productImage != 'no_selection')) {
            $imageSize = Mage::helper('adabra_feed')->getImageSize();
            if ($imageSize) {
                $imageUrl = Mage::helper('catalog/image')->init($product, $imageType)->resize($imageSize);
            } else {
                $imageUrl = Mage::getSingleton('catalog/product_media_config')->getMediaUrl($productImage);
            }
        } else {
            $imageUrl = '';
        }

        $availability = $product->isSaleable() ? '1' : '0';

        $related = $product->getRelatedProductCollection();
        $relatedSkus = array();
        foreach ($related as $i) {
            $relatedSkus[] = $i->getSku();
        }

        $productUrl = $product->getProductUrl();

        $isVisible = in_array(
                $product->getVisibility(),
                array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                )
            ) && ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $qty = $this->_getStockQty($product);

        if (!in_array($product->getTypeId(), array(
            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
        ))) {
            if ($qty !== false) {
                $qty = $this->_getStockSum($product);
            }
        }

        $_taxHelper  = Mage::helper('tax');
        $priceIncTax = $_taxHelper->getPrice($product, $product->getPrice(), true);
        $price = $priceIncTax;

        $finalPriceIncTax = $_taxHelper->getPrice($product, $product->getFinalPrice());
        $cataloPriceRule = Mage::getSingleton('catalogrule/rule')->calcProductPriceRule($product, $finalPriceIncTax);

        if($cataloPriceRule == null || $cataloPriceRule == 0 ) {
            $finalPrice = $finalPriceIncTax;
        } else {
            $finalPrice = $cataloPriceRule;
        }

        // Find first category
        $mainCategoryId = Mage::helper('adabra_feed')->getFirstValidCategory($categoryIds, $this->getStoreId());

        $tagsList = '';
        $tagsList = $this->_getCustomTagsList($product);




        return array(array(
            $product->getSku(),
            $mainCategoryId,
            $productUrl,
            $product->getName(),
            $product->getShortDescription() ?: $product->getName(),
            $product->getDescription(),
            $this->getVirtualField($product, 'brand'),
            $this->getVirtualField($product, 'modello'),
            $this->getVirtualField($product, 'prezzo_spedizione'),
            $this->_toCurrency($price, true),
            $this->_toCurrency($finalPrice, true),
            $this->getFeed()->getCurrency(),
            $this->getVirtualField($product, 'info_pagamento'),
            $this->_toBoolean($shippable),
            $this->getVirtualField($product, 'tempo_spedizione'),
            $this->getVirtualField($product, 'info_spedizione'),
            $imageUrl,
            $this->getVirtualField($product, 'fine_validita'),
            $availability,
            floatval($qty),
            $this->getVirtualField($product, 'disponibile_dal'),
            intval($this->getVirtualField($product, 'priorita')),
            intval($this->getVirtualField($product, 'condizione')),
            $this->_toBoolean($this->getVirtualField($product, 'f_peradulti')),
            $this->_toBoolean($isVisible),
            $product->getSku(),
            $this->getVirtualField($product, 'GTIN'),
            $this->getVirtualField($product, 'UPC'),
            $this->getVirtualField($product, 'EAN'),
            $this->getVirtualField($product, 'ISBN'),
            $this->getVirtualField($product, 'ASIN'),
            $this->getVirtualField($product, 'PZN'),
            $this->getVirtualField($product, 'CNET'),
            $this->getVirtualField($product, 'MUZEID'),
            $this->getVirtualField($product, 'MPN'),
            implode('|', $relatedSkus),
            $tagsList,
            implode('|', $categories),
            implode('|', $categoryIds),
        ));
    }

    /**
     * Prepare collection
     */
    protected function _prepareCollection()
    {
        $this->_collection = Mage::getModel('catalog/product')->getCollection();
        $this->_collection
            ->setStoreId($this->getStoreId())
            ->addStoreFilter()
            ->addAttributeToSelect('*')
            ->addWebsiteFilter($this->getStore()->getWebsiteId())
            ->addUrlRewrite()
//            ->addPriceData(0, $this->getStore()->getWebsiteId()) // This filters out out-of-stock products
            ->addCategoryIds();

        // Add stock information
        $stockItemTableName = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item');
        $this->_collection->getSelect()
            ->joinLeft(
                array('s' => $stockItemTableName),
                's.product_id=e.entity_id'
            );
    }
}
