<?php
class ControllerAccountQuote extends Controller {
    private $error = array();

    public function index() {
        if (!$this->customer->isLogged()) {
                $this->session->data['redirect'] = $this->url->link('account/quote', '', TRUE);

                $this->response->redirect($this->url->link('account/login', '', TRUE));
        }

        $this->load->language('account/quote');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_account'),
                'href' => $this->url->link('account/account', '', TRUE)
        );

        $url = '';

        if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('account/quote', $url, TRUE)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_empty'] = $this->language->get('text_empty');

        $data['column_quote_id'] = $this->language->get('column_quote_id');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_date_added'] = $this->language->get('column_date_added');

        $data['button_view'] = $this->language->get('button_view');

        if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
        } else {
                $page = 1;
        }

        $data['quotes'] = array();

        $this->load->model('account/quote');
        $this->load->model('catalog/product');

        $quote_total = $this->model_account_quote->getTotalQuotes();

        $results = $this->model_account_quote->getQuotes(($page - 1) * 10, 10);

        foreach ($results as $result) {
                $status = '';

                switch ($result['status']) {
                        case 1: $status = 'Reviewed'; break;
                        case 2: $status = 'Expired'; break;
                        case 3: $status = 'Rejected'; break;
                        default: $status = 'Pending';
                }

                $data['quotes'][] = array(
                        'quote_id' => $result['quote_id'],
                        'status' => $status,
                        'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_on'])),
                        'href' => $this->url->link('account/quote/info', 'quote_id=' . $result['quote_id'], TRUE),
                );
        }

        $pagination = new Pagination();
        $pagination->total = $quote_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('account/quote', 'page={page}', TRUE);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($quote_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($quote_total - 10)) ? $quote_total : ((($page - 1) * 10) + 10), $quote_total, ceil($quote_total / 10));

        $data['continue'] = $this->url->link('account/account', '', TRUE);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['accountmenu'] = $this->load->controller('account/accountmenu');

        $this->response->setOutput($this->load->view('account/quote_list', $data));
       
    }

    public function info() {
            $this->load->language('account/quote');

            if (isset($this->request->get['quote_id'])) {
                    $quote_id = $this->request->get['quote_id'];
            } else {
                    $quote_id = 0;
            }

            if (!$this->customer->isLogged()) {
                    $this->session->data['redirect'] = $this->url->link('account/quote/info', 'quote_id=' . $quote_id, TRUE);

                    $this->response->redirect($this->url->link('account/login', '', TRUE));
            }

            $this->document->setTitle($this->language->get('text_quote'));

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_account'),
                    'href' => $this->url->link('account/account', '', TRUE)
            );

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('account/quote', '', TRUE)
            );

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_quote'),
                    'href' => $this->url->link('account/quote/info', 'quote_id=' . $this->request->get['quote_id'], TRUE)
            );

            $data['back'] = $this->url->link('account/quote', '', TRUE);

            if (isset($this->session->data['error'])) {
                    $data['error_warning'] = $this->session->data['error'];

                    unset($this->session->data['error']);
            } else {
                    $data['error_warning'] = '';
            }

            if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
            } else {
                    $data['success'] = '';
            }

            $this->load->model('account/quote');

            $date90DaysBack = date('Y-m-d', strtotime('-90 days'));
            $this->model_account_quote->updateQuoteStatusToExpired($date90DaysBack, $quote_id);

            $quote_info = $this->model_account_quote->getQuote($quote_id);

            if ($quote_info) {
                    $data['quote_id'] = $this->request->get['quote_id'];
                    $data['date_added'] = date('m/d/Y', strtotime($quote_info['created_on']));            

                    $this->load->model('catalog/product');

                    $data['quote_status'] = $quote_info['status'];
                    $data['validity'] = $quote_info['created_on'];
                    $data['comment'] = $quote_info['comment'];

                    $data['products'] = array();

                    foreach (unserialize($quote_info['quote_data']) as $product) {
                            $product_id = $product['product_id'];
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            
                            $option_data = $this->model_account_wishlist->getOptionsData($product_info, $product['option']);

                            foreach ($product['option'] as $option_price) {
                                    if ($option_price['price_prefix'] === '-') {
                                            $product_info['price'] -= $option_price['price'];
                                    } else {
                                            $product_info['price'] += $option_price['price'];
                                    }
                            }

                            $original_price = str_replace('$', '', $product_info['price']);

                            switch ($quote_info['status']) {
                                    case 1: $data['status'] = 'Reviewed'; break;
                                    case 2: $data['status'] = 'Expired'; break;
                                    case 3: $data['status'] = 'Rejected'; break;
                                    default: $data['status'] = 'Pending';
                            }

                            $price = $this->currency->format($this->tax->calculate($product['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

                            $data['products'][] = array(
                                    'product_id' => $product_id,
                                    'name'       => $this->cart->formatProductName($product_info['name'], $product['option']),
                                    'model'      => ($product_info['special_product'] == 1) ? $this->cart->formatProductName(html_entity_decode($product_info['description']), $product['option'], 'description') : $product_info['model'], 
                                    'quantity' => $product['quantity'],
                                    'price' => $price, 
                                    'option' => !empty($product['option']) ? $product['option'] : array(),
                                    'original_price' => ($original_price > 0) ? $this->currency->format($original_price, $this->session->data['currency']) : 'Quote Requested',
                                    'created_on' => $quote_info['created_on']
                            );
                    }
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('account/quote_info', $data));
    }

    public function requote() {
            $json = array();

            $this->load->language('account/quote');

            if (isset($this->request->get['quote_id'])) {
                    $quote_id = $this->request->get['quote_id'];
            } else {
                    $quote_id = 0;
            }

            $this->load->model('account/quote');

            $quote_info = $this->model_account_quote->getQuote($quote_id);

            if ($quote_info) {
                    $productKeys = array();

                    foreach (unserialize($quote_info['quote_data']) as $product) {
                            $option = array();

                            if (isset($product['option']) && !empty($product['option'])) {
                                    foreach ($product['option'] as $product_option) {
                                            $option[$product_option['product_option_id']] = $product_option['product_option_value_id'];
                                    }
                            }

                            $index = array('product_id' => $product['product_id'], 'option' => $option);
                            
                            $productKeys[base64_encode(serialize($index))] = array(
                                    'quantity' => $product['quantity'],
                                    'price' => $product['price']
                            );
                    }

                    if (isset($this->request->post['quote_product_id'])) {
                            $quote_product_id = $this->request->post['quote_product_id'];
                    } else {
                            $quote_product_id = 0;
                    }

                    if (isset($this->request->post['quantity_entered'])) {
                            $quantity_entered = $this->request->post['quantity_entered'];
                    } else {
                            $quantity_entered = 0;
                    }

                    if (isset($this->request->post['option'])) {
                            $option = array_filter($this->request->post['option']);
                    } else {
                            $option = array();
                    }
                    
                    $selectedProductKey = base64_encode(serialize(array(
                            'product_id' => $quote_product_id,
                            'option' => $option
                    )));

                    if (!array_key_exists($selectedProductKey, $productKeys)) {
                            $json['error'] = sprintf($this->language->get('error_requote'), 'Product');
                    } else {
                            if ($quantity_entered < $productKeys[$selectedProductKey]['quantity']) {
                                    $json['error'] = $this->language->get('text_reject');
                            }
                    }

                    $this->load->model('catalog/product');

                    $product_info = $this->model_catalog_product->getProduct($quote_product_id);

                    if (!$product_info) {
                            $json['error'] = sprintf($this->language->get('error_requote'), 'Product');
                    }

                    if (!$json) {
                            $this->cart->add($quote_product_id, $quantity_entered, $option, 0, json_encode(array(
                                    'price' => $productKeys[$selectedProductKey]['price'],
                                    'quantity' => $productKeys[$selectedProductKey]['quantity']
                            )));
                            
                            $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('checkout/cart'));
                            
                            //Cart total calculation - Totals
                            $this->load->model('setting/extension');

                            $totals = array();
                            $taxes = $this->cart->getTaxes();
                            $total = 0;

                            // Because __call can not keep var references so we put them into an array. 			
                            $total_data = array(
                                    'totals' => &$totals,
                                    'taxes'  => &$taxes,
                                    'total'  => &$total
                            );

                            // Display prices
                            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                                    $sort_order = array();

                                    $results = $this->model_setting_extension->getExtensions('total');

                                    foreach ($results as $key => $value) {
                                            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
                                    }

                                    array_multisort($sort_order, SORT_ASC, $results);

                                    foreach ($results as $result) {
                                            if ($this->config->get('total_' . $result['code'] . '_status')) {
                                                    $this->load->model('extension/total/' . $result['code']);

                                                    // We have to put the totals in an array so that they pass by reference.
                                                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                                            }
                                    }

                                    $sort_order = array();

                                    foreach ($totals as $key => $value) {
                                            $sort_order[$key] = $value['sort_order'];
                                    }

                                    array_multisort($sort_order, SORT_ASC, $totals);
                            }

                            $this->load->language('checkout/cart');

                            $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));

                            if (defined('JOURNAL3_ACTIVE')) {
                                    $json['notification'] = $this->journal3->loadController('journal3/notification/wishlist', array('product_info' => $product_info, 'message' => $json['success']));
                                    $json['count'] = $this->model_account_wishlist->getTotalWishlist();
                                    $json['items_count'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
                                    $json['items_price'] = $this->currency->format($total, $this->session->data['currency']);
                            }

                            unset($this->session->data['shipping_method']);
                            unset($this->session->data['shipping_methods']);
                            unset($this->session->data['payment_method']);
                            unset($this->session->data['payment_methods']);
                    }
            } else {
                    $json['error'] = sprintf($this->language->get('error_requote'), 'Product');
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
    }
}
