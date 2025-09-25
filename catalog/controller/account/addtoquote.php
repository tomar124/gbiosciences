<?php
/*** Using dompdf for generating pdf files ***/
require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
/*** Ending dompdf ***/
class ControllerAccountAddtoquote extends Controller {
    private $error = array();

    public function index() {
            $this->document->addScript('catalog/view/javascript/jquery/jquery.form-validator.min.js');

            $this->load->language('account/wishlist');

            $this->load->language('account/addtoquote');
            
            $this->load->model('catalog/product');

	   $this->load->model('tool/image');

            if (!$this->customer->isLogged()) {
                    $this->response->redirect($this->url->link('account/login', '', true));
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

            $data['action_quotes'] = $this->url->link('account/addtoquote/edit', '', true);

            $data['products'] = array();

            $results = $this->model_account_wishlist->getWishlist();

            foreach ($results as $result) {
                    $product_info = $this->model_catalog_product->getProduct($result['product_id']);

                    if ($product_info) {
                                $parent_id = $this->model_catalog_product->getparent_id($result['product_id']);
                                
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
                                        $image = defined('JOURNAL3_ACTIVE') ? $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height')) : false;
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
                                        'quantity'   => $result['quantity'],
                                        'option'     => $option_data,
                                        'options'    => $result['option'],
					'price'      => $price,
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $parent_id),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'] . '&option=' . $result['option'])
				);
			} else {
				$this->model_account_wishlist->deleteWishlist($result['product_id'], $result['option']);
			}
            }

            $this->document->setTitle($this->language->get('heading_title'));

            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');

            $this->load->model('account/customer');

            $this->load->model('account/addtoquote');

            $customer_id = $this->customer->getid();
            $data['customer_id'] = $customer_id;
            $customer_info = $this->model_account_customer->getCustomer($customer_id);
            $customer_address = $this->model_account_customer->getAddress($customer_id);
            
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                    $this->load->model('localisation/country');

                    $data['name'] = $this->request->post['name'];
                    $data['email'] = $this->request->post['email'];
                    $data['telephone'] = $this->request->post['telephone'];
                    $data['company'] = $this->request->post['company'];
                    $data['comment'] = $this->request->post['comment'];
                    
                    $data['country'] = $this->model_localisation_country->getCountry($this->request->post['country_id']); 
                    $last_quote_id = $this->model_account_addtoquote->addQuotes($data);

                    /*** Generating pdf ***/
                    $this->load->model('tool/image');

                    $data['store_url'] = $this->config->get('config_url');
                    $data['store_name'] = $this->config->get('config_name');
                    $data['store_title'] = $this->config->get('config_title');
                    $data['logo'] = 'image/'.$this->config->get('config_logo');
                    
                    $result = $this->load->view('mail/quote', $data, true);

                    $dompdf = new Dompdf();
                    $dompdf->set_option('enable_html5_parser', TRUE);
                    $dompdf->loadHtml($result, 'UTF-8');
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $output = $dompdf->output();
                    $file_to_save = DIR_UPLOAD . $data['name'] . ' Quote Request ' . $last_quote_id . '.pdf';
                    file_put_contents($file_to_save, $output);
                    /*** End Generating Pdf ***/

                    //Emails
                    //Admin Email
                    $emailTemplate = $this->emailTemplate(8);
                    if($emailTemplate){
                            $body =  html_entity_decode($emailTemplate['description'], ENT_QUOTES, "UTF-8");
                            $login_url = $this->url->link('account/link', '', true);
                            $body = str_replace('[FIRST-NAME] [LAST-NAME]', $data['name'], $body);
                            $subject = str_replace('[Quote-ID]', $last_quote_id, $emailTemplate['email_subject']);
                    }else{
                            $body = "Quote has been requested by " . $data['name'];
                            $subject = "New Quote Request Quote_ID " . $last_quote_id;
                    }
	/*	#$log = new Log('errors.log');
		#$log->write($this->config->get('config_email'));
	*/
                    $this->sendEmail('thanh@gbiosciences.com', $subject, $body, array($file_to_save));
                    
                    //Customer Email
                    $emailTemplate = $this->emailTemplate(7);
                    if($emailTemplate){
                            $body =  html_entity_decode($emailTemplate['description'], ENT_QUOTES, "UTF-8");
                            $login_url = $this->url->link('account/login', '', true);
                            $body = str_replace(array('[USER-LOGIN-URL]'), array($login_url), $body);
                            $subject = str_replace('[Quote-ID]', $last_quote_id, $emailTemplate['email_subject']);
                    }else{
                            $subject = "New Quote Request Quote_ID " . $last_quote_id;
                            
                            $body = $this->load->view('mail/quote_customer');
                    }
                    
                    $this->sendEmail($data['email'], $subject, $body);
                    
                    //Distributor Email
                    if ($this->config->get('config_distributor_email')) {
                            $distributor_info = $this->model_account_addtoquote->getDistributorsbycountry($this->request->post['country_id']);
                        
                            if (!empty($distributor_info)) {
                                    foreach ($distributor_info as $distributor) {
                                                if ($distributor['email']) {
                                                        $body = "Please see the attached with respect to a quote request we received through G-Biosciences website <p>Please feel free to reach out to the customer directly.";
                                                        $subject = "New Quote Request At G-Biosciences customer Quote_id " . $last_quote_id;
                                                        $this->sendEmail($distributor['email'], $subject, $body, array($file_to_save));
                                                }
                                    }
                            }
                    }

                    /* ending */
                    unlink(DIR_UPLOAD . $data['name'] . ' Quote Request ' . $last_quote_id . '.pdf');
                    $this->model_account_wishlist->clear();
                    $this->response->redirect($this->url->link('account/quote_success', 'quote_id=' . $last_quote_id));
            }
            
            if (isset($this->error['error_warning'])) {
                    $data['error_warning'] = $this->error['error_warning'];
            } else {
                    $data['error_warning'] = '';
            }
            
            if (isset($this->error['name'])) {
                    $data['error_name'] = $this->error['name'];
            } else {
                    $data['error_name'] = '';
            }

            if (isset($this->error['email'])) {
                    $data['error_email'] = $this->error['email'];
            } else {
                    $data['error_email'] = array();
            }
            
            if (isset($this->error['telephone'])) {
                    $data['error_telephone'] = $this->error['telephone'];
            } else {
                    $data['error_telephone'] = '';
            }

            if (isset($this->error['company'])) {
                    $data['error_company'] = $this->error['company'];
            } else {
                    $data['error_company'] = array();
            }
            
            if (isset($this->error['country_id'])) {
                    $data['error_country'] = $this->error['country_id'];
            } else {
                    $data['error_country'] = '';
            }

            if (isset($this->session->data['success'])) {
                    $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
            } else {
                    $data['success'] = '';
            }

            $data['action'] = $this->url->link('account/addtoquote', '', TRUE);

            $data['back'] = $this->url->link('account/wishlist', '', true);
            
            if (isset($this->request->post['name'])) {
                    $data['name'] = $this->request->post['name'];
            }else if((isset($customer_info['firstname'])) && (isset($customer_info['lastname']))){
                    $data['name'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
            }
            
            if (isset($this->request->post['telephone'])) {
                    $data['telephone'] = $this->request->post['telephone'];
            } else if(isset($customer_info['telephone'])){
                    $data['telephone'] = $customer_info['telephone'];
            }
            
            if (isset($this->request->post['email'])) {
                    $data['email'] = $this->request->post['email'];
            } else if(isset($customer_info['email'])){
                    $data['email'] = $customer_info['email'];
            }
            
            if (isset($this->request->post['company'])) {
                    $data['company'] = $this->request->post['company'];
            } else if(isset($customer_address['company'])){
                    $data['company'] = $customer_address['company'];
            }
            
            if (isset($this->request->post['comment'])) {
                    $data['comment'] = $this->request->post['comment'];
            } else {
                    $data['comment'] = '';
            }
            
            $this->load->model('localisation/country');
            
            if (isset($this->request->post['country_id'])) {
                    $data['country_id'] = $this->request->post['country_id'];
                    $data['country'] = $this->model_localisation_country->getCountry($data['country_id']);
            } else if(isset($customer_address['country_id'])){
                $data['country_id'] = $customer_address['country_id'];
                $data['country'] = $this->model_localisation_country->getCountry($data['country_id']);
            }
            
             $data['countries'] = $this->model_localisation_country->getCountries();

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');
            
            $this->response->setOutput($this->load->view('account/addtoquote', $data));
    }

    public function validate() {
       
            if ((utf8_strlen($this->request->post['name']) < 2) || ((utf8_strlen($this->request->post['name']) > 255))) {
                    $this->error['name'] = $this->language->get('error_name');
            }
            
            if ((utf8_strlen($this->request->post['email']) < 5) || ((utf8_strlen($this->request->post['email']) > 255))) {
                    $this->error['email'] = $this->language->get('error_email');
            }
            
            if ((utf8_strlen($this->request->post['telephone']) < 8) || ((utf8_strlen($this->request->post['telephone']) > 15))) {
                    $this->error['telephone'] = $this->language->get('error_telephone');
            }
            
            if ((utf8_strlen($this->request->post['company']) < 2) || ((utf8_strlen($this->request->post['company']) > 255))) {
                    $this->error['company'] = $this->language->get('error_company');
            }
            
            if ((utf8_strlen($this->request->post['country_id']) < 1) || ((utf8_strlen($this->request->post['country_id']) > 4))) {
                    $this->error['country_id'] = $this->language->get('error_country');
            }
            
            return !$this->error;
    }

    public function edit() {
            $this->load->language('account/wishlist');
            if (!empty($this->request->post['quantity'])) {
                    $this->load->model('account/wishlist');
                    $this->model_account_wishlist->editWishlist($this->request->post['product_name'], $this->request->post['quantity'] ,$this->request->post['option']);

                    $this->session->data['success'] = $this->language->get('text_remove');

                    $this->response->redirect($this->url->link('account/addtoquote'));
            }
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array()));
    }

    public function remove() {
            $this->load->language('account/wishlist');

            // Remove
            if (isset($this->request->post['key'])) {
                    $this->wishlist->remove($this->request->post['key']);

                    $this->session->data['success'] = $this->language->get('text_remove');
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array()));
    }
    
    public function emailTemplate($emailTemplateID){
            $query = $this->db->query("SELECT id, description, email_subject FROM email_template where id=$emailTemplateID and status=1");

            return $query->row;
    }
    
    public function sendEmail($to, $subject, $body, $attachments = array()){
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            
            $mail->setTo($to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setHtml($body);
            
            if ($attachments) {
                    foreach ($attachments as $attachment) {
                            $mail->addAttachment($attachment);
                    }
            }
            
            $mail->send();
    }
}
