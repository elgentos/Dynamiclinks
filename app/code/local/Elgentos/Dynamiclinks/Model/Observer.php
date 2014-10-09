<?php
class Elgentos_Dynamiclinks_Model_Observer
{
    public function dispatcher($observer)
    {
    	if(!Mage::getStoreConfig('dynamiclinks/general/disabled',Mage::app()->getStore())) {
    	   Mage::getSingleton('core/session')->setProductCollection($observer->getEvent()->getCollection()->getData());
    	}
    }
}