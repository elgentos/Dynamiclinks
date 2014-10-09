<?php
class Elgentos_Dynamiclinks_Block_Catalog_Product_List_Upsell extends Mage_Catalog_Block_Product_List_Upsell {

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        $this->_itemCollection = $product->getUpSellProductCollection()
            ->addStoreFilter();

        if(version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            $this->_itemCollection->addAttributeToSort('position', 'asc');
        } else {
            $this->_itemCollection->setPositionOrder();
        }

        if(count($this->_itemCollection)==0 AND Mage::getStoreConfig('dynamiclinks/general/disabled',Mage::app()->getStore())==0) {
            $_productCollection = Mage::getSingleton('core/session')->getProductCollection();

            $direction = Mage::getStoreConfig('dynamiclinks/general/direction',Mage::app()->getStore());
            $sorton = Mage::getStoreConfig('dynamiclinks/general/sorton',Mage::app()->getStore());
            $under = Mage::getStoreConfig('dynamiclinks/general/unders',Mage::app()->getStore());
            $above = Mage::getStoreConfig('dynamiclinks/general/aboves',Mage::app()->getStore());

            // put all ids in an array
            foreach($_productCollection as $key=>$_product) {
                $productIds[] = $_product['entity_id'];
            }

            // create a new collection with all products and sort it on price
            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->setOrder($sorton,'asc');

           $products = $products->getData();

            // find the current product's key in the array
            foreach($products as $key=>$_product) {
                if($_product['entity_id']==$product->getId()) $currentKey = $key;
            }

            // find the X products that are cheaper and the Y products that are more expensive
            $relatedProductIds = array();
            for($i=1;$i<=$under;$i++) { $relatedProductIds[] = $products[$currentKey-$i]['entity_id']; }
            for($i=1;$i<=$above;$i++) { $relatedProductIds[] = $products[$currentKey+$i]['entity_id']; }

            // create a new collection with the selected products
            $this->_itemCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $relatedProductIds));

            // sort them according to price or name if that is forced
            if(Mage::getStoreConfig('dynamiclinks/general/force',Mage::app()->getStore())) {
                $this->_itemCollection->setOrder($sorton,$direction);
            }
        } else {
            $this->_itemCollection = $product->getUpSellProductCollection()
                ->addAttributeToSort('position', 'asc')
                ->addStoreFilter()
            ;
        }

        Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
            Mage::getSingleton('checkout/session')->getQuoteId()
        );
        $this->_addProductAttributesAndPrices($this->_itemCollection);

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        if ($this->getItemLimit('upsell') > 0) {
            $this->_itemCollection->setPageSize($this->getItemLimit('upsell'));
        }

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product'       => $product,
            'collection'    => $this->_itemCollection,
            'limit'         => $this->getItemLimit()
        ));

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

}