<?php
    class ModelExtensionModuleDistributer extends Model {
        public function addDistributer($data) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "distributor SET country = '" . $this->db->escape($data['country']) . "', postalcode = '" . $this->db->escape($data['pincode']) . "', officenumber = '" . $this->db->escape($data['officenumber']) . "', mobilenumber = '" . $this->db->escape($data['mobilenumber']) . "', faxno= '" . $this->db->escape($data['faxno']) . "', storagespace= '" . $this->db->escape($data['storage']) . "', status= '" . $this->db->escape($data['status']) . "'");

                $distributer_id = $this->db->getLastId();
                
                foreach ($data['distributor_discription'] as $language_id => $value) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "distributor_discription SET distributer_id = '" . (int)$distributer_id . "', language_id = '" . (int)$language_id . "', companyname = '" . $this->db->escape($value['companyname']) . "', firstname = '" . $this->db->escape($value['firstname']) . "', lastname = '" . $this->db->escape($value['lastname']) . "', email = '" . $this->db->escape($value['email']) . "', website = '" . $this->db->escape($value['website']) . "', address = '" . $this->db->escape($value['address']) . "', city = '" . $this->db->escape($value['city']) . "'");
                }
                
        }
        
        public function editDistributer($distributer_id , $data) {           
                $this->db->query("UPDATE " . DB_PREFIX . "distributor SET country = '" . $this->db->escape($data['country']) . "', postalcode = '" . $this->db->escape($data['pincode']) . "', officenumber = '" . $this->db->escape($data['officenumber']) . "', mobilenumber = '" . $this->db->escape($data['mobilenumber']) . "', faxno= '" . $this->db->escape($data['faxno']) . "', storagespace= '" . $this->db->escape($data['storage']) . "', status= '" . $this->db->escape($data['status']) . "' WHERE distributer_id = '" . (int)$distributer_id . "'");
                
                foreach ($data['distributor_discription'] as $language_id => $value) {
                        $this->db->query("UPDATE " . DB_PREFIX . "distributor_discription SET companyname = '" . $this->db->escape($value['companyname']) . "', firstname = '" . $this->db->escape($value['firstname']) . "', lastname = '" . $this->db->escape($value['lastname']) . "', email = '" . $this->db->escape($value['email']) . "', website = '" . $this->db->escape($value['website']) . "', address = '" . $this->db->escape($value['address']) . "', city = '" . $this->db->escape($value['city']) . "' WHERE distributer_id = '" . (int)$distributer_id . "' AND language_id = '" . (int)$language_id . "'");
                        
                }
        }
        
        public function deleteDistributer($distributer_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "distributor WHERE distributer_id = '" . (int)$distributer_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "distributor_discription WHERE distributer_id = '" . (int)$distributer_id . "'");
        }
        
        public function getTotalDistributer() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "distributor");

		return $query->row['total'];
	}
        
        public function getDistributers($data = array()) {
                $sql = "Select *, dd.companyname AS companyname , dd.email , d.mobilenumber AS phone , c.name AS countryname from " . DB_PREFIX . "distributor AS d LEFT JOIN " .DB_PREFIX . "distributor_discription AS dd ON (d.distributer_id = dd.distributer_id) LEFT JOIN " . DB_PREFIX . "country AS c ON c.country_id = d.country WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
               
                $sort_data = array(
                        'companyname',
                        'email',
                        'phone',
                        'countryname'
                );

                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $sql .= " ORDER BY " . $data['sort'];
                } else {
                        $sql .= " ORDER BY companyname";
                }

                if (isset($data['order']) && ($data['order'] == 'DESC')) {
                        $sql .= " DESC";
                } else {
                        $sql .= " ASC";
                }

                if (isset($data['start']) || isset($data['limit'])) {
                        if ($data['start'] < 0) {
                                $data['start'] = 0;
                        }

                        if ($data['limit'] < 1) {
                                $data['limit'] = 20;
                        }

                        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
                }

                $query = $this->db->query($sql);
               
                return $query->rows;
        }
        
        public function getDistributer($distributer_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "distributor WHERE distributer_id = '" . (int)$distributer_id . "'");
                
                if ($query->num_rows) {
                        $data = $query->row;
                        $data['distributor_discription'] = $this->getDistributorDescriptions($distributer_id);

                        return $data;
                }
        }
        
        public function getDistributorDescriptions($distributer_id) {
                $distributor_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "distributor_discription WHERE distributer_id = '" . (int)$distributer_id . "'");

		foreach ($query->rows as $result) {
			$distributor_description_data[$result['language_id']] = array(
				'companyname'   => $result['companyname'],
				'firstname'     => $result['firstname'],
				'lastname'      => $result['lastname'],
				'email'         => $result['email'],
				'website'       => $result['website'],
				'address'       => $result['address'],
                                'city'          => $result['city']
			);
		}
		return $distributor_description_data;
        }
    }
?>