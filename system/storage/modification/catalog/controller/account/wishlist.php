<?php
class ControllerAccountWishList extends Controller {

            public function addCartToQuote() {
                    $this->load->language('account/wishlist');

                    if ($this->cart->getProducts()) {
                            $this->load->model('account/wishlist');
                        
                            foreach ($this->cart->getProducts() as $product) {
                                    $option = array();
                                
                                    if ($product['option']) {
                                            foreach ($product['option'] as $option_value) {
                                                    $option[$option_value['product_option_id']] = $option_value['product_option_value_id'];
                                            }
                                    }
                                    
                                    $this->model_account_wishlist->addWishlist($product['product_id'], $product['quantity'], json_encode($option));
                            }

                            $this->cart->clear();

                            $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->url->link('account/wishlist'));

                            $this->response->redirect($this->url->link('account/wishlist', '', 'SSL'));
                    } else {
                            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
                    }
            }
            
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			// Remove Wishlist
			
            $this->model_account_wishlist->deleteWishlist($this->request->get['remove'],$this->request->get['option']);
            

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


            $data['action'] = $this->url->link('account/wishlist/edit', '', true);
            
		$data['products'] = array();

		$results = $this->model_account_wishlist->getWishlist();

		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);

			if ($product_info) {

            $parent_id = $this->model_catalog_product->getparent_id($result['product_id']);
            
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
					
            $image = defined('JOURNAL3_ACTIVE') ? $this->model_tool_image->resize('placeholder.png', $this->journal3->settings->get('image_dimensions_wishlist.width'), $this->journal3->settings->get('image_dimensions_wishlist.height')) : false;
            
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}


            $result['options'] = json_decode($result['option'] , TRUE);
            $option_data = $this->model_account_wishlist->getOptionsData($product_info, $result['options']);
            foreach ($option_data as $option_price) {
                    if ($option_price['price_prefix'] === '-') {
                            $product_info['price'] -= $option_price['price'];
                    } else {
                            $product_info['price'] += $option_price['price'];
                    }
            }
            
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					
            'name'       => $this->cart->formatProductName($product_info['name'], $option_data),
            'model'      => ($product_info['special_product'] == 1) ? $this->cart->formatProductName(html_entity_decode($product_info['description']), $option_data, 'description') : $product_info['model'],
            'option'     => $result['option'],    
            
					'stock'      => $stock,

            'classes'        => array(
                defined('JOURNAL3_ACTIVE') ? $this->journal3->productExcludeButton($product_info, $price, $special) : null,
            ),
            
            'quantity'       => $result['quantity'],
            
            
					'price'      => $price,
					'special'    => $special,
					
            'href'       => $this->url->link('product/product', 'product_id=' . $parent_id),
            'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'] .'&option=' . $result['option'])
            
				);
			} else {
				
            $this->model_account_wishlist->deleteWishlist($result['product_id'], $result['option']);
            
			}
		}

		
            $data['continue'] = $this->url->link('account/addtoquote', '', true);
            

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}

	
            public function add() {
                if (!isset($this->request->post['products'])) {
                        if (isset($this->request->post['product_id'], $this->request->post['quantity'])) {
                                $this->request->post['products'][0] = $this->request->post;
                        }
                }
                
		$this->load->language('account/wishlist');

                $array_length = sizeof($this->request->post['products']);
                
                for ($i = 0; $i < $array_length; $i++) {
                        $json = $product = array();

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}

		if (isset($this->request->post['products'][$i]['product_id'])) {
			$product_id = (int)$this->request->post['products'][$i]['product_id'];
		} else {
			$product_id = 0;
		}
            

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {

            if (isset($this->request->post['products'][$i]['quantity']) && ((int)$this->request->post['products'][$i]['quantity'] >= $product_info['minimum'])) {
                    $quantity = (int)$this->request->post['products'][$i]['quantity'];
            } else {
                    $quantity = 1;
            }

            if (isset($this->request->post['products'][$i]['option'])) {
                    $option = array_filter($this->request->post['products'][$i]['option']);
            } else {
                    $option = array();
            }

            $product_options = $this->model_catalog_product->getProductOptions($this->request->post['products'][$i]['product_id']);

            foreach ($product_options as $product_option) {
                    if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                            $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                    }
            }
            

            if (!$json){
            
			if ($this->customer->isLogged()) {
				// Edit customers cart
				$this->load->model('account/wishlist');

				
            $option = json_encode($option);
            $this->model_account_wishlist->addWishlist($this->request->post['products'][$i]['product_id'], $quantity, $option);
            

				
            $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['products'][$i]['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
            

            if (defined('JOURNAL3_ACTIVE')) {
                $json['notification'] = $this->journal3->loadController('journal3/notification/wishlist', array('product_info' => $product_info, 'message' => $json['success']));
                $json['count'] = $this->model_account_wishlist->getTotalWishlist();
            }
            

				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				
            $option = json_encode($option);
            $this->session->data['wishlist'][] = array(
                'product_id'           => $this->request->post['products'][$i]['product_id'],
                'quantity'             => $quantity,
                'option'               => $option
            );
            

				
            

				
            $json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['products'][$i]['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
            

            if (defined('JOURNAL3_ACTIVE')) {
                $json['notification'] = $this->journal3->loadController('journal3/notification/wishlist', array('product_info' => $product_info, 'message' => $json['success']));
                $json['count'] = isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0;
            }
            

				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			}

            }else {
                    $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $product_id));

            if (defined('JOURNAL3_ACTIVE')) {
                $json['options_popup'] = true;
            }
            }
            
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

                    public function edit() {
                            $this->load->language('account/wishlist');

                            // Update
                            if (!empty($this->request->post['quantity'])) {

                                    $this->load->model('account/wishlist');
                                    $this->model_account_wishlist->editWishlist($this->request->post['product_name'], $this->request->post['quantity'], $this->request->post['option']);

                                    $this->session->data['success'] = $this->language->get('text_remove');

                                    $this->response->redirect($this->url->link('account/wishlist'));
                            }

                            $this->response->addHeader('Content-Type: application/json');
                            $this->response->setOutput(json_encode(array()));
                    }
            }
            
