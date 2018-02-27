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

class Adabra_Realtime_Model_Api_Product_All extends Adabra_Realtime_Model_Api
{
    const ENDPOINT = 'https://staging.marketingspray.com/api/v1/catalog/product/add';

    protected $_store = null;

    /**
     * Get feed store
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore($this->getStoreId());
        }

        return $this->_store;
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

    private function _prepareProductsData(Mage_Catalog_Model_Resource_Product_Collection $produtsCollection)
    {
        $productsArray = array();

        foreach ($produtsCollection->getItems() as $product) {

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

            $productsArray[] = array(
                'idProdotto' => $product->getSku(),
                'idCatalogo' => $this->_getHelper()->getCatalogId(),
                'idCategoriaPrincipale' => 8,
                'linkNegozio' => $productUrl,
                'nome' => $product->getName(),
                'descrizioneBreve' => $product->getDescription(),
                'prezzoSpedizione' => '',
                'prezzoBase' => $price,
                'prezzoFinale' => $finalPrice,
                'valuta' =>'EU',
                'fSpedizione' => $this->_toBoolean($shippable),
                'tempoSpedizione' => 5,
                'immagine' => '',
                'disponibilita' => $availability,
                'quantitaDisponibile' => floatval($qty),
                'condizione' => 0,
                'fPerAdulti' => false,
                'fAttivo' => $this->_toBoolean($isVisible)
            );
        }

        return $productsArray;
    }

    public function send(Mage_Catalog_Model_Resource_Product_Collection $produtsCollection) {
        $productsArray = $this->_prepareProductsData($produtsCollection);

        $payload = array(
            'apiKey'    => $this->_getHelper()->getApiKey(),
            'apiSecret' => $this->_getHelper()->getApiSecret(),
            'bulk'      => $productsArray
        );

        return $this->_send($payload);

//        return $this->_send($params);
    }
}