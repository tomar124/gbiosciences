<?php
class ModelCatalogQuote extends Model {
	public function editQuote($quote_id, $data) {
                $details = array();

                foreach($data['product'] as $product) {
                        if (isset($product['option'])) {
                                $option = array_filter($product['option']);
                        } else {
                                $option = array();
                        }

                        $details[] = array(
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'price' => $product['price'],
                            'option' => $option
                        );
                }
                
                $this->db->query("UPDATE `" . DB_PREFIX . "quote` SET comment = '" . $this->db->escape($data['comment']) . "', quote_data = '" . serialize($details) . "', status = '" . $data['status'] . "' WHERE quote_id = '" . $quote_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "quote_history` SET comment = '" . $this->db->escape($data['comment']) . "', quote_data = '" . serialize($details) . "', status = '" . $data['status'] . "', quote_id = '" . $quote_id . "', date_added = NOW()");
        }

	public function getQuote($quote_id) {
		$query = $this->db->query("SELECT DISTINCT q.*, c.customer_id, CONCAT(c.firstname, ' ', c.lastname) as customer_name, c.email as customer_email, co.name as country FROM `" . DB_PREFIX . "quote` q LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = q.customer_id) LEFT JOIN `" . DB_PREFIX . "country` co ON (co.country_id = q.country_id) WHERE q.quote_id = '" . (int)$quote_id . "'");

		return $query->row;
	}

	public function getQuotes($data = array()) {
		$sql = "SELECT q.*, CONCAT(c.firstname, ' ', c.lastname) as name, co.name as country FROM `" . DB_PREFIX . "quote` q LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = q.customer_id) LEFT JOIN `" . DB_PREFIX . "country` co ON (co.country_id = q.country_id)";
                
                if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " WHERE q.status = '" . (int)$data['filter_status'] . "'";
		}
                
                if (!empty($data['filter_country'])) {
                        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                                $sql .= " AND q.country_id LIKE '" . (int)$data['filter_country'] . "'";
                        }else {
                                $sql .= " WHERE q.country_id = '" . (int)$data['filter_country'] . "'";
                        }
		}
		
		$sql .= " GROUP BY q.quote_id";
                
		$sort_data = array(
			'q.quote_id',
			'name',
                        'q.status',
                        'co.name',
                        'q.status',
                        'q.created_on'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY q.quote_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
                
                $sql .= " , q.created_on DESC";

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

	public function getTotalQuotes($data = array()) {
                $sql = "SELECT COUNT(DISTINCT quote_id) AS total FROM `" . DB_PREFIX . "quote` q";

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " WHERE status = '" . (int)$data['filter_status'] . "'";
		}
                
                if (!empty($data['filter_country'])) {
                        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                                $sql .= " AND country_id LIKE '" . (int)$data['filter_country'] . "'";
                        }else {
                                $sql .= " WHERE country_id = '" . (int)$data['filter_country'] . "'";
                        }
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        
        public function updateQuoteStatusToExpired($date) {
                $this->db->query("UPDATE `" . DB_PREFIX . "quote` SET status = 2 WHERE cast(created_on as date) < '" . $date . "'");
        }
        
        public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM `" . DB_PREFIX . "seo_url` WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getQuoteHistories($quote_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT qh.date_added, qh.status, qh.comment FROM " . DB_PREFIX . "quote_history qh WHERE qh.quote_id = '" . (int)$quote_id . "' ORDER BY qh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalQuoteHistories($quote_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "quote_history WHERE quote_id = '" . (int)$quote_id . "'");

		return $query->row['total'];
	}
        
        public function getProductSpecial($product_id) {
            $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
            
            return $query->num_rows ? $query->row['price'] : 0;
        }
}
