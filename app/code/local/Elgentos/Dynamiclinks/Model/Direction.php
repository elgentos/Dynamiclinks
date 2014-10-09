<?php
class Elgentos_Dynamiclinks_Model_Direction
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = array();
        $return[] = array('value'=>'asc','label'=>Mage::helper('dynamiclinks')->__('Ascending'));
        $return[] = array('value'=>'desc','label'=>Mage::helper('dynamiclinks')->__('Descending'));
        return $return;
    }

}