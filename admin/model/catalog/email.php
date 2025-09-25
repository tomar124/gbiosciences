<?php
class ModelCatalogEmail extends Model {
	public function addEmail($data) {
		$this->db->query("INSERT INTO email_template SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', email_subject = '" . $this->db->escape($data['email_subject']) . "', status = '" . (int)$data['status'] . "'");

		$id = $this->db->getLastId();
                
		return $id;
	}

	public function editEmail($id, $data) {
            $this->db->query("UPDATE email_template SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', email_subject = '" . $this->db->escape($data['email_subject']) . "', status = '" . (int)$data['status'] . "' WHERE id = '" . (int)$id . "'");

	}

	public function deleteEmail($id) {
		$this->db->query("DELETE FROM email_template WHERE id = '" . (int)$id . "'");
	}

	
	public function getEmail($id) {
		$query = $this->db->query("SELECT * FROM email_template WHERE id = " . (int)$id);

		return $query->row;
	}

	public function getEmails($data = array()) {
		$sql = "SELECT id, name, description, email_subject, status FROM email_template";

		

		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
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

	public function getTotalEmails() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM email_template");

		return $query->row['total'];
	}
	
}
