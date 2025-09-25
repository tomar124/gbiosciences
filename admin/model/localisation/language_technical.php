<?php
class ModelLocalisationLanguageTechnical extends Model {
	public function addLanguageTechnical($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "language_technical SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "'");
                
                $language_technical_id = $this->db->getLastId();
                
                /*if($data['is_default']){
                    $this->db->query("UPDATE " . DB_PREFIX . "language_technical SET is_default = 0");
                    
                    $this->db->query("UPDATE " . DB_PREFIX . "language_technical SET is_default = 1 WHERE language_technical_id = " . $language_technical_id);
                }*/

		$this->cache->delete('language_technical');

		return $language_technical_id;
	}

	public function editLanguageTechnical($language_technical_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "language_technical SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "' WHERE language_technical_id = '" . (int)$language_technical_id . "'");
                
                /*if($data['is_default']){
                    $this->db->query("UPDATE " . DB_PREFIX . "language_technical SET is_default = 0");
                    
                    $this->db->query("UPDATE " . DB_PREFIX . "language_technical SET is_default = 1 WHERE language_technical_id = " . $language_technical_id);
                }*/
                
		$this->cache->delete('language_technical');
	}
	
	public function deleteLanguageTechnical($language_technical_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "language_technical WHERE language_technical_id = '" . (int)$language_technical_id . "'");

		$this->cache->delete('language_technical');
	}

	public function getLanguageTechnical($language_technical_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language_technical WHERE language_technical_id = '" . (int)$language_technical_id . "'");

		return $query->row;
	}

	public function getLanguageTechnicals($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "language_technical";

			$sort_data = array(
				'name',
				'code',
				'sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY sort_order, name";
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
		} else {
			$language_technical_data = $this->cache->get('language_technical');

			if (!$language_technical_data) {
				$language_technical_data = array();

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language_technical ORDER BY sort_order, name");

				foreach ($query->rows as $result) {
					$language_technical_data[$result['code']] = array(
						'language_technical_id' => $result['language_technical_id'],
						'name'        => $result['name'],
						'code'        => $result['code'],
						'sort_order'  => $result['sort_order'],
						'is_default'      => $result['is_default']
					);
				}

				$this->cache->set('language_technical', $language_technical_data);
			}

			return $language_technical_data;
		}
	}

	public function getLanguageTechnicalByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language_technical` WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function getTotalLanguageTechnicals() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "language_technical");

		return $query->row['total'];
	}
}
