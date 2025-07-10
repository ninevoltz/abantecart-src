<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelExtensionNexusAddress extends Model {

    /**
     * @param $state
     * @return mixed
     */
    public function getNexusByState($state) {
        $sql = "SELECT count(*) as total FROM ".$this->db->table('taxjar_nexus')." WHERE `region_code`='".$state."'";
        $query = $this->db->query($sql);
        return (int)$query->row['total'];
    }

    /**
     * @param $region_code
     * @param $country_code
     * @return mixed
     */
    public function getFallbackRate($region_code,$country_code) {
        $sql="SELECT fallback_rate FROM ".$this->db->table('taxjar_nexus')." WHERE region_code='".$region_code."' AND country_code='".$country_code."'";
        $query=$this->db->query($sql);
        return $query->row['fallback_rate'];
    }
}