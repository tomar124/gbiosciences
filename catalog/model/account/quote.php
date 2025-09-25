<?php
class ModelAccountQuote extends Model {
	public function getQuote($quote_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote WHERE quote_id = '" . (int)$quote_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' ");
                
		return $query->row;
		} 
	
        public function getQuotes($start = 0, $limit = 20) { 
                
                if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}
            
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote WHERE customer_id=" . (int)$this->customer->getId() . " ORDER BY created_on DESC  LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getQuoteProduct($quote_id, $quote_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote_product WHERE quote_id = '" . (int)$quote_id . "' AND quote_product_id = '" . (int)$quote_product_id . "'");

		return $query->row;
	}

	public function getQuoteProducts($quote_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote_product WHERE quote_id = '" . (int)$quote_id . "'");

		return $query->rows;
	}

	public function getQuoteOptions($quote_id, $quote_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote_option WHERE quote_id = '" . (int)$quote_id . "' AND quote_product_id = '" . (int)$quote_product_id . "'");

		return $query->rows;
	}

	public function getQuoteVouchers($quote_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quote_voucher` WHERE quote_id = '" . (int)$quote_id . "'");

		return $query->rows;
	}

	public function getQuoteTotals($quote_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "quote_total WHERE quote_id = '" . (int)$quote_id . "' ORDER BY sort_quote");

		return $query->rows;
	}

	public function getQuoteHistories($quote_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "quote_history oh LEFT JOIN " . DB_PREFIX . "quote_status os ON oh.quote_status_id = os.quote_status_id WHERE oh.quote_id = '" . (int)$quote_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

		return $query->rows;
	}

	public function getTotalQuotes() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "quote`  WHERE customer_id = '" . (int)$this->customer->getId() . "' ");
                
		return $query->row['total'];
	}

	public function updateQuoteStatusToExpired($date90DaysBack, $quote_id) {
            $this->db->query("UPDATE `" . DB_PREFIX . "quote` SET status = 2 WHERE quote_id = '" . (int)$quote_id . "' AND cast(created_on as date) < '" . $date90DaysBack . "'");
        }
}