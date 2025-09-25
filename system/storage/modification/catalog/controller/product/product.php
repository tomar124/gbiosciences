<?php

            /* * * Using dompdf for generating pdf files ** */
            require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
            use Dompdf\Dompdf;
            /* * * Ending dompdf ** */
            
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {

            if (defined('JOURNAL3_ACTIVE')) {
                $this->journal3->document->addStyle('catalog/view/theme/journal3/lib/imagezoom/imagezoom.min.css');
			    $this->journal3->document->addScript('catalog/view/theme/journal3/lib/imagezoom/jquery.imagezoom.min.js', 'footer');

                $this->journal3->document->addStyle('catalog/view/theme/journal3/lib/lightgallery/css/lightgallery.min.css');
                $this->journal3->document->addStyle('catalog/view/theme/journal3/lib/lightgallery/css/lg-transitions.min.css');
                $this->journal3->document->addScript('catalog/view/theme/journal3/lib/lightgallery/js/lightgallery-all.js', 'footer');

                $this->journal3->document->addStyle('catalog/view/theme/journal3/lib/swiper/swiper.min.css');
			    $this->journal3->document->addScript('catalog/view/theme/journal3/lib/swiper/swiper.min.js', 'footer');
            }
            

            $this->load->language('product/gp_grouped');
            

            $this->load->language('product/gp_grouped');
            $this->load->model('account/customer');
                
            if(isset($this->request->get['rp']) && !empty($this->request->get['rp'])){
                    $customer = $this->model_account_customer->getCustomerByMD5($this->request->get['rp']);

                    if ($customer) {
                        $data['review_customer'] = $customer;
                    }
            }
            
		$this->load->language('product/product');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');


            $this->load->model('journal3/product');
            
		$product_info = $this->model_catalog_product->getProduct($product_id);

		
                    if ($product_info && $product_info['gp_parent_id']) {
                    $url = '';

                    if (isset($this->request->get['path'])) {
                            $url .= '&path=' . $this->request->get['path'];
                    }

                    if (isset($this->request->get['filter'])) {
                            $url .= '&filter=' . $this->request->get['filter'];
                    }

                    if (isset($this->request->get['search'])) {
                            $url .= '&search=' . $this->request->get['search'];
                    }

                    if (isset($this->request->get['tag'])) {
                            $url .= '&tag=' . $this->request->get['tag'];
                    }

                    if (isset($this->request->get['description'])) {
                            $url .= '&description=' . $this->request->get['description'];
                    }

                    if (isset($this->request->get['category_id'])) {
                            $url .= '&category_id=' . $this->request->get['category_id'];
                    }

                    if (isset($this->request->get['sub_category'])) {
                            $url .= '&sub_category=' . $this->request->get['sub_category'];
                    }

                    if (isset($this->request->get['sort'])) {
                            $url .= '&sort=' . $this->request->get['sort'];
                    }

                    if (isset($this->request->get['order'])) {
                            $url .= '&order=' . $this->request->get['order'];
                    }

                    if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];
                    }

                    if (isset($this->request->get['limit'])) {
                            $url .= '&limit=' . $this->request->get['limit'];
                    }
                    $this->response->redirect($this->url->link('product/product', $url . '&product_id=' . $product_info['gp_parent_id']));
            } else if ($product_info) {
            

            if (defined('JOURNAL3_ACTIVE')) {
                $this->load->language('product/compare');

                $data['text_weight'] = $this->language->get('text_weight');
                $data['text_dimension'] = $this->language->get('text_dimension');
                $data['product_quantity'] = $product_info['quantity'];
                $data['product_price_value'] = $product_info['special'] ? $product_info['special'] > 0 : $product_info['price'] > 0;
                $data['product_sku'] = $product_info['sku'];
                $data['product_upc'] = $product_info['upc'];
                $data['product_ean'] = $product_info['ean'];
                $data['product_jan'] = $product_info['jan'];
                $data['product_isbn'] = $product_info['isbn'];
                $data['product_mpn'] = $product_info['mpn'];
                $data['product_location'] = $product_info['location'];
                $data['product_dimension'] = (float)$product_info['length'] || (float)$product_info['width'] || (float)$product_info['height'];
                $data['product_length'] = $this->length->format($product_info['length'], $product_info['length_class_id']);
                $data['product_width'] = $this->length->format($product_info['width'], $product_info['length_class_id']);
                $data['product_height'] = $this->length->format($product_info['height'], $product_info['length_class_id']);
                $data['product_weight'] = (float)$product_info['weight'] ? $this->weight->format($product_info['weight'], $product_info['weight_class_id']) : false;

                $data['product_labels'] = $this->journal3->productLabels($product_info, $product_info['price'], $product_info['special']);
                $data['product_exclude_classes'] = $this->journal3->productExcludeButton($product_info, $product_info['price'], $product_info['special']);
                $data['product_extra_buttons'] = $this->journal3->productExtraButton($product_info, $product_info['price'], $product_info['special']);
                $data['product_blocks'] = array();

                foreach($this->journal3->productBlocks($product_info, $product_info['price'], $product_info['special']) as $module_id => $module_data) {
                    if ($module_data['position'] === 'quickview' && $this->journal3->document->isPopup()) {
                    	if ($block = $this->load->controller('journal3/product_blocks', array('module_id' => $module_id, 'module_type' => 'product_blocks', 'product_info' => $product_info))) {
							$data['product_blocks']['default'][] = $block;
						}
                    } else if ($module_data['position'] === 'quickview_details' && $this->journal3->document->isPopup()) {
                    	if ($block = $this->load->controller('journal3/product_blocks', array('module_id' => $module_id, 'module_type' => 'product_blocks', 'product_info' => $product_info))) {
							$data['product_blocks']['bottom'][] = $block;
						}
                    } else if ($module_data['position'] === 'quickview_image' && $this->journal3->document->isPopup()) {
                    	if ($block = $this->load->controller('journal3/product_blocks', array('module_id' => $module_id, 'module_type' => 'product_blocks', 'product_info' => $product_info))) {
							$data['product_blocks']['image'][] = $block;
						}
                    } else if (!$this->journal3->document->isPopup()) {
                    	if ($block = $this->load->controller('journal3/product_blocks', array('module_id' => $module_id, 'module_type' => 'product_blocks', 'product_info' => $product_info))) {
							$data['product_blocks'][$module_data['position']][] = $block;
						}
                    }
                }

                $product_tabs = array();

                foreach($this->journal3->productTabs($product_info, $product_info['price'], $product_info['special']) as $module_id => $module_data) {
                    if ($module_data['position'] === 'quickview' && $this->journal3->document->isPopup()) {
                    	if ($tab = $this->load->controller('journal3/product_tabs', array('module_id' => $module_id, 'module_type' => 'product_tabs', 'product_info' => $product_info))) {
							$product_tabs['default'][] = $tab;
						}
                    } else if ($module_data['position'] === 'quickview_details' && $this->journal3->document->isPopup()) {
                    	if ($tab = $this->load->controller('journal3/product_tabs', array('module_id' => $module_id, 'module_type' => 'product_tabs', 'product_info' => $product_info))) {
							$product_tabs['bottom'][] = $tab;
						}
                    } else if ($module_data['position'] === 'quickview_image' && $this->journal3->document->isPopup()) {
                    	if ($tab = $this->load->controller('journal3/product_tabs', array('module_id' => $module_id, 'module_type' => 'product_tabs', 'product_info' => $product_info))) {
							$product_tabs['image'][] = $tab;
						}
                    } else if (!$this->journal3->document->isPopup()) {
                    	if ($tab = $this->load->controller('journal3/product_tabs', array('module_id' => $module_id, 'module_type' => 'product_tabs', 'product_info' => $product_info))) {
							$product_tabs[$module_data['position']][] = $tab;
						}
                    }
                }

                foreach ($product_tabs as $position => &$items) {
                    $_items = array();

                    foreach ($items as $item) {
                        $_items[$item['display']][] = $item;
                    }

                    foreach ($_items as $items) {
                        $data['product_blocks'][$position][] = $this->load->controller('journal3/product_tabs/tabs', array('items' => $items, 'position' => $position));
                    }
                }

                $this->load->model('catalog/manufacturer');

                $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

                if ($manufacturer_info && $manufacturer_info['image']) {
                    $data['manufacturer_image'] = $this->model_journal3_image->resize($manufacturer_info['image'], $this->journal3->settings->get('image_dimensions_manufacturer_logo.width'), $this->journal3->settings->get('image_dimensions_manufacturer_logo.height'), $this->journal3->settings->get('image_dimensions_manufacturer_logo.resize'));
                    $data['manufacturer_image2x'] = $this->model_journal3_image->resize($manufacturer_info['image'], $this->journal3->settings->get('image_dimensions_manufacturer_logo.width') * 2, $this->journal3->settings->get('image_dimensions_manufacturer_logo.height') * 2, $this->journal3->settings->get('image_dimensions_manufacturer_logo.resize'));
                } else {
                    $data['manufacturer_image'] = false;
                }

                if ($product_info['special']) {
                    $data['date_end'] = $this->journal3->productCountdown($product_info);
                } else {
                    $data['date_end'] = false;
                }

                if ($this->journal3->document->isPopup()) {
                    $data['view_more_url'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);
                }
            }
            
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);


            $product_category = $this->model_catalog_category->getcategoryofproduct($product_id);
            $paths  = $this->model_catalog_category->getlist($product_category);
            $paths = array_reverse($paths);
            $canonical_link = implode('_', $paths);
            $product_seo = $this->model_catalog_category->getproductseo($product_id);
            $this->document->addLink($this->url->link('product/category', 'path=' . $canonical_link) . '/' . $product_seo, 'canonical');
            
			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			
            //$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
            
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			$data['heading_title'] = $product_info['name'];

			// TMD Question Ans Module XML Code
			$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
				$language_id=$this->config->get('config_language_id');
				$questions_qatab=$this->config->get('questions_qatab');

				if(!empty($questions_qatab[$language_id]['qatab'])){
					$data['tab_questionanswer'] = $questions_qatab[$language_id]['qatab'];
				}else{
					$data['tab_questionanswer'] = $this->language->get('tab_questionanswer');
				}
			}
			// TMD Question Ans Module XML Code
			

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

            $data['download'] = $this->url->link('product/product/download', 'product_id=' . $product_id, true);
            

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];

			$this->load->model('extension/tmdquestionans');
			$data['hasquestions'] = $this->model_extension_tmdquestionans->getQuestionByProductId($data['product_id']);
			$data['hasTotalquestions'] = $data['hasquestions'];
			
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            if (defined('JOURNAL3_ACTIVE')) {
                
            array_unshift($results, array('image' => $product_info['image'],'alt_text' => $product_info['alt_text'],'image_caption' => $product_info['caption']));
            

                foreach ($results as $result) {
				    $data['images'][] = array(
                        'galleryThumb'  => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_popup_thumb.width'), $this->journal3->settings->get('image_dimensions_popup_thumb.height'), $this->journal3->settings->get('image_dimensions_popup_thumb.resize')),
                        'image'         => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_thumb.width'), $this->journal3->settings->get('image_dimensions_thumb.height'), $this->journal3->settings->get('image_dimensions_thumb.resize')),
                        'image2x'       => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_thumb.width') * 2, $this->journal3->settings->get('image_dimensions_thumb.height') * 2, $this->journal3->settings->get('image_dimensions_thumb.resize')),
                        'popup'         => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_popup.width'), $this->journal3->settings->get('image_dimensions_popup.height'), $this->journal3->settings->get('image_dimensions_popup.resize')),
                        'thumb'         => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_additional.width'), $this->journal3->settings->get('image_dimensions_additional.height'), $this->journal3->settings->get('image_dimensions_additional.resize')),
                        
            'thumb2x'       => $this->model_journal3_image->resize($result['image'], $this->journal3->settings->get('image_dimensions_additional.width') * 2, $this->journal3->settings->get('image_dimensions_additional.height') * 2, $this->journal3->settings->get('image_dimensions_additional.resize')),
            'alt_text'      => $result['alt_text'],
            'image_caption' => $result['image_caption']
            
				    );
			    }

			    $results = array();
            }
            

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							
            'image'                   => defined('JOURNAL3_ACTIVE') ? ($option_value['image'] ? $this->model_journal3_image->resize($option_value['image'], $this->journal3->settings->get('image_dimensions_options.width'), $this->journal3->settings->get('image_dimensions_options.height'), $this->journal3->settings->get('image_dimensions_options.resize')) : false) : $this->model_tool_image->resize($option_value['image'], 50, 50),
            
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();

			$results = defined('JOURNAL3_ACTIVE') ? array() : $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			// TMD Question Ans Module XML Code
			$data['tmdquestions_status']=$this->config->get('module_tmdquestions_status');
			$data['tmdquestionans'] = $this->load->controller('extension/tmdquestionans');
			// TMD Question Ans Module XML Code
			

            if (defined('JOURNAL3_ACTIVE')) {
                $this->load->model('journal3/product');
                $this->model_journal3_product->addRecentlyViewedProduct($this->request->get['product_id']);

                $data['products_sold'] = $this->model_journal3_product->getProductsSold($this->request->get['product_id']);
                $data['product_views'] = $product_info['viewed'];
            }
            
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			
            $citation_count = $this->model_catalog_product->getReferencesCount($this->request->get['product_id']);

            $data['citations_count'] = isset($citation_count['count']) && $citation_count['count'] ? sprintf('(%s Citations)', $citation_count['count']) : '';

            $is_gp_grouped = $this->model_catalog_product->getGroupedProductGrouped($this->request->get['product_id']);

            /* Google Remarketing Event Snippet */
            if (!$is_gp_grouped) {
                    $data['gtag_items'] = $this->populateGtagData($product_info, 'SINGLE');
            }

            if ($is_gp_grouped) {
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $tcg_customer_price = true;
                    } else {
                            $tcg_customer_price = false;
                    }

                    $tcg_tax = $this->config->get('config_tax');

                    $this->language->load('product/gp_grouped');

                    $data['text_gp_no_stock'] = $this->language->get('text_gp_no_stock');
                    $data['text_gp_total'] = $this->language->get('text_gp_total');

                    $data['gp_child_info'] = array();
                    if ($this->config->get('gp_grouped_child_info')) {
                            foreach ($this->config->get('gp_grouped_child_info') as $field) {
                                    $data['gp_child_info'][$field] = $this->language->get('text_gp_child_' . $field);
                            }
                    }

                    $data['childs'] = $data['technicals'] = $otherTechnicals = array();

                    $data['pdf_icon'] = $this->model_tool_image->resize('pdf_icon.png', 20, 20);

                    $product_grouped = $this->model_catalog_product->getGroupedProductGroupedChilds($product_id);

                    foreach ($product_grouped as $child) {                                    
                            $child_info = $this->model_catalog_product->getProduct($child['child_id']);

                            if ($child_info) {
                                     //Technical Documents
                                     if($protocol = $this->model_catalog_product->getProtocol($child['child_id'])){
                                         $data['technicals']['protocol'][$child_info['name']] = $protocol;
                                     }

                                     if($sds = $this->model_catalog_product->getSds($child['child_id'])){
                                         $data['technicals']['sds'][$child_info['name']] = $sds;
                                     }

                                     if($coa = $this->model_catalog_product->getCoa($child['child_id'])){
                                         $data['technicals']['coa'][$child_info['name']] = $coa;
                                     }

                                     $otherTechnicals[] = $child['child_id'];

                                     if ($child_info['quantity'] <= 0) {
                                             $child_info['stock'] = $child_info['stock_status'];
                                     } elseif ($this->config->get('config_stock_display')) {
                                             $child_info['stock'] = $child_info['quantity'];
                                     } else {
                                             $child_info['stock'] = $this->language->get('text_instock');
                                     }

                                     if ($tcg_customer_price) {
                                              if ($child_info['price'] > 0) {
                                              $child_price = $this->currency->format($this->tax->calculate($child_info['price'], $child_info['tax_class_id'], $tcg_tax ), $this->session->data['currency']); 
                                              } else {
                                                      $child_price = false;
                                              }
                                     } else {
                                             $child_price = false;
                                     }

                                    if ((float)$child_info['special']) {
                                            $child_special = $this->currency->format($this->tax->calculate($child_info['special'], $child_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                                    } else {
                                            $child_special = false;
                                    }

                                    $child_discounts = $this->model_catalog_product->getProductDiscounts($child_info['product_id']);

                                    $child_discount_details = array();

                                    foreach ($child_discounts as $child_discount) {
                                            $child_discount_details[] = array(
                                                    'quantity' => $child_discount['quantity'],
                                                    'price'    => $this->currency->format($this->tax->calculate($child_discount['price'], $child_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
                                            );
                                    }

                                     // Disable button cart
                                     if ($this->config->get('gp_grouped_child_nocart') && !$this->config->get('config_stock_checkout') && $child_info['quantity'] <= 0) {
                                             $child_child_nocart = true;
                                     } else {
                                             $child_child_nocart = false;
                                     }

                                     $qty_now = '';
                                     foreach ($this->cart->getProducts() as $gp_cart) {
                                             if ($child['child_id'] == $gp_cart['product_id']) {
                                                     $qty_now = $gp_cart['quantity'];
                                             }
                                     }

                                     $data['childs'][$child_info['product_id']] = array(
                                             'child_id'   => $child_info['product_id'],
                                             'info'       => $child_info,
                                             'name'       => str_replace($product_info['name'], '', $child_info['name']),
                                             'price'      => $child_price,
                                             'special'    => $child_special,
                                             'discounts'  => $child_discount_details,
                                             'nocart'     => $child_child_nocart,
                                             'qty_now'    => $qty_now,
                                     );
                            }
                        }

            /* Google Remarketing Event Snippet */
            $data['gtag_items'] = $this->populateGtagData($data['childs'], 'MULTIPLE');

            //Technical Documents
            if($otherTechnicals && $technical = $this->model_catalog_product->getOtherTechnical($otherTechnicals)){
                    $data['technicals']['technical'] = $technical;
            }


            /*** Structured Data ***/
            $data['ld_json_products'] = $this->getStructuredDataJSCode($data, 'GroupProduct');
            /*** Structured Data ***/
            
            $this->response->setOutput($this->load->view('product/gp_grouped_default', $data));
            } else {
                    //Technical Documents
                    $data['technicals'] = array();

                    $data['pdf_icon'] = $this->model_tool_image->resize('pdf_icon.png', 20, 20);

                    if($protocol = $this->model_catalog_product->getProtocol($product_id)){
                        $data['technicals']['protocol'] = $protocol;
                    }

                    if($sds = $this->model_catalog_product->getSds($product_id)){
                        $data['technicals']['sds'] = $sds;
                    }

                   $attribute_groups = $data['attribute_groups'];

                   foreach($attribute_groups as $attribute_group_key => $attribute_group){
                           foreach($attribute_group['attribute'] as $attribute_key => $attribute){
                                   $attribute_groups[$attribute_group_key]['attribute'][$attribute_key]['text'] = html_entity_decode($attribute['text'], ENT_QUOTES, 'UTF-8');
                           }
                   }

                   $data['attribute_groups'] = $attribute_groups;

                   if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                           $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                   } else {
                           $data['price'] = false;
                   }

                   if ((float)$product_info['special']) {
                           $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                   } else {
                           $data['special'] = false;
                   }

            /*** Structured Data ***/
            $data['price'] = $product_info['price'];
            $data['weight'] = $product_info['weight'];
            $data['weight_class_id'] = $product_info['weight_class_id'];
            $data['ld_json_products'] = $this->getStructuredDataJSCode($data, 'Product');
            /*** Structured Data ***/
            
            $this->response->setOutput($this->load->view('product/product', $data));
            }
            
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		
            $this->response->setOutput(json_encode($json));
            }

            /*** Structured Data ***/
            function getStructuredDataJSCode($data, $type){
                    $response = $responseData = array();

                    switch($type){
                            case 'Product' :
                                    $response = array(
                                        "@context" => "http://schema.org/",
                                        "@type" => "Product",
                                        "name" => $data['heading_title'],
                                        "model" => $data['model'],
                                        "image" => $data['popup'],
                                        "logo" => $data['popup'],
                                        "description" => $data['description'],
                                        "url" => $data['breadcrumbs'][count($data['breadcrumbs']) - 1]['href'],
                                        "brand" => array(
                                                "@type" => "Thing",
                                                "name" => $this->config->get('config_name')
                                        ),
                                    );

                                    if ($data['weight'] > 0) {
                                            $response["weight"] = array(
                                                    "@type" => "QuantitativeValue",
                                                    "value" => $data['weight'],
                                                    "unitText" => $this->weight->getUnit($data['weight_class_id'])
                                            );
                                    }

                                    $this->load->model('catalog/review');
                                    $review_results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id']);

                                    if (count($review_results) > 0) {
                                            $response["aggregateRating"] = array(
                                                    "@type" => "AggregateRating",
                                                    "ratingCount" => count($review_results),
                                                    "reviewCount" => count($review_results),
                                                    "ratingValue" => max(array_column($review_results, 'rating')),
                                                    "bestRating" => max(array_column($review_results, 'rating')),
                                                    "worstRating" => min(array_column($review_results, 'rating'))
                                            );
                                    }

                                    $special_price = trim($data['special'], '$');
                                    if ((float)$special_price) {
                                            $data['price'] = $special_price;
                                    }

                                    if ($data['price']) {
                                            $response["offers"] = array(
                                                    "@type" => "Offer",
                                                    "priceCurrency" => "USD",
                                                    "price" => $data['price'],
                                                    "availability" => "http://schema.org/InStock"
                                            );
                                    }

                                    return $response;
                            break;
                            case 'GroupProduct' :
                                    $count = 1;
                                    foreach ($data['childs'] as $child) {
                                            if ($child['info']['price'] > 0) {
                                                    $price = $child['info']['price'];
                                                    $special_price = trim($child['info']['special'], '$');

                                                    if ((float)$special_price) {
                                                            $price = $special_price;
                                                    }

                                                    $item = array(
                                                            "@type" => "Product",
                                                            "name" => $child['info']['name'],
                                                            "model" => $child['info']['model'],
                                                            "image" => $data['popup'],
                                                            "logo" => $data['popup'],
                                                            "description" => $data['description'],
                                                            "url" => $data['breadcrumbs'][count($data['breadcrumbs']) - 1]['href'] . '#' . $child['info']['name'],
                                                            "brand" => array(
                                                                    "@type" => "Thing",
                                                                    "name" => $this->config->get('config_name')
                                                            ),
                                                            "offers" => array(
                                                                    "@type" => "Offer",
                                                                    "priceCurrency" => "USD",
                                                                    "price" => $price,
                                                                    "availability" => "http://schema.org/InStock"
                                                            )
                                                    );

                                                    if ($child['info']['weight'] > 0) {
                                                            $item["weight"] = array(
                                                                    "@type" => "QuantitativeValue",
                                                                    "value" => $child['info']['weight'],
                                                                    "unitText" => $this->weight->getUnit($child['info']['weight_class_id'])
                                                            );
                                                    }

                                                    $this->load->model('catalog/review');
                                                    $review_results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id']);

                                                    if (count($review_results) > 0) {
                                                            $item["aggregateRating"] = array(
                                                                    "@type" => "AggregateRating",
                                                                    "ratingCount" => count($review_results),
                                                                    "reviewCount" => count($review_results),
                                                                    "ratingValue" => max(array_column($review_results, 'rating')),
                                                                    "bestRating" => max(array_column($review_results, 'rating')),
                                                                    "worstRating" => min(array_column($review_results, 'rating'))
                                                            );
                                                    }

                                                    $responseData[] = array(
                                                            "@type" => "ListItem",
                                                            "position" => $count++,
                                                            "item" => $item
                                                    );
                                            }
                                    }

                                    if ($responseData) {
                                            $response = array(
                                                    "@context" => "http://schema.org/",
                                                    "@type" => "ItemList",
                                                    "itemListElement" => $responseData
                                            );
                                    }

                                    return $response;
                            break;
                    }

                    return array();
            }
            /*** Structured Data ***/
            
            public function download() {
                    $this->load->language('product/product');

                    if (isset($this->request->get['product_id'])) {
                            $product_id = (int)$this->request->get['product_id'];
                    } else {
                            $product_id = 0;
                    }

                    $this->load->model('catalog/product');

                    $product_info = $this->model_catalog_product->getProduct($product_id);

                    if ($product_info) {
                            $product_info['options'] = array();

                            $product_options = $this->model_catalog_product->getProductOptions($product_id);

                            foreach ($product_options as $product_option) {                                
                                    $product_info['options'][$product_option['name']] = [];
                                    foreach($product_option['product_option_value'] as $product_option_value){
                                            $option_name = strstr($product_option_value['name'], '(', TRUE);
                                            $text = trim(($option_name != FALSE) ? $option_name : $product_option_value['name']);
                                            $product_info['options'][$product_option['name']][] = $text;
                                    }
                                    $product_info['options'][$product_option['name']] = implode(', ', $product_info['options'][$product_option['name']]);
                            }

                            $product_info['attributes'] = $this->model_catalog_product->getProductAttributes($product_id);
                            $product_info['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
                            $product_info['store_url'] = $this->config->get('config_url');
                            $product_info['store_name'] = $this->config->get('config_name');
                            $product_info['store_title'] = $this->config->get('config_title');
                            $product_info['logo'] = DIR_IMAGE . $this->config->get('config_logo');

                            $html = $this->load->view('default/template/mail/product', $product_info);

                            $dompdf = new Dompdf();
                            $dompdf->set_option('enable_html5_parser', TRUE);
                            $dompdf->loadHtml($html, 'UTF-8');
                            $dompdf->setPaper('A4', 'portrait');
                            $dompdf->render();
                            $dompdf->stream($product_info['name'], array("Attachment" => TRUE));

                            $dompdf->output();
                    } else {
                            $this->response->redirect($this->url->link('product/product', 'product_id=' . $product_id));
                    }
            }

            /* Google Remarketing Event Snippet */
            function populateGtagData ($products, $set_type) {
                    $gtag_items = array(
                            'total_value' => 0,
                            'items' => array()
                    );

                    switch ($set_type) {
                        case 'SINGLE':
                                $gtag_items['total_value'] += $products['price'] > 0 ? $products['price'] : 0;
                                $gtag_items['items'][] = array(
                                    'id' => $products['product_id'],
                                    'google_business_vertical'=> 'retail'
                                );
                                break;
                        case 'MULTIPLE':
                                foreach ($products as $product) {
                                        $gtag_items['total_value'] += $product['price'] > 0 ? $product['price'] : 0;
                                        $gtag_items['items'][] = array(
                                            'id' => $product['child_id'],
                                            'google_business_vertical'=> 'retail'
                                        );
                                }
                                break;
                    }

                    return $gtag_items;
            }
            
}
