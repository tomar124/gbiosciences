<?php
// Error Handler
function error_handler_for_export_import($errno, $errstr, $errfile, $errline) {
	global $registry;

	switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
					$errors = "Notice";
					break;
			case E_WARNING:
			case E_USER_WARNING:
					$errors = "Warning";
					break;
			case E_ERROR:
			case E_USER_ERROR:
					$errors = "Fatal Error";
					break;
			default:
					$errors = "Unknown";
					break;
	}

	$config = $registry->get('config');
	$url = $registry->get('url');
	$request = $registry->get('request');
	$session = $registry->get('session');
	$log = $registry->get('log');

	if ($config->get('config_error_log')) {
			$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	if (($errors=='Warning') || ($errors=='Unknown')) {
			return true;
	}

	if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='tool/export_import/download'))  {
			if ($config->get('config_error_display')) {
					echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
			}
	} else {
			$session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
			$token = $request->get['token'];
			$link = $url->link( 'catalog/gmc_special_product', 'token='.$token, 'SSL' );
			header('Status: ' . 302);
			header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
			exit();
	}

	return true;
}
function fatal_error_shutdown_handler_for_export_import() {
$last_error = error_get_last();
if ($last_error['type'] === E_ERROR) {
	// fatal error
	error_handler_for_export_import(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
}
}
ini_set('max_execution_time', 3600000);
ini_set('memory_limit','5012M');
class ModelCatalogGmcSpecialProduct extends Model {
        private $error = array();
        protected $null_array = array();
		public function addProduct($data)
		{
			$this->db->query("INSERT INTO " . DB_PREFIX . "product SET is_ground_hazmat = '" . $data['is_ground_hazmat'] . "', model = '" . $this->db->escape($data['model']) . "', size = '" . $this->db->escape($data['size']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', shipping_code = '" . $data['shipping_code'] . "', hazardous = '" . (int)$data['hazardous'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', cart_comment = '" . $this->db->escape($data['cart_comment']) . "', gmc_special_product = 1, date_added = NOW()");
	
			$product_id = $this->db->getLastId();
	
			if (isset($data['is_gp']) && $data['is_gp'] == 'grouped') {
				$this->insertGroupedProductGrouped($product_id, $data);
			}
	
			if (isset($data['gp_parent_id'])) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET gp_parent_id = '" . (int)$data['gp_parent_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
			}
	
			if (isset($data['image'])) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "',alt_text = '" . $this->db->escape($data['alt_text']) . "',caption = '" . $this->db->escape($data['caption']) . "' WHERE product_id = '" . (int)$product_id . "'");
			}
	
			// Technical Documents
			if (isset($data['protocol']) && !empty($data['protocol'])) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_protocol SET product_id = '" . (int)$product_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $data['protocol'])) . "'");
			}
	
			if (isset($data['coa'])) {
				foreach ($data['coa'] as $coa) {
					if (!empty($coa['pdf'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_coa SET product_id = '" . (int)$product_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $coa['pdf'])) . "', description = '" . $this->db->escape($coa['description']) . "', sort_order = '" . (int)$coa['sort_order'] . "'");
					}
				}
			}
	
			if (isset($data['sds'])) {
				foreach ($data['sds'] as $language_technical_id => $sds) {
					if (!empty($sds['pdf'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_sds SET product_id = '" . (int)$product_id . "', language_technical_id = '" . $language_technical_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $sds['pdf'])) . "', sort_order = '" . (int)$sds['sort_order'] . "'");
					}
				}
			}
	
			if (isset($data['technical'])) {
				foreach ($data['technical'] as $technical) {
					if (!empty($technical['title'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_technical SET product_id = '" . (int)$product_id . "', title = '" . $this->db->escape($technical['title']) . "', description = '" . $this->db->escape($technical['description']) . "', link = '" . $technical['link'] . "'");
					}
				}
			}
	
			if (isset($data['product_references'])) {
				foreach ($data['product_references'] as $product_references) {
					if ((isset($product_references['text']))) {
						$product_references['islink'] = (!empty($product_references['link']) ? '1' : '0');
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_references SET product_id = '" . (int)$product_id . "', islink = '" . $this->db->escape($product_references['islink']) . "', text = '" . $this->db->escape($product_references['text']) . "',  link = '" . $this->db->escape($product_references['link']) . "', year = '" . (int)$product_references['sort_order'] . "'");
					}
				}
			}
	
	
			foreach ($data['product_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			}
	
			if (isset($data['product_store'])) {
				foreach ($data['product_store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
	
			if (isset($data['product_attribute'])) {
				foreach ($data['product_attribute'] as $product_attribute) {
					if ($product_attribute['attribute_id']) {
						// Removes duplicates
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
	
						foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");
	
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
						}
					}
				}
			}
	
			if (isset($data['product_option'])) {
				foreach ($data['product_option'] as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						if (isset($product_option['product_option_value'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
	
							$product_option_id = $this->db->getLastId();
	
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						}
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
					}
				}
			}
	
			if (isset($data['product_recurring'])) {
				foreach ($data['product_recurring'] as $recurring) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
				}
			}
	
			if (isset($data['product_discount'])) {
				foreach ($data['product_discount'] as $product_discount) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
				}
			}
	
			if (isset($data['product_special'])) {
				foreach ($data['product_special'] as $product_special) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
				}
			}
	
			if (isset($data['product_image'])) {
				foreach ($data['product_image'] as $product_image) {
	
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', image_caption = '" . $this->db->escape($product_image['caption']) . "',  alt_text = '" . $this->db->escape($product_image['alt_text']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
				}
			}
	
			if (isset($data['product_download'])) {
				foreach ($data['product_download'] as $download_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				}
			}
	
			if (isset($data['product_category'])) {
				foreach ($data['product_category'] as $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
				}
			}
	
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}
			}
	
			if (isset($data['product_related'])) {
				foreach ($data['product_related'] as $related_id) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
				}
			}
	
			if (isset($data['product_reward'])) {
				foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
					if ((int)$product_reward['points'] > 0) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
					}
				}
			}
	
			// SEO URL
			if (isset($data['product_seo_url'])) {
				foreach ($data['product_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
	
			if (isset($data['product_layout'])) {
				foreach ($data['product_layout'] as $store_id => $layout_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
				}
			}
	
	
			$this->cache->delete('product');
	
			return $product_id;
		}
	
	
		public function editProduct($product_id, $data)
		{
			if (isset($data['is_gp']) && $data['is_gp'] == 'grouped') {
				$this->deleteGroupedProductGrouped($product_id, $data);
				$this->insertGroupedProductGrouped($product_id, $data);
			}
	
			$this->event->trigger('pre.admin.product.edit', $data);
	
			$this->db->query("UPDATE " . DB_PREFIX . "product SET is_ground_hazmat = '" . $data['is_ground_hazmat'] . "', model = '" . $this->db->escape($data['model']) . "', hazardous = '" . (int)$data['hazardous'] . "', shipping_code = '" . $data['shipping_code'] . "', points = '" . (int)$data['points'] . "', size = '" . $this->db->escape($data['size']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "',  status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', cart_comment = '" . $this->db->escape($data['cart_comment']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['gp_parent_id'])) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET gp_parent_id = '" . (int)$data['gp_parent_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
			}
	
			//Technical Documents
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_protocol WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['protocol']) && !empty($data['protocol'])) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_protocol SET product_id = '" . (int)$product_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $data['protocol'])) . "'");
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_coa WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['coa'])) {
				foreach ($data['coa'] as $coa) {
					if (!empty($coa['pdf'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_coa SET product_id = '" . (int)$product_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $coa['pdf'])) . "', description = '" . $this->db->escape($coa['description']) . "', sort_order = '" . (int)$coa['sort_order'] . "'");
					}
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_sds WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['sds'])) {
				foreach ($data['sds'] as $language_technical_id => $sds) {
					if (!empty($sds['pdf'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_sds SET product_id = '" . (int)$product_id . "', language_technical_id = '" . $language_technical_id . "', pdf = '" . $this->db->escape(str_replace(array('//', '///'), '/', $sds['pdf'])) . "', sort_order = '" . (int)$sds['sort_order'] . "'");
					}
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_technical WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['technical'])) {
				foreach ($data['technical'] as $technical) {
					if (!empty($technical['title'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_technical SET product_id = '" . (int)$product_id . "', title = '" . $this->db->escape($technical['title']) . "', description = '" . $this->db->escape($technical['description']) . "', link = '" . $technical['link'] . "'");
					}
				}
			}
	
			if (isset($data['image'])) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "',alt_text = '" . $this->db->escape($data['alt_text']) . "',caption = '" . $this->db->escape($data['caption']) . "' WHERE product_id = '" . (int)$product_id . "'");
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_references WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_references'])) {
				foreach ($data['product_references'] as $product_references) {
					if ((isset($product_references['text']))) {
						$product_references['islink'] = (!empty($product_references['link']) ? '1' : '0');
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_references SET product_id = '" . (int)$product_id . "', islink = '" . $this->db->escape($product_references['islink']) . "', text = '" . $this->db->escape($product_references['text']) . "',  link = '" . $this->db->escape($product_references['link']) . "', year = '" . (int)$product_references['sort_order'] . "'");
					}
				}
			}
	
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($data['product_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_store'])) {
				foreach ($data['product_store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
	
			if (!empty($data['product_attribute'])) {
				foreach ($data['product_attribute'] as $product_attribute) {
					if ($product_attribute['attribute_id']) {
						// Removes duplicates
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
	
						foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
						}
					}
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_option'])) {
				foreach ($data['product_option'] as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						if (isset($product_option['product_option_value'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
	
							$product_option_id = $this->db->getLastId();
	
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						}
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
					}
				}
			}
	
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);
	
			if (isset($data['product_recurring'])) {
				foreach ($data['product_recurring'] as $product_recurring) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_discount'])) {
				foreach ($data['product_discount'] as $product_discount) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_special'])) {
				foreach ($data['product_special'] as $product_special) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_image'])) {
				foreach ($data['product_image'] as $product_image) {
	
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', image_caption = '" . $this->db->escape($product_image['caption']) . "',  alt_text = '" . $this->db->escape($product_image['alt_text']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_download'])) {
				foreach ($data['product_download'] as $download_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_category'])) {
				foreach ($data['product_category'] as $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_related'])) {
				foreach ($data['product_related'] as $related_id) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_reward'])) {
				foreach ($data['product_reward'] as $customer_group_id => $value) {
					if ((int)$value['points'] > 0) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
					}
				}
			}
	
			// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
	
			if (isset($data['product_seo_url'])) {
				foreach ($data['product_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
	
			if (isset($data['product_layout'])) {
				foreach ($data['product_layout'] as $store_id => $layout_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
				}
			}
	
			$this->cache->delete('product');
		}
	
		public function copyProduct($product_id)
		{
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");
	
			if ($query->num_rows) {
				$data = $query->row;
	
				$data['sku'] = '';
				$data['upc'] = '';
				$data['viewed'] = '0';
				$data['keyword'] = '';
				$data['status'] = '0';
	
	
				$data['is_gp'] = $this->getGPData($product_id);
				if ($data['is_gp'] == 'grouped') {
					$data['gp_child'] = $this->getGroupedProductGroupedChilds($product_id);
				}
				$data['product_description'] = $this->getProductDescriptions($product_id);
				$data['product_discount'] = $this->getProductDiscounts($product_id);
				$data['product_store'] = $this->getProductStores($product_id);
				$data['product_category'] = $this->getProductCategories($product_id);
				$data['product_related'] = $this->getProductRelated($product_id);
				$data['product_image'] = $this->getProductImages($product_id);
				$data['product_special'] = $this->getProductSpecials($product_id);
				$data['product_layout'] = $this->getProductLayouts($product_id);
				$data['product_references'] = $this->getProductReferences($product_id);
				$data['product_reward'] = $this->getProductRewards($product_id);
				$data['technical'] = $this->getProductsTechnicals($product_id);
				$data['coa'] = $this->getProductsCoa($product_id);
				$data['sds'] = $this->getProductsSds($product_id);
				$data['protocol'] = $this->getProductsProtocol($product_id);
				$data['protocol'] = $data['protocol']['pdf'];
				$data['download'] = $this->getProductDownloads($product_id);
	
	
				$this->addProduct($data);
			}
		}
	
		public function deleteProduct($product_id)
		{
			$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_references WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
			$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_technical WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_coa WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_sds WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_protocol WHERE product_id = '" . (int)$product_id . "'");

			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_special_product_filter WHERE product_id = '" . (int)$product_id . "'");

			
	
			$this->cache->delete('product');
		}
	
		public function getProduct($product_id)
		{
			 $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.special_product = 0 AND p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
	
	
			return $query->row;
		}
	
		public function getProducts($data = array())
		{
	
			$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE gmc_special_product = 1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
	
			if (!empty($data['filter_gpt'])) {
				$sql = "SELECT * FROM " . DB_PREFIX . $data['filter_gpt'] . " gpt LEFT JOIN " . DB_PREFIX . "product p ON (gpt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE gmc_special_product = 1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			}
	
	
			if (!empty($data['filter_name'])) {
	
				$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}
	
			if (!empty($data['filter_model'])) {
	
				$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
			}
	
			if (!empty($data['filter_price'])) {
	
				$sql .= " AND p.price LIKE '%" . $this->db->escape($data['filter_price']) . "%'";
			}
	
			if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
				$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
			}
	
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
	
			$sql .= " GROUP BY p.product_id";
	
			$sort_data = array(
	
				'p.product_id',
	
				'pd.name',
				'p.model',
				'p.price',
				'p.quantity',
				'p.status',
				'p.sort_order'
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
	
				$sql .= " ORDER BY p.product_id";
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

		public function getLastInsertId(){
			$query = $this->db->query("SELECT MAX(product_id) as product_id FROM " . DB_PREFIX . "product");
			
			return (int)$query->row['product_id'] + 1;
		}
		
		public function is_special($product_id){
				return $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '".$product_id."' AND gmc_special_product = 1")->num_rows;
		}

		function isSpecialProductFilterExists($product_id, $special_product_filter_group_id, $special_product_filter_id) {
			return $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "product_to_special_product_filter WHERE product_id = '" . (int)$product_id . "' AND special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "' AND special_product_filter_id = '" . (int)$special_product_filter_id . "'")->row['count'];
		}
		
		public function getAllProductIdsOnly(){
				return $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE gmc_special_product = 1")->rows;
		}

		  /***
         * Import / Export Functions Below
         ***/
        public function download($category_id = 0, $option = 'PRODUCTS_ALL') {
			// we use our own error handler
			global $registry;
			$registry = $this->registry;
			set_error_handler('error_handler_for_export_import', E_ALL);
			register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

			// Use the PHPExcel package from http://phpexcel.codeplex.com/
			$cwd = getcwd();
			chdir( DIR_SYSTEM.'PHPExcel' );
			require_once( 'Classes/PHPExcel.php' );
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
			chdir( $cwd );

			// find out whether all data is to be downloaded
			//$all = !isset($offset) && !isset($rows) && !isset($min_id) && !isset($max_id);

			// Memory Optimization
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

			try {
				// set appropriate timeout limit
				set_time_limit( 1800 );

				$languages = $this->getLanguages();
				$default_language_id = $this->getDefaultLanguageId();

				// create a new workbook
				$workbook = new PHPExcel();

				// set some default styles
				$workbook->getDefaultStyle()->getFont()->setName('Verdana');
				$workbook->getDefaultStyle()->getFont()->setSize(10);
				//$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
				$workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

				// pre-define some commonly used styles
				$box_format = array(
					'fill' => array(
						'type'      => PHPExcel_Style_Fill::FILL_SOLID,
						'color'     => array( 'rgb' => '275E6E')
					),
									'font' => array(
											'color'     => array( 'rgb' => 'FFFFFF')
									)
				);
				$text_format = array(
					'numberformat' => array(
						'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
					),
									'alignment' => array(
											'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
											'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
											'wrap' => true,
									)
				);
				$price_format = array(
					'numberformat' => array(
						'code' => '######0.00'
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						/*'wrap'       => false,
						'indent'     => 0
						*/
					)
				);
				$weight_format = array(
					'numberformat' => array(
						'code' => '##0.00'
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						/*'wrap'       => false,
						'indent'     => 0
						*/
					)
				);
				
				// create the worksheets
				$worksheet_index = 0;
							
							if ($option == 'PRODUCTS_ALL') {
									// creating the Products worksheet
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Products' );
									$this->populateProductsWorksheet( $category_id, $worksheet, $languages, $default_language_id, $price_format, $box_format, $weight_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );

									// Protocols worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Protocol' );
									$this->populateProtocolsWorksheet( $category_id, $worksheet, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );

									// Msds worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Msds' );
									$this->populateMsdsWorksheet( $category_id, $worksheet, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );

									// creating the Rewards worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Rewards' );
									$this->populateRewardsWorksheet( $category_id, $worksheet, $default_language_id, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
									
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Images' );
									$this->populateImagesWorksheet( $category_id, $worksheet, $default_language_id, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
									
									// creating the Discounts worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Discounts' );
									$this->populateDiscountsWorksheet( $category_id, $worksheet, $default_language_id, $price_format, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
									
									// creating the Specials worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Specials' );
									$this->populateSpecialsWorksheet( $category_id, $worksheet, $default_language_id, $price_format, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
									
									// creating the Valid Values worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Data Terminology' );
									$this->populateValidValuesWorksheet( $worksheet, $default_language_id, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );

									// creating the Valid Values For Categoryworksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'Valid Categories' );
									$this->populateCategoryValuesWorksheet( $worksheet, $default_language_id, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
							}
							
							if ($option == 'PRODUCTS_ATTRIBUTES') {
									// creating the ProductAttributes worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'ProductAttributes' );
									$this->populateProductAttributesWorksheet( $category_id, $worksheet, $languages, $default_language_id, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
							}
							
							if ($option == 'PRODUCTS_OPTIONS') {
									// creating the ProductOptions worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'ProductOptions' );
									$this->populateProductOptionsWorksheet( $category_id, $worksheet, $box_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );

									// creating the ProductOptionValues worksheet
									$workbook->createSheet();
									$workbook->setActiveSheetIndex($worksheet_index++);
									$worksheet = $workbook->getActiveSheet();
									$worksheet->setTitle( 'ProductOptionValues' );
									$this->populateProductOptionValuesWorksheet( $category_id, $worksheet, $price_format, $box_format, $weight_format, $text_format );
									$worksheet->freezePaneByColumnAndRow( 1, 2 );
							}

				$workbook->setActiveSheetIndex(0);
							
				// redirect output to client browser
				$datetime = date('Y-m-d');
							
							if ($option == 'PRODUCTS_ALL') {
									$filename = 'products_all-'.$datetime.'.xlsx';
							}
							
							if ($option == 'PRODUCTS_ATTRIBUTES') {
									$filename = 'products_attributes-'.$datetime.'.xlsx';
							}
							
							if ($option == 'PRODUCTS_OPTIONS') {
									$filename = 'products_options-'.$datetime.'.xlsx';
							}
							
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
				$objWriter->setPreCalculateFormulas(false);
				$objWriter->save('php://output');

				// Clear the spreadsheet caches
				$this->clearSpreadsheetCache();
				exit();

			} catch (Exception $e) {
				$errstr = $e->getMessage();
				$errline = $e->getLine();
				$errfile = $e->getFile();
				$errno = $e->getCode();
				$this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
				if ($this->config->get('config_error_log')) {
					$this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
				}
				return;
			}
		}

		protected function clearSpreadsheetCache() {
			$files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');
			
			if ($files) {
				foreach ($files as $file) {
					if (file_exists($file)) {   
						@unlink($file);
						clearstatcache();
					}
				}
			}
		}

		protected function populateProtocolsWorksheet( $category_id, &$worksheet, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document')+30);

			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
			$data[$j++] = 'document';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );

			// The actual product rewards data
			$i += 1;
			$j = 0;
			$protocols = $this->getProtocols($category_id);
			foreach ($protocols as $row) {
					$worksheet->getRowDimension($i)->setRowHeight(26);
					$data = array();
					$data[$j++] = $row['product_id'];
					$data[$j++] = $row['document'];
					$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
					$i += 1;
					$j = 0;
			}
		}

		protected function getProtocols($category_id) {
			// get the product protocols
			$sql  = "SELECT pp.product_id, pp.pdf as document, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
			$sql .= "FROM `".DB_PREFIX."product_protocol` pp ";
			$sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pp.product_id=p.product_id) ";
			$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id AND pc.category_id = $category_id ";
			$sql .= "WHERE p.gmc_special_product='1' AND p.product_id<>'' GROUP BY p.product_id HAVING categories LIKE '%$category_id%'  ORDER BY pp.product_id";

			$result = $this->db->query( $sql );
			return $result->rows;
		}

		function populateProductsWorksheet( $category_id, &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
			// get list of the field names, some are only available for certain OpenCart versions
			$query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
			$product_fields = array();
			foreach ($query->rows as $row) {
				$product_fields[] = $row['Field'];
			}
	
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
			foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog')+4,30)+1);
			}
					foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('description')+4,32)+1);
			}
					foreach ($languages as $language) {
							$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_title')+4,20)+1);
					}
			foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_description')+4,32)+1);
			}
			foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_keywords')+4,32)+1);
			}
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('categories'),12)+1);
			//$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('quantity'),4)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('gene'),8)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_name'),12)+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('alt_text'),12)+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('caption'),12)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('shipping'),5)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('hazardous'),12)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('size'),5)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('cart_comment'),10)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'),6)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_unit'),3)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length'),8)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('width'),8)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('height'),8)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length_unit'),3)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('seo_keyword'),16)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'),5)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'),5)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_added'),19)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_modified'),19)+1);
			//$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('store_ids'),16)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'),8)+1);
			//$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('subtract'),5)+1);
			//$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('minimum'),8)+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('link')+4,30)+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_link')+4,30)+1);
					
			// The product headings row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'catalog('.$language['code'].')';
			}
					foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'description('.$language['code'].')';
			}
					foreach ($languages as $language) {
							$styles[$j] = &$text_format;
							$data[$j++] = 'meta_title('.$language['code'].')';
					}
			foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'meta_description('.$language['code'].')';
			}
			foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'meta_keywords('.$language['code'].')';
			}
			$styles[$j] = &$text_format;
			$data[$j++] = 'categories';
			//$data[$j++] = 'quantity';
			$styles[$j] = &$text_format;
			$data[$j++] = 'gene';
					$data[$j++] = 'image';
					$data[$j++] = 'alt_text';
					$data[$j++] = 'caption';
			$data[$j++] = 'shipping';
			$styles[$j] = &$price_format;
			$data[$j++] = 'price';
					$data[$j++] = 'hazardous';
					$data[$j++] = 'size';
					$data[$j++] = 'cart_comment';
			$styles[$j] = &$weight_format;
			$data[$j++] = 'weight';
			$data[$j++] = 'weight_unit';
			$data[$j++] = 'length';
			$data[$j++] = 'width';
			$data[$j++] = 'height';
			$data[$j++] = 'length_unit';
			$data[$j++] = 'seo_keyword';
			$data[$j++] = 'status';		
			$data[$j++] = 'points';
			$data[$j++] = 'date_added';
			$data[$j++] = 'date_modified';
			//$data[$j++] = 'store_ids';
			$data[$j++] = 'sort_order';
			//$data[$j++] = 'subtract';
			//$data[$j++] = 'minimum';
					$styles[$j] = &$text_format;
					$data[$j++] = 'plink';
					$styles[$j] = &$text_format;
					$data[$j++] = 'pimagelink';
	
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual products data
			$i += 1;
			$j = 0;
			$store_ids = $this->getStoreIdsForProducts();
			$products = $this->getImportExportProducts( $category_id, $languages, $default_language_id, $product_fields );
			$len = count($products);
			foreach ($products as $row) {
				$data = array();
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$product_id = $row['product_id'];
				$data[$j++] = $product_id;
				foreach ($languages as $language) {
					$data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
				}
							foreach ($languages as $language) {
					$data[$j++] = html_entity_decode($row['description'][$language['code']],ENT_QUOTES,'UTF-8');
				}
							foreach ($languages as $language) {
									$data[$j++] = html_entity_decode($row['meta_title'][$language['code']],ENT_QUOTES,'UTF-8');
							}
				foreach ($languages as $language) {
					$data[$j++] = html_entity_decode($row['meta_description'][$language['code']],ENT_QUOTES,'UTF-8');
				}
				foreach ($languages as $language) {
					$data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']],ENT_QUOTES,'UTF-8');
				}
				$data[$j++] = $row['categories'];
				//$data[$j++] = $row['quantity'];
				$data[$j++] = $row['model'];
				$data[$j++] = $row['image_name'];
							$data[$j++] = $row['alt_text'];
							$data[$j++] = $row['caption'];
				$data[$j++] = $row['shipping_code'];
				$data[$j++] = $row['price'];
				$data[$j++] = $row['hazardous'];
				$data[$j++] = $row['size'];
				$data[$j++] = $row['cart_comment'];
				$data[$j++] = $row['weight'];
				$data[$j++] = $row['weight_unit'];
				$data[$j++] = $row['length'];
				$data[$j++] = $row['width'];
				$data[$j++] = $row['height'];
				$data[$j++] = $row['length_unit'];
				$data[$j++] = ($row['keyword']) ? $row['keyword'] : '';
				$data[$j++] = ($row['status']==0) ? 'false' : 'true';			
				$data[$j++] = $row['points'];
				$data[$j++] = $row['date_added'];
				$data[$j++] = $row['date_modified'];
				/*$store_id_list = '';
				if (isset($store_ids[$product_id])) {
					foreach ($store_ids[$product_id] as $store_id) {
						$store_id_list .= ($store_id_list=='') ? $store_id : ','.$store_id;
					}
				}
				$data[$j++] = $store_id_list;*/
				$data[$j++] = $row['sort_order'];
				//$data[$j++] = ($row['subtract']==0) ? 'false' : 'true';
				//$data[$j++] = $row['minimum'];
							if (!empty($row['keyword'])) {
								$data[$j++] = html_entity_decode(HTTPS_CATALOG.$row['keyword'],ENT_QUOTES,'UTF-8');
							} else {
								$data[$j++] = html_entity_decode(HTTPS_CATALOG.'index.php?route=product/product&product_id='.$product_id,ENT_QUOTES,'UTF-8');
							}
							if (!empty($row['image_name'])) {
								$data[$j++] = HTTPS_CATALOG . 'image/' . $row['image_name'];
							} else {
								$data[$j++] = '';
							}
	
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}

		protected function populateSpecialsWorksheet( $category_id, &$worksheet, $language_id, &$price_format, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'),19)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'),19)+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
			$data[$j++] = 'customer_group';
			$data[$j++] = 'priority';
			$styles[$j] = &$price_format;
			$data[$j++] = 'price';
			$styles[$j] = &$text_format;
			$data[$j++] = 'date_start';
			$styles[$j] = &$text_format;
			$data[$j++] = 'date_end';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product specials data
			$i += 1;
			$j = 0;
			$specials = $this->getSpecials( $category_id, $language_id );
			foreach ($specials as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(13);
				$data = array();
				$data[$j++] = $row['product_id'];
				$data[$j++] = $row['name'];
				$data[$j++] = $row['priority'];
				$data[$j++] = $row['price'];
				$data[$j++] = $row['date_start'];
				$data[$j++] = $row['date_end'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}
	
		protected function getSpecials( $category_id, $language_id ) {
			// get the product specials
					$sql  = "SELECT ps.*, cgd.name FROM ";
			$sql .= "( SELECT pi.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
			$sql .= " FROM `".DB_PREFIX."product` pi";
					$sql .= " LEFT JOIN `".DB_PREFIX."product_to_category` pc ON pi.product_id=pc.product_id WHERE pi.gmc_special_product=1 GROUP BY pi.product_id HAVING categories LIKE '%$category_id%' ";
			$sql .= "  ORDER BY pi.product_id ASC ";
			$sql .= ") AS p ";
			$sql .= "INNER JOIN `".DB_PREFIX."product_special` ps ON ps.product_id=p.product_id ";
					$sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=ps.customer_group_id ";
					$sql .= "AND cgd.language_id=$language_id ";
			$sql .= "ORDER BY ps.product_id, name, ps.priority";
					
					$result = $this->db->query( $sql );
			return $result->rows;
		}

		protected function populateDiscountsWorksheet( $category_id, &$worksheet, $language_id, &$price_format, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('quantity')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'),19)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'),19)+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] =  'product_id';
			$styles[$j] = &$text_format;
			$data[$j++] =  'customer_group';
			$data[$j++] =  'quantity';
			$data[$j++] =  'priority';
			$styles[$j] = &$price_format;
			$data[$j++] =  'price';
			$styles[$j] = &$text_format;
			$data[$j++] =  'date_start';
			$styles[$j] = &$text_format;
			$data[$j++] =  'date_end';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product discounts data
			$i += 1;
			$j = 0;
			$discounts = $this->getDiscounts( $category_id, $language_id );
			foreach ($discounts as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(13);
				$data = array();
				$data[$j++] =$row['product_id'];
				$data[$j++] =$row['name'];
				$data[$j++] =$row['quantity'];
				$data[$j++] =$row['priority'];
				$data[$j++] =$row['price'];
				$data[$j++] =$row['date_start'];
				$data[$j++] =$row['date_end'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}
	
		protected function getDiscounts( $category_id, $language_id ) {// Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
			// get the product discounts
					$sql  = "SELECT pd.*, cgd.name FROM ";
			$sql .= "( SELECT pi.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
			$sql .= " FROM `".DB_PREFIX."product` pi";
					$sql .= " LEFT JOIN `".DB_PREFIX."product_to_category` pc ON pi.product_id=pc.product_id WHERE pi.gmc_special_product=1 GROUP BY pi.product_id HAVING categories LIKE '%$category_id%' ";
			$sql .= "  ORDER BY pi.product_id ASC ";
			$sql .= ") AS p ";
			$sql .= "INNER JOIN `".DB_PREFIX."product_discount` pd ON pd.product_id=p.product_id ";
					$sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=pd.customer_group_id ";
					$sql .= "AND cgd.language_id=$language_id ";
			$sql .= "ORDER BY pd.product_id, name, pd.priority";
					
					$result = $this->db->query( $sql );
			return $result->rows;
		}

		protected function populateValidValuesWorksheet( &$worksheet, $language_id, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Attribute Name')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Valid Values (Use these values)')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Valid Values Name')+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'Attribute Name';
			$styles[$j] = &$text_format;
			$data[$j++] = 'Valid Values (Use these values)';
			$data[$j++] = 'Valid Values Name';
					$data[$j++] = 'Description';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product rewards data
			$i += 1;
			$j = 0;
					
					$weightUnits = $weightTitles = $lengthUnits = $lengthTitles = array();
					
					foreach($this->getAllWeightUnit() as $weight){
						$weightUnits[] = $weight['unit'];
						
						$weightTitles[] = $weight['title'];
					}
					
					foreach($this->getAllMeasurementUnit() as $length){
						$lengthUnits[] = $length['unit'];
						
						$lengthTitles[] = $length['title'];
					}
					
					$values = array(
						0 => array(
							'Attribute Name' => 'hazardous',
							'Valid Value' => '0, 1, 2',
							'Valid Value Name' => 'NO, ACCESSIBLE, INACCESSIBLE',
							'Description' => ''
						),
						1 => array(
							'Attribute Name' => 'shipping',
							'Valid Value' => '2DAY, GROUND, STANDARD, BLUE, DRY',
							'Valid Value Name' => 'Ambient, Ambient (GROUND), Standard, Blue Ice, Dry Ice',
							'Description' => ''
						),
						2 => array(
							'Attribute Name' => 'status',
							'Valid Value' => '1, 0',
							'Valid Value Name' => 'Enabled, Disabled',
							'Description' => ''
						),
						3 => array(
							'Attribute Name' => 'weight_unit',
							'Valid Value' => implode(', ', $weightUnits),
							'Valid Value Name' => implode(', ', $weightTitles),
							'Description' => ''
						),
						4 => array(
							'Attribute Name' => 'length_unit',
							'Valid Value' => implode(', ', $lengthUnits),
							'Valid Value Name' => implode(', ', $lengthTitles),
							'Description' => ''
						),
						5 => array(
							'Attribute Name' => 'required',
							'Valid Value' => 'true, false',
							'Valid Value Name' => 'Mandatory, Not Mandatory',
							'Description' => ''
						),
						6 => array(
							'Attribute Name' => 'subtract',
							'Valid Value' => 'true, false',
							'Valid Value Name' => '',
							'Description' => '(In case of options - Always use false if required is true)'
						),
						7 => array(
							'Attribute Name' => 'price_prefix',
							'Valid Value' => '+, -',
							'Valid Value Name' => 'Add, Subtract',
							'Description' => '(In case of options - Whether to deduct or add option price from/to main price)'
						),
						8 => array(
							'Attribute Name' => 'weight_prefix',
							'Valid Value' => '+, -',
							'Valid Value Name' => 'Add, Subtract',
							'Description' => '(In case of options - Whether to deduct or add option weight from/to main weight)'
						)
					);
			foreach ($values as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$data = array();
				$data[$j++] = $row['Attribute Name'];
				$data[$j++] = $row['Valid Value'];
				$data[$j++] = $row['Valid Value Name'];
				$data[$j++] = $row['Description'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}
			
		protected function populateCategoryValuesWorksheet( &$worksheet, $language_id, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Category ID (Use these values)')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Category Name')+40);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Category Status')+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'Category ID (Use these values)';
			$styles[$j] = &$text_format;
			$data[$j++] = 'Category Name';
			$data[$j++] = 'Category Status';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product rewards data
			$i += 1;
			$j = 0;
					
					$sql = "SELECT c.category_id, cd.name, c.status FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id) WHERE c.category_id <> ''; ";
					
					$query = $this->db->query($sql);
					
					if($query->num_rows){
						foreach ($query->rows as $row) {
								$worksheet->getRowDimension($i)->setRowHeight(26);
								$data = array();
								$data[$j++] = $row['category_id'];
								$data[$j++] = $row['name'];
								$data[$j++] = $row['status'];
								$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
								$i += 1;
								$j = 0;
						}
					}
		}

		protected function getStoreIdsForProducts() {
			$sql =  "SELECT product_id, store_id FROM `".DB_PREFIX."product_to_store` ps;";
			$store_ids = array();
			$result = $this->db->query( $sql );
			foreach ($result->rows as $row) {
				$productId = $row['product_id'];
				$store_id = $row['store_id'];
				if (!isset($store_ids[$productId])) {
					$store_ids[$productId] = array();
				}
				if (!in_array($store_id,$store_ids[$productId])) {
					$store_ids[$productId][] = $store_id;
				}
			}
			return $store_ids;
		}

		protected function getImportExportProducts( $category_id, &$languages, $default_language_id, $product_fields ) {
			$sql  = "SELECT ";
			$sql .= "  p.product_id,";
			$sql .= "  IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories,";
			$sql .= "  p.quantity,";
			$sql .= "  p.model,";
			$sql .= "  p.image AS image_name,";
					$sql .= "  p.shipping_code,";
					$sql .= "  p.alt_text,";
			$sql .= "  p.caption,";
			$sql .= "  p.price,";
			$sql .= "  p.points,";
			$sql .= "  p.date_added,";
			$sql .= "  p.date_modified,";
			$sql .= "  p.weight,";
			$sql .= "  wc.unit AS weight_unit,";
			$sql .= "  p.length,";
			$sql .= "  p.width,";
			$sql .= "  p.height,";
			$sql .= "  p.hazardous,";
			$sql .= "  p.size,";
			$sql .= "  p.cart_comment,";
			$sql .= "  ua.keyword,";
			$sql .= "  p.status,";
			$sql .= "  p.sort_order,";
			$sql .= "  mc.unit AS length_unit, ";
			$sql .= "  p.subtract, ";
			$sql .= "  p.minimum ";
			$sql .= "FROM `".DB_PREFIX."product` p ";
			$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
					$sql .= "LEFT JOIN `".DB_PREFIX."seo_url` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
			$sql .= "LEFT JOIN `".DB_PREFIX."weight_class_description` wc ON wc.weight_class_id = p.weight_class_id ";
			$sql .= "  AND wc.language_id=$default_language_id ";
			$sql .= "LEFT JOIN `".DB_PREFIX."length_class_description` mc ON mc.length_class_id=p.length_class_id ";
			$sql .= "  AND mc.language_id=$default_language_id WHERE p.gmc_special_product=1 ";		
			$sql .= "GROUP BY p.product_id HAVING categories LIKE '%$category_id%' ";
			$sql .= "ORDER BY p.product_id ";
			$sql .= "; ";
	
			$results = $this->db->query( $sql );
			$product_descriptions = $this->getImportExportProductDescriptions( $category_id, $languages );
			foreach ($languages as $language) {
				$language_code = $language['code'];
				foreach ($results->rows as $key=>$row) {
					if (isset($product_descriptions[$language_code][$key])) {
						$results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
						$results->rows[$key]['description'][$language_code] = $product_descriptions[$language_code][$key]['description'];
											$results->rows[$key]['meta_title'][$language_code] = $product_descriptions[$language_code][$key]['meta_title'];
						$results->rows[$key]['meta_description'][$language_code] = $product_descriptions[$language_code][$key]['meta_description'];
						$results->rows[$key]['meta_keyword'][$language_code] = $product_descriptions[$language_code][$key]['meta_keyword'];
					} else {
						$results->rows[$key]['name'][$language_code] = '';
						$results->rows[$key]['description'][$language_code] = '';$results->rows[$key]['meta_title'][$language_code] = '';
						$results->rows[$key]['meta_description'][$language_code] = '';
						$results->rows[$key]['meta_keyword'][$language_code] = '';
					}
				}
			}
			return $results->rows;
		}
			
			protected function getImportExportProductDescriptions( $category_id, &$languages ) {
			// query the product_description table for each language
			$product_descriptions = array();
			foreach ($languages as $language) {
				$language_id = $language['language_id'];
				$language_code = $language['code'];
				$sql  = "SELECT p.product_id, pd.*, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
				$sql .= "FROM `".DB_PREFIX."product` p ";
				$sql .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id=p.product_id AND pd.language_id='".(int)$language_id."' ";
							$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id AND pc.category_id = $category_id WHERE p.gmc_special_product=1 ";
				$sql .= "GROUP BY p.product_id HAVING categories LIKE '%$category_id%' ";
				$sql .= "ORDER BY p.product_id ";
				$sql .= "; ";
	
				$query = $this->db->query( $sql );
				$product_descriptions[$language_code] = $query->rows;
			}
			return $product_descriptions;
		}
			
		protected function getDefaultLanguageId() {
			$code = $this->config->get('config_language');
			$sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = '$code'";
			$result = $this->db->query( $sql );
			$language_id = 1;
			if ($result->rows) {
				foreach ($result->rows as $row) {
					$language_id = $row['language_id'];
					break;
				}
			}
			return $language_id;
		}
	
		protected function getLanguages() {
			$query = $this->db->query( "SELECT * FROM `".DB_PREFIX."language` WHERE `status`=1 ORDER BY `code`" );
			return $query->rows;
		}
			
			protected function setCellRow( $worksheet, $row/*1-based*/, $data, &$default_style=null, &$styles=null ) {
			if (!empty($default_style)) {
				$worksheet->getStyle( "$row:$row" )->applyFromArray( $default_style, false );
			}
			if (!empty($styles)) {
				foreach ($styles as $col=>$style) {
					$worksheet->getStyleByColumnAndRow($col,$row)->applyFromArray($style,false);
				}
			}
			$worksheet->fromArray( $data, null, 'A'.$row, true );
		}
			
			protected function getRewards( $category_id, $language_id ) {
					// Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
			$exist_table_customer_group_description = false;
			$query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."customer_group_description'" );
			$exist_table_customer_group_description = ($query->num_rows > 0);
	
			// get the product rewards
			$sql  = "SELECT pr.*, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories, ";
			$sql .= ($exist_table_customer_group_description) ? "cgd.name " : "cg.name ";
			$sql .= "FROM `".DB_PREFIX."product_reward` pr LEFT JOIN ".DB_PREFIX."product p ON (p.product_id=pr.product_id) ";
					$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id AND pc.category_id = $category_id ";
			if ($exist_table_customer_group_description) {
				$sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=pr.customer_group_id ";
				$sql .= "  AND cgd.language_id=$language_id ";
			} else {
				$sql .= "LEFT JOIN `".DB_PREFIX."customer_group` cg ON cg.customer_group_id=pr.customer_group_id ";
			}
			$sql .= " WHERE p.gmc_special_product=1 GROUP BY p.product_id HAVING categories LIKE '%$category_id%' ORDER BY pr.product_id, name";
	
			$result = $this->db->query( $sql );
			return $result->rows;
		}
	
		protected function populateRewardsWorksheet( $category_id, &$worksheet, $language_id, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('points')+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
			$data[$j++] = 'customer_group';
			$data[$j++] = 'points';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product rewards data
			$i += 1;
			$j = 0;
			$rewards = $this->getRewards( $category_id, $language_id );
			foreach ($rewards as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$data = array();
				$data[$j++] = $row['product_id'];
				$data[$j++] = $row['name'];
				$data[$j++] = $row['points'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}
			
			protected function getImages( $category_id ) {
			// get the product images
					$sql  = "SELECT pim.* FROM ";
			$sql .= "( SELECT pi.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
			$sql .= " FROM `".DB_PREFIX."product` pi";
					$sql .= " LEFT JOIN `".DB_PREFIX."product_to_category` pc ON pi.product_id=pc.product_id WHERE pi.gmc_special_product=1 GROUP BY pi.product_id HAVING categories LIKE '%$category_id%' ";
			$sql .= "  ORDER BY pi.product_id ASC ";
			$sql .= ") AS p ";
			$sql .= "INNER JOIN `".DB_PREFIX."product_image` pim ON pim.product_id=p.product_id ";
			$sql .= "ORDER BY pim.product_id ASC, pim.sort_order ASC";
	
			$result = $this->db->query( $sql );
			return $result->rows;
			}
			
			protected function populateImagesWorksheet( $category_id, &$worksheet, $language_id, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('image')+20);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('alt_text')+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('image_caption')+1);
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order')+1);
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
			$data[$j++] = 'image';
			$data[$j++] = 'alt_text';
					$data[$j++] = 'image_caption';
					$data[$j++] = 'sort_order';
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product rewards data
			$i += 1;
			$j = 0;
					
					$images = $this->getImages($category_id);
	
					if($images){
						foreach ($images as $image) {
								$worksheet->getRowDimension($i)->setRowHeight(26);
								$data = array();
								$data[$j++] = $image['product_id'];
								$data[$j++] = $image['image'];
								$data[$j++] = $image['alt_text'];
								$data[$j++] = $image['image_caption'];
								$data[$j++] = $image['sort_order'];
								$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
								$i += 1;
								$j = 0;
						}
					}
		}

		protected function populateMsdsWorksheet( $category_id, &$worksheet, &$box_format, &$text_format ) {
			//fetching all technical languages
			$language_technicals = $this->getAllLanguageTechnicals();

			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			foreach ($language_technicals as $language) {
					$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document('.$language['name'].')')+30);
			}

			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
			foreach ($language_technicals as $language) {
					$styles[$j] = &$text_format;
					$data[$j++] = 'document('.$language['name'].')';
			}
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );

			// The actual product rewards data
			$i += 1;
			$j = 0;
			$msds = $this->getMsds( $category_id, $language_technicals );
			foreach ($msds as $row) {
					$worksheet->getRowDimension($i)->setRowHeight(26);
					$data = array();
					$data[$j++] = $row['product_id'];
					foreach ($language_technicals as $language) {
							$data[$j++] = html_entity_decode($row[$language['name']],ENT_QUOTES,'UTF-8');
					}
					$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
					$i += 1;
					$j = 0;
			}
		}
	
		protected function getMsds( $category_id, $language_technicals ) {
				// get the product msds
				$sql  = "SELECT DISTINCT ps.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
				$sql .= "FROM `".DB_PREFIX."product_sds` ps ";
				$sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (ps.product_id=p.product_id) ";
						$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
				$sql .= "WHERE p.gmc_special_product='1' AND p.product_id<>'' GROUP BY p.product_id HAVING categories LIKE '%$category_id%' ORDER BY ps.product_id";
				$results = $this->db->query( $sql );            
				$product_descriptions = $this->getMsdsDocuments( $language_technicals );
				foreach ($language_technicals as $language) {
						$language_name = $language['name'];
						foreach ($results->rows as $key => $row) {
								if (isset($product_descriptions[$language_name][$row['product_id']])) {
										$results->rows[$key][$language_name] = $product_descriptions[$language_name][$row['product_id']];
								} else {
										$results->rows[$key][$language_name] = '';
								}
						}
				}
				return $results->rows;
		}
		
		protected function getMsdsDocuments( &$language_technicals ) {
				// query the catalogs msds table for each language
				$result = array();
				foreach ($language_technicals as $language) {
						$language_id = $language['language_technical_id'];
						$language_name = $language['name'];
						$sql  = "SELECT ps.product_id, ps.pdf as document ";
						$sql .= "FROM `".DB_PREFIX."product_sds` ps ";
						$sql .= "WHERE ps.language_technical_id='".(int)$language_id."' ORDER BY ps.product_id";
						$sql .= "; ";
						$query = $this->db->query( $sql );
						if($query->rows){
								foreach($query->rows as $row){
										$result[$language_name][$row['product_id']] = $row['document'];
								}
						} else {
								$result[$language_name] = array();
						}
				}
				return $result;
		}

		protected function populateProductAttributesWorksheet( $category_id, &$worksheet, &$languages, $default_language_id, &$box_format, &$text_format ) {
			// Set the column widths
			$j = 0;
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute_group'),30)+1);
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute'),30)+1);
			foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text')+4,30)+1);
			}
	
			// The heading row and column styles
			$styles = array();
			$data = array();
			$i = 1;
			$j = 0;
			$data[$j++] = 'product_id';
			$styles[$j] = &$text_format;
					$data[$j++] = 'attribute_group';
			$styles[$j] = &$text_format;
					$data[$j++] = 'attribute';
			foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'text('.$language['code'].')';
			}
			$worksheet->getRowDimension($i)->setRowHeight(30);
			$this->setCellRow( $worksheet, $i, $data, $box_format );
	
			// The actual product attributes data
			$attribute_group_names = $this->getAttributeGroupNames( $default_language_id );
			$attribute_names = $this->getAttributeNames( $default_language_id );
					
			$i += 1;
			$j = 0;
			$product_attributes = $this->getProductImportExportAttributes( $category_id, $languages );
			foreach ($product_attributes as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$data = array();
				$data[$j++] = $row['product_id'];
				if ($this->config->get( 'export_import_settings_use_attribute_group_id' )) {
					$data[$j++] = $row['attribute_group_id'];
				} else {
					$data[$j++] = html_entity_decode($attribute_group_names[$row['attribute_group_id']],ENT_QUOTES,'UTF-8');
				}
				if ($this->config->get( 'export_import_settings_use_attribute_id' )) {
					$data[$j++] = $row['attribute_id'];
				} else {
					$data[$j++] = html_entity_decode($attribute_names[$row['attribute_id']],ENT_QUOTES,'UTF-8');
				}
				foreach ($languages as $language) {
					$data[$j++] = html_entity_decode($row['text'][$language['code']],ENT_QUOTES,'UTF-8');
				}
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
			}
		}

		protected function getAttributeGroupNames( $language_id ) {
			$sql  = "SELECT attribute_group_id, name ";
			$sql .= "FROM `".DB_PREFIX."attribute_group_description` ";
			$sql .= "WHERE language_id='".(int)$language_id."' ";
			$sql .= "ORDER BY attribute_group_id ASC";
			$query = $this->db->query( $sql );
			$attribute_group_names = array();
			foreach ($query->rows as $row) {
				$attribute_group_id = $row['attribute_group_id'];
				$name = $row['name'];
				$attribute_group_names[$attribute_group_id] = $name;
			}
			return $attribute_group_names;
		}
	
		protected function getAttributeNames( $language_id ) {
			$sql  = "SELECT attribute_id, name ";
			$sql .= "FROM `".DB_PREFIX."attribute_description` ";
			$sql .= "WHERE language_id='".(int)$language_id."' ";
			$sql .= "ORDER BY attribute_id ASC";
			$query = $this->db->query( $sql );
			$attribute_names = array();
			foreach ($query->rows as $row) {
				$attribute_id = $row['attribute_id'];
				$attribute_name = $row['name'];
				$attribute_names[$attribute_id] = $attribute_name;
			}
			return $attribute_names;
		}

		protected function getProductImportExportAttributes( $category_id, &$languages ) {
			$sql  = "SELECT pa.product_id, ag.attribute_group_id, pa.attribute_id, pa.language_id, pa.text, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
			$sql .= "FROM `".DB_PREFIX."product_attribute` pa LEFT JOIN " . DB_PREFIX . "product p ON p.product_id=pa.product_id ";
					$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
			$sql .= "INNER JOIN `".DB_PREFIX."attribute` a ON a.attribute_id=pa.attribute_id ";
			$sql .= "INNER JOIN `".DB_PREFIX."attribute_group` ag ON ag.attribute_group_id=a.attribute_group_id WHERE p.gmc_special_product=1 ";		
			$sql .= "GROUP BY p.product_id, pa.attribute_id HAVING categories LIKE '%$category_id%' ORDER BY pa.product_id ASC, ag.attribute_group_id ASC, pa.attribute_id ASC";
			$query = $this->db->query( $sql );
					
			$texts = array();
			foreach ($query->rows as $row) {
				$product_id = $row['product_id'];
				$attribute_group_id = $row['attribute_group_id'];
				$attribute_id = $row['attribute_id'];
				$language_id = $row['language_id'];
				$text = html_entity_decode($row['text'], ENT_QUOTES, 'UTF-8');
				$texts[$product_id][$attribute_group_id][$attribute_id][$language_id] = $text;
			}
			$product_attributes = array();
			foreach ($texts as $product_id=>$level1) {
				foreach ($level1 as $attribute_group_id=>$level2) {
					foreach ($level2 as $attribute_id=>$text) {
						$product_attribute = array();
						$product_attribute['product_id'] = $product_id;
						$product_attribute['attribute_group_id'] = $attribute_group_id;
						$product_attribute['attribute_id'] = $attribute_id;
						$product_attribute['text'] = array();
						foreach ($languages as $language) {
							$language_id = $language['language_id'];
							$code = $language['code'];
							if (isset($text[$language_id])) {
								$product_attribute['text'][$code] = $text[$language_id];
							} else {
								$product_attribute['text'][$code] = '';
							}
						}
						$product_attributes[] = $product_attribute;
					}
				}
			}
			return $product_attributes;
		}
	
		public function getProductsByCategoryId($category_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");
	
			return $query->rows;
		}
	
		public function getProductDescriptions($product_id)
		{
			$product_description_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_description_data[$result['language_id']] = array(
					'name'             => $result['name'],
					'description'      => $result['description'],
					'meta_title'       => $result['meta_title'],
					'meta_description' => $result['meta_description'],
					'meta_keyword'     => $result['meta_keyword'],
					'tag'              => $result['tag']
				);
			}
	
			return $product_description_data;
		}
	
		public function getProductCategories($product_id)
		{
			$product_category_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_category_data[] = $result['category_id'];
			}
	
			return $product_category_data;
		}
	
		public function getProductFilters($product_id)
		{
			$product_filter_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_filter_data[] = $result['filter_id'];
			}
	
			return $product_filter_data;
		}
	
		public function getProductAttributes($product_id)
		{
			$product_attribute_data = array();
	
			$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");
	
			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_description_data = array();
	
				$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
	
				foreach ($product_attribute_description_query->rows as $product_attribute_description) {
					$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
				}
	
				$product_attribute_data[] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'product_attribute_description' => $product_attribute_description_data
				);
			}
	
			return $product_attribute_data;
		}
	
		public function getProductOptions($product_id)
		{
			$product_option_data = array();
	
			$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
	
			foreach ($product_option_query->rows as $product_option) {
				$product_option_value_data = array();
	
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");
	
				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
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
	
		public function getProductOptionValue($product_id, $product_option_value_id)
		{
			$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
	
			return $query->row;
		}
	
		public function getProductImages($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");
	
			return $query->rows;
		}
	
		public function getProductDiscounts($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
	
			return $query->rows;
		}
	
		public function getProductSpecials($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
	
			return $query->rows;
		}
	
		public function getProductRewards($product_id)
		{
			$product_reward_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
			}
	
			return $product_reward_data;
		}
	
		public function getProductDownloads($product_id)
		{
			$product_download_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_download_data[] = $result['download_id'];
			}
	
			return $product_download_data;
		}
	
		public function getProductStores($product_id)
		{
			$product_store_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_store_data[] = $result['store_id'];
			}
	
			return $product_store_data;
		}
	
		public function getProductSeoUrls($product_id)
		{
			$product_seo_url_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
			}
	
			return $product_seo_url_data;
		}
	
		public function getProductLayouts($product_id)
		{
			$product_layout_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_layout_data[$result['store_id']] = $result['layout_id'];
			}
	
			return $product_layout_data;
		}
	
		public function getProductRelated($product_id)
		{
			$product_related_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
	
			foreach ($query->rows as $result) {
				$product_related_data[] = $result['related_id'];
			}
	
			return $product_related_data;
		}
	
		public function getRecurrings($product_id)
		{
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");
	
			return $query->rows;
		}
	
		public function getTotalProducts($data = array())
		{
	
			$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
	
			if (!empty($data['filter_gpt'])) {
				$sql = "SELECT COUNT(DISTINCT gpt.product_id) AS total FROM " . DB_PREFIX . $data['filter_gpt'] . " gpt LEFT JOIN " . DB_PREFIX . "product p ON (gpt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
			}
	
			$sql .= " WHERE p.gmc_special_product = 1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
	
			if (!empty($data['filter_name'])) {
	
				$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}
	
			if (!empty($data['filter_model'])) {
	
				$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
			}
	
			if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
	
				$sql .= " AND p.price LIKE '%" . $this->db->escape($data['filter_price']) . "%'";
			}
	
			if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
				$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
			}
	
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
	
			$query = $this->db->query($sql);
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByTaxClassId($tax_class_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByStockStatusId($stock_status_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByWeightClassId($weight_class_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByLengthClassId($length_class_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByDownloadId($download_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByManufacturerId($manufacturer_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByAttributeId($attribute_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByOptionId($option_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");
	
			return $query->row['total'];
		}
	
		public function getTotalProductsByProfileId($recurring_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");
	
			return $query->row['total'];
		}
	
	
		public function getparent_id($product_id)
		{
			$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "gp_grouped_child WHERE child_id = '" . $product_id . "'");
	
			if ($query->num_rows) {
				$parent_id = $query->row['product_id'];
			} else {
				$parent_id = $product_id;
			}
			return $parent_id;
		}
	
		public function getTotalProductsByLayoutId($layout_id)
		{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
	
			return $query->row['total'];
		}
	
		public function getGroupedProductGrouped($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gp_grouped WHERE product_id = '" . (int)$product_id . "'");
	
			return $query->row;
		}
	
		public function getGPData($product_id)
		{
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "gp_grouped` WHERE product_id = '" . (int)$product_id . "'");
			if ($query->num_rows == 1) {
				$gp_product = 'grouped';
				return $gp_product;
			}
		}
	
		public function getGroupedProductGroupedChilds($product_id)
		{
			$child_data = array();
	
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gp_grouped_child WHERE product_id = '" . (int)$product_id . "' ORDER BY child_sort_order");
	
			foreach ($query->rows as $result) {
				$child_data[$result['child_id']] = array(
					'child_id'         => $result['child_id'],
					'child_sort_order' => $result['child_sort_order']
				);
			}
	
			return $child_data;
		}
	
		public function getProductsTechnicals($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_technical WHERE product_id = '" . (int)$product_id . "'");
	
			return $query->rows;
		}
	
		public function getProductsProtocol($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_protocol WHERE product_id = '" . (int)$product_id . "'");
	
			return $query->row;
		}
	
		public function getLanguageTechnicalForProducts()
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language_technical ORDER BY is_default DESC, name ASC");
	
			return $query->rows;
		}
	
		public function getProductsSds($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_sds WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");
	
			$result = array();
	
			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$result[$row['language_technical_id']] = array(
						'pdf' => $row['pdf'],
						'sort_order' => $row['sort_order']
					);
				}
			}
	
			return $result;
		}
	
		public function getProductsCoa($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_coa WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order");
	
			return $query->rows;
		}
	
		public function insertGroupedProductGrouped($product_id, $data)
		{
			$data_gp_price_min = isset($data['gp_price_min']) ? $data['gp_price_min'] : 0; //set zero just to prevent error in mask
			$data_gp_price_max = isset($data['gp_price_max']) ? $data['gp_price_max'] : 0; //set zero just to prevent error in mask
	
			$this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped SET product_id = '" . (int)$product_id . "', gp_price_min = '" . $this->db->escape($data_gp_price_min) . "', gp_price_max = '" . $this->db->escape($data_gp_price_max) . "'");
	
			if (isset($data['gp_child'])) {
				foreach ($data['gp_child'] as $child_id => $child) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped_child SET product_id = '" . (int)$product_id . "', child_id = '" . (int)$child_id . "', child_sort_order = '" . (int)$child['child_sort_order'] . "'");
					if (isset($data['product_category']) && !empty($data['product_category'])) {
						$getProductCategoriesdetails = $this->getProductCategories($child_id);
						foreach ($data['product_category'] as $category_id) {
							if (!in_array($category_id, $getProductCategoriesdetails)) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$child_id . "', category_id = '" . (int)$category_id . "'");
							}
						}
					}
				}
			}
		}
	
		public function getProductReferences($product_id)
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_references WHERE product_id = '" . (int)$product_id . "' ORDER BY year DESC");
	
			return $query->rows;
		}
	
		public function deleteGroupedProductGrouped($product_id, $data = array())
		{
			$this->db->query("DELETE FROM " . DB_PREFIX . "gp_grouped WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "gp_grouped_child WHERE product_id = '" . (int)$product_id . "'");
			if (isset($data['gp_child']) && !empty($data['gp_child'])) {
				foreach ($data['gp_child'] as $gp_child_id => $gp_child) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$gp_child_id . "'");
				}
			}
		}

		public function uploadGroupedProduct( $filename, $incremental=true ) {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import',E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            try {
                    $this->session->data['export_import_nochange'] = 1;

                    // we use the PHPExcel package from http://phpexcel.codeplex.com/
                    $cwd = getcwd();
                    chdir( DIR_SYSTEM.'PHPExcel' );
                    require_once( 'Classes/PHPExcel.php' );
                    chdir( $cwd );

                    // Memory Optimization
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array( ' memoryCacheSize '  => '128MB'  );
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    // parse uploaded spreadsheet file
                    $inputFileType = PHPExcel_IOFactory::identify($filename);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $reader = $objReader->load($filename);
                    
                    // read the various worksheets and load them to the database
                    if (!$this->validateUploadGroupedProduct( $reader )) {
                            return false;
                    }
                    $this->clearCache();
                    $this->session->data['export_import_nochange'] = 0;
                    $available_product_ids = array();
                    $this->uploadGrouped( $reader, $incremental, $available_product_ids );
                    $this->uploadGPData( $reader, $incremental, $available_product_ids );
                    $this->uploadGroupedProductImages( $reader, $incremental, $available_product_ids  );
                    $this->uploadGroupedProductReferences( $reader, $incremental, $available_product_ids  );

					$this->uploadProductAttributes( $reader, $incremental, $available_product_ids );
					$this->uploadSpecialProductFilters( $reader, $incremental, $available_product_ids );

                    return true;
            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return false;
            }
    	}

		protected function validateUploadGroupedProduct( &$reader ){
            $ok = true;

            // worksheets must have correct heading rows
            if (!$this->validateGrouped( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_heading') );
                    $ok = false;
            }
            if (!$this->validateGPData( $reader )) {
                    $this->log->write( $this->language->get('error_gpdata') );
                    $ok = false;
            }            
            if (!$this->validateGroupedImages( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_images') );
                    $ok = false;
            }  

            if (!$this->validateGroupedReferences( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_citations') );
                    $ok = false;
            }

            // certain worksheets rely on the existence of other worksheets
            $names = $reader->getSheetNames();
            $exist_products = false;
            $exist_rewards = false;
            foreach ($names as $name) {
                    if ($name=='Products') {
                            $exist_products = true;
                            continue;
                    }
                    if ($name=='GP_Data') {
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Images') {
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Citations') {
                            $exist_rewards = true;
                            continue;
                    }
            }

            if (!$ok) {
                    return false;
            }

            if (!$this->validateGroupedIdColumns( $reader )) {
                    $ok = false;
            }

            return $ok;
    	}

		protected function validateGrouped( &$reader ) {
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return true;
            }

            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            $expected_heading = array
            ( "product_id", "product_name", "description", "meta_tag_title", "meta_tag_description", "meta_tag_keywords", "image", "alt_text", "caption", "sort_order", "status", "categories", "name", "related_products", "seo_keywords" );

            $expected_multilingual = array( "product_name", "description", "meta_tag_title", "meta_tag_description", "meta_tag_keywords" );

            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
            
    	}

		protected function validateGroupedIdColumns( &$reader ) {
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return true;
            }
            $ok = true;

            // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
            $previous_product_id = 0;
            $has_missing_product_ids = false;
            $product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $product_id = $this->getCell($data,$i,1);
                    if ($product_id=="") {
                            if (!$has_missing_product_ids) {
                                    $msg = str_replace( '%1', 'Products', $this->language->get( 'error_missing_product_id' ) );
                                    $this->log->write( $msg );
                                    $has_missing_product_ids = true;
                            }
                            $ok = false;
                            continue;
                    }
                    if (!$this->isInteger($product_id)) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_invalid_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                            continue;
                    }
                    if (in_array( $product_id, $product_ids )) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_duplicate_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $product_ids[] = $product_id;
                    if ($product_id < $previous_product_id) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_wrong_order_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $previous_product_id = $product_id;
            }

            // make sure product_ids are numeric entries and are also mentioned in worksheet 'Products'
            $worksheets = array( 'GP_Data', 'Images','Citations' );
            foreach ($worksheets as $worksheet) {
                    $data = $reader->getSheetByName( $worksheet );
                    if ($data==null) {
                            continue;
                    }
                    $ok = true;
                    $previous_product_id = 0;
                    $has_missing_product_ids = false;
                    $unlisted_product_ids = array();
                    $k = $data->getHighestRow();
                    for ($i=1; $i<$k; $i+=1) {
                            $product_id = $this->getCell($data,$i,1);
                            if ($product_id=="") {
                                    if (!$has_missing_product_ids) {
                                            $msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_product_id' ) );
                                            $this->log->write( $msg );
                                            $has_missing_product_ids = true;
                                    }
                                    $ok = false;
                                    continue;
                            }
                            if (!$this->isInteger($product_id)) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                                    continue;
                            }
                            if (!in_array( $product_id, $product_ids )) {
                                    if (!in_array( $product_id, $unlisted_product_ids )) {
                                            $unlisted_product_ids[] = $product_id;
                                            $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_product_id' ) ) );
                                            $this->log->write( $msg );
                                            $ok = false;
                                    }
                            }
                            if ($product_id < $previous_product_id) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                            }
                            $previous_product_id = $product_id;
                    }
            }

            return $ok;
    	}
		protected function validateProductIdColumns( &$reader ) {
				$data = $reader->getSheetByName( 'Catalogs' );
				if ($data==null) {
						return true;
				}
				$ok = true;

				// only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
				$previous_product_id = 0;
				$has_missing_product_ids = false;
				$product_ids = array();
				$k = $data->getHighestRow();
				for ($i=1; $i<$k; $i+=1) {
						$product_id = $this->getCell($data,$i,1);
						if ($product_id=="") {
								if (!$has_missing_product_ids) {
										$msg = str_replace( '%1', 'Products', $this->language->get( 'error_missing_product_id' ) );
										$this->log->write( $msg );
										$has_missing_product_ids = true;
								}
								$ok = false;
								continue;
						}
						if (!$this->isInteger($product_id)) {
								$msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_invalid_product_id' ) ) );
								$this->log->write( $msg );
								$ok = false;
								continue;
						}
						if (in_array( $product_id, $product_ids )) {
								$msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_duplicate_product_id' ) ) );
								$this->log->write( $msg );
								$ok = false;
						}
						$product_ids[] = $product_id;
						if ($product_id < $previous_product_id) {
								$msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_wrong_order_product_id' ) ) );
								$this->log->write( $msg );
								$ok = false;
						}
						$previous_product_id = $product_id;
				}

				// make sure product_ids are numeric entries and are also mentioned in worksheet 'Products'
				$worksheets = array( 'Protocol', 'Msds', 'Coa', 'Technicals', 'Rewards', 'Specials', 'Discounts' );
				foreach ($worksheets as $worksheet) {
						$data = $reader->getSheetByName( $worksheet );
						if ($data==null) {
								continue;
						}
						$previous_product_id = 0;
						$has_missing_product_ids = false;
						$unlisted_product_ids = array();
						$k = $data->getHighestRow();
						for ($i=1; $i<$k; $i+=1) {
								$product_id = $this->getCell($data,$i,1);
								if ($product_id=="") {
										if (!$has_missing_product_ids) {
												$msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_product_id' ) );
												$this->log->write( $msg );
												$has_missing_product_ids = true;
										}
										$ok = false;
										continue;
								}
								if (!$this->isInteger($product_id)) {
										$msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_product_id' ) ) );
										$this->log->write( $msg );
										$ok = false;
										continue;
								}
								if (!in_array( $product_id, $product_ids )) {
										if (!in_array( $product_id, $unlisted_product_ids )) {
												$unlisted_product_ids[] = $product_id;
												$msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_product_id' ) ) );
												$this->log->write( $msg );
												$ok = false;
										}
								}
								if ($product_id < $previous_product_id) {
										$msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_product_id' ) ) );
										$this->log->write( $msg );
										$ok = false;
								}
								$previous_product_id = $product_id;
						}
				}

				return $ok;
		}
		
		protected function validateGPData( &$reader ) {
            $data = $reader->getSheetByName( 'GP_Data' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "catalog_id" );
            $expected_multilingual = array( );
            return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
		}
		protected function validateGroupedImages( &$reader ) {
				$data = $reader->getSheetByName( 'Images' );
				if ($data==null) {
						return true;
				}
				$expected_heading = array ( "product_id", "image", "alt_text", "image_caption", "sort_order" );
				$expected_multilingual = array( );
				return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
		}
		protected function validateGroupedReferences( &$reader ) {
				$data = $reader->getSheetByName( 'Citations' );
				if ($data==null) {
						return true;
				}
				$expected_heading = array ( "product_id", "islink", "text", "link", "year" );
				$expected_multilingual = array( );
				return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
		}
		protected function validateCatalogCoas( &$reader ) {
				$data = $reader->getSheetByName( 'Coa' );
				if ($data==null) {
						return true;
				}
				$expected_heading = array ( "product_id", "document", "description", "sort_order" );
				$expected_multilingual = array();
				return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function uploadGroupedProductReferences( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Citations' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old product images
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            } 

            // load the worksheet cells and store them to the database
            $old_product_image_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j= 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $islink = $this->getCell($data,$i,$j++,'');
                    $text = $this->getCell($data,$i,$j++,'');
                    $link = $this->getCell($data,$i,$j++,'');
                    $year = $this->getCell($data,$i,$j++,0);
                    $reference = array();
                    $reference['product_id'] = $product_id;
                    $reference['islink'] = $islink;
                    $reference['text'] = $text;
                    $reference['link'] = $link;
                    $reference['year'] = $year;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $this->deleteAdditionalRefrences( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->storeAdditionalRefrencesIntoDatabase( $reference );
                    $previous_product_id = $product_id;
            }
    	}

		public function upload( $filename, $incremental=true ) {
			// we use our own error handler
			global $registry;
			$registry = $this->registry;
			set_error_handler('error_handler_for_export_import',E_ALL);
			register_shutdown_function('fatal_error_shutdown_handler_for_export_import');
	
			try { 
				$this->session->data['export_import_nochange'] = 1;
	
				// we use the PHPExcel package from http://phpexcel.codeplex.com/
				$cwd = getcwd();
				chdir( DIR_SYSTEM.'PHPExcel' );
				require_once( 'Classes/PHPExcel.php' );
				chdir( $cwd );
				
				// Memory Optimization
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
				$cacheSettings = array( ' memoryCacheSize '  => '16MB'  );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	
				// parse uploaded spreadsheet file
				$inputFileType = PHPExcel_IOFactory::identify($filename);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setReadDataOnly(true);
				$reader = $objReader->load($filename);
	
				// read the various worksheets and load them to the database			
				if (!$this->validateUpload( $reader )) {
					return false;
				}
				$this->clearCache();
				$this->session->data['export_import_nochange'] = 0;
				$available_product_ids = array();
				$this->uploadProducts( $reader, $incremental, $available_product_ids );
				// $this->uploadGrouped( $reader, $incremental, $available_product_ids );
				$this->uploadGPData( $reader, $incremental, $available_product_ids );
				$this->uploadGroupedProductImages( $reader, $incremental, $available_product_ids  );
				// $this->uploadGroupedProductReferences( $reader, $incremental, $available_product_ids  );

				$this->uploadProtocols( $reader, $incremental );
				$this->uploadMsds( $reader, $incremental );
				$this->uploadRewards( $reader, $incremental, $available_product_ids );
				$this->uploadProductAttributes( $reader, $incremental, $available_product_ids );
				$this->uploadSpecialProductFilters( $reader, $incremental, $available_product_ids );
				// $this->uploadProductOptions( $reader, $incremental, $available_product_ids );
				// $this->uploadProductOptionValues( $reader, $incremental, $available_product_ids );
				$this->uploadDiscounts( $reader, $incremental, $available_product_ids );
				$this->uploadSpecials( $reader, $incremental, $available_product_ids );
				$this->uploadImagesData( $reader, $incremental, $available_product_ids );
	
				return true;
			} catch (Exception $e) {
				$errstr = $e->getMessage();
				$errline = $e->getLine();
				$errfile = $e->getFile();
				$errno = $e->getCode();
				$this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
				if ($this->config->get('config_error_log')) {
					$this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
				}
				return false;
			}
		}

		protected function validateUpload( &$reader ){
			$ok = true;
	
			// worksheets must have correct heading rows
			if (!$this->validateProducts( $reader )) {
				$this->log->write( $this->language->get('error_products_header') );
				$ok = false;
			}

			if (!$this->validateProtocols( $reader )) {
					$this->log->write( $this->language->get('error_protocols_header') );
					$ok = false;
			}

			if (!$this->validateMsds( $reader )) {
					$this->log->write( $this->language->get('error_msds_header') );
					$ok = false;
			}
			
			if (!$this->validateRewards( $reader )) {
				$this->log->write( $this->language->get('error_rewards_header') );
				$ok = false;
			}

			if (!$this->validateProductAttributes( $reader )) {
				$this->log->write( $this->language->get('error_product_attributes_header') );
				$ok = false;
			}
			// if (!$this->validateProductOptions( $reader )) {
			// 	$this->log->write( $this->language->get('error_product_options_header') );
			// 	$ok = false;
			// }
			// if (!$this->validateProductOptionValues( $reader )) {
			// 	$this->log->write( $this->language->get('error_product_option_values_header') );
			// 	$ok = false;
			// }
			if (!$this->validateProductImagesValues( $reader )) {
				$this->log->write( $this->language->get('error_product_images_header') );
				$ok = false;
			}
			if (!$this->validateDiscounts( $reader )) {
				$this->log->write( $this->language->get('error_discounts_header') );
				$ok = false;
			}
			if (!$this->validateSpecials( $reader )) {
				$this->log->write( $this->language->get('error_specials_header') );
				$ok = false;
			}
	
	
			if (!$this->validateSpecialProductFilter( $reader )) {
					$this->log->write( $this->language->get('error_special_product_filter_header') );
					$ok = false;
			}
				
			// certain worksheets rely on the existence of other worksheets
			$names = $reader->getSheetNames();
			$exist_products = false;
			$exist_rewards = false;
			$exist_discounts = false;
			$exist_specials = false;
	
				$exist_special_product_filters = false;
				
			foreach ($names as $name) {
				if ($name=='Products') {
					$exist_products = true;
					continue;
				}
							if ($name=='Protocol') {
									if (!$exist_products) {
											// Missing Products worksheet, or Products worksheet not listed before Rewards
											$this->log->write( $this->language->get('error_protocol') );
											$ok = false;
									}
									$exist_rewards = true;
									continue;
							}
							if ($name=='Msds') {
									if (!$exist_products) {
											// Missing Products worksheet, or Products worksheet not listed before Rewards
											$this->log->write( $this->language->get('error_msds') );
											$ok = false;
									}
									$exist_rewards = true;
									continue;
							}
	
							if ($name=='Images') {
									if (!$exist_products) {
											// Missing Products worksheet, or Products worksheet not listed before Rewards
											$this->log->write( $this->language->get('error_images') );
											$ok = false;
									}
									$exist_rewards = true;
									continue;
							}
							
							if ($name=='Discounts') {
					if (!$exist_products) {
						// Missing Products worksheet, or Products worksheet not listed before Discounts
						$this->log->write( $this->language->get('error_discounts') );
						$ok = false;
					}
					$exist_discounts = true;
					continue;
				}
	
				if ($name=='Specials') {
					if (!$exist_products) {
						// Missing Products worksheet, or Products worksheet not listed before Specials
						$this->log->write( $this->language->get('error_specials') );
						$ok = false;
					}
					$exist_specials = true;
					continue;
				}
				
	
				if ($name=='Filters') {
						if (!$exist_products) {
								// Missing Filters worksheet, or Products worksheet not listed before Filters
								$this->log->write( $this->language->get('error_special_product_filters') );
								$ok = false;
						}
						$exist_special_product_filters = true;
						continue;
				}
				
				if ($name=='Rewards') {
					if (!$exist_products) {
						// Missing Products worksheet, or Products worksheet not listed before Rewards
						$this->log->write( $this->language->get('error_rewards') );
						$ok = false;
					}
					$exist_rewards = true;
					continue;
				}
				// if ($name=='ProductOptions') {
				// 	if (!$exist_products) {
				// 		// Missing Products worksheet, or Products worksheet not listed before ProductOptions
				// 		$this->log->write( $this->language->get('error_product_options') );
				// 		$ok = false;
				// 	}
				// 	$exist_product_options = true;
				// 	continue;
				// }
				// if ($name=='ProductOptionValues') {
				// 	if (!$exist_products) {
				// 		// Missing Products worksheet, or Products worksheet not listed before ProductOptionValues
				// 		$this->log->write( $this->language->get('error_product_option_values') );
				// 		$ok = false;
				// 	}
				// 	if (!$exist_product_options) {
				// 		// Missing ProductOptions worksheet, or ProductOptions worksheet not listed before ProductOptionValues
				// 		$this->log->write( $this->language->get('error_product_option_values_2') );
				// 		$ok = false;
				// 	}
				// 	$exist_product_option_values = true;
				// 	continue;
				// }
			}
	
			if (!$ok) {
				return false;
			}
	
			if (!$this->validateProductIdColumns( $reader )) {
				$ok = false;
			}
	
			return $ok;
		}

		public function getAllLanguageTechnicals(){
			$sql = "SELECT * FROM " . DB_PREFIX . "language_technical lt";

			return $this->db->query($sql)->rows;
		}

		protected function validateProtocols( &$reader ) {
			$data = $reader->getSheetByName( 'Protocol' );
			if ($data==null) {
					return true;
			}
			$expected_heading = array ( "product_id", "document" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function validateProducts( &$reader ) {
			$data = $reader->getSheetByName( 'Products' );
			if ($data==null) {
				return true;
			}
	
			// get list of the field names, some are only available for certain OpenCart versions
			$query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
			$product_fields = array();
			foreach ($query->rows as $row) {
				$product_fields[] = $row['Field'];
			}
	
			$expected_heading = array
			( "product_id", "catalog", "description", "meta_title", "meta_description", "meta_keywords", "categories", "name", "image","alt_text","caption", "shipping", "price", "hazardous", "size", "cart_comment", "weight", "weight_unit", "length", "width", "height", "length_unit", "seo_keyword", "status", "points", "date_added", "date_modified", "sort_order" );
			
			$expected_multilingual = array( "catalog", "description", "meta_title", "meta_description", "meta_keywords" );
					
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function validateRewards( &$reader ) {
			$data = $reader->getSheetByName( 'Rewards' );
			if ($data==null) {
				return true;
			}
			$expected_heading = array( "product_id", "customer_group", "points" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}
	
		protected function validateDiscounts( &$reader ) {
			$data = $reader->getSheetByName( 'Discounts' );
			if ($data==null) {
				return true;
			}
			$expected_heading = array( "product_id", "customer_group", "quantity", "priority", "price", "date_start", "date_end" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}
	
		protected function validateSpecials( &$reader ) {
			$data = $reader->getSheetByName( 'Specials' );
			if ($data==null) {
				return true;
			}
			$expected_heading = array( "product_id", "customer_group", "priority", "price", "date_start", "date_end" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}
			
		protected function validateProductAttributes( &$reader ) {
			$data = $reader->getSheetByName( 'ProductAttributes' );
			if ($data==null) {
				return true;
			}
			$expected_heading = array( "product_id", "attribute_group_id", "attribute", "text" );
			$expected_heading = array( "product_id", "attribute_group", "attribute", "text" );
			$expected_multilingual = array( "text" );
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function validateProductImagesValues( &$reader ) {
			$data = $reader->getSheetByName( 'Images' );
			if ($data==null) {
					return true;
			}
			$expected_heading = array ( "product_id", "image", "alt_text", "image_caption", "sort_order" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function validateSpecialProductFilter( &$reader ) {
			$data = $reader->getSheetByName( 'Filters' );
			if ($data==null) {
					return true;
			}
			$expected_heading = array( "product_id", "special_product_filter_group_id", "special_product_filter_id" );
			$expected_multilingual = array();
			return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
		}

		protected function uploadSpecialProductFilters( &$reader, $incremental, &$available_product_ids ){
			
			$data = $reader->getSheetByName( 'Filters' );
			if ($data==null) {
					return;
			}

			// if incremental then find current product IDs else delete all old specials
			if ($incremental) {
					$unlisted_product_ids = $available_product_ids;
			}


			// load the worksheet cells and store them to the database
			$special_product_filter_string = '';
			$previous_product_id = 0;
			$i = 0;
			$k = $data->getHighestRow();
			for ($i=0; $i<$k; $i+=1) {
					$j = 1;
					if ($i==0) {
							continue;
					}

					$product_id = trim($this->getCell($data,$i,$j++));
					if ($product_id=="") {
							continue;
					}

					$special_product_filter_group_id = trim($this->getCell($data,$i,$j++));
					if ($special_product_filter_group_id=="") {
							continue;
					}

					$special_product_filter_id = trim($this->getCell($data,$i,$j++));
					if ($special_product_filter_id=="") {
							continue;
					}

					$special_product_filter = array();
					$special_product_filter['product_id'] = $product_id;
					$special_product_filter['special_product_filter_group_id'] = $special_product_filter_group_id;
					$special_product_filter['special_product_filter_id'] = $special_product_filter_id;

					if (($incremental) && ($product_id != $previous_product_id)) {
							$this->deleteSpecialproductFilter( $product_id );
							if (isset($unlisted_product_ids[$product_id])) {
									unset($unlisted_product_ids[$product_id]);
							}
					}

					if ($product_id == $previous_product_id || $previous_product_id == 0) {
							$special_product_filter_string .= 'SPFG:'.$special_product_filter['special_product_filter_group_id'].',SPF:'.$special_product_filter['special_product_filter_id']. '#';
		
							// Save last product_id entry.
							if ( ((int)$k - $i ) === 1) {
									$special_product_filter_string = rtrim($special_product_filter_string, '#');

									$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_special_product_filter_grouped_values WHERE product_id = '" . (int)$product_id . "'");
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_special_product_filter_grouped_values SET product_id = '" . (int)$product_id . "', value = '" . $this->db->escape($special_product_filter_string) . "'");
							}
					} else {
							if ($previous_product_id != 0) {
									$special_product_filter_string = rtrim($special_product_filter_string, '#');

									$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_special_product_filter_grouped_values WHERE product_id = '" . (int)$previous_product_id . "'");
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_special_product_filter_grouped_values SET product_id = '" . (int)$previous_product_id . "', value = '" . $this->db->escape($special_product_filter_string) . "'");

									$special_product_filter_string = 'SPFG:'.$special_product_filter['special_product_filter_group_id'].',SPF:'.$special_product_filter['special_product_filter_id']. '#';   
							}
					}

					if($this->isSpecialProductFilterExists($product_id, $special_product_filter_group_id, $special_product_filter_id)) {
							continue;
					}

					$this->moreSpecialCells( $i, $j, $data, $special_product_filter );
					$this->storeSpecialProductFilterIntoDatabase( $special_product_filter );
					$previous_product_id = $product_id;
			}
		}

	
		protected function storeSpecialProductFilterIntoDatabase( &$special_product_filter ) {
			
			// $this->log->write('storeSpecialProductFilterIntoDatabase '.$special_product_filter['product_id'].', '.$special_product_filter['special_product_filter_group_id'].', '.$special_product_filter['special_product_filter_id'] );
				$this->insertQuery('product_to_special_product_filter', [
						'product_id' => (int)$special_product_filter['product_id'],
						'special_product_filter_group_id' => (int)$special_product_filter['special_product_filter_group_id'],
						'special_product_filter_id' => (int)$special_product_filter['special_product_filter_id'],
				]);
		}

		protected function uploadProducts( &$reader, $incremental, &$available_product_ids=array() ) {
			// get worksheet, if not there return immediately
			$data = $reader->getSheetByName( 'Products' );
			if ($data==null) {
				return;
			}
	
			// save product view counts
			$view_counts = $this->getProductViewCounts();
					
					// save old seo_url_ids
			$seo_url_ids = $this->getProductUrlAliasIds();
					
			// if incremental then find current product IDs else delete all old products
			$available_product_ids = array();
			if ($incremental) {
				$old_product_ids = $this->getAvailableProductIds($data);
					}
	
			// get pre-defined store_ids
			$available_store_ids = $this->getAvailableStoreIds();
	
			// find the installed languages
			$languages = $this->getLanguages();
	
			// find the default units
			$default_weight_unit = $this->getDefaultWeightUnit();
			$default_measurement_unit = $this->getDefaultMeasurementUnit();
			$default_stock_status_id = $this->config->get('config_stock_status_id');
	
			// get weight classes
			// $weight_class_ids = $this->getWeightClassIds();
	
			// get length classes
			// $length_class_ids = $this->getLengthClassIds();
	
			// get list of the field names, some are only available for certain OpenCart versions
			$query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
			$product_fields = array();
			foreach ($query->rows as $row) {
				$product_fields[] = $row['Field'];
			}
	
			// load the worksheet cells and store them to the database
			$first_row = array();
			$i = 0;
			$k = $data->getHighestRow();
			for ($i=0; $i<$k; $i+=1) {
				if ($i==0) {
					$max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
					for ($j=1; $j<=$max_col; $j+=1) {
						$first_row[] = $this->getCell($data,$i,$j);
					}
					continue;
				}
				$j = 1;
				$product_id = trim($this->getCell($data,$i,$j++));
				if ($product_id=="") {
					continue;
				}
				$names = array();
				while ($this->startsWith($first_row[$j-1],"catalog(")) {
					$language_code = substr($first_row[$j-1],strlen("catalog("),strlen($first_row[$j-1])-strlen("catalog(")-1);
					$name = $this->getCell($data,$i,$j++);
					$name = htmlspecialchars( $name );
					$names[$language_code] = $name;
				}
							$descriptions = array();
				while ($this->startsWith($first_row[$j-1],"description(")) {
					$language_code = substr($first_row[$j-1],strlen("description("),strlen($first_row[$j-1])-strlen("description(")-1);
					$description = $this->getCell($data,$i,$j++);
					$description = htmlspecialchars( $description );
					$descriptions[$language_code] = $description;
				}
							$meta_titles = array();
							while ($this->startsWith($first_row[$j-1],"meta_title(")) {
									$language_code = substr($first_row[$j-1],strlen("meta_title("),strlen($first_row[$j-1])-strlen("meta_title(")-1);
									$meta_title = $this->getCell($data,$i,$j++);
									$meta_title = htmlspecialchars( $meta_title );
									$meta_titles[$language_code] = $meta_title;
							}
				$meta_descriptions = array();
				while ($this->startsWith($first_row[$j-1],"meta_description(")) {
					$language_code = substr($first_row[$j-1],strlen("meta_description("),strlen($first_row[$j-1])-strlen("meta_description(")-1);
					$meta_description = $this->getCell($data,$i,$j++);
					$meta_description = htmlspecialchars( $meta_description );
					$meta_descriptions[$language_code] = $meta_description;
				}
				$meta_keywords = array();
				while ($this->startsWith($first_row[$j-1],"meta_keywords(")) {
					$language_code = substr($first_row[$j-1],strlen("meta_keywords("),strlen($first_row[$j-1])-strlen("meta_keywords(")-1);
					$meta_keyword = $this->getCell($data,$i,$j++);
					$meta_keyword = htmlspecialchars( $meta_keyword );
					$meta_keywords[$language_code] = $meta_keyword;
				}
				$categories = $this->getCell($data,$i,$j++);
				// $related_products = $this->getCell($data,$i,$j++,'');
				//$quantity = $this->getCell($data,$i,$j++,'0');
				
				$model = $this->getCell($data,$i,$j++,'   ');
				$image_name = $this->getCell($data,$i,$j++);
				$alt_text = $this->getCell($data,$i,$j++);
				$caption = $this->getCell($data,$i,$j++);
				$shipping = $this->getCell($data,$i,$j++);
				$price = $this->getCell($data,$i,$j++,'0.00');
				$hazardous = $this->getCell($data,$i,$j++.'0');
				$pack = $this->getCell($data,$i,$j++);
				$cart_comment = $this->getCell($data,$i,$j++);			
				$weight = $this->getCell($data,$i,$j++,'0');
				$weight_unit = $this->getCell($data,$i,$j++,$default_weight_unit);
				$length = $this->getCell($data,$i,$j++,'0');
				$width = $this->getCell($data,$i,$j++,'0');
				$height = $this->getCell($data,$i,$j++,'0');
				$measurement_unit = $this->getCell($data,$i,$j++,$default_measurement_unit);
				$keyword = $this->getCell($data,$i,$j++);
				$status = $this->getCell($data,$i,$j++,'true');
				$points = $this->getCell($data,$i,$j++,'0');
				$date_added = $this->getCell($data,$i,$j++);
				$date_added = ((is_string($date_added)) && (strlen($date_added)>0)) ? $date_added : "NOW()";
				$date_modified = $this->getCell($data,$i,$j++);
				$date_modified = ((is_string($date_modified)) && (strlen($date_modified)>0)) ? $date_modified : "NOW()";
							//$store_ids = $this->getCell($data,$i,$j++);
				$sort_order = $this->getCell($data,$i,$j++,'0');
				//$subtract = $this->getCell($data,$i,$j++,'true');
				//$minimum = $this->getCell($data,$i,$j++,'1');
				$product = array();
				$product['product_id'] = $product_id;
				$product['name'] = $names;			
				$product['description'] = $descriptions;
				$product['meta_title'] = $meta_titles;
				$product['meta_description'] = $meta_descriptions;
				$product['meta_keyword'] = $meta_keywords;
				$categories = trim( $this->clean($categories, false) );
				$product['categories'] = ($categories=="") ? array() : explode( ",", $categories );
				if ($product['categories']===false) {
					$product['categories'] = array();
				}
				// $related_products = trim( $this->clean($related_products, false) );
				$product['related_products'] = array();  // ($related_products=="") ? array() : explode( ",", $related_products );
				
				if ($product['related_products']===false) {
						$product['related_products'] = array();
				}
				// $product['quantity'] = $quantity;
				$product['model'] = $model;
				$product['image'] = $image_name;
				$product['alt_text'] = $alt_text;
				$product['caption'] = $caption;
				$product['shipping'] = $shipping;
				$product['price'] = $price;
				$product['hazardous'] = $hazardous;
				$product['pack'] = $pack;
				$product['cart_comment'] = $cart_comment;
				$product['weight'] = $weight;
				$product['weight_unit'] = $weight_unit;
				$product['length'] = $length;
				$product['width'] = $width;
				$product['height'] = $height;
				$product['measurement_unit'] = $measurement_unit;
				$product['seo_keyword'] = $keyword;
				$product['status'] = $status;
				$product['points'] = $points;
				$product['date_added'] = $date_added;
				$product['date_modified'] = $date_modified;
				$product['viewed'] = isset($view_counts[$product_id]) ? $view_counts[$product_id] : 0;
				$store_ids = trim( $this->clean($store_ids, false) );
				$product['store_ids'] = array(0);
				$product['stock_status_id'] = $default_stock_status_id;
				//$product['subtract'] = $subtract;
				//$product['minimum'] = $minimum;
				$product['sort_order'] = $sort_order;                        
				if ($incremental) {
					// $this->deleteImportExportProduct( $product_id );
					$this->deleteGroupedProduct( $product_id );
				}
				$available_product_ids[$product_id] = $product_id;
				$this->moreProductCells( $i, $j, $data, $product );
				$this->storeGroupedProductIntoDatabase( $product, $languages, $product_fields, $available_store_ids ,$seo_url_ids);
				// $this->storeProductIntoDatabase( $product, $languages, $product_fields, $available_store_ids, $weight_class_ids, $length_class_ids, $seo_url_ids );
				
			}
		}

		protected function uploadGrouped( &$reader, $incremental, &$available_product_ids=array() ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old products
            $available_product_ids = array();
            if ($incremental) {
                    $old_product_ids = $this->getAvailableProductIds($data);
            }
           
            // get pre-defined store_ids
            $available_store_ids = $this->getAvailableStoreIds();
            // find the installed languages
            $languages = $this->getLanguages();

			// var_dump($languages); die;

            // find the default units
            $seo_url_ids = $this->getProductUrlAliasIds();
            
            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }
            
            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $names = array();
                    while ($this->startsWith($first_row[$j-1],"Product_Name(")) {
                            $language_code = substr($first_row[$j-1],strlen("Product_Name("),strlen($first_row[$j-1])-strlen("Product_Name(")-1);
                            $name = $this->getCell($data,$i,$j++);
                            $name = htmlspecialchars( $name );
                            $names[$language_code] = $name;
                    }
                    while ($this->startsWith($first_row[$j-1],"Description(")) {
                            $language_code = substr($first_row[$j-1],strlen("Description("),strlen($first_row[$j-1])-strlen("Description(")-1);
                            $description = $this->getCell($data,$i,$j++);
                            $description = htmlspecialchars( $description );
                            $descriptions[$language_code] = $description;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Title(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Title("),strlen($first_row[$j-1])-strlen("Meta_Tag_Title(")-1);
                            $meta_tag_title = $this->getCell($data,$i,$j++);
                            $meta_tag_title = htmlspecialchars( $meta_tag_title );
                            $meta_tag_titles[$language_code] = $meta_tag_title;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Description(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Description("),strlen($first_row[$j-1])-strlen("Meta_Tag_Description(")-1);
                            $meta_tag_description = $this->getCell($data,$i,$j++);
                            $meta_tag_description = htmlspecialchars( $meta_tag_description );
                            $meta_tag_descriptions[$language_code] = $meta_tag_description;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Keywords(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Keywords("),strlen($first_row[$j-1])-strlen("Meta_Tag_Keywords(")-1);
                            $meta_tag_keyword = $this->getCell($data,$i,$j++);
                            $meta_tag_keyword = htmlspecialchars( $meta_tag_keyword );
                            $meta_tag_keywords[$language_code] = $meta_tag_keyword;
                    }
                   
                    $image = $this->getCell($data,$i,$j++,'');
                    $alt_text = $this->getCell($data,$i,$j++,'');
                    $caption = $this->getCell($data,$i,$j++,'');
                    $sort_order = $this->getCell($data,$i,$j++,'');
                    $status = $this->getCell($data,$i,$j++,'');
                    $catagories = $this->getCell($data,$i,$j++,'');
					$model = $this->getCell($data,$i,$j++,''); // This is actual product name. 

                    $related_products = $this->getCell($data,$i,$j++,'');
                    // $reference = $this->getCell($data,$i,$j++,'');
                    $seo_keywords = $this->getCell($data,$i,$j++,'');
                    $product = array();
                    $product['product_id'] = $product_id;
                    $product['name'] = $names;
                    $product['description'] = $descriptions;
                    $product['meta_title'] = $meta_tag_titles;
                    $product['meta_description'] = $meta_tag_descriptions;
                    $product['meta_keyword'] = $meta_tag_keywords;
                    $product['image'] = $image;
                    $product['alt_text'] = $alt_text;
                    $product['caption'] = $caption;
                    $product['sort_order'] = $sort_order;
                    $product['status'] = $status;
                    $categories = trim( $this->clean($catagories, false) );
                    $product['categories'] = ($categories=="") ? array() : explode( ",", $categories );
                    if ($product['categories']===false) {
                            $product['categories'] = array();
                    }
					$product['model'] = $model;
                    $related_products = trim( $this->clean($related_products, false) );
                    $product['related_products'] = ($related_products=="") ? array() : explode( ",", $related_products );
                    if ($product['related_products']===false) {
                            $product['related_products'] = array();
                    }
                    // $product['reference'] = htmlspecialchars( $reference );
                    $product['keyword'] = $seo_keywords;
                    if ($incremental) {
                            $this->deleteGroupedProduct( $product_id );
                    }
                    $available_product_ids[$product_id] = $product_id;

					// echo "<pre>"; print_r($product); die;

                    $this->moreProductCells( $i, $j, $data, $product );
                    $this->storeGroupedProductIntoDatabase( $product, $languages, $product_fields, $available_store_ids ,$seo_url_ids);
            }
    	}

		protected function deleteAdditionalRefrences( $product_id ) {
				$this->db->query( "DELETE FROM `".DB_PREFIX."product_references` WHERE product_id='".(int)$product_id."'" );
		}
		protected function uploadGroupedProductImages( &$reader, $incremental, &$available_product_ids ) {
				// get worksheet, if not there return immediately
				$data = $reader->getSheetByName( 'Images' );
				if ($data==null) {
						return;
				}

				// if incremental then find current product IDs else delete all old product images
				if ($incremental) {
						$unlisted_product_ids = $available_product_ids;
				} 

				// load the worksheet cells and store them to the database
				$old_product_image_ids = array();
				$previous_product_id = 0;
				$i = 0;
				$k = $data->getHighestRow();
				for ($i=0; $i<$k; $i+=1) {
						$j= 1;
						if ($i==0) {
								continue;
						}
						$product_id = trim($this->getCell($data,$i,$j++));
						if ($product_id=="") {
								continue;
						}
						$image_name = $this->getCell($data,$i,$j++,'');
						if ($image_name=="") {
								continue;
						}
						$alt_text = $this->getCell($data,$i,$j++,'');
						$image_caption = $this->getCell($data,$i,$j++,'');
						$sort_order = $this->getCell($data,$i,$j++,0);
						$image = array();
						$image['product_id'] = $product_id;
						$image['image'] = $image_name;
						$image['alt_text'] = $alt_text;
						$image['image_caption'] = $image_caption;
						$image['sort_order'] = $sort_order;
						if (($incremental) && ($product_id != $previous_product_id)) {
								$this->deleteAdditionalImage( $product_id );
								if (isset($unlisted_product_ids[$product_id])) {
										unset($unlisted_product_ids[$product_id]);
								}
						}
						$this->storeAdditionalImageIntoDatabase( $image );
						$previous_product_id = $product_id;
				}
		}
		protected function storeAdditionalImageIntoDatabase( &$image ) {
				$this->db->query("INSERT INTO `".DB_PREFIX."product_image` SET `product_id` = '" . $image['product_id'] . "', `image` = '" . $this->db->escape($image['image']) . "', "
						. "`alt_text` = '" . $this->db->escape($image['alt_text']) . "', `image_caption` = '" . $this->db->escape($image['image_caption']) . "', "
						. "`sort_order` = '" . $this->db->escape($image['sort_order']) . "'");
		}
		protected function storeAdditionalRefrencesIntoDatabase( &$reference ) {
				$this->db->query("INSERT INTO `".DB_PREFIX."product_references` SET `product_id` = '" . $reference['product_id'] . "', `islink` = '" . $this->db->escape($reference['islink']) . "', "
						. "`text` = '" . $this->db->escape($reference['text']) . "', `link` = '" . $this->db->escape($reference['link']) . "', "
						. "`year` = '" . $this->db->escape($reference['year']) . "'");
		}

		protected function storeGroupedProductIntoDatabase( &$product, &$languages, &$product_fields, &$available_store_ids, &$seo_url_ids ) {
            // extract the product details
            $product_id = $product['product_id'];
            $name = $product['name'];
            $description = $product['description'];
            $meta_title = $product['meta_title'];
            $meta_description = $product['meta_description'];
            $meta_keyword = $product['meta_keyword'];
            $image = isset($product['image']) ? $this->db->escape($product['image']) : '';
            $alt_text = isset($product['image']) ? $this->db->escape($product['alt_text']) : '';
            $caption = isset($product['image']) ? $this->db->escape($product['caption']) : '';
            $sort_order = $product['sort_order'];
            $status = $product['status'];
            $categories = $product['categories'];
			$model = isset($product['model']) ? $this->db->escape($product['model']) : ''; // Actual product name.
            $related_products = $product['related_products'];
            $reference = isset($product['reference']) ? $this->db->escape($product['reference']) : '';
            $keyword = isset($product['keyword']) ? $this->db->escape($product['keyword']) : '';

            $sql = "INSERT INTO `".DB_PREFIX."gp_grouped` SET product_id = '".$product_id."';";
            $this->db->query($sql);
            // generate and execute SQL for inserting the product
            $sql = "INSERT INTO `".DB_PREFIX."product` SET product_id = '".$product_id."', model= '".$model."', image = '".$image."', alt_text = '".$alt_text."', caption = '".$caption."',sort_order = '".$sort_order."', status = '".$status."', images_processed = 0,  gmc_special_product = 1, reference = '".$reference."';";
            $this->db->query($sql);
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    $language_id = $language['language_id'];
                    $name = isset($name[$language_code]) ? $this->db->escape($name[$language_code]) : '';
                    $descriptions = isset($description[$language_code]) ? $this->db->escape($description[$language_code]) : '';
                    $meta_titles = isset($meta_title[$language_code]) ? $this->db->escape($meta_title[$language_code]) : '';
                    $meta_descriptions = isset($meta_description[$language_code]) ? $this->db->escape($meta_description[$language_code]) : '';
                    $meta_keywords = isset($meta_keyword[$language_code]) ? $this->db->escape($meta_keyword[$language_code]) : '';
                	$sql  = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES  ( $product_id, $language_id, '$name', '$descriptions', '$meta_titles', '$meta_descriptions', '$meta_keywords' );";
                $this->db->query( $sql );
            }
			
            if ($keyword) {
                    if (isset($seo_url_ids[$product_id])) {
                            $seo_url_id = $seo_url_ids[$product_id];
                            $sql = "INSERT INTO `".DB_PREFIX."seo_url` (`seo_url_id`,`language_id`,`query`,`keyword`) VALUES ($seo_url_id,'1','product_id=$product_id','$keyword');";
                            
                            unset($seo_url_ids[$product_id]);
                    } else {
                            $sql = "INSERT INTO `".DB_PREFIX."seo_url` (`language_id`,`query`,`keyword`) VALUES ('1','product_id=$product_id','$keyword');";
                    }
                    $this->db->query($sql);
            }
            if (count($categories) > 0) {
                    $sql = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
                    $first = true;
                    foreach ($categories as $category_id) {
                            $sql .= ($first) ? "\n" : ",\n";
                            $first = false;
                            $sql .= "($product_id,$category_id)";
                    }
                    $sql .= ";";
                    $this->db->query($sql);
            }
            if (count($related_products) > 0) {
				$sql = "INSERT INTO `".DB_PREFIX."product_related` (`product_id`,`related_id`) VALUES ";
				$first = true;
				foreach ($related_products as $related_id) {
					$sql .= ($first) ? "\n" : ",\n";
					$first = false;
					$sql .= "($product_id,$related_id)";
				}
				$sql .= ";";
				$this->db->query($sql);
			}

			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . $product_id . "', store_id = 0");
		}

		protected function uploadGPData( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'GP_Data' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old product catalogs
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            }
           
            // load the worksheet cells and store them to the database
            $first_row = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                if ($i==0) {
                        $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                        for ($j=1; $j<=$max_col; $j+=1) {
                                $first_row[] = $this->getCell($data,$i,$j);
                        }
                        continue;
                }
                $j = 1;
                $product_id = trim($this->getCell($data,$i,$j++,''));
                if ($product_id=="") {
                        continue;
                }
                $child_id = $this->getCell($data,$i,$j++,'');
                $child_sort_order = $this->getCell($data,$i,$j++,0);

                $product = array();
                $product['product_id'] = $product_id;
                $product['child_id'] = $child_id;
                $product['child_sort_order'] = $child_sort_order;
                if (($incremental) && ($product_id != $previous_product_id)) {
                        $this->deleteGPData( $product_id );
                        if (isset($unlisted_product_ids[$product_id])) {
                                unset($unlisted_product_ids[$product_id]);
                        }
                }
                $this->storeGPDataIntoDatabase( $product );
                $previous_product_id = $product_id;
            }
    	}

		protected function deleteGroupedProduct( $product_id ) {
            $sql  = "DELETE FROM `".DB_PREFIX."gp_grouped` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_description` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_related` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_to_category` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE 'product_id=".(int)$product_id."';\n";
			$sql .= "DELETE FROM " .DB_PREFIX."product_to_store WHERE `product_id` = '$product_id';\n";
            $this->multiquery( $sql );
    	}

		protected function storeGPDataIntoDatabase( &$product ) {
            // extract the product details
            $product_id = $product['product_id'];
            $child_id = $product['child_id'];
            $child_sort_order = $product['child_sort_order'];
            
            // generate and execute SQL for inserting the product
            $sql = "INSERT INTO `".DB_PREFIX."gp_grouped_child` SET product_id = '".$product_id."', child_id = '".$child_id."', child_sort_order = '".$child_sort_order."'";
            $this->db->query($sql);
            
		}

		protected function deleteGPData( $product_id ) {
				$this->db->query("DELETE FROM `".DB_PREFIX."gp_grouped_child` WHERE `product_id` = '" . (int)$product_id . "'");
		}

		protected function uploadProtocols( &$reader, $incremental ) {
			// get worksheet, if not there return immediately
			$data = $reader->getSheetByName( 'Protocol' );
			if ($data==null) {
					return;
			}

			// load the worksheet cells and store them to the database            
			$i = 0;
			$k = $data->getHighestRow();
			for ($i=0; $i<$k; $i+=1) {
					$j = 1;
					if ($i==0) {
							continue;
					}
					$product_id = trim($this->getCell($data,$i,$j++));
					if ($product_id=="") {
							continue;
					}
					$document = $this->getCell($data,$i,$j++,'');
					$protocol = array();
					$protocol['product_id'] = $product_id;
					$protocol['pdf'] = $document;
					if ($incremental) {
							$this->deleteProtocol( $product_id );
					}
					$this->storeProtocolIntoDatabase( $protocol );
			}
		}

		protected function uploadImagesData( &$reader, $incremental, &$available_product_ids ) {
			// get worksheet, if not there return immediately
			$data = $reader->getSheetByName( 'Images' );
			if ($data==null) {
				return;
			}

			// if incremental then find current product IDs else delete all old product attributes
			if ($incremental) {
				$unlisted_product_ids = $available_product_ids;
			} 

			// load the worksheet cells and store them to the database
			$previous_product_id = 0;
			$i = 0;
			$k = $data->getHighestRow();
			for ($i=0; $i<$k; $i+=1) {
				$j= 1;
				if ($i==0) {
					continue;
				}
				$product_id = trim($this->getCell($data,$i,$j++));
				if ($product_id=="") {
					continue;
				}
				$image_name = $this->getCell($data,$i,$j++,'');
							$alt_text = $this->getCell($data,$i,$j++,'0');
							$image_caption = $this->getCell($data,$i,$j++,'1');
							$sort_order = $this->getCell($data,$i,$j++,'2');
				$image = array();
							$image['product_id'] = $product_id;
							$image['image'] = $image_name;
							$image['alt_text'] = $alt_text;
				$image['image_caption'] = $image_caption;
				$image['sort_order'] = $sort_order;
							if (($incremental) && ($product_id != $previous_product_id)) {
					$this->deleteAdditionalImage( $product_id );
					if (isset($unlisted_product_ids[$product_id])) {
						unset($unlisted_product_ids[$product_id]);
					}
				}
				$this->storeAdditionalImageIntoDatabase( $image );
							$previous_product_id = $product_id;
			}
		}
        
        protected function uploadMsds( &$reader, $incremental ) {
                // get worksheet, if not there return immediately
                $data = $reader->getSheetByName( 'Msds' );
                if ($data==null) {
                        return;
                }

                // load the worksheet cells and store them to the database
                $first_row = array();
                $i = 0;
                $k = $data->getHighestRow();
                for ($i=0; $i<$k; $i+=1) {
                        if ($i==0) {
                                $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                                for ($j=1; $j<=$max_col; $j+=1) {
                                        $first_row[] = $this->getCell($data,$i,$j);
                                }
                                continue;
                        }
                        $j = 1;
                        if ($i==0) {
                                continue;
                        }
                        $product_id = trim($this->getCell($data,$i,$j++));
                        if ($product_id=="") {
                                continue;
                        }
                        $pdfs = array();
                        while (isset($first_row[$j-1]) && $this->startsWith($first_row[$j-1],"document(")) {
                                $language_code = substr($first_row[$j-1],strlen("document("),strlen($first_row[$j-1])-strlen("document(")-1);
                                $language_technical_id = $this->getLanguageTechnicalIdByLanguageTechnicalName($language_code);
                                $pdf = $this->getCell($data,$i,$j++);
                                $pdf = htmlspecialchars( $pdf );
                                if($language_technical_id)
                                    $pdfs[$language_technical_id] = $pdf;
                        }
                        $msds = array();
                        $msds['product_id'] = $product_id;
                        $msds['pdfs'] = $pdfs;
                        if ($incremental) {
                                $this->deleteMsds( $product_id );
                        }
                        $this->storeMsdsIntoDatabase( $msds );
                }
        }

		protected function getProductUrlAliasIds() {
			$sql  = "SELECT seo_url_id, SUBSTRING( query, CHAR_LENGTH('product_id=')+1 ) AS product_id ";
			$sql .= "FROM `".DB_PREFIX."seo_url` ";
			$sql .= "WHERE query LIKE 'product_id=%'";
			$query = $this->db->query( $sql );
			$seo_url_ids = array();
			foreach ($query->rows as $row) {
				$seo_url_id = $row['seo_url_id'];
				$product_id = $row['product_id'];
				$seo_url_ids[$product_id] = $seo_url_id;
			}
			return $seo_url_ids;
		}

        protected function insertQuery ($table, $data) {
                if (!empty($table)){
                        $columns = [];
                        
                        foreach ($data as $index => $value) {
                                $columns[] = "`$index` = '" . $this->db->escape($value) . "'";
                        }
                        
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "$table` SET " . implode(', ', $columns));
                }
        }
        
        protected function storeSpecialIntoDatabase( &$special, &$customer_group_ids ) {
			$name = $special['customer_group'];
			$customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');

                $this->insertQuery('product_special', [
                        'product_id' => (int)$special['product_id'],
                        'customer_group_id' => (int)$customer_group_id,
                        'priority' => $special['priority'],
                        'price' => $special['price'],
                        'date_start' => $special['date_start'],
                        'date_end' => $special['date_end'],
                ]);
		}

		protected function deleteSpecial( $product_id ) {
					$this->db->query( "DELETE FROM `".DB_PREFIX."product_special` WHERE product_id='".(int)$product_id."'" );
		}

		protected function getAttributeGroupIds() {
			$language_id = $this->getDefaultLanguageId();
			$sql  = "SELECT attribute_group_id, name FROM `".DB_PREFIX."attribute_group_description` ";
			$sql .= "WHERE language_id='".(int)$language_id."'";
			$query = $this->db->query( $sql );
			$attribute_group_ids = array();
			foreach ($query->rows as $row) {
				$attribute_group_id = $row['attribute_group_id'];
				$name = $row['name'];
				$attribute_group_ids[$name] = $attribute_group_id;
			}
			return $attribute_group_ids;
		}
	
		protected function getAttributeIds() {
			$language_id = $this->getDefaultLanguageId();
			$sql  = "SELECT a.attribute_group_id, ad.attribute_id, ad.name FROM `".DB_PREFIX."attribute_description` ad ";
			$sql .= "INNER JOIN `".DB_PREFIX."attribute` a ON a.attribute_id=ad.attribute_id ";
			$sql .= "WHERE ad.language_id='".(int)$language_id."'";
			$query = $this->db->query( $sql );
			$attribute_ids = array();
			foreach ($query->rows as $row) {
				$attribute_group_id = $row['attribute_group_id'];
				$attribute_id = $row['attribute_id'];
				$name = $row['name'];
				$attribute_ids[$attribute_group_id][$name] = $attribute_id;
			}
			return $attribute_ids;
		}

		protected function moreProductAttributeCells( $i, &$j, &$worksheet, &$product_attribute ) {
			return;
		}

		protected function uploadProductAttributes( &$reader, $incremental, &$available_product_ids ) {
			// get worksheet, if not there return immediately
			$data = $reader->getSheetByName( 'ProductAttributes' );
			if ($data==null) {
				return;
			}
	
			// if incremental then find current product IDs else delete all old product attributes
			if ($incremental) {
				$unlisted_product_ids = $available_product_ids;
			} 
	
			$attribute_group_ids = $this->getAttributeGroupIds();
			$attribute_ids = $this->getAttributeIds();
	
			// load the worksheet cells and store them to the database
			$languages = $this->getLanguages();
			$previous_product_id = 0;
			$first_row = array();
			$i = 0;
			$k = $data->getHighestRow();
			for ($i=0; $i<$k; $i+=1) {
				if ($i==0) {
					$max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
					for ($j=1; $j<=$max_col; $j+=1) {
						$first_row[] = $this->getCell($data,$i,$j);
					}
					continue;
				}
				$j = 1;
				$product_id = trim($this->getCell($data,$i,$j++));
				if ($product_id=='') {
					continue;
				}
				$attribute_group_name = $this->getCell($data,$i,$j++);
							$attribute_group_id = isset($attribute_group_ids[$attribute_group_name]) ? $attribute_group_ids[$attribute_group_name] : '';
				if ($attribute_group_id=='') {
					continue;
				}
				$attribute_name = $this->getCell($data,$i,$j++);
							$attribute_id = isset($attribute_ids[$attribute_group_id][$attribute_name]) ? $attribute_ids[$attribute_group_id][$attribute_name] : '';
				if ($attribute_id=='') {
					continue;
				}
				$texts = array();
				while (($j<=$max_col) && $this->startsWith($first_row[$j-1],"text(")) {
					$language_code = substr($first_row[$j-1],strlen("text("),strlen($first_row[$j-1])-strlen("text(")-1);
					$text = $this->getCell($data,$i,$j++);
					$text = htmlspecialchars( $text );
					$texts[$language_code] = $text;
				}
				$product_attribute = array();
				$product_attribute['product_id'] = $product_id;
				$product_attribute['attribute_group_id'] = $attribute_group_id;
				$product_attribute['attribute_id'] = $attribute_id;
				$product_attribute['texts'] = $texts;
				if (($incremental) && ($product_id != $previous_product_id)) {
					$this->deleteProductAttribute( $product_id );
					if (isset($unlisted_product_ids[$product_id])) {
						unset($unlisted_product_ids[$product_id]);
					}
				}
				$this->moreProductAttributeCells( $i, $j, $data, $product_attribute );
				$this->storeProductAttributeIntoDatabase( $product_attribute, $languages );
				$previous_product_id = $product_id;
			}
			if ($incremental) {
				$this->deleteUnlistedProductAttributes( $unlisted_product_ids );
			}
		}
			
		protected function deleteProductAttribute( $product_id ) {
			$sql = "DELETE FROM `".DB_PREFIX."product_attribute` WHERE product_id='".(int)$product_id."'";
			$this->db->query( $sql );
		}
			
		protected function deleteUnlistedProductAttributes( &$unlisted_product_ids ) {
			foreach ($unlisted_product_ids as $product_id) {
				$sql = "DELETE FROM `".DB_PREFIX."product_attribute` WHERE product_id='".(int)$product_id."'";
				$this->db->query( $sql );
			}
		}
			
		protected function storeProductAttributeIntoDatabase( &$product_attribute, &$languages ) {
			$product_id = $product_attribute['product_id'];
			$attribute_id = $product_attribute['attribute_id'];
			$texts = $product_attribute['texts'];
			foreach ($languages as $language) {
				$language_code = $language['code'];
				$language_id = $language['language_id'];
				$text = isset($texts[$language_code]) ? $this->db->escape($texts[$language_code]) : '';
				$sql  = "INSERT INTO `".DB_PREFIX."product_attribute` (`product_id`, `attribute_id`, `language_id`, `text`) VALUES ";
				$sql .= "( $product_id, $attribute_id, $language_id, '$text' );";
				$this->db->query( $sql );
			}
		}
		   

	// function for reading additional cells in class extensions
	protected function moreSpecialCells( $i, &$j, &$worksheet, &$special ) {
		return;
	}

	protected function uploadSpecials( &$reader, $incremental, &$available_product_ids ) {
		// get worksheet, if not there return immediately
		$data = $reader->getSheetByName( 'Specials' );
		if ($data==null) {
			return;
		}

		// if incremental then find current product IDs else delete all old specials
		if ($incremental) {
			$unlisted_product_ids = $available_product_ids;
		}

		// get existing customer groups
		$customer_group_ids = $this->getCustomerGroupIds();

		// load the worksheet cells and store them to the database
		$previous_product_id = 0;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($i==0) {
				continue;
			}
			$product_id = trim($this->getCell($data,$i,$j++));
			if ($product_id=="") {
				continue;
			}
			$customer_group = trim($this->getCell($data,$i,$j++));
			if ($customer_group=="") {
				continue;
			}
			$priority = $this->getCell($data,$i,$j++,'0');
			$price = $this->getCell($data,$i,$j++,'0');
			$date_start = $this->getCell($data,$i,$j++,'0000-00-00');
			$date_end = $this->getCell($data,$i,$j++,'0000-00-00');
			$special = array();
			$special['product_id'] = $product_id;
			$special['customer_group'] = $customer_group;
			$special['priority'] = $priority;
			$special['price'] = $price;
			$special['date_start'] = $date_start;
			$special['date_end'] = $date_end;
			if (($incremental) && ($product_id != $previous_product_id)) {
				$this->deleteSpecial( $product_id );
				if (isset($unlisted_product_ids[$product_id])) {
					unset($unlisted_product_ids[$product_id]);
				}
			}
			$this->moreSpecialCells( $i, $j, $data, $special );
			$this->storeSpecialIntoDatabase( $special, $customer_group_ids );
			$previous_product_id = $product_id;
		}
	}

	protected function storeDiscountIntoDatabase( &$discount, &$customer_group_ids ) {
		$name = $discount['customer_group'];
		$customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');

                $this->insertQuery('product_discount', [
                        'product_id' => (int)$discount['product_id'],
                        'customer_group_id' => (int)$customer_group_id,
                        'quantity' => $discount['quantity'],
                        'priority' => $discount['priority'],
                        'price' => $discount['price'],
                        'date_start' => $discount['date_start'],
                        'date_end' => $discount['date_end'],
                ]);
	}

	protected function deleteDiscount( $product_id ) {
		$this->db->query( "DELETE FROM `".DB_PREFIX."product_discount` WHERE product_id='".(int)$product_id."'" );
	}

	// function for reading additional cells in class extensions
	protected function moreDiscountCells( $i, &$j, &$worksheet, &$discount ) {
		return;
	}

	protected function uploadDiscounts( &$reader, $incremental, &$available_product_ids ) {
		// get worksheet, if not there return immediately
		$data = $reader->getSheetByName( 'Discounts' );
		if ($data==null) {
			return;
		}

		// if incremental then find current product IDs else delete all old discounts
		if ($incremental) {
			$unlisted_product_ids = $available_product_ids;
		}

		// get existing customer groups
		$customer_group_ids = $this->getCustomerGroupIds();

		// load the worksheet cells and store them to the database
		$previous_product_id = 0;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($i==0) {
				continue;
			}
			$product_id = trim($this->getCell($data,$i,$j++));
			if ($product_id=="") {
				continue;
			}
			$customer_group = trim($this->getCell($data,$i,$j++));
			if ($customer_group=="") {
				continue;
			}
			$quantity = $this->getCell($data,$i,$j++,'0');
			$priority = $this->getCell($data,$i,$j++,'0');
			$price = $this->getCell($data,$i,$j++,'0');
			$date_start = $this->getCell($data,$i,$j++,'0000-00-00');
			$date_end = $this->getCell($data,$i,$j++,'0000-00-00');
			$discount = array();
			$discount['product_id'] = $product_id;
			$discount['customer_group'] = $customer_group;
			$discount['quantity'] = $quantity;
			$discount['priority'] = $priority;
			$discount['price'] = $price;
			$discount['date_start'] = $date_start;
			$discount['date_end'] = $date_end;
			if (($incremental) && ($product_id != $previous_product_id)) {
				$this->deleteDiscount( $product_id );
				if (isset($unlisted_product_ids[$product_id])) {
					unset($unlisted_product_ids[$product_id]);
				}
			}
			$this->moreDiscountCells( $i, $j, $data, $discount );
			$this->storeDiscountIntoDatabase( $discount, $customer_group_ids );
			$previous_product_id = $product_id;
		}
	}
        
	protected function uploadRewards( &$reader, $incremental, &$available_product_ids ) {
		// get worksheet, if not there return immediately
		$data = $reader->getSheetByName( 'Rewards' );
		if ($data==null) {
			return;
		}

		// if incremental then find current product IDs else delete all old rewards
		if ($incremental) {
			$unlisted_product_ids = $available_product_ids;
		}

		// get existing customer groups
		$customer_group_ids = $this->getCustomerGroupIds();

		// load the worksheet cells and store them to the database
		$previous_product_id = 0;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($i==0) {
				continue;
			}
			$product_id = trim($this->getCell($data,$i,$j++));
			if ($product_id=="") {
				continue;
			}
			$customer_group = trim($this->getCell($data,$i,$j++));
			if ($customer_group=="") {
				continue;
			}
			$points = $this->getCell($data,$i,$j++,'0');
			$reward = array();
			$reward['product_id'] = $product_id;
			$reward['customer_group'] = $customer_group;
			$reward['points'] = $points;
			if (($incremental) && ($product_id != $previous_product_id)) {
				$this->deleteReward( $product_id );
				if (isset($unlisted_product_ids[$product_id])) {
					unset($unlisted_product_ids[$product_id]);
				}
			}
			$this->moreRewardCells( $i, $j, $data, $reward );
			$this->storeRewardIntoDatabase( $reward, $customer_group_ids );
			$previous_product_id = $product_id;
		}
	}

	protected function moreRewardCells( $i, &$j, &$worksheet, &$reward ) {
		return;
	}

	protected function storeRewardIntoDatabase( &$reward, &$customer_group_ids ) {
		$name = $reward['customer_group'];
		$customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');

				$this->insertQuery('product_reward', [
						'product_id' => (int)$reward['product_id'],
						'customer_group_id' => (int)$customer_group_id,
						'points' => $reward['points'],
				]);
	}

	protected function getCustomerGroupIds() {
		$sql = "SHOW TABLES LIKE \"".DB_PREFIX."customer_group_description\"";
		$query = $this->db->query( $sql );
		if ($query->num_rows) {
			$language_id = $this->getDefaultLanguageId();
			$sql  = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group_description` ";
			$sql .= "WHERE language_id=$language_id ";
			$sql .= "ORDER BY `customer_group_id` ASC";
			$query = $this->db->query( $sql );
		} else {
			$sql  = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group` ";
			$sql .= "ORDER BY `customer_group_id` ASC";
			$query = $this->db->query( $sql );
		}
		$customer_group_ids = array();
		foreach ($query->rows as $row) {
			$customer_group_id = $row['customer_group_id'];
			$name = $row['name'];
			$customer_group_ids[$name] = $customer_group_id;
		}
		return $customer_group_ids;
	}
        
	protected function deleteProtocol( $product_id ) {
			$this->db->query( "DELETE FROM `".DB_PREFIX."product_protocol` WHERE product_id='".(int)$product_id."'" );
	}
	
	protected function deleteMsds( $product_id ) {
			$this->db->query( "DELETE FROM `".DB_PREFIX."product_sds` WHERE product_id='".(int)$product_id."'" );
	}
        
	protected function deleteReward( $product_id ) {
		$sql = "SELECT product_reward_id, product_id, customer_group_id FROM `".DB_PREFIX."product_reward` WHERE product_id='".(int)$product_id."'";
		$query = $this->db->query( $sql );
		$old_product_reward_ids = array();
		foreach ($query->rows as $row) {
			$product_reward_id = $row['product_reward_id'];
			$product_id = $row['product_id'];
			$customer_group_id = $row['customer_group_id'];
			$old_product_reward_ids[$product_id][$customer_group_id] = $product_reward_id;
		}
		if ($old_product_reward_ids) {
			$sql = "DELETE FROM `".DB_PREFIX."product_reward` WHERE product_id='".(int)$product_id."'";
			$this->db->query( $sql );
		}
		return $old_product_reward_ids;
	}
        
	protected function storeProductIntoDatabase( &$product, &$languages, &$product_fields, &$available_store_ids, &$weight_class_ids, &$length_class_ids, $seo_url_ids ) {
		// extract the product details
		$product_id = $product['product_id'];
		$names = $product['names'];
		$categories = $product['categories'];
		//$quantity = $product['quantity'];
		$model = $this->db->escape($product['model']);
		$image = $this->db->escape($product['image']);
				$alt_text = $this->db->escape($product['alt_text']);
				$caption = $this->db->escape($product['caption']);
		$shipping = $product['shipping'];
		//$shipping = ((strtoupper($shipping)=="YES") || (strtoupper($shipping)=="Y") || (strtoupper($shipping)=="TRUE")) ? 1 : 0;
		$price = trim($product['price']);
		$points = $product['points'];
		$date_added = $product['date_added'];
		$date_modified = $product['date_modified'];
		$weight = ($product['weight']=="") ? 0 : $product['weight'];
		$weight_unit = $product['weight_unit'];
		$weight_class_id = (isset($weight_class_ids[$weight_unit])) ? $weight_class_ids[$weight_unit] : 0;
		$keyword = $this->db->escape($product['seo_keyword']);
		$status = $product['status'];
		$status = ((strtoupper($status)=="TRUE") || (strtoupper($status)=="YES") || (strtoupper($status)=="ENABLED") || (strtoupper($status)==1) || (strtoupper($status)=="1")) ? 1 : 0;
		$hazardous = $product['hazardous'];
		$size = $product['pack'];
		$cart_comment = $product['cart_comment'];
		$viewed = $product['viewed'];
		$descriptions = $product['descriptions'];
		$stock_status_id = $product['stock_status_id'];
		$length = $product['length'];
		$width = $product['width'];
		$height = $product['height'];
		$length_unit = $product['measurement_unit'];
		$length_class_id = (isset($length_class_ids[$length_unit])) ? $length_class_ids[$length_unit] : 0;
		//$subtract = $product['subtract'];
		//$subtract = ((strtoupper($subtract)=="TRUE") || (strtoupper($subtract)=="YES") || (strtoupper($subtract)=="ENABLED")) ? 1 : 0;
		//$minimum = $product['minimum'];
				$meta_titles = $product['meta_titles'];
				$meta_descriptions = $product['meta_descriptions'];
				$meta_keywords = $product['meta_keywords'];
		$sort_order = $product['sort_order'];

		// generate and execute SQL for inserting the product
				$sql = "INSERT INTO `".DB_PREFIX."product` SET product_id = '".$product_id."', stock_status_id = '".$stock_status_id."', model = '".$model."', image = '".$image."', alt_text = '".$alt_text."', caption = '".$caption."',"
						. " shipping_code = '".$shipping."', price = '".$price."', points = '".$points."', date_added = '".(($date_added=='NOW()') ? date('Y-m-d H:i:s') : $date_added)."', date_modified = '".(($date_modified=='NOW()') ? date('Y-m-d H:i:s') : $date_modified)."',"
						. " weight = '".$weight."', weight_class_id = '".$weight_class_id."', length = '".$length."', width = '".$width."', height = '".$height."',"
						. " length_class_id = '".$length_class_id."', status = '".$status."', hazardous = '".$hazardous."', size = '".$size."', cart_comment = '".$cart_comment."',"
						. " viewed = '".$viewed."', sort_order = '".$sort_order."', gmc_special_product = 1, images_processed = 0";
		$this->db->query($sql);
		foreach ($languages as $language) {
			$language_code = $language['code'];
			$language_id = $language['language_id'];
			$name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
			$description = isset($descriptions[$language_code]) ? $this->db->escape($descriptions[$language_code]) : '';	
						$meta_title = isset($meta_titles[$language_code]) ? $this->db->escape($meta_titles[$language_code]) : '';
			$meta_description = isset($meta_descriptions[$language_code]) ? $this->db->escape($meta_descriptions[$language_code]) : '';
			$meta_keyword = isset($meta_keywords[$language_code]) ? $this->db->escape($meta_keywords[$language_code]) : '';		
			$sql  = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES ";
						$sql .= "( $product_id, $language_id, '$name', '$description', '$meta_title', '$meta_description', '$meta_keyword' );";
						$this->db->query( $sql );
		}
		if (count($categories) > 0) {
			$sql = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
			$first = true;
			foreach ($categories as $category_id) {
				$sql .= ($first) ? "\n" : ",\n";
				$first = false;
				$sql .= "($product_id,$category_id)";
			}
			$sql .= ";";
			$this->db->query($sql);
		}                
				if ($keyword) {
			if (isset($seo_url_ids[$product_id])) {
				$seo_url_id = $seo_url_ids[$product_id];
				$sql = "INSERT INTO `".DB_PREFIX."seo_url` (`seo_url_id`,`language_id`,`query`,`keyword`) VALUES ($seo_url_id,'1','product_id=$product_id','$keyword');";
				unset($seo_url_ids[$product_id]);
			} else {
				$sql = "INSERT INTO `".DB_PREFIX."seo_url` (`language_id`,`query`,`keyword`) VALUES ('1','product_id=$product_id','$keyword');";
			}
			$this->db->query($sql);
		}
		$sql = "INSERT INTO `".DB_PREFIX."product_to_store` (`product_id`,`store_id`) VALUES ($product_id,0);";
				$this->db->query($sql);
	}
			
	protected function storeProtocolIntoDatabase( &$protocol ) {
			$this->db->query("INSERT INTO `".DB_PREFIX."product_protocol` SET product_id = '".(int)$protocol['product_id']."', pdf = '".$this->db->escape($protocol['pdf'])."'");
	}
	
	protected function storeMsdsIntoDatabase( &$msds ) {
			foreach($msds['pdfs'] as $language_technical_id => $pdf){
					$this->db->query("INSERT INTO `".DB_PREFIX."product_sds` SET product_id = '".(int)$msds['product_id']."', language_technical_id = '".(int)$language_technical_id."', pdf = '".$this->db->escape($pdf)."'");
			}
	}
	
	protected function moreProductCells( $i, &$j, &$worksheet, &$product ) {
		return;
	}
        
	protected function getProductViewCounts() {
		$query = $this->db->query( "SELECT product_id, viewed FROM `".DB_PREFIX."product`" );
		$view_counts = array();
		foreach ($query->rows as $row) {
			$product_id = $row['product_id'];
			$viewed = $row['viewed'];
			$view_counts[$product_id] = $viewed;
		}
		return $view_counts;
	}
        
	protected function getAvailableProductIds(&$data) {
		$available_product_ids = array();
		$k = $data->getHighestRow();
		for ($i=1; $i<$k; $i+=1) {
			$j = 1;
			$product_id = trim($this->getCell($data,$i,$j++));
			if ($product_id=="") {
				continue;
			}
			$available_product_ids[$product_id] = $product_id;
		}
		return $available_product_ids;
	}
        
	protected function getAvailableStoreIds() {
		$sql = "SELECT store_id FROM `".DB_PREFIX."store`;";
		$result = $this->db->query( $sql );
		$store_ids = array(0);
		foreach ($result->rows as $row) {
			if (!in_array((int)$row['store_id'],$store_ids)) {
				$store_ids[] = (int)$row['store_id'];
			}
		}
		return $store_ids;
	}
        
	protected function getDefaultWeightUnit() {
		$weight_class_id = $this->config->get( 'config_weight_class_id' );
		$language_id = $this->getDefaultLanguageId();
		$sql = "SELECT unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			return $query->row['unit'];
		}
		$sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = 'en'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			$language_id = $query->row['language_id'];
			$sql = "SELECT unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
			$query = $this->db->query( $sql );
			if ($query->num_rows > 0) {
				return $query->row['unit'];
			}
		}
		return 'kg';
	}

	protected function getDefaultMeasurementUnit() {
		$length_class_id = $this->config->get( 'config_length_class_id' );
		$language_id = $this->getDefaultLanguageId();
		$sql = "SELECT unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			return $query->row['unit'];
		}
		$sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = 'en'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			$language_id = $query->row['language_id'];
			$sql = "SELECT unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
			$query = $this->db->query( $sql );
			if ($query->num_rows > 0) {
				return $query->row['unit'];
			}
		}
		return 'cm';
	}
        
	protected function getAllWeightUnit() {
		$language_id = $this->getDefaultLanguageId();
		$sql = "SELECT title, unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			return $query->rows;
		}
		return 'lb';
	}

	protected function getAllMeasurementUnit() {
		$language_id = $this->getDefaultLanguageId();
		$sql = "SELECT title, unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
		$query = $this->db->query( $sql );
		if ($query->num_rows > 0) {
			return $query->rows;
		}
		return 'in';
	}
        
	protected function getWeightClassIds() {
		// find the default language id
		$language_id = $this->getDefaultLanguageId();
		
		// find all weight classes already stored in the database
		$weight_class_ids = array();
		$sql = "SELECT `weight_class_id`, `unit` FROM `".DB_PREFIX."weight_class_description` WHERE `language_id`=$language_id;";
		$result = $this->db->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$weight_class_id = $row['weight_class_id'];
				$unit = $row['unit'];
				if (!isset($weight_class_ids[$unit])) {
					$weight_class_ids[$unit] = $weight_class_id;
				}
			}
		}

		return $weight_class_ids;
	}

	protected function getLengthClassIds() {
		// find the default language id
		$language_id = $this->getDefaultLanguageId();
		
		// find all length classes already stored in the database
		$length_class_ids = array();
		$sql = "SELECT `length_class_id`, `unit` FROM `".DB_PREFIX."length_class_description` WHERE `language_id`=$language_id;";
		$result = $this->db->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$length_class_id = $row['length_class_id'];
				$unit = $row['unit'];
				if (!isset($length_class_ids[$unit])) {
					$length_class_ids[$unit] = $length_class_id;
				}
			}
		}

		return $length_class_ids;
	}
        
	function getCell(&$worksheet,$row,$col,$default_val='') {
		$col -= 1; // we use 1-based, PHPExcel uses 0-based column index
		$row += 1; // we use 0-based, PHPExcel uses 1-based row index
		$val = ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
		if ($val===null) {
			$val = $default_val;
		}
		return $val;
	}
        
	protected function deleteImportExportProduct( $product_id ) {
		$sql  = "DELETE FROM `".DB_PREFIX."product` WHERE `product_id` = '$product_id';\n";
		$sql .= "DELETE FROM `".DB_PREFIX."product_description` WHERE `product_id` = '$product_id';\n";
		$sql .= "DELETE FROM `".DB_PREFIX."product_to_category` WHERE `product_id` = '$product_id';\n";
		$sql .= "DELETE FROM `".DB_PREFIX."product_to_store` WHERE `product_id` = '$product_id';\n";
				$sql .= "DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE 'product_id=".(int)$product_id."';\n";
		$this->multiquery( $sql );
	}
        
	protected function multiquery( $sql ) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);
			if ($sql) {
				$this->db->query($sql);
			}
		}
	}
        
    protected function isInteger($input){
		return(ctype_digit(strval($input)));
	}
        
    protected function clearCache() {
		$this->cache->delete('*');
	}
        
	protected function startsWith( $haystack, $needle ) {
		if (strlen( $haystack ) < strlen( $needle )) {
			return false;
		}
		return (substr( $haystack, 0, strlen($needle) ) == $needle);
	}
        
    protected function clean( &$str, $allowBlanks=false ) {
		$result = "";
		$n = strlen( $str );
		for ($m=0; $m<$n; $m++) {
			$ch = substr( $str, $m, 1 );
			if (($ch==" ") && (!$allowBlanks) || ($ch=="\n") || ($ch=="\r") || ($ch=="\t") || ($ch=="\0") || ($ch=="\x0B")) {
				continue;
			}
			$result .= $ch;
		}
		return $result;
	}
	
	public function getLanguageTechnicalIdByLanguageTechnicalName($languageTechnicalName){
			$query = $this->db->query("SELECT language_technical_id FROM " . DB_PREFIX . "language_technical WHERE name = '".$this->db->escape($languageTechnicalName)."'");
			if($query->num_rows){
					return $query->row['language_technical_id'];
			} else {
					return FALSE;
			}
	}
	
	protected function deleteAdditionalImage( $product_id ) {
			$this->db->query( "DELETE FROM `".DB_PREFIX."product_image` WHERE product_id='".(int)$product_id."'" );
	}

	function validateHeading( &$data, &$expected, &$multilingual ) {
		$default_language_code = $this->config->get('config_language');
		$heading = array();
		$k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
		$i = 0;
		for ($j=1; $j <= $k; $j+=1) {
			$entry = $this->getCell($data,$i,$j);
			$bracket_start = strripos( $entry, '(', 0 );
			if ($bracket_start === false) {
				if (in_array( $entry, $multilingual )) {
					return false;
				}
				$heading[] = strtolower($entry);
			} else {
				$name = strtolower(substr( $entry, 0, $bracket_start ));
				if (!in_array( $name, $multilingual )) {
					return false;
				}
				$bracket_end = strripos( $entry, ')', $bracket_start );
				if ($bracket_end <= $bracket_start) {
					return false;
				}
				if ($bracket_end+1 != strlen($entry)) {
					return false;
				}
				$language_code = strtolower(substr( $entry, $bracket_start+1, $bracket_end-$bracket_start-1 ));
				if (count($heading) <= 0) {
					return false;
				}
				if ($heading[count($heading)-1] != $name) {
					$heading[] = $name;
				}
			}
		}
		for ($i=0; $i < count($expected); $i+=1) {
			if (!isset($heading[$i])) {
				return false;
			}
			if ($heading[$i] != $expected[$i]) {
				return false;
			}
		}
		return true;
	}

	function validateMsdsHeading( &$data, &$expected, &$multilingual ) {
		$language_technical = $this->getAllLanguageTechnicals();
		$default_language_code = 'English';            
		$heading = array();
		$k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );

		$i = 0;
		for ($j=1; $j <= $k; $j+=1) {
				$entry = $this->getCell($data,$i,$j);
				$bracket_start = strripos( $entry, '(', 0 );
				if ($bracket_start === false) {
						if (in_array( $entry, $multilingual )) {
								return false;
						}
						$heading[] = strtolower($entry);
				} else {
						$name = strtolower(substr( $entry, 0, $bracket_start ));
						if (!in_array( $name, $multilingual )) {
								return false;
						}
						$bracket_end = strripos( $entry, ')', $bracket_start );
						if ($bracket_end <= $bracket_start) {
								return false;
						}
						if ($bracket_end+1 != strlen($entry)) {
								return false;
						}
						$language_code = strtolower(substr( $entry, $bracket_start+1, $bracket_end-$bracket_start-1 ));
						if (count($heading) <= 0) {
								return false;
						}
						if ($heading[count($heading)-1] != $name) {
								$heading[] = $name;
						}
				}
		}
		for ($i=0; $i < count($expected); $i+=1) {
				if (!isset($heading[$i])) {
						return false;
				}
				if ($heading[$i] != $expected[$i]) {
						return false;
				}
		}
		return true;
	}

	protected function validateMsds( &$reader ) {
		$data = $reader->getSheetByName( 'Msds' );
		if ($data==null) {
				return true;
		}
		$expected_heading = array ( "product_id", "document" );
		$expected_multilingual = array( "document" );
		return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
	}


// DBM DOWNLOAD GROUP PRODUCT STARTS HERE
	public function download_gbm_gpproduct($category_id = 0, $option = 'PRODUCTS_ALL') {
		// we use our own error handler
		global $registry;
		$registry = $this->registry;
		set_error_handler('error_handler_for_export_import', E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

		// Use the PHPExcel package from http://phpexcel.codeplex.com/
		$cwd = getcwd();
		chdir( DIR_SYSTEM.'PHPExcel' );
		require_once( 'Classes/PHPExcel.php' );
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
		chdir( $cwd );

		// Memory Optimization
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		try {
				// set appropriate timeout limit
				set_time_limit( 1800 );

				$languages = $this->getLanguages();
				$default_language_id = $this->getDefaultLanguageId();

				// create a new workbook
				$workbook = new PHPExcel();

				// set some default styles
				$workbook->getDefaultStyle()->getFont()->setName('Verdana');
				$workbook->getDefaultStyle()->getFont()->setSize(10);
				//$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
				$workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

				// pre-define some commonly used styles
				$box_format = array(
						'fill' => array(
								'type'      => PHPExcel_Style_Fill::FILL_SOLID,
								'color'     => array( 'rgb' => '275E6E')
						),
						'font' => array(
								'color'     => array( 'rgb' => 'FFFFFF')
						)
				);
				$text_format = array(
						'numberformat' => array(
								'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
								'wrap' => true,
						)
				);
				$price_format = array(
						'numberformat' => array(
								'code' => '######0.00'
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
								'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
						)
				);
				$weight_format = array(
						'numberformat' => array(
								'code' => '##0.00'
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
								'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
						)
				);

				// create the worksheets
				$worksheet_index = 0;

			if($option == 'PRODUCTS_ALL') {
				// creating the Grouped Product worksheet
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'Products' );
				$this->populateGroupedProductWorksheet( $worksheet, $languages, $default_language_id, $price_format, $box_format, $weight_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
				
				// GP-Data worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'GP_Data' );
				$this->populateGPDataWorksheet( $worksheet, $box_format, $text_format, $category_id);
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
								   
				// Coas worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'Images' );
				$this->populateGroupedProductsImagesWorksheet( $worksheet, $box_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
				
				// Reference worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'Citations' );
				$this->populateGroupedProductsRefrenceWorksheet( $worksheet, $box_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
				
				// creating the ProductAttributes worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'ProductAttributes' );
				$this->populateProductAttributesWorksheet( $category_id, $worksheet, $languages, $default_language_id, $box_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
				
				// creating the Filter Values worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'Filters' );
				$this->populateSpecialProductFilterWorksheet( $category_id, $worksheet, $default_language_id, $box_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );
					
				 // creating the Valid Filter Values worksheet
				$workbook->createSheet();
				$workbook->setActiveSheetIndex($worksheet_index++);
				$worksheet = $workbook->getActiveSheet();
				$worksheet->setTitle( 'Valid Filter Values' );
				$this->populateValidFilterValues( $category_id, $worksheet, $box_format, $text_format );
				$worksheet->freezePaneByColumnAndRow( 1, 2 );


			}

				if ($option == 'PRODUCTS_ATTRIBUTES') {
					// creating the ProductAttributes worksheet
					$workbook->createSheet();
					$workbook->setActiveSheetIndex($worksheet_index++);
					$worksheet = $workbook->getActiveSheet();
					$worksheet->setTitle( 'ProductAttributes' );
					$this->populateProductAttributesWorksheet( $category_id, $worksheet, $languages, $default_language_id, $box_format, $text_format );
					$worksheet->freezePaneByColumnAndRow( 1, 2 );
				}
				
				if ($option == 'PRODUCTS_OPTIONS') {
					// creating the ProductOptions worksheet
					$workbook->createSheet();
					$workbook->setActiveSheetIndex($worksheet_index++);
					$worksheet = $workbook->getActiveSheet();
					$worksheet->setTitle( 'ProductOptions' );
					$this->populateProductOptionsWorksheet( $category_id, $worksheet, $box_format, $text_format );
					$worksheet->freezePaneByColumnAndRow( 1, 2 );

					// creating the ProductOptionValues worksheet
					$workbook->createSheet();
					$workbook->setActiveSheetIndex($worksheet_index++);
					$worksheet = $workbook->getActiveSheet();
					$worksheet->setTitle( 'ProductOptionValues' );
					$this->populateProductOptionValuesWorksheet( $category_id, $worksheet, $price_format, $box_format, $weight_format, $text_format );
					$worksheet->freezePaneByColumnAndRow( 1, 2 );
				}
				
				$workbook->setActiveSheetIndex(0);

				// redirect output to client browser
				$datetime = date('m-d-Y');

				$filename = 'GroupedProducts-'.$datetime.'.xlsx';

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
				$objWriter->setPreCalculateFormulas(false);
				$objWriter->save('php://output');

				// Clear the spreadsheet caches
				$this->clearSpreadsheetCache();
				exit();

		} catch (Exception $e) {
				$errstr = $e->getMessage();
				$errline = $e->getLine();
				$errfile = $e->getFile();
				$errno = $e->getCode();
				$this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
				if ($this->config->get('config_error_log')) {
						$this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
				}
				return;
		}
	}

	protected function populateGroupedProductWorksheet( &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
		$query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
		$product_fields = array();
		foreach ($query->rows as $row) {
				$product_fields[] = $row['Field'];
		}

		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Product_Name')+4,20)+1);
		}
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Description')+4,20)+1);
		}
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Title')+4,30)+1);
		}
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Description')+4,30)+1);
		}
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Keywords')+4,30)+1);
		}
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image_Alt'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image_caption'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Sort_order'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Status'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Categories'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Related_Products'),20)+1);
		// $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Reference'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('SEO_Keywords'),20)+1);

		// The product headings row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'Product_Name('.$language['code'].')';
		}
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'Description('.$language['code'].')';
		}
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'Meta_Tag_Title('.$language['code'].')';
		}
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'Meta_Tag_Description('.$language['code'].')';
		}
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'Meta_Tag_Keywords('.$language['code'].')';
		}
		$styles[$j] = &$text_format;
		$data[$j++] = 'Image';
		$data[$j++] = 'alt_text';
		$data[$j++] = 'caption';
		$data[$j++] = 'Sort_order';
		$data[$j++] = 'Status';
		// $styles[$j] = &$price_format;
		$data[$j++] = 'Categories';
		$styles[$j] = &$text_format;

		$data[$j++] = 'name';
		$styles[$j] = &$text_format;

		$data[$j++] = 'Related_Products';
		$styles[$j] = &$text_format;
		// $data[$j++] = 'Reference';
		// $styles[$j] = &$text_format;
		$data[$j++] = 'SEO_Keywords';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual products data
		$i += 1;
		$j = 0;
		$store_ids = $this->getStoreIdsForProducts();
		$products = $this->getImportExportGroupedProducts( $languages, $default_language_id, $product_fields );
		$len = count($products);
		foreach ($products as $row) {
				$data = array();
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$product_id = $row['product_id'];
				$data[$j++] = $product_id;
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['name'][$language['code']],  ENT_QUOTES, 'UTF-8');
				}
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['description'][$language['code']], ENT_QUOTES, 'UTF-8');
				}
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['meta_title'][$language['code']], ENT_QUOTES, 'UTF-8');
				}
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['meta_description'][$language['code']], ENT_QUOTES, 'UTF-8');
				}
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']] ,ENT_QUOTES, 'UTF-8');
				}
				$data[$j++] = $row['image'];
				$data[$j++] = $row['alt_text'];
				$data[$j++] = $row['caption'];
				$data[$j++] = $row['sort_order'];
				$data[$j++] = $row['status'];
				$data[$j++] = $row['categories'];
				$data[$j++] = $row['model'];
				$data[$j++] = $row['relateds'];
				// $data[$j++] = html_entity_decode($row['reference'], ENT_QUOTES, 'UTF-8');
				$data[$j++] = $row['keyword'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
		}
	} 

	protected function populateGPDataWorksheet( $worksheet, $box_format, $text_format, $category_id ){
		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog_id'),4)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('child_sort_order'),4)+1);
		
		// The product headings row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$data[$j++] = 'catalog_id';
		$data[$j++] = 'child_sort_order';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );
		// The actual products data
		$i += 1;
		$j = 0;

		//$sql = "SELECT product_id, child_id, child_sort_order from `".DB_PREFIX."gp_grouped_child` WHERE product_id <> 0 ORDER BY product_id ASC;";
		
		$sql = "SELECT ggc.product_id, child_id, child_sort_order from `".DB_PREFIX."gp_grouped_child` ggc LEFT JOIN `".DB_PREFIX."product_to_category` p2c ON p2c.product_id =  ggc.product_id WHERE ggc.product_id <> 0 AND p2c.category_id = ".$category_id." ORDER BY ggc.product_id ASC;";
		$query = $this->db->query( $sql );
		foreach ($query->rows as $row) {
				$data = array();
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$product_id = $row['product_id'];
				$data[$j++] = $product_id;
				$data[$j++] = $row['child_id'];
				$data[$j++] = $row['child_sort_order'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
		}
	}

	protected function populateGroupedProductsImagesWorksheet( $worksheet, $box_format, $text_format ) {
        // Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('alt_text'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_caption'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order')+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'image';
		$styles[$j] = &$text_format;
		$data[$j++] = 'alt_text';
		$styles[$j] = &$text_format;
                $data[$j++] = 'image_caption';
                $data[$j++] = 'sort_order';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product rewards data
		$i += 1;
		$j = 0;
                
                $sql = "SELECT pi.product_id As product_id, pi.image As image, pi.alt_text As alt_text, pi.image_caption As image_caption, pi.sort_order As sort_order "
                        . "FROM " . DB_PREFIX . "product_image pi "
                        . "LEFT JOIN " . DB_PREFIX . "product p ON pi.product_id = p.product_id "
                        . "LEFT JOIN " . DB_PREFIX . "gp_grouped gp ON p.product_id = gp.product_id "
                        . "WHERE p.gmc_special_product = 1 AND gp.product_id IS NOT NULL ORDER BY pi.product_id ASC, pi.sort_order ASC";
                $query = $this->db->query($sql);
                
                if($query->num_rows){
                    foreach ($query->rows as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(26);
                            $data = array();
                            $data[$j++] = $row['product_id'];
                            if ($row['image'] == "") {
                                continue;
                            }
                            $data[$j++] = str_replace(['///', '//'], '/', $row['image']);
                            $data[$j++] = $row['alt_text'];
                            $data[$j++] = $row['image_caption'];
                            $data[$j++] = $row['sort_order'];
                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
                }
    }

	protected function populateCatalogsWorksheet( &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
		$query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
		$product_fields = array();
		foreach ($query->rows as $row) {
				$product_fields[] = $row['Field'];
		}

		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
		foreach ($languages as $language) {
				$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog')+4,20)+1);
		}
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Description'),20)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('shipping'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('hazardous'),12)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('is_ground_hazmat'),12)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('size'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('cart_comment'),10)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'),6)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_unit'),3)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length'),8)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('width'),8)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('height'),8)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length_unit'),3)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'),8)+1);

		// The product headings row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		foreach ($languages as $language) {
				$styles[$j] = &$text_format;
				$data[$j++] = 'catalog('.$language['code'].')';
		}
		$styles[$j] = &$text_format;
		$data[$j++] = 'description';
		$data[$j++] = 'shipping';
		$styles[$j] = &$price_format;
		$data[$j++] = 'price';
		$data[$j++] = 'hazardous';
		$data[$j++] = 'is_ground_hazmat';
		$styles[$j] = &$text_format;
		$data[$j++] = 'size';
		$styles[$j] = &$text_format;
		$data[$j++] = 'cart_comment';
		$styles[$j] = &$weight_format;
		$data[$j++] = 'weight';
		$data[$j++] = 'weight_unit';
		$data[$j++] = 'length';
		$data[$j++] = 'width';
		$data[$j++] = 'height';
		$data[$j++] = 'length_unit';
		$data[$j++] = 'status';		
		$data[$j++] = 'points';
		$data[$j++] = 'sort_order';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual products data
		$i += 1;
		$j = 0;
		$store_ids = $this->getStoreIdsForProducts();
		$products = $this->getImportExportCatalogs( $languages, $default_language_id, $product_fields );
		$len = count($products);
		foreach ($products as $row) {
				$data = array();
				$worksheet->getRowDimension($i)->setRowHeight(26);
				$product_id = $row['product_id'];
				$data[$j++] = $product_id;
				foreach ($languages as $language) {
						$data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
				}
				$data[$j++] = $row['model'];
				$data[$j++] = $row['shipping_code'];
				$data[$j++] = $row['price'];
				$data[$j++] = $row['hazardous'];
				$data[$j++] = $row['is_ground_hazmat'];
				$data[$j++] = $row['size'];
				$data[$j++] = $row['cart_comment'];
				$data[$j++] = $row['weight'];
				$data[$j++] = $row['weight_unit'];
				$data[$j++] = $row['length'];
				$data[$j++] = $row['width'];
				$data[$j++] = $row['height'];
				$data[$j++] = $row['length_unit'];
				$data[$j++] = ($row['status']==0) ? 'false' : 'true';			
				$data[$j++] = $row['points'];
				$data[$j++] = $row['sort_order'];
				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
		}
	} 
	
	protected function populateGroupedProductsRefrenceWorksheet( $worksheet, $box_format, $text_format ) {
        // Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('islink'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('link'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('year')+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
                $data[$j++] = 'islink';
		$styles[$j] = &$text_format;
		$data[$j++] = 'text';
		$styles[$j] = &$text_format;
		$data[$j++] = 'link';
                $data[$j++] = 'year';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product rewards data
		$i += 1;
		$j = 0;
                
                $sql = "SELECT pr.product_id As product_id,pr.islink, pr.text As text, pr.link As link, pr.year As year "
                        . "FROM " . DB_PREFIX . "product_references pr "
                        . "LEFT JOIN " . DB_PREFIX . "product p ON pr.product_id = p.product_id "
                        . "LEFT JOIN " . DB_PREFIX . "gp_grouped gp ON p.product_id = gp.product_id "
                        . "WHERE p.gmc_special_product = 1 AND gp.product_id IS NOT NULL ORDER BY pr.product_id ASC, pr.year DESC";
                $query = $this->db->query($sql);
                
                if($query->num_rows){
                    foreach ($query->rows as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(26);
                            $data = array();
                            $data[$j++] = $row['product_id'];
                            $data[$j++] = $row['islink'];
                            $data[$j++] = $row['text'];
                            $data[$j++] = $row['link'];
                            $data[$j++] = $row['year'];
                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
                }
    }

	protected function getImportExportGroupedProducts( &$languages, $default_language_id, $product_fields ) {
		$sql  = "SELECT p.product_id, p.model, p.image, p.alt_text, p.caption, p.reference, ua.keyword,p.sort_order, p.status, ";
		$sql .= "GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ) AS categories, ";
		$sql .= "GROUP_CONCAT( DISTINCT CAST(pr.product_id AS CHAR(11)) SEPARATOR \",\" ) AS relateds ";
		$sql .= "FROM `".DB_PREFIX."product` p ";
		$sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
		$sql .= "LEFT JOIN `".DB_PREFIX."product_related` pr ON p.product_id=pr.product_id ";
		$sql .= "LEFT JOIN `".DB_PREFIX."seo_url` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
		$sql .= "WHERE p.gmc_special_product = '1' ";
		$sql .= "AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id <> '' ";		
		$sql .= "GROUP BY p.product_id ";
		$sql .= "ORDER BY p.product_id";
		$sql .= "; ";

		// echo $sql; die;
		
		$results = $this->db->query( $sql );
		$product_descriptions = $this->getExportGroupProductDescriptions( $languages );
		foreach ($languages as $language) {
				$language_code = $language['code'];
				foreach ($results->rows as $key => $row) {
						if (isset($product_descriptions[$language_code][$key])) {
								$results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
								$results->rows[$key]['description'][$language_code] = $product_descriptions[$language_code][$key]['description'];
								$results->rows[$key]['meta_title'][$language_code] = $product_descriptions[$language_code][$key]['meta_title'];
								$results->rows[$key]['meta_description'][$language_code] = $product_descriptions[$language_code][$key]['meta_description'];
								$results->rows[$key]['meta_keyword'][$language_code] = $product_descriptions[$language_code][$key]['meta_keyword'];
						} else {
								$results->rows[$key]['name'][$language_code] = '';
								$results->rows[$key]['description'][$language_code] = '';
								$results->rows[$key]['meta_title'][$language_code] = '';
								$results->rows[$key]['meta_description'][$language_code] = '';
								$results->rows[$key]['meta_keyword'][$language_code] = '';
						}
				}
		}
		return $results->rows;
	}

	protected function getExportGroupProductDescriptions( &$languages ) {
		// query the product_description table for each language
		$product_descriptions = array();
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$language_code = $language['code'];
			$sql  = "SELECT p.product_id, pd.* ";
                        $sql .= "FROM `".DB_PREFIX."product` p ";
                        $sql .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id=p.product_id AND pd.language_id='".(int)$language_id."' ";
                        $sql .= "WHERE p.gmc_special_product ='1' AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";			
                        $sql .= "GROUP BY p.product_id ";
                        $sql .= "ORDER BY p.product_id ";
			$sql .= "; ";

			// echo $sql; die;
			$query = $this->db->query( $sql );
			$product_descriptions[$language_code] = $query->rows;
		}
		return $product_descriptions;
	}

	protected function populateProductOptionsWorksheet( $category_id, &$worksheet, &$box_format, &$text_format ) {
		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('required'),5)+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$styles[$j] = &$text_format;
                $data[$j++] = 'option';
		$data[$j++] = 'required';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product options data
		$i += 1;
		$j = 0;
		$product_options = $this->getProductImportExportOptions($category_id);
		foreach ($product_options as $row) {
			$worksheet->getRowDimension($i)->setRowHeight(13);
			$data = array();
			$data[$j++] = $row['product_id'];
			$data[$j++] = html_entity_decode($row['option'],ENT_QUOTES,'UTF-8');
			$data[$j++] = ($row['required']==0) ? 'false' : 'true';
			$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
			$i += 1;
			$j = 0;
		}
	}
	
	protected function populateSpecialProductFilterWorksheet( $category_id, &$worksheet, $language_id, &$box_format, &$text_format ) {
		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_group_id'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_id'),30)+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'special_product_filter_group_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'special_product_filter_id';

		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product specials data
		$i += 1;
		$j = 0;
		$special_product_filters = $this->getSpecialProductFilters( $category_id, $language_id );
		foreach ($special_product_filters as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(13);
				$data = array();
				$data[$j++] = $row['product_id'];
				$data[$j++] = $row['special_product_filter_group_id'];
				$data[$j++] = $row['special_product_filter_id'];

				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
		}
}

protected function getSpecialProductFilters( $category_id ) {
		$sql = "SELECT p2spf.product_id, p2spf.special_product_filter_group_id, p2spf.special_product_filter_id FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id LEFT JOIN `" . DB_PREFIX . "product_to_special_product_filter` p2spf ON p.product_id = p2spf.product_id where (p.product_id = p2spf.product_id AND p2c.category_id = '" . (int)$category_id . "') ORDER BY p2spf.product_id";

		$result = $this->db->query( $sql );
		return $result->rows;
}

protected function getSpecialProductFiltersValues() {
		$sql = "SELECT spfg.special_product_filter_group_id, spfgd.name as special_product_filter_group_name, spf.special_product_filter_id, spfd.name as special_product_filter_name FROM `" . DB_PREFIX . "special_product_filter_group` spfg LEFT JOIN `" . DB_PREFIX . "special_product_filter_group_description` spfgd ON (spfg.special_product_filter_group_id = spfgd.special_product_filter_group_id) LEFT JOIN `" . DB_PREFIX . "special_product_filter` spf ON (spfg.special_product_filter_group_id = spf.special_product_filter_group_id) LEFT JOIN `" . DB_PREFIX . "special_product_filter_description` spfd ON (spf.special_product_filter_id = spfd.special_product_filter_id) ORDER BY spfg.special_product_filter_group_id, spfd.special_product_filter_id";

		$result = $this->db->query( $sql );
		return $result->rows;
}

protected function populateValidFilterValues( $category_id, $worksheet, $box_format, $text_format ){
		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_group_id'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('special product filter group name')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_id'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special product filter name'),30)+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;

		$data[$j++] = 'special_product_filter_group_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'special product filter group name';
		$styles[$j] = &$text_format;
		$data[$j++] = 'special_product_filter_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'special product filter name';

		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product specials data
		$i += 1;
		$j = 0;
		$special_product_filter_groups = $this->getSpecialProductFiltersValues();

		foreach ($special_product_filter_groups as $row) {
				$worksheet->getRowDimension($i)->setRowHeight(13);
				$data = array();
				$data[$j++] = $row['special_product_filter_group_id'];
				$data[$j++] = $row['special_product_filter_group_name'];
				$data[$j++] = $row['special_product_filter_id'];
				$data[$j++] = $row['special_product_filter_name'];

				$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
				$i += 1;
				$j = 0;
		}
}

	
	protected function deleteSpecialproductFilter( $product_id ) {
		$this->db->query( "DELETE FROM `" . DB_PREFIX . "product_to_special_product_filter` WHERE product_id='" . (int)$product_id . "'" );
 	}

	protected function getProductImportExportOptions($category_id) {
		// get default language id
		$language_id = $this->getDefaultLanguageId();

		$sql  = "SELECT p.product_id, po.option_id, po.value AS option_value, po.required, od.name AS `option` FROM ";
		$sql .= "( SELECT pi.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
		$sql .= " FROM `".DB_PREFIX."product` pi";
		$sql .= " LEFT JOIN `".DB_PREFIX."product_to_category` pc ON pi.product_id=pc.product_id WHERE pi.gmc_special_product=1 GROUP BY pi.product_id HAVING categories LIKE '%$category_id%' ";
		$sql .= "  ORDER BY pi.product_id ASC ";
		$sql .= ") AS p ";
		$sql .= "INNER JOIN `".DB_PREFIX."product_option` po ON po.product_id=p.product_id ";
		$sql .= "INNER JOIN `".DB_PREFIX."option_description` od ON od.option_id=po.option_id AND od.language_id='".(int)$language_id."' ";
		$sql .= "ORDER BY p.product_id ASC, po.option_id ASC";
		$query = $this->db->query( $sql );
		return $query->rows;
	}

	protected function getProductOptionValues($category_id) {
		$language_id = $this->getDefaultLanguageId();
		$sql  = "SELECT ";
		$sql .= "  p.product_id, pov.option_id, pov.option_value_id, pov.quantity, pov.subtract, od.name AS `option`, ovd.name AS option_value, ";
		$sql .= "  pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix ";
		$sql .= "FROM ";
		$sql .= "( SELECT pi.product_id, IFNULL(GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ), 0) AS categories ";
		$sql .= "  FROM `".DB_PREFIX."product` pi ";
                $sql .= " LEFT JOIN `".DB_PREFIX."product_to_category` pc ON pi.product_id=pc.product_id WHERE pi.gmc_special_product=1 GROUP BY pi.product_id HAVING categories LIKE '%$category_id%' ";
		$sql .= "  ORDER BY pi.product_id ASC ";
		$sql .= ") AS p ";
		$sql .= "INNER JOIN `".DB_PREFIX."product_option_value` pov ON pov.product_id=p.product_id ";
		$sql .= "INNER JOIN `".DB_PREFIX."option_value_description` ovd ON ovd.option_value_id=pov.option_value_id AND ovd.language_id='".(int)$language_id."' ";
		$sql .= "INNER JOIN `".DB_PREFIX."option_description` od ON od.option_id=ovd.option_id AND od.language_id='".(int)$language_id."' ";
		$sql .= "ORDER BY p.product_id ASC, pov.option_id ASC, pov.option_value_id";
		$query = $this->db->query( $sql );
		return $query->rows;
	}

	protected function populateProductOptionValuesWorksheet( $category_id, &$worksheet, &$price_format, &$box_format, &$weight_format, &$text_format ) {
		// Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_value'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('quantity'),4)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('subtract'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price_prefix'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'),10)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points_prefix'),5)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'),10)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_prefix'),5)+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$styles[$j] = &$text_format;
                $data[$j++] = 'option';
		$styles[$j] = &$text_format;
                $data[$j++] = 'option_value';
		$data[$j++] = 'quantity';
		$data[$j++] = 'subtract';
		$styles[$j] = &$price_format;
		$data[$j++] = 'price';
		$data[$j++] = "price_prefix";
		$data[$j++] = 'points';
		$data[$j++] = "points_prefix";
		$styles[$j] = &$weight_format;
		$data[$j++] = 'weight';
		$data[$j++] = 'weight_prefix';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product option values data
		$i += 1;
		$j = 0;
		$product_option_values = $this->getProductOptionValues($category_id);
		foreach ($product_option_values as $row) {
			$worksheet->getRowDimension($i)->setRowHeight(13);
			$data = array();
			$data[$j++] = $row['product_id'];
			$data[$j++] = html_entity_decode($row['option'],ENT_QUOTES,'UTF-8');
			$data[$j++] = html_entity_decode($row['option_value'],ENT_QUOTES,'UTF-8');
			$data[$j++] = $row['quantity'];
			$data[$j++] = ($row['subtract']==0) ? 'false' : 'true';
			$data[$j++] = $row['price'];
			$data[$j++] = $row['price_prefix'];
			$data[$j++] = $row['points'];
			$data[$j++] = $row['points_prefix'];
			$data[$j++] = $row['weight'];
			$data[$j++] = $row['weight_prefix'];
			$this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
			$i += 1;
			$j = 0;
		}
	}


}
