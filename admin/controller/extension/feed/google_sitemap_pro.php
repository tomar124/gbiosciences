<?php
class ControllerExtensionFeedGoogleSitemapPro extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/feed/google_sitemap_pro');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('feed_google_sitemap_pro', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/google_sitemap_pro', 'user_token=' . $this->session->data['user_token'], true)
		);

                $data['user_token'] = $this->session->data['user_token'];

                // API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['action'] = $this->url->link('extension/feed/google_sitemap_pro', 'user_token=' . $this->session->data['user_token'], true);

                $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);

		if (isset($this->request->post['feed_google_sitemap_pro_status'])) {
			$data['feed_google_sitemap_pro_status'] = $this->request->post['feed_google_sitemap_pro_status'];
		} else {
			$data['feed_google_sitemap_pro_status'] = $this->config->get('feed_google_sitemap_pro_status');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_products'])) {
			$data['feed_google_sitemap_pro_products'] = $this->request->post['feed_google_sitemap_pro_products'];
		} else {
			$data['feed_google_sitemap_pro_products'] = $this->config->get('feed_google_sitemap_pro_products');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_categories'])) {
			$data['feed_google_sitemap_pro_categories'] = $this->request->post['feed_google_sitemap_pro_categories'];
		} else {
			$data['feed_google_sitemap_pro_categories'] = $this->config->get('feed_google_sitemap_pro_categories');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_informations'])) {
			$data['feed_google_sitemap_pro_informations'] = $this->request->post['feed_google_sitemap_pro_informations'];
		} else {
			$data['feed_google_sitemap_pro_informations'] = $this->config->get('feed_google_sitemap_pro_informations');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_product_images'])) {
			$data['feed_google_sitemap_pro_product_images'] = $this->request->post['feed_google_sitemap_pro_product_images'];
		} else {
			$data['feed_google_sitemap_pro_product_images'] = $this->config->get('feed_google_sitemap_pro_product_images');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_protocols'])) {
			$data['feed_google_sitemap_pro_protocols'] = $this->request->post['feed_google_sitemap_pro_protocols'];
		} else {
			$data['feed_google_sitemap_pro_protocols'] = $this->config->get('feed_google_sitemap_pro_protocols');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_product_sds'])) {
			$data['feed_google_sitemap_pro_sds'] = $this->request->post['feed_google_sitemap_pro_sds'];
		} else {
			$data['feed_google_sitemap_pro_sds'] = $this->config->get('feed_google_sitemap_pro_sds');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_custom'])) {
			$data['feed_google_sitemap_pro_custom'] = $this->request->post['feed_google_sitemap_pro_custom'];
		} else {
			$data['feed_google_sitemap_pro_custom'] = $this->config->get('feed_google_sitemap_pro_custom');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_change_frequency'])) {
			$data['feed_google_sitemap_pro_change_frequency'] = $this->request->post['feed_google_sitemap_pro_change_frequency'];
		} else {
			$data['feed_google_sitemap_pro_change_frequency'] = $this->config->get('feed_google_sitemap_pro_change_frequency');
		}
                
                if (isset($this->request->post['feed_google_sitemap_pro_max_url_in_single_sitemap'])) {
			$data['feed_google_sitemap_pro_max_url_in_single_sitemap'] = $this->request->post['feed_google_sitemap_pro_max_url_in_single_sitemap'];
		} else {
			$data['feed_google_sitemap_pro_max_url_in_single_sitemap'] = $this->config->get('feed_google_sitemap_pro_max_url_in_single_sitemap');
		}

		$data['data_feed'] = HTTP_CATALOG . 'sitemap.xml';

                $files = glob(DIR_IMAGE . 'sitemap/*.xml');

                $data['hasFiles'] = count($files) > 0 ? true : false;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/google_sitemap_pro', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/google_sitemap_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

                if (empty($this->request->post['feed_google_sitemap_pro_max_url_in_single_sitemap']) 
                        || $this->request->post['feed_google_sitemap_pro_max_url_in_single_sitemap'] > 50000) {
                        $this->error['warning'] = $this->language->get('error_max_url');
                }

		return !$this->error;
	}

        public function custom() {
		$this->load->language('extension/feed/google_sitemap_pro');

		$this->load->model('extension/feed/google_sitemap_pro');

                $data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['customs'] = array();

		$results = $this->model_extension_feed_google_sitemap_pro->getCustomLinks(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['customs'][] = array(
				'url'    => $result['url'],
				'added_on' => date($this->language->get('date_format_short'), strtotime($result['added_on'])),
                                'google_sitemap_pro_custom_url_id' => $result['google_sitemap_pro_custom_url_id']
			);
		}

		$history_total = $this->model_extension_feed_google_sitemap_pro->getTotalCustomLinks();

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/feed/google_sitemap_pro/custom', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('extension/feed/google_sitemap_pro_custom', $data));
	}

        public function addCustom() {
		$this->load->language('extension/feed/google_sitemap_pro');
                $this->load->model('extension/feed/google_sitemap_pro');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/feed/google_sitemap_pro')) {
			$json['error'] = $this->language->get('error_permission');
                } elseif(empty($this->request->post['url']) 
                        || filter_var(HTTPS_CATALOG . $this->request->post['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
                        $json['error'] = $this->language->get('error_custom_url');
                } elseif($this->model_extension_feed_google_sitemap_pro->isUrlExists(HTTPS_CATALOG . $this->request->post['url'])) {
                        $json['error'] = $this->language->get('error_custom_url_exists');
                } else {
			$this->model_extension_feed_google_sitemap_pro->addCustomLink(HTTPS_CATALOG . $this->request->post['url']);

			$json['success'] = $this->language->get('text_success_custom');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

        public function deleteCustom() {
		$this->load->language('extension/feed/google_sitemap_pro');
                $this->load->model('extension/feed/google_sitemap_pro');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/feed/google_sitemap_pro')) {
			$json['error'] = $this->language->get('error_permission');
                } else {
			$this->model_extension_feed_google_sitemap_pro->deleteCustomLink($this->request->get['google_sitemap_pro_custom_url_id']);

			$json['success'] = $this->language->get('text_success_custom');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

        public function sitemaps() {
		$this->load->language('extension/feed/google_sitemap_pro');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

                $files = glob(DIR_ROOT . 'sitemaps/*.xml');
                $sitemaps_total = count($files);

		$data['sitemaps'] = array();

		$results = array_slice($files, ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['sitemaps'][] = array(
				'filename' => basename($result),
				'filesize' => number_format(filesize($result) / 1024 / 1024, 2),
                                'conversion_type' => 'MB'
			);
		}

		$pagination = new Pagination();
		$pagination->total = $sitemaps_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/feed/google_sitemap_pro/sitemaps', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($sitemaps_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($sitemaps_total - 10)) ? $sitemaps_total : ((($page - 1) * 10) + 10), $sitemaps_total, ceil($sitemaps_total / 10));

		$this->response->setOutput($this->load->view('extension/feed/google_sitemap_pro_sitemaps', $data));
	}

        public function install() {
		$this->load->model('extension/feed/google_sitemap_pro');

		$this->model_extension_feed_google_sitemap_pro->install();
	}

	public function uninstall() {
		$this->load->model('extension/feed/google_sitemap_pro');

		$this->model_extension_feed_google_sitemap_pro->uninstall();
	}
}