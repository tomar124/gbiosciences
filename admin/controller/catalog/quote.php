<?php
/* * * Using dompdf for generating pdf files ** */
require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
/* * * Ending dompdf ** */
class ControllerCatalogQuote extends Controller {
    private $error = array();

    public function index() {
            $this->load->language('catalog/quote');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->load->model('catalog/quote');
            $this->load->model('sale/customer');
            $this->load->model('localisation/country');

            $this->getList();
    }

    public function edit() {
            $this->load->language('catalog/quote');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->load->model('catalog/product');
            $this->load->model('catalog/quote');
            $this->load->model('sale/customer');
            $this->load->model('localisation/country');

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    $this->model_catalog_quote->editQuote($this->request->get['quote_id'], $this->request->post);
                    
                    if ($this->request->post['status'] == 1 || $this->request->post['status'] == 3) {
                            $quote_details = $this->model_catalog_quote->getQuote($this->request->get['quote_id']);
                            $country = $this->model_localisation_country->getCountry($quote_details['country_id']);
                            $customer_details = $this->model_sale_customer->getCustomer($quote_details['customer_id']);

                            $pdfData = array(
                                'name' => $customer_details['firstname'] . ' ' . $customer_details['lastname'],
                                'telephone' => $customer_details['telephone'],
                                'email' => $customer_details['email'],
                                'country' => $country['name'],
                                'comment' => $quote_details['comment'],
                                'store_url' => HTTP_CATALOG,
                                'store_name' => $this->config->get('config_name'),
                                'store_email' => $this->config->get('config_email'),
                                'store_telephone' => $this->config->get('config_telephone'),
                                'store_title' => $this->config->get('config_title'),
                                'logo' => DIR_IMAGE . '' . $this->config->get('config_logo'),
                                'quote_date' => $quote_details['created_on'],
                                'products' => array()
                            );

                            if (isset($this->request->post['product']) && !empty($this->request->post['product'])) {
                                    foreach ($this->request->post['product'] as $product) {
                                            $product_info = $this->model_catalog_quote->getProduct($product['product_id']);
                                            $list_price = $product_info['price'];

                                            $options = array();
                                            
                                            if (isset($product['option']) && !empty($product['option'])) {
                                                    foreach ($product['option'] as $option) {
                                                            $options[$option['product_option_id']] = $option['product_option_value_id'];
                                                    }
                                                    
                                                 //  $this->load('catalog/quote');
                                                  // $cart = new Cart($this->registry);
                                                    $option_data = $this->cart->getProductOptionPrice($product_info['product_id'], $options);

                                                    if (isset($option_data['option_price'])) {
                                                            $list_price += $option_data['option_price'];
                                                    }
                                            }
                                            
                                            $pdfData['products'][] = array(
                                                'quote_id' => $quote_details['quote_id'],
                                                'name' => isset($product['option']) && is_array($product['option']) ? $this->formatProductName($product_info['name'], (isset($product['option']) ? $product['option'] : array())): $product_info['name'],
                                                'model' => ($product_info['special_product'] == 1) ? $this->formatProductName(html_entity_decode($product_info['description']), (isset($product['option']) ? $product['option'] : array()), 'description') : html_entity_decode($product_info['model']),
                                                'quantity' => $product['quantity'],
                                                'list_price' => ($list_price > 0) ? $this->currency->format($list_price, $this->config->get('config_currency')) : 'Quote Requested',
                                                'quoted_price' => $this->currency->format($product['price'], $this->config->get('config_currency'))
                                            );
                                    }
                            }

                            /*** Generating Pdf ***/
                            $dompdf = new Dompdf();
                            $dompdf->set_option('enable_html5_parser', TRUE);
                            $dompdf->loadHtml($this->load->view('mail/quote_pdf', $pdfData), 'UTF-8');
                            $dompdf->setPaper('A4', 'portrait');
                            $dompdf->render();
                            $output = $dompdf->output();
                            $file_to_save = DIR_UPLOAD . $pdfData['name'] . ' Quote Request ' . $this->request->get['quote_id'] . '.pdf';
                            file_put_contents($file_to_save, $output);

                            $emailTemplate = $this->emailTemplate(9);
                            if ($emailTemplate) {
                                    $html =  html_entity_decode($emailTemplate['description'], ENT_QUOTES, "UTF-8");
                                    $subject = str_replace('[Quote-ID]', $this->request->get['quote_id'], $emailTemplate['email_subject']);
                            } else {
                                    $html = $this->load->view('mail/quote', $pdfData);
                                    $subject = "Quote Request Updated";
                            }

                            $mail = new Mail($this->config->get('config_mail_engine'));
                            $mail->parameter = $this->config->get('config_mail_parameter');
                            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                            $mail->setTo($this->request->post['email']);
                            $mail->setFrom($this->config->get('config_email'));
                            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                            $mail->setSubject($subject);
                            $mail->setHtml($html);
                            $mail->addAttachment($file_to_save);
                            $mail->send();

                            unlink($file_to_save);
                    }

                    $this->session->data['success'] = $this->language->get('text_success');

                    $url = '';

                    if (isset($this->request->get['filter_country'])) {
                            $url .= '&filter_country=' . $this->request->get['filter_country'];
                    }

                    if (isset($this->request->get['filter_status'])) {
                            $url .= '&filter_status=' . $this->request->get['filter_status'];
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

                    $this->response->redirect($this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
            }
            
            $this->getForm();
    }

    protected function getList() {
            $this->updateQuoteStatusToExpired();

            if (isset($this->request->get['filter_country'])) {
                    $filter_country = $this->request->get['filter_country'];
            } else {
                    $filter_country = null;
            }

            if (isset($this->request->get['filter_status'])) {
                    $filter_status = $this->request->get['filter_status'];
            } else {
                    $filter_status = null;
            }

            if (isset($this->request->get['sort'])) {
                    $sort = $this->request->get['sort'];
            } else {
                    $sort = 'q.quote_id';
            }

            if (isset($this->request->get['order'])) {
                    $order = $this->request->get['order'];
            } else {
                    $order = 'DESC';
            }

            if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
            } else {
                    $page = 1;
            }

            $url = '';

            if (isset($this->request->get['filter_country'])) {
                    $url .= '&filter_country=' . $this->request->get['filter_country'];
            }

            if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
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

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
            );

            $data['quotes'] = array();

            $filter_data = array(
                'filter_country' => $filter_country,
                'filter_status' => $filter_status,
                'sort' => $sort,
                'order' => $order,
                'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                'limit' => $this->config->get('config_limit_admin')
            );

            $quote_total = $this->model_catalog_quote->getTotalQuotes($filter_data);

            $results = $this->model_catalog_quote->getQuotes($filter_data);

            foreach ($results as $result) {
                    $quote_info = $this->model_catalog_quote->getQuote($result['quote_id']);

                    $quote_price = 0;

                    foreach (unserialize($quote_info['quote_data']) as $product) {
                            $quote_price = $quote_price + $product['quantity'] * trim(str_replace(',', '', $product['price']), "$");
                    }
                    
                    $data['quotes'][] = array(
                        'quote_id' => $result['quote_id'],
                        'quote_price' => $this->currency->format($quote_price, $this->config->get('config_currency')),
                        'name' => $result['name'],
                        'country' => $result['country'],
                        'status' => $result['status'],
                        'date_added' => date('m-d-Y', strtotime($result['created_on'])),
                        'date_expired' => date('m-d-Y', strtotime($result['created_on'] . ' +90 days')),
                        'edit' => $this->url->link('catalog/quote/edit', 'user_token=' . $this->session->data['user_token'] . '&quote_id=' . $result['quote_id'] . $url, TRUE)
                    );
            }

            $data['user_token'] = $this->session->data['user_token'];

            if (isset($this->error['warning'])) {
                    $data['error_warning'] = $this->error['warning'];
            } else {
                    $data['error_warning'] = '';
            }

            if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
            } else {
                    $data['success'] = '';
            }

            if (isset($this->request->post['selected'])) {
                    $data['selected'] = (array) $this->request->post['selected'];
            } else {
                    $data['selected'] = array();
            }

            $url = '';

            if (isset($this->request->get['filter_country'])) {
                    $url .= '&filter_country=' . $this->request->get['filter_country'];
            }

            if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if ($order == 'ASC') {
                    $url .= '&order=DESC';
            } else {
                    $url .= '&order=ASC';
            }

            if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];
            }

            $data['countries'] = $this->model_localisation_country->getCountries();

            $data['sort_quote_id'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . '&sort=q.quote_id' . $url, TRUE);
            $data['sort_name'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, TRUE);
            $data['sort_country'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . '&sort=co.name' . $url, TRUE);
            $data['sort_status'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . '&sort=q.status' . $url, TRUE);
            $data['sort_added_on'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . '&sort=q.created_on' . $url, TRUE);

            $url = '';

            if (isset($this->request->get['filter_country'])) {
                    $url .= '&filter_country=' . $this->request->get['filter_country'];
            }

            if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
            }

            $pagination = new Pagination();
            $pagination->total = $quote_total;
            $pagination->page = $page;
            $pagination->limit = $this->config->get('config_limit_admin');
            $pagination->url = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', TRUE);

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($quote_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($quote_total - $this->config->get('config_limit_admin'))) ? $quote_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $quote_total, ceil($quote_total / $this->config->get('config_limit_admin')));

            $data['filter_country'] = $filter_country;
            $data['filter_status'] = $filter_status;

            $data['sort'] = $sort;
            $data['order'] = $order;

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('catalog/quote_list', $data));
    }

    protected function getForm() {
            $this->load->model('catalog/quote');
            $this->load->model('sale/customer');
            $this->load->model('localisation/country');

            $data['user_token'] = $this->session->data['user_token'];

            $quote_info = $this->model_catalog_quote->getQuote($this->request->get['quote_id']);
            
            if (isset($this->request->post['status'])) {
                    $data['status'] = $this->request->post['status'];
            } elseif (!empty($quote_info)) {
                    $data['status'] = $quote_info['status'];
            } else {
                    $data['status'] = 0;
            }
            
            if (isset($this->request->post['comment'])) {
                    $data['comment'] = $this->request->post['comment'];
            } elseif (!empty($quote_info)) {
                    $data['comment'] = $quote_info['comment'];
            } else {
                    $data['comment'] = 0;
            }
            
            if ($quote_info) {
                    $quote_id = $quote_info['quote_id'];

                    $data['quote_id'] = $quote_id;
                    $data['customer_id'] = $quote_info['customer_id'];
                    $data['customer_name'] = $quote_info['customer_name'];
                    $data['customer_email'] = $quote_info['customer_email'];
                    $data['quote_location'] = $quote_info['country'];
                    $data['customer_address'] = $this->model_sale_customer->getAddress($quote_info['customer_id']);
                    $data['customer_location'] = $this->model_localisation_country->getCountry($data['customer_address']['country_id']);

                    $data['quotes'] = array();

                    $products = unserialize($quote_info['quote_data']);
                    if (is_array($products) || is_object($products)) {
                        
                        foreach ($products as $product) {
                            $product_id = $product['product_id'];
                            $product_info = $this->model_catalog_quote->getProduct($product_id);
                            
                            $data['quotes'][] = array(
                                'quote_id' => $quote_info['quote_id'],
                                'product_id' => $product_id,
                                'name' => isset($product['option']) && is_array($product['option']) ? $this->formatProductName($product_info['name'],  (isset($product['option']) ? $product['option'] : array())): $product_info['name'],
                                'option' => isset($product['option']) && is_array($product['option']) ? $product['option'] : array(),
                                'model' => ($product_info['special_product'] == 1) ? $this->formatProductName(html_entity_decode($product_info['description']), (isset($product['option']) ? $product['option'] : array()), 'description') : html_entity_decode($product_info['model']),
                                'quantity' => $product['quantity'],
                                'price' => trim(str_replace(',', '', $product['price']), "$"),
                                'href' => $this->url->link('catalog/' . (!$product_info['special_product'] ? 'product' : 'special_product') . '/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id),
                                'special_price' => $this->model_catalog_quote->getProductSpecial($product['product_id'])
                            );
                    }
                   }
                    
            } else {
                $data['quote_id'] = 0;
            }

            $url = '';

            if (isset($this->request->get['filter_country'])) {
                    $url .= '&filter_country=' . $this->request->get['filter_country'];
            }

            if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . $this->request->get['filter_status'];
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

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
            );

            $data['action'] = $this->url->link('catalog/quote/edit', 'user_token=' . $this->session->data['user_token'] . '&quote_id=' . $this->request->get['quote_id'] . $url, TRUE);
            $data['remove'] = $this->url->link('catalog/quote/remove', 'user_token=' . $this->session->data['user_token'], TRUE);
            $data['cancel'] = $this->url->link('catalog/quote', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
            $data['quote_id'] = $this->request->get['quote_id'];
            $data['user_token'] = $this->session->data['user_token'];

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('catalog/quote_form', $data));
    }

    protected function validateForm() {
            if (!$this->user->hasPermission('modify', 'catalog/quote')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }

            return !$this->error;
    }

    public function remove($key) {
            $this->data = array();

            unset($this->session->data['cart'][$key]);
    }

    public function updateQuoteStatusToExpired() {
            $this->load->model('catalog/quote');
            $date90DaysBack = date('Y-m-d', strtotime('-90 days'));
            $this->model_catalog_quote->updateQuoteStatusToExpired($date90DaysBack);
    }
    
    public function emailTemplate($emailTemplateID){
            $query = $this->db->query("SELECT id, description, email_subject FROM email_template where id=$emailTemplateID and status=1");

            return $query->row;
    }
        
    public function formatProductName($product, $options, $type = ''){
        $response = array();
        $response[] = $product;
        
        foreach ($options as $option) {
                if ($option['type'] != 'file') {
                        switch($type){
                            case 'description': 
                                $value = strstr($option['value'], '(', TRUE);
                                $response[] = trim(($value != FALSE) ? $value : $option['value']);
                                break;
                            default :
                                $option_name = explode('(', $option['value']);
                                if(count($option_name) > 1){
                                    $response[] = explode(')', $option_name[1])[0];
                                }else{
                                    $response[] = $option['value'];
                                }
                        }
                }
        }

        switch($type){
            case 'description': 
                return implode(', ', $response);
            default :
                return implode('-', $response);
        }
    }

    public function history() {
            $this->load->language('catalog/quote');

            if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
            } else {
                    $page = 1;
            }

            $data['histories'] = array();

            $this->load->model('catalog/quote');

            $results = $this->model_catalog_quote->getQuoteHistories($this->request->get['quote_id'], ($page - 1) * 10, 10);

            foreach ($results as $result) {
                    $data['histories'][] = array(
                            'status'     => $result['status'],
                            'comment'    => nl2br($result['comment']),
                            'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
                    );
            }

            $history_total = $this->model_catalog_quote->getTotalQuoteHistories($this->request->get['quote_id']);

            $pagination = new Pagination();
            $pagination->total = $history_total;
            $pagination->page = $page;
            $pagination->limit = 10;
            $pagination->url = $this->url->link('catalog/quote/history', 'user_token=' . $this->session->data['user_token'] . '&quote_id=' . $this->request->get['quote_id'] . '&page={page}', true);

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

            $this->response->setOutput($this->load->view('catalog/quote_history', $data));
    }
}
