<?php 
    class ModelAccountAddtoquote extends Model {
        public function addQuote($data) {
            $query = $this->db->query("UPDATE " . DB_PREFIX . "quote SET quote_data = '" . $this->db->escape($data['data_items']) . "' WHERE quote_id = " . $data['last_id']);
        }
        
        public function addQuotes($data) {
            $query = $this->db->query("INSERT INTO " . DB_PREFIX . "quote SET customer_id = '" . $data['customer_id'] . "',  comment = '" . $this->db->escape($data['comment']) . "',  country_id = '" . $data['country']['country_id'] . "' ");
        
            $data['last_id'] = $this->db->getLastId();
            
            foreach($data['products'] as $product) {
               $details[] = array(
                   'product_id' => $product['product_id'],
                   'quantity' => $product['quantity'],
                   'price' => $product['price'],
                   'option' => isset($product['option']) ? $product['option'] : array()
               );
            }
           $data['data_items'] = serialize($details);
           $this->addQuote($data);
           
           $this->db->query("INSERT INTO " . DB_PREFIX . "quote_history SET quote_id = '" . (int)$data['last_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', quote_data = '" . $this->db->escape($data['data_items']) . "', status = 'PENDING', date_added = NOW()");
           
           return $data['last_id'];
        }
        
        public function getDistributorsbycountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "distributor  WHERE country = '" . (int)$country_id . "'");
                
		return $query->rows;
	}
        
    }