<?php

            ini_set('max_execution_time', -1);
            ini_set('memory_limit', '4096M');
            
class ControllerExtensionFeedGoogleBase extends Controller {
	public function index() {
		if ($this->config->get('feed_google_base_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8" ?>';
			$output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
			$output .= '  <channel>';
			$output .= '  <title>' . $this->config->get('config_name') . '</title>';
			$output .= '  <description>' . $this->config->get('config_meta_description') . '</description>';
			$output .= '  <link>' . $this->config->get('config_url') . '</link>';

			$this->load->model('extension/feed/google_base');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			$product_data = array();

			$google_base_categories = $this->model_extension_feed_google_base->getCategories();

			foreach ($google_base_categories as $google_base_category) {
				$filter_data = array(
					'filter_category_id' => $google_base_category['category_id'],
					'filter_filter'      => false
				);

				$products = $this->model_catalog_product->getProducts($filter_data);

				foreach ($products as $product) {

            $product_id = $product['product_id'];

            if (!$product['special_product']) {
                    $grouped_product = $this->model_catalog_product->getGroupedProductGrouped($product_id);

                    if ($grouped_product) {
                            continue;
                    } else {
                            $product_id = $this->model_catalog_product->getparent_id($product_id);
                            $parent_product_info = $this->model_catalog_product->getProduct($product_id);

                            if ($parent_product_info) {
                                    $id = $product['name'];
                                    $product['name'] = substr($parent_product_info['name'], 0, 150);
                                    $product['description'] = substr(strip_tags(html_entity_decode($parent_product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 5000);
                                    $product['image'] = $parent_product_info['image'];
                            } else {
                                    continue;
                            }
                    }
            } else {
                $id = $product['product_id'];
            }
            
					if (!in_array($product['product_id'], $product_data) && $product['description']) {
						
						$product_data[] = $product['product_id'];
						
						$output .= '<item>';
						$output .= '<title><![CDATA[' . substr($product['name'], 0, 150) . ']]></title>';
						$output .= '<link>' . $this->url->link('product/product', 'product_id=' . $product_id) . '</link>';
						$output .= '<description><![CDATA[' . substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, 5000) . ']]></description>';
						$output .= '<g:brand><![CDATA[' . $this->config->get('config_name') . ']]></g:brand>';
						$output .= '<g:condition>new</g:condition>';
						$output .= '<g:id>' . $product['product_id'] . '</g:id>';

						if ($product['image']) {
							$output .= '  <g:image_link>' . $this->model_tool_image->resize($product['image'], 500, 500) . '</g:image_link>';
						} else {
							$output .= '  <g:image_link></g:image_link>';
						}

						

						if ($product['mpn']) {
							$output .= '  <g:mpn><![CDATA[' . $product['mpn'] . ']]></g:mpn>' ;
						} else {
							$output .= '  <g:identifier_exists>false</g:identifier_exists>';
						}

						if ($product['upc']) {
							$output .= '  <g:upc>' . $product['upc'] . '</g:upc>';
						}

						if ($product['ean']) {
							$output .= '  <g:ean>' . $product['ean'] . '</g:ean>';
						}

						$currencies = array(
							'USD',
							'EUR',
							'GBP'
						);

						if (in_array($this->session->data['currency'], $currencies)) {
							$currency_code = $this->session->data['currency'];
							$currency_value = $this->currency->getValue($this->session->data['currency']);
						} else {
							$currency_code = 'USD';
							$currency_value = $this->currency->getValue('USD');
						}

						
            if ((float)$product['special']) {
                    $price = number_format($this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id']), $currency_code, $currency_value, false), 2);
            } else {
                    $price = number_format($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']), $currency_code, $currency_value, false), 2);
            }

            $output .= '  <g:price>' . $price . ' ' . $currency_code . '</g:price>';
            $output .= '  <g:shipping><g:country>US</g:country><g:price>50.00 USD</g:price></g:shipping>';
            

						$output .= '  <g:google_product_category>' . $google_base_category['google_base_category_id'] . '</g:google_product_category>';

						$categories = $this->model_catalog_product->getCategories($product_id);

						
            $category = $categories ? $categories[count($categories) - 1] : [];

            if ($category) {
                    $path = $this->getPath($category['category_id']);
            
							$path = $this->getPath($category['category_id']);

							if ($path) {
								$string = '';

								foreach (explode('_', $path) as $path_id) {
									$category_info = $this->model_catalog_category->getCategory($path_id);

									if ($category_info) {
										if (!$string) {
											$string = $category_info['name'];
										} else {
											$string .= ' &gt; ' . $category_info['name'];
										}
									}
								}

								$output .= '<g:product_type><![CDATA[' . $string . ']]></g:product_type>';
							}
						}

						
            if ((float)$product['weight'] > 0) {
                    $weightUnit = $this->weight->getUnit($product['weight_class_id']);
                    $output .= '  <g:shipping_weight>' . number_format($product['weight'], 2) . ' ' . $weightUnit . '</g:shipping_weight>';
            }
            
            if ((float)$product['length'] > 0 || (float)$product['width'] > 0 || (float)$product['height'] > 0) {
                    $lengthUnit = $this->length->getUnit($product['length_class_id']);
            }
            
            if ((float)$product['length'] > 0) {
                    $output .= '  <g:shipping_length>' . number_format($product['length'], 2) . ' ' . $lengthUnit . '</g:shipping_length>';
            }
            
            if ((float)$product['width'] > 0) {
                    $output .= '  <g:shipping_width>' . number_format($product['width'], 2) . ' ' . $lengthUnit . '</g:shipping_width>';
            }
            
            if ((float)$product['height'] > 0) {
                    $output .= '  <g:shipping_height>' . number_format($product['height'], 2) . ' ' . $lengthUnit . '</g:shipping_height>';
            }
            
						$output .= '  <g:availability><![CDATA[in_stock]]></g:availability>';
						$output .= '</item>';
					}
				}
			}

			$output .= '  </channel>';
			$output .= '</rss>';

			$this->response->addHeader('Content-Type: application/rss+xml');
			$this->response->setOutput($output);
		}
	}

	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getPath($category_info['parent_id'], $new_path);

			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}
}
