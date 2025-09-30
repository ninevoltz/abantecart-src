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
     * @param $data
     * @return int
     */
    public function addNexusAddress($data) {
       $sql = "INSERT INTO " . $this->db->table('taxjar_nexus') . " SET 
           `country_code`='" . $data['country_code'] . "',
           `country`='" . $data['country'] . "',
           `region_code`='" . $data['region_code'] . "',
           `region`='" . $data['region'] . "'";
       $this->db->query($sql);
       return $this->db->getLastId();
    }

    /**
     *
     */
    public function deleteNexus() {
        $this->db->query("DELETE FROM ".$this->db->table('taxjar_nexus'));
    }

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
     * @return mixed
     */
    public function getNexus() {
        $sql = "SELECT count(*) as total FROM ".$this->db->table('taxjar_nexus');
        $query = $this->db->query($sql);
        return (int)$query->row['total'];
    }

    /**
     * @return mixed
     */
    public function getNexusAddress() {
        $sql = "SELECT * FROM ".$this->db->table('taxjar_nexus');
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param $nexus_id
     * @param $rate
     */
    public function addFallbackRate($nexus_id,$rate) {
        $sql="UPDATE ".$this->db->table('taxjar_nexus')." SET fallback_rate=".(float)$rate." WHERE nexus_id=".(int)$nexus_id;
        $this->db->query($sql);
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