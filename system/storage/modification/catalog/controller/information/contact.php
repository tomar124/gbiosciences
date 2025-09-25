<?php
class ControllerInformationContact extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('information/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->load->model('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            $subject = html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name'], $this->request->post['user_telephone'], $country_info['name']), ENT_QUOTES, 'UTF-8');

            $message  = "New Enquiry By: \n";
            $message .= "Name: " . html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "Email: " . html_entity_decode($this->request->post['email'], ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "Telephone: " . html_entity_decode($this->request->post['user_telephone'], ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "Country: " . $country_info['name'] . "\n";
            $message .= "Enquiry: " . html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8') . "\n";

            $emailTemplate = $this->emailTemplate(10);
            if($emailTemplate){
                $message =  html_entity_decode($emailTemplate['description'], ENT_QUOTES, "UTF-8");
                $message = str_replace(array('[FIRST-NAME]', '[EMAIL]', '[TELEPHONE]', '[COUNRTY]', '[ENQUIRY]'), array(
                html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'),
                html_entity_decode($this->request->post['email'], ENT_QUOTES, 'UTF-8'),
                html_entity_decode($this->request->post['user_telephone'], ENT_QUOTES, 'UTF-8'),
                $country_info['name'],
                html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')
                ), $message);
                $subject = str_replace('[FIRST-NAME]', $this->request->post['name'], $emailTemplate['email_subject']);
            }
            
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			
            if (!isset($this->request->post['mail_to']) && empty($this->request->post['mail_to'])) {
                    $mail->setTo($this->config->get('config_email'));
            } else {
                    $mail->setTo($this->request->post['mail_to']);
            }
            
			$mail->setFrom($this->config->get('config_email'));
			$mail->setReplyTo($this->request->post['email']);
			$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
			
            if($emailTemplate){
                    $mail->setSubject($subject);
                    $mail->setHtml($message);
            }else{
                    $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
                    $mail->setText($message);
            }    
            
			$mail->send();

			$this->response->redirect($this->url->link('information/contact/success'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['enquiry'])) {
			$data['error_enquiry'] = $this->error['enquiry'];
		} else {
			$data['error_enquiry'] = '';
		}


            if (isset($this->error['country'])) {
                    $data['error_country'] = $this->error['country'];
            } else {
                    $data['error_country'] = '';
            }

            if (isset($this->error['telephone'])) {
                    $data['error_telephone'] = $this->error['telephone'];
            } else {
                    $data['error_telephone'] = '';
            }

            if (isset($this->error['mail_to'])) {
                    $data['error_mail_to'] = $this->error['mail_to'];
            } else {
                    $data['error_mail_to'] = '';
            }
            
		$data['button_submit'] = $this->language->get('button_submit');

		$data['action'] = $this->url->link('information/contact', '', true);

		$this->load->model('tool/image');

		if ($this->config->get('config_image')) {
			$data['image'] = $this->model_tool_image->resize($this->config->get('config_image'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_height'));
		} else {
			$data['image'] = false;
		}

		$data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['geocode'] = $this->config->get('config_geocode');
		$data['geocode_hl'] = $this->config->get('config_language');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['fax'] = $this->config->get('config_fax');
		$data['open'] = nl2br($this->config->get('config_open'));
		$data['comment'] = $this->config->get('config_comment');

		$data['locations'] = array();

		$this->load->model('localisation/location');

		foreach((array)$this->config->get('config_location') as $location_id) {
			$location_info = $this->model_localisation_location->getLocation($location_id);

			if ($location_info) {
				if ($location_info['image']) {
					$image = $this->model_tool_image->resize($location_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_height'));
				} else {
					$image = false;
				}

				$data['locations'][] = array(
					'location_id' => $location_info['location_id'],
					'name'        => $location_info['name'],
					'address'     => nl2br($location_info['address']),
					'geocode'     => $location_info['geocode'],
					'telephone'   => $location_info['telephone'],
					'fax'         => $location_info['fax'],
					'image'       => $image,
					'open'        => nl2br($location_info['open']),
					'comment'     => $location_info['comment']
				);
			}
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = $this->customer->getEmail();
		}


            if (isset($this->request->post['user_telephone'])) {
                    $data['user_telephone'] = $this->request->post['user_telephone'];
            } else {
                    $data['user_telephone'] = '';
            }

            if (isset($this->request->post['country_id'])) {
                    $data['country_id'] = $this->request->post['country_id'];
            } else {
                    $data['country_id'] = $this->config->get('config_country_id');
            }

            if (isset($this->request->post['mail_to'])) {
                    $data['mail_to'] = $this->request->post['mail_to'];
            } else {
                    $data['mail_to'] = '';
            }

            $this->load->model('localisation/country');

            $data['countries'] = $this->model_localisation_country->getCountries();
            
		if (isset($this->request->post['enquiry'])) {
			$data['enquiry'] = $this->request->post['enquiry'];
		} else {
			$data['enquiry'] = '';
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/contact', $data));
	}

	protected function validate() {
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}


            if ((utf8_strlen($this->request->post['user_telephone']) < 5) || (utf8_strlen($this->request->post['user_telephone']) > 20)) {
                    $this->error['telephone'] = $this->language->get('error_telephone');
            }

            if ($this->request->post['country_id'] == '') {
                    $this->error['country'] = $this->language->get('error_country');
            }

            if ($this->request->post['mail_to'] == '') {
                    $this->error['mail_to'] = $this->language->get('error_mail_to');
            }
            
		if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
			$this->error['enquiry'] = $this->language->get('error_enquiry');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}


            public function emailTemplate($emailTemplateID){
                    $query = $this->db->query("SELECT id, description, email_subject FROM email_template where id=$emailTemplateID and status=1");
                    
                    return $query->row;
            }
            
	public function success() {
		$this->load->language('information/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);


            $data['text_message'] = $this->language->get('text_success');
            
		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
