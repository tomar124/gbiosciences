<?php
class ModelCatalogProduct extends Model {
	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
	}

	
         public function getProduct($product_id) {
                $sql = "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer,";
                $sql .= " (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount,";
                $sql .= " (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,";
                $sql .= " (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "') AS reward, ";
                $sql .= "(SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "') AS stock_status, ";
                $sql .= "(SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int) $this->config->get('config_language_id') . "') AS weight_class,";
                $sql .= " (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int) $this->config->get('config_language_id') . "') AS length_class, ";
                $sql .= "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,";
                $sql .= " (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p ";
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "' ";

                $query = $this->db->query($sql);
                if ($query->num_rows) {
                        $detail = array(
                                'product_id' => $query->row['product_id'],
                                'gp_parent_id' => $query->row['gp_parent_id'],
                                'name' => $query->row['name'],
                                'description' => $query->row['description'],
                                'reference' => $query->row['reference'],
                                'meta_title' => $query->row['meta_title'],
                                'meta_description' => $query->row['meta_description'],
                                'meta_keyword' => $query->row['meta_keyword'],
                                'tag' => $query->row['tag'],
                                'model' => $query->row['model'],
                                'sku' => $query->row['sku'],
                                'upc' => $query->row['upc'],
                                'ean' => $query->row['ean'],
                                'jan' => $query->row['jan'],
                                'isbn' => $query->row['isbn'],
                                'mpn' => $query->row['mpn'],
                                'location' => $query->row['location'],
                                'quantity' => $query->row['quantity'],
                                'stock_status' => $query->row['stock_status'],
                                'image' => $query->row['image'],
                                'alt_text' => $query->row['alt_text'],
                                'caption' => $query->row['caption'],
                                'manufacturer_id' => $query->row['manufacturer_id'],
                                'manufacturer' => $query->row['manufacturer'],
                                'price' => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                                'special' => $query->row['special'],
                                'reward' => $query->row['reward'],
                                'points' => $query->row['points'],
                                'tax_class_id' => $query->row['tax_class_id'],
                                'date_available' => $query->row['date_available'],
                                'weight' => $query->row['weight'],
                                'weight_class_id' => $query->row['weight_class_id'],
                                'length' => $query->row['length'],
                                'width' => $query->row['width'],
                                'height' => $query->row['height'],
                                'length_class_id' => $query->row['length_class_id'],
                                'subtract' => $query->row['subtract'],
                                'rating' => round($query->row['rating']),
                                'reviews' => $query->row['reviews'] ? $query->row['reviews'] : 0,
                                'minimum' => $query->row['minimum'],
                                'sort_order' => $query->row['sort_order'],
                                'status' => $query->row['status'],
                                'date_added' => $query->row['date_added'],
                                'date_modified' => $query->row['date_modified'],
                                'viewed' => $query->row['viewed'],
                                'shipping_condition' => $query->row['shipping_condition'],
                                'size' => $query->row['size'],
                                'special_product' => $query->row['special_product'],
                        );
                return $detail;
                } else {
                        return false;
                }
            }
        
	public function getProducts($data = array()) {
		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = $this->cache->get('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			
			$this->cache->set('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$product_data = array();

			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($product_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "product_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.product_id = '" . (int)$product_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($product_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "product_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.product_id = " . (int)$product_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}


 public function getGMCDatatableProducts($data){
                    $sql = "SELECT SQL_CALC_FOUND_ROWS ";

                    for ($i = 0; $i < count($data['columns']); $i++) {
                            $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                            if (!empty($data['columns'][$i])) {
                                    $sql .= $data['columns'][$i] . ", ";
                            }
                    }

                    $sql = substr_replace($sql, "", -2);
                    $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
                    
if($data['category_id'] == '346') {
$sql .= ", (SELECT GROUP_CONCAT(name) AS Target FROM " . DB_PREFIX . "special_product_filter_description spfd LEFT JOIN " . DB_PREFIX . "product_to_special_product_filter p2spf ON (spfd.special_product_filter_id = p2spf.special_product_filter_id) WHERE p2spf.product_id = p.product_id AND p2spf.special_product_filter_group_id = 7) AS Target";
$sql .= ", (SELECT GROUP_CONCAT(name) AS pathway FROM " . DB_PREFIX . "special_product_filter_description spfd LEFT JOIN " . DB_PREFIX . "product_to_special_product_filter p2spf ON (spfd.special_product_filter_id = p2spf.special_product_filter_id) WHERE p2spf.product_id = p.product_id AND p2spf.special_product_filter_group_id = 8) AS Pathway";
}
$sql .= ", p.image FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_special_product_filter_grouped_values p2spfgv ON (p.product_id = p2spfgv.product_id)";
            

$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";
$sql .= " AND p.gmc_special_product = '1'  AND p2c.category_id = '" . (int) $data['category_id'] . "'";


if(!empty($data['special_product_filter_options'])) {
$special_product_filter_grouped_string = "";
foreach($data['special_product_filter_options'] as $special_product_filter_group_id => $special_product_filter_id){
$special_product_filter_grouped_string = "%SPFG:".$special_product_filter_group_id.",SPF:".$special_product_filter_id."%";
$sql .= " AND p2spfgv.value LIKE '".$special_product_filter_grouped_string."'";
}
}
            

            if(!empty($data['special_product_filter_options'])) {
                    $special_product_filter_grouped_string = "";
                    foreach($data['special_product_filter_options'] as $special_product_filter_group_id => $special_product_filter_id){
                            $special_product_filter_grouped_string = "%SPFG:".$special_product_filter_group_id.",SPF:".$special_product_filter_id."%";

                            $sql .= " AND p2spfgv.value LIKE '".$special_product_filter_grouped_string."'";
                    }
            }
            
                    $sSearch = "";
                    $sSearchFlag = false;
                        for ($i = 0; $i < count($data['columns']); $i++) {
                            $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                            $data['sSearch'][$i] = str_replace(' ', '', $data['sSearch'][$i]);
                            if (!empty($data['columns'][$i]) && !empty($data['sSearch'][$i])) {
                                if ($i == 0) {
                                   $sSearchFlag = true;
                                   $sSearch .= " AND (";
                                }

                                $sSearch .= $data['columns'][$i] . " LIKE '%" . $data['sSearch'][$i] . "%' OR ";
                        }
                    }

                    
                    if($sSearchFlag) {
                        $sSearch = substr_replace($sSearch, "", -3);
                        $sSearch .= ")";

                        $sql .= $sSearch;
                    } else {
                        $sql .= substr_replace($sSearch, "", -4);
                    }
                    if (isset($data['search'])) {
                        $sql .= (substr($sql, -6) == 'WHERE ') ? " ( " : " AND ( ";

                        for ($i = 0; $i < count($data['columns']); $i++) {
                                $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                                if (!empty($data['columns'][$i]) && $data['serchable'][$i] != 'false') {
                                        $sql .= $data['columns'][$i] . " LIKE '%" . $data['search']['keyword'] . "%' OR ";
                                }
                        }

                        $sql = substr_replace($sql, "", -3);
                        $sql .= ')';
                    }

if (substr($sql, -6) == 'WHERE ') {
                            $sql = substr_replace($sql, "", -6);
                        }
                        
                        if (isset($data['sort'])) {
                            $sql .= " ORDER BY " . $data['sort']['column'] . " " . $data['sort']['order'];
                        }
                        
                        if (isset($data['limit'])) {
                            $sql .= " LIMIT " . $data['limit']['start'] . ", " . $data['limit']['end'];
                        }
                        
                        // echo $sql; die;
            

                    $query = $this->db->query($sql);

                    /* Data set length after filtering */
                    $sQuery = "SELECT FOUND_ROWS() as found";
                    $rResultFilterTotal = $this->db->query($sQuery);
                    $aResultFilterTotal = $rResultFilterTotal->row;
                    $iFilteredTotal = $aResultFilterTotal['found'];

                    /* Total data set length */
                    $sQuery = "SELECT COUNT(p.product_id) as count FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) WHERE p2c.category_id = '" . (int) $data['category_id'] . "'";
                    $rResultTotal = $this->db->query($sQuery);
                    $aResultTotal = $rResultTotal->row;
                    $iTotal = $aResultTotal['count'];

                    $output = array();

                    $output = array(
                            "sEcho" => intval($data['sEcho']),
                            "iTotalRecords" => $iTotal,
                            "iTotalDisplayRecords" => $iFilteredTotal
                    );

                    $aaData = array();

                    //$this->load->model('tool/image');
                    
// echo "<pre>"; print_r($query->rows); die;
                    if ($query->num_rows) {
foreach ($query->rows as $key => $result) {

if($data['path']){
$link = $this->url->link('product/product', 'path=' . $data['path'] . '&product_id=' . $result['product_id']);
}else{
$link = $this->url->link('product/product', '&product_id=' . $result['product_id']);
}

$aaData[$key] = array(

'<h4 class="name"><a href="'.$link.'">'.$result['model'].'</a></h4>',
str_replace(array("&lt;p&gt;", "&lt;/p&gt;"), "", $result['description']),
$result['Target'],
$result['Pathway'],
'<h4 class="name"><a href="'.$link.'"><i class="fa fa-eye fa-2x"></i></a></h4>',
// $result['model'],
// '<h4 class="name"><a href="'.$link.'">Details <i class="fa fa-eye"></a></h4>',
// $result['size'],
// $priceHtml ?? $price,
// (((float)$result['price'] > 0) ? '<a id="addToCart'.$result['product_id'].'" onclick="addToCart('.$result['product_id'].');" class="hint--top" data-toggle="tooltip" title="Add to Cart"><i class="glyphicon glyphicon-shopping-cart"></i></a> | ' : '') . '<a id="addToQuote'.$result['product_id'].'" onclick="addToQuote('.$result['product_id'].');" class="hint--top" data-toggle="tooltip" title="Add to Quote"><i class="glyphicon glyphicon-list-alt"></i></a>',
);                                    
}
                    }

                    $output["aaData"] = $aaData;

                    return $output;
            }

            public function getDatatableProducts($data){
                    $sql = "SELECT SQL_CALC_FOUND_ROWS ";

                    for ($i = 0; $i < count($data['columns']); $i++) {
                            $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                            if (!empty($data['columns'][$i])) {
                                    $sql .= $data['columns'][$i] . ", ";
                            }
                    }

                    $sql = substr_replace($sql, "", -2);
                    $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
                    $sql .= ", p.image FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_special_product_filter_grouped_values p2spfgv ON (p.product_id = p2spfgv.product_id)";
            
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";
                    $sql .= " AND p2c.category_id = '" . (int) $data['category_id'] . "'";


            if(!empty($data['special_product_filter_options'])) {
                    $special_product_filter_grouped_string = "";
                    foreach($data['special_product_filter_options'] as $special_product_filter_group_id => $special_product_filter_id){
                            $special_product_filter_grouped_string = "%SPFG:".$special_product_filter_group_id.",SPF:".$special_product_filter_id."%";

                            $sql .= " AND p2spfgv.value LIKE '".$special_product_filter_grouped_string."'";
                    }
            }
            
                    $sSearch = "";
                    for ($i = 0; $i < count($data['columns']); $i++) {
                            $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                            $data['sSearch'][$i] = str_replace(' ', '', $data['sSearch'][$i]);
                            if (!empty($data['columns'][$i]) && !empty($data['sSearch'][$i])) {
                                    if ($i == 0) {
                                        $sSearch .= " AND ";
                                    }

                                    $sSearch .= $data['columns'][$i] . " LIKE '%" . $data['sSearch'][$i] . "%' AND ";
                            }
                    }

                    $sql .= substr_replace($sSearch, "", -4);

                    if (isset($data['search'])) {
                            $sql .= (substr($sql, -6) == 'WHERE ') ? " ( " : " AND ( ";

                            for ($i = 0; $i < count($data['columns']); $i++) {
                                    $data['columns'][$i] = str_replace(' ', '', $data['columns'][$i]);
                                    if (!empty($data['columns'][$i]) && $data['serchable'][$i] != 'false') {
                                            $sql .= $data['columns'][$i] . " LIKE '%" . $data['search']['keyword'] . "%' OR ";
                                    }
                            }

                            $sql = substr_replace($sql, "", -3);
                            $sql .= ')';
                    }

                    if (substr($sql, -6) == 'WHERE ') {
                            $sql = substr_replace($sql, "", -6);
                    }

                    if (isset($data['sort'])) {
                            $sql .= " ORDER BY " . $data['sort']['column'] . " " . $data['sort']['order'];
                    }

                    if (isset($data['limit'])) {
                            $sql .= " LIMIT " . $data['limit']['start'] . ", " . $data['limit']['end'];
                    }

                    $query = $this->db->query($sql);

                    /* Data set length after filtering */
                    $sQuery = "SELECT FOUND_ROWS() as found";
                    $rResultFilterTotal = $this->db->query($sQuery);
                    $aResultFilterTotal = $rResultFilterTotal->row;
                    $iFilteredTotal = $aResultFilterTotal['found'];

                    /* Total data set length */
                    $sQuery = "SELECT COUNT(p.product_id) as count FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) WHERE p2c.category_id = '" . (int) $data['category_id'] . "'";
                    $rResultTotal = $this->db->query($sQuery);
                    $aResultTotal = $rResultTotal->row;
                    $iTotal = $aResultTotal['count'];

                    $output = array();

                    $output = array(
                            "sEcho" => intval($data['sEcho']),
                            "iTotalRecords" => $iTotal,
                            "iTotalDisplayRecords" => $iFilteredTotal
                    );

                    $aaData = array();

                    //$this->load->model('tool/image');

                    if ($query->num_rows) {
                            foreach ($query->rows as $key => $result) {
                                    /*if($result['image']){
                                            $image = $this->model_tool_image->resize($result['image'], 500, 500);
                                    }else{
                                            $image = $this->model_tool_image->resize('no_image.png', 500, 500);
                                    }*/

                                    if($data['path']){
                                            $link = $this->url->link('product/product', 'path=' . $data['path'] . '&product_id=' . $result['product_id']);
                                    }else{
                                            $link = $this->url->link('product/product', '&product_id=' . $result['product_id']);
                                    }

                                    if ((float)$result['price'] > 0) {
                                            $price = $this->currency->format($result['price'], $this->session->data['currency']);

                                            if ((float)$result['special']) {
                                                    $newPrice = $this->currency->format($result['special'], $this->session->data['currency']);
                                            } elseif ((float)$result['discount'])  {
                                                    $newPrice = $this->currency->format($result['discount'], $this->session->data['currency']);
                                            }

                                            $priceHtml = "";
                                            if ((float)$result['special'] || (float)$result['discount']) {
                                                    $priceHtml = '<div class="price-group inline">';
                                                    $priceHtml .= '<div class="product-price-new">' . $newPrice . '</div>';
                                                    $priceHtml .= '<div class="product-price-old">' . $price . '</div>';
                                                    $priceHtml .= '<div>';
                                            } else {
                                                    $priceHtml = '<div class="price-group">';
                                                    $priceHtml .= '<div class="product-price">' . $price . '</div>';
                                                    $priceHtml .= '<div>';
                                            }
                                    } else {
                                        $priceHtml = '<div class="price-group">';
                                        $priceHtml .= '<div class="product-price"><a href="javascript:open_popup(22);">Please Inquire</a></div>';
                                        $priceHtml .= '<div>';
                                    }

                                    $aaData[$key] = array(
                                            //'<img width="50" height="50" src="'.$image.'" title="'.$result['name'].'" alt="'.$result['name'].'" />',                    
                                            $result['model'],
                                            $result['description'],
                                            '<h4 class="name"><a href="'.$link.'">'.$result['name'].'</a></h4>',
                                            $result['size'],
                                            $priceHtml ?? $price,
                                            (((float)$result['price'] > 0) ? '<a id="addToCart'.$result['product_id'].'" onclick="addToCart('.$result['product_id'].');" class="hint--top" data-toggle="tooltip" title="Add to Cart"><i class="glyphicon glyphicon-shopping-cart"></i></a> | ' : '') . '<a id="addToQuote'.$result['product_id'].'" onclick="addToQuote('.$result['product_id'].');" class="hint--top" data-toggle="tooltip" title="Add to Quote"><i class="glyphicon glyphicon-list-alt"></i></a>',
                                    );
                            }
                    }

                    $output["aaData"] = $aaData;

                    return $output;
            }
                
            public function getChilddetails($product_id) {
                    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "gp_grouped_child WHERE child_id=" . $product_id);

                    if ($query->num_rows) {
                            return $query->row['total'];
                    } else {
                            return 0;
                    }
            }
            

            public function getGroupProducts($data = array()) {
                    $sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

                    if (!empty($data['filter_category_id'])) {
                            if (!empty($data['filter_sub_category'])) {
                                    $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                            } else {
                                    $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                            }

                            if (!empty($data['filter_filter'])) {
                                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                            } else {
                                    $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                            }
                    } else {
                            $sql .= " FROM " . DB_PREFIX . "product p";
                    }

                    $sql .= " RIGHT JOIN " . DB_PREFIX . "gp_grouped gp ON (gp.product_id = p.product_id) ";

                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                    if (!empty($data['filter_category_id'])) {
                            if (!empty($data['filter_sub_category'])) {
                                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                            } else {
                                    $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                            }

                            if (!empty($data['filter_filter'])) {
                                    $implode = array();

                                    $filters = explode(',', $data['filter_filter']);

                                    foreach ($filters as $filter_id) {
                                            $implode[] = (int)$filter_id;
                                    }

                                    $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                            }
                    }

                    if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                            $sql .= " AND (";

                            if (!empty($data['filter_name'])) {
                                    $implode = array();

                                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                                    foreach ($words as $word) {
                                            $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                                    }

                                    if ($implode) {
                                            $sql .= " " . implode(" AND ", $implode) . "";
                                    }

                                    if (!empty($data['filter_description'])) {
                                            $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                                    }
                            }

                            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                                    $sql .= " OR ";
                            }

                            if (!empty($data['filter_tag'])) {
                                    $implode = array();

                                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                                    foreach ($words as $word) {
                                            $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                                    }

                                    if ($implode) {
                                            $sql .= " " . implode(" AND ", $implode) . "";
                                    }
                            }

                            if (!empty($data['filter_name'])) {
                                    $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            }

                            $sql .= ")";
                    }

                    if (!empty($data['filter_manufacturer_id'])) {
                            $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }

                    $sql .= " GROUP BY p.product_id";

                    $sort_data = array(
                            'pd.name',
                            'p.model',
                            'p.quantity',
                            'p.price',
                            'rating',
                            'p.sort_order',
                            'p.date_added'
                    );

                    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                                    $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
                            } elseif ($data['sort'] == 'p.price') {
                                    $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
                            } else {
                                    $sql .= " ORDER BY " . $data['sort'];
                            }
                    } else {
                            $sql .= " ORDER BY p.sort_order";
                    }

                    if (isset($data['order']) && ($data['order'] == 'DESC')) {
                            $sql .= " DESC, LCASE(pd.name) DESC";
                    } else {
                            $sql .= " ASC, LCASE(pd.name) ASC";
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

                    $product_data = array();

                    $query = $this->db->query($sql);

                    foreach ($query->rows as $result) {
                            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
                    }

                    return $product_data;
            }

            public function getSpecialProducts($data = array()) {
                    $sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

                    if (!empty($data['filter_category_id'])) {
                            if (!empty($data['filter_sub_category'])) {
                                    $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                            } else {
                                    $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                            }

                            if (!empty($data['filter_filter'])) {
                                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                            } else {
                                    $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                            }
                    } else {
                            $sql .= " FROM " . DB_PREFIX . "product p";
                    }

                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.special_product = 1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                    if (!empty($data['filter_category_id'])) {
                            if (!empty($data['filter_sub_category'])) {
                                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                            } else {
                                    $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                            }

                            if (!empty($data['filter_filter'])) {
                                    $implode = array();

                                    $filters = explode(',', $data['filter_filter']);

                                    foreach ($filters as $filter_id) {
                                            $implode[] = (int)$filter_id;
                                    }

                                    $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                            }
                    }

                    if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                            $sql .= " AND (";

                            if (!empty($data['filter_name'])) {
                                    $implode = array();

                                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                                    foreach ($words as $word) {
                                            $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                                    }

                                    if ($implode) {
                                            $sql .= " " . implode(" AND ", $implode) . "";
                                    }

                                    if (!empty($data['filter_description'])) {
                                            $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                                    }
                            }

                            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                                    $sql .= " OR ";
                            }

                            if (!empty($data['filter_tag'])) {
                                    $implode = array();

                                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                                    foreach ($words as $word) {
                                            $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                                    }

                                    if ($implode) {
                                            $sql .= " " . implode(" AND ", $implode) . "";
                                    }
                            }

                            if (!empty($data['filter_name'])) {
                                    $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                    $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            }

                            $sql .= ")";
                    }

                    if (!empty($data['filter_manufacturer_id'])) {
                            $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }

                    $sql .= " GROUP BY p.product_id";

                    $sort_data = array(
                            'pd.name',
                            'p.model',
                            'p.quantity',
                            'p.price',
                            'rating',
                            'p.sort_order',
                            'p.date_added'
                    );

                    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                                    $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
                            } elseif ($data['sort'] == 'p.price') {
                                    $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
                            } else {
                                    $sql .= " ORDER BY " . $data['sort'];
                            }
                    } else {
                            $sql .= " ORDER BY p.sort_order";
                    }

                    if (isset($data['order']) && ($data['order'] == 'DESC')) {
                            $sql .= " DESC, LCASE(pd.name) DESC";
                    } else {
                            $sql .= " ASC, LCASE(pd.name) ASC";
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

                    $product_data = array();

                    $query = $this->db->query($sql);

                    foreach ($query->rows as $result) {
                            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
                    }

                    return $product_data;
            }
        
            public function getAllProtocol($productStatus = true){
                    $sql = "SELECT pp.* FROM " . DB_PREFIX . "product_protocol pp";

                    if ($productStatus) {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pp.product_id) AND p.product_id IS NOT NULL"
                                    . " AND p.status = 1";
                    }

                    $sql .= " WHERE pp.pdf <> '' ORDER BY pp.product_id";

                    $query = $this->db->query($sql);

                    return $query->rows;
            }

            public function getAllSds($productStatus = true){
                    $sql = "SELECT ps.*, lt.name as language FROM " . DB_PREFIX . "product_sds ps"
                            . " LEFT JOIN " . DB_PREFIX . "language_technical lt ON (lt.language_technical_id = ps.language_technical_id)"
                            . " AND lt.language_technical_id IS NOT NULL";

                    if ($productStatus) {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = ps.product_id) AND p.product_id IS NOT NULL"
                                    . " AND p.status = 1";
                    }

                    $sql .= " WHERE ps.pdf <> '' ORDER BY lt.sort_order";

                    $query = $this->db->query($sql);

                    return $query->rows;
            }
            


            public function getFilteredSpecialProductFilterOptionByCategory($category_id, $special_product_filter_group_id) {
                    $sql = "SELECT DISTINCT(special_product_filter_id) FROM `oc_product_to_special_product_filter` where product_id IN (
                                select p.product_id from oc_product p 
                                left join oc_product_description pd on pd.product_id = p.product_id
                                left join oc_product_to_category pc on pc.product_id = p.product_id 
                                where pc.category_id = '" . (int)$category_id . "'
                            ) AND special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'";

                    $query = $this->db->query($sql);

                    return $query->rows;
            }
            
        public function getGroupedProductGrouped($product_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gp_grouped WHERE product_id = '" . (int) $product_id . "'");

                return $query->row;
        }

        public function getGroupedProductGroupedChilds($product_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gp_grouped_child WHERE product_id = '" . (int) $product_id . "' ORDER BY child_sort_order");

                return $query->rows;
        }
        
        public function getProtocol($product_id){
                $sql = "SELECT pdf FROM " . DB_PREFIX . "product_protocol WHERE product_id = '" . (int)$product_id . "'";
                $query = $this->db->query($sql);
            
                if($query->num_rows){
                        return $query->row['pdf'];
                }
                return FALSE;
        }
        
        public function getSds($product_id){
                $sql = "SELECT p.pdf, lt.name as language FROM " . DB_PREFIX . "product_sds p";
                $sql .= " LEFT JOIN " . DB_PREFIX . "language_technical lt ON (lt.language_technical_id = p.language_technical_id)";
                $sql .= " WHERE p.product_id = '" . (int)$product_id . "' AND lt.language_technical_id IS NOT NULL ORDER BY lt.sort_order";

                $query = $this->db->query($sql);

                if($query->num_rows){
                        return $query->rows;
                }
            return FALSE;
        }
        
        public function getCoa($product_id){
                $sql = "SELECT pdf, description FROM " . DB_PREFIX . "product_coa WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order";

                $query = $this->db->query($sql);

                if($query->num_rows){
                        return $query->rows;
                }
                return FALSE;
        }
        
        public function getOtherTechnical($product_id = array()){
                $sql = "SELECT DISTINCT(title), link, description FROM " . DB_PREFIX . "product_technical WHERE product_id IN ( " . implode(', ', $product_id) . ") GROUP BY title";

                $query = $this->db->query($sql);

                if($query->num_rows){
                        return $query->rows;
                }
                return FALSE;
        }
        
        public function getparent_id($product_id) {
                $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "gp_grouped_child WHERE child_id = '" . $product_id . "'");

                if ($query->num_rows) {
                        $parent_id = $query->row['product_id'];
                } else {
                        $parent_id = $product_id;
                }
            return $parent_id;
        }
        
        public function getProductReferences($product_id) { 
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_references WHERE product_id = '" . (int)$product_id . "' ORDER BY year DESC, text ASC");

                return $query->rows;
        }
        
        public function getReferencesCount($product_id) { 
                $query = $this->db->query("SELECT Count(*) as count FROM " . DB_PREFIX . "product_references WHERE product_id = '" . (int)$product_id . "' ORDER BY year ASC");

                return $query->row;
        }
        

            public function getTotalliterature($data = array()) {
                    if(isset($data['technical']) && !empty($data['technical'])){
                            $sql = " SELECT COUNT(" . $data['technical'] . ".product_id) as total FROM " . DB_PREFIX . "product_" . $data['technical'] . " " . $data['technical'] . " "
                                    . "LEFT JOIN " . DB_PREFIX . "gp_grouped_child gp ON (gp.child_id = " . $data['technical'] . ".product_id) "
                                    . "LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = gp.child_id) ";
                    } else {
                            $sql = " SELECT COUNT(DISTINCT protocol.product_id) as total FROM " . DB_PREFIX . "product_protocol protocol "
                                    . "LEFT JOIN " . DB_PREFIX . "gp_grouped_child gp ON (gp.child_id = protocol.product_id) "
                                    . "LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = gp.child_id) ";
                    }

                    $sql .= "LEFT JOIN " . DB_PREFIX . "product_description pdp ON (pdp.product_id = gp.product_id) "
                            . "LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) "
                            . "LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) "
                            . "WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";

                    if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                        $sql .= " AND (";

                        if (!empty($data['filter_name'])) {
                            $implode = array();

                            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                            foreach ($words as $word) {
                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                            }

                            if ($implode) {
                                $sql .= " " . implode(" AND ", $implode) . "";
                            }

                            if (!empty($data['filter_description'])) {
                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                            }

                            $sql .= " OR pdp.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                            $sql .= " OR pdp.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                        }

                        if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                            $sql .= " OR ";
                        }

                        if (!empty($data['filter_tag'])) {
                            $sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
                        }

                        if (!empty($data['filter_name'])) {
                            $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        }

                        $sql .= ")";
                    }        
                    $query = $this->db->query($sql);        
                    return $query->row['total'];
            }

            public function getLiterature($data = array()) { 
                    if(isset($data['technical']) && !empty($data['technical'])){
                            $sql = " SELECT " . $data['technical'] . ".*, pd.name AS product_name, pd.description AS product_description " . ($data['technical'] == 'sds' ? ", lt.name as language " : "")
                                    . "FROM " . DB_PREFIX . "product_" . $data['technical'] . " " . $data['technical'] . " "
                                    . "LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = " . $data['technical'] . ".product_id) ";

                            if($data['technical'] == 'sds'){
                                    $sql .= "LEFT JOIN " . DB_PREFIX . "language_technical lt ON (lt.language_technical_id = sds.language_technical_id) ";
                            }
                    } else {
                            $sql = " SELECT COUNT(DISTINCT protocol.product_id) as total FROM " . DB_PREFIX . "product_protocol protocol "
                                    . "LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = protocol.product_id) ";
                    }

                    $sql .= "LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) "
                            . "LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) "
                            . "WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW()" . ($data['technical'] != 'technical' ? ' AND (' . $data['technical'] . '.pdf <> "" AND ' . $data['technical'] . '.pdf IS NOT NULL)' : ' AND (technical.link <> "" AND technical.link IS NOT NULL)') . " AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";

                    if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                        $sql .= " AND (";

                        if (!empty($data['filter_name'])) {
                            $implode = array();

                            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                            foreach ($words as $word) {
                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                            }

                            if ($implode) {
                                $sql .= " " . implode(" AND ", $implode) . "";
                            }

                            if (!empty($data['filter_description'])) {
                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                            }
                        }

                        if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                            $sql .= " OR ";
                        }

                        if (!empty($data['filter_tag'])) {
                            $sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
                        }

                        if (!empty($data['filter_name'])) {
                            $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        }

                        $sql .= ")";
                    }

                    $sql .= " ORDER BY pd.name ";

                    if (isset($data['start']) || isset($data['limit'])) {
                        if ($data['start'] < 0) {
                            $data['start'] = 0;
                        }

                        if ($data['limit'] < 1) {
                            $data['limit'] = 20;
                        }

                        $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
                    }

                    $query = $this->db->query($sql);

                    if ($query->num_rows) {
                        $detail = array();
                        foreach ($query->rows as $row) {
                            $parent_id = $this->getparent_id($row['product_id']);

                            if ($parent_id != $row['product_id']) {
                                    $parent_info = $this->getProduct($parent_id);
                            }
                            
                            $detail[] = array(
                                'product_id' => $row['product_id'],
                                'pdf' => isset($row['pdf']) ? $row['pdf'] : '',
                                'link' => isset($row['link']) ? $row['link'] : '',
                                'title' => isset($row['title']) ? $row['title'] : '',
                                'product_name' => $row['product_name'],
                                'name' => ($data['technical'] == 'sds' && isset($row['language'])) ? $row['language'] : ($data['technical'] == 'coa' ? '#' . $row['description'] : ''),
                                'type' => (in_array($data['technical'], array('protocol', 'sds', 'coa'))) ? 'pdf' : 'text',
                                'parent_product_name' => isset($parent_info) ? $parent_info['name'] : '',
                                'product_description' => isset($parent_info) ? utf8_substr(strip_tags(html_entity_decode($parent_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..' : (utf8_substr(strip_tags(html_entity_decode($row['product_description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..'),
                                'href' => $this->url->link('product/product', 'product_id=' . $parent_id)
                            );
                        }

                        return $detail;
                    } else {
                        return false;
                    }
            }
            
	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
}
