<?php
class Elgentos_Dynamiclinks_Model_Sorton
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = array();
        $return[] = array('value'=>'price','label'=>Mage::helper('dynamiclinks')->__('Price'));
        $return[] = array('value'=>'name','label'=>Mage::helper('dynamiclinks')->__('Name'));
        $return[] = array('value'=>'position','label'=>Mage::helper('dynamiclinks')->__('Position'));
        return $return;
    }

}