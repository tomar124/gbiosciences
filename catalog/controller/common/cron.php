<?php
class ControllerCommonCron extends Controller {
	public function index() {
		$this->load->model('catalog/cron');
                
                $orders = $this->model_catalog_cron->getOrders();
                
                $this->load->model('checkout/order');
                
                $emailTemplate = $this->model_checkout_order->emailTemplate(16);

                if($emailTemplate){
                    $this->language->load('mail/order');
                    $data['text_product'] = $this->language->get('text_new_product');
                    $data['text_model'] = $this->language->get('text_new_model');
                    $data['text_review_link'] = $this->language->get('text_review_link');
                    
                    foreach ($orders as $order) {
                        $data['order_info'] = $order;
                        $body =  html_entity_decode($emailTemplate['description'], ENT_QUOTES, "UTF-8");
                        $body = str_replace(array('[FIRST-NAME]', '[LAST-NAME]'), array($order['firstname'], $order['lastname']), $body);
                        $marketing_cart_detail = $this->load->view('mail/marketing_cart_detail', $data);
                        $body = str_replace(array('[MARKETING-CART-SECTION]'), array($marketing_cart_detail), $body);
                        $subject = str_replace('[ORDER-ID]', $order['order_id'], $emailTemplate['email_subject']);

                        $mail = new Mail($this->config->get('config_mail_engine'));
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                        $mail->setTo($order['email']);
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($body);
                        $mail->send();
                        
                        $this->model_catalog_cron->notified($order['order_id']);
                    }
                }
                
                exit;
	}

        public function rewards_expiry() {
		$this->load->model('catalog/cron');

                $results = $this->model_catalog_cron->getAllRewardPointsFroCustomer();

                if ($results) {
                        foreach ($results as $result) {
                                if ($result['added_points'] > $result['used_points']) {
                                        $this->model_catalog_cron->addRewardsExiredEntry($result['customer_id'], $result['added_points'] - $result['used_points']);
                                }
                        }
                }

                exit;
	}
}