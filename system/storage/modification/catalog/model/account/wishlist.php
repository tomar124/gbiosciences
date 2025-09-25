<?php
class ModelAccountWishlist extends Model {
	
            public function addWishlist($product_id , $quantity , $option) {
            $quote_entry = $this->db->query("Select quantity FROM ". DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "' AND `option` = '" . $option . "'");
            
            if(isset($quote_entry->row['quantity'])){
                $quantity = $quantity + $quote_entry->row['quantity'];
            }
            
		
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "' AND `option` = '" . $option . "'");
            

		
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_wishlist SET customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', quantity = '" . (int)$quantity . "', `option` = '" . $option . "', date_added = NOW()");
            
	}

	
            public function deleteWishlist($product_id ,$option) {
                    $option = html_entity_decode($option);
            
		
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "' AND `option` = '" . $option . "'");
            
	}

	public function getWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->rows;
	}


             public function editWishlist($product_id , $quantity ,$option) {
                    $option = html_entity_decode($option);
                    $this->db->query("UPDATE " . DB_PREFIX . "customer_wishlist SET  quantity = '" . (int)$quantity . "', date_added = NOW() WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "' AND `option` = '" . $option . "'");
            }
        
            public function clear() {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");
            }
        
            public function getOptionsData($product_info, $selected_options) {
                    $product_id = $product_info['product_id'];
                    $quantity = $product_info['quantity'];

                    $option_data = array();

                    if (!empty($selected_options)) {
                            $option_price = 0;
                            $option_points = 0;
                            $option_weight = 0;

                            $options = $selected_options ;

                            foreach ($options as $product_option_id => $value) {
                                    $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                                    if ($option_query->num_rows) {
                                            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                                                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                                                    if ($option_value_query->num_rows) {
                                                            if ($option_value_query->row['price_prefix'] == '+') {
                                                                    $option_price += $option_value_query->row['price'];
                                                            } elseif ($option_value_query->row['price_prefix'] == '-') {
                                                                    $option_price -= $option_value_query->row['price'];
                                                            }

                                                            if ($option_value_query->row['points_prefix'] == '+') {
                                                                    $option_points += $option_value_query->row['points'];
                                                            } elseif ($option_value_query->row['points_prefix'] == '-') {
                                                                    $option_points -= $option_value_query->row['points'];
                                                            }

                                                            if ($option_value_query->row['weight_prefix'] == '+') {
                                                                    $option_weight += $option_value_query->row['weight'];
                                                            } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                                                    $option_weight -= $option_value_query->row['weight'];
                                                            }

                                                            if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                                                                    $stock = false;
                                                            }

                                                            $option_data[] = array(
                                                                    'product_option_id'       => $product_option_id,
                                                                    'product_option_value_id' => $value,
                                                                    'option_id'               => $option_query->row['option_id'],
                                                                    'option_value_id'         => $option_value_query->row['option_value_id'],
                                                                    'name'                    => $option_query->row['name'],
                                                                    'value'                   => $option_value_query->row['name'],
                                                                    'type'                    => $option_query->row['type'],
                                                                    'quantity'                => $option_value_query->row['quantity'],
                                                                    'subtract'                => $option_value_query->row['subtract'],
                                                                    'price'                   => $option_value_query->row['price'],
                                                                    'price_prefix'            => $option_value_query->row['price_prefix'],
                                                                    'points'                  => $option_value_query->row['points'],
                                                                    'points_prefix'           => $option_value_query->row['points_prefix'],
                                                                    'weight'                  => $option_value_query->row['weight'],
                                                                    'weight_prefix'           => $option_value_query->row['weight_prefix']
                                                            );
                                                    }

                                            } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                                                    foreach ($value as $product_option_value_id) {
                                                            $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                                                            if ($option_value_query->num_rows) {
                                                                    if ($option_value_query->row['price_prefix'] == '+') {
                                                                            $option_price += $option_value_query->row['price'];
                                                                    } elseif ($option_value_query->row['price_prefix'] == '-') {
                                                                            $option_price -= $option_value_query->row['price'];
                                                                    }

                                                                    if ($option_value_query->row['points_prefix'] == '+') {
                                                                            $option_points += $option_value_query->row['points'];
                                                                    } elseif ($option_value_query->row['points_prefix'] == '-') {
                                                                            $option_points -= $option_value_query->row['points'];
                                                                    }

                                                                    if ($option_value_query->row['weight_prefix'] == '+') {
                                                                            $option_weight += $option_value_query->row['weight'];
                                                                    } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                                                            $option_weight -= $option_value_query->row['weight'];
                                                                    }

                                                                    if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                                                                            $stock = false;
                                                                    }

                                                                    $option_data[] = array(
                                                                            'product_option_id'       => $product_option_id,
                                                                            'product_option_value_id' => $product_option_value_id,
                                                                            'option_id'               => $option_query->row['option_id'],
                                                                            'option_value_id'         => $option_value_query->row['option_value_id'],
                                                                            'name'                    => $option_query->row['name'],
                                                                            'value'                   => $option_value_query->row['name'],
                                                                            'type'                    => $option_query->row['type'],
                                                                            'quantity'                => $option_value_query->row['quantity'],
                                                                            'subtract'                => $option_value_query->row['subtract'],
                                                                            'price'                   => $option_value_query->row['price'],
                                                                            'price_prefix'            => $option_value_query->row['price_prefix'],
                                                                            'points'                  => $option_value_query->row['points'],
                                                                            'points_prefix'           => $option_value_query->row['points_prefix'],
                                                                            'weight'                  => $option_value_query->row['weight'],
                                                                            'weight_prefix'           => $option_value_query->row['weight_prefix']
                                                                    );
                                                            }
                                                    }
                                            } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                                                    $option_data[] = array(
                                                            'product_option_id'       => $product_option_id,
                                                            'product_option_value_id' => '',
                                                            'option_id'               => $option_query->row['option_id'],
                                                            'option_value_id'         => '',
                                                            'name'                    => $option_query->row['name'],
                                                            'value'                   => $value,
                                                            'type'                    => $option_query->row['type'],
                                                            'quantity'                => '',
                                                            'subtract'                => '',
                                                            'price'                   => '',
                                                            'price_prefix'            => '',
                                                            'points'                  => '',
                                                            'points_prefix'           => '',
                                                            'weight'                  => '',
                                                            'weight_prefix'           => ''
                                                    );
                                            }
                                    }
                            }
                    }

                    return $option_data;
            }
            
	public function getTotalWishlist() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
