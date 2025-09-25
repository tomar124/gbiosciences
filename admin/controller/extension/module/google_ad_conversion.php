<?php
class ControllerExtensionModuleGoogleAdConversion extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/google_ad_conversion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {                    
			$this->model_setting_setting->editSetting('module_google_ad_conversion', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['conversion_id'])) {
			$data['error_conversion_id'] = $this->error['conversion_id'];
		} else {
			$data['error_conversion_id'] = '';
		}

		if (isset($this->error['conversion_label'])) {
			$data['error_conversion_label'] = $this->error['conversion_label'];
		} else {
			$data['error_conversion_label'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/google_ad_conversion', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/google_ad_conversion', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_google_ad_conversion_conversion_id'])) {
			$data['module_google_ad_conversion_conversion_id'] = $this->request->post['module_google_ad_conversion_conversion_id'];
		} else {
			$data['module_google_ad_conversion_conversion_id'] = $this->config->get('module_google_ad_conversion_conversion_id');
		}

		if (isset($this->request->post['module_google_ad_conversion_conversion_label'])) {
			$data['module_google_ad_conversion_conversion_label'] = $this->request->post['module_google_ad_conversion_conversion_label'];
		} else {
			$data['module_google_ad_conversion_conversion_label'] = $this->config->get('module_google_ad_conversion_conversion_label');
		}

		if (isset($this->request->post['module_google_ad_conversion_status'])) {
			$data['module_google_ad_conversion_status'] = $this->request->post['module_google_ad_conversion_status'];
		} else {
			$data['module_google_ad_conversion_status'] = $this->config->get('module_google_ad_conversion_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/google_ad_conversion', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/google_ad_conversion')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['module_google_ad_conversion_conversion_id']) {
			$this->error['conversion_id'] = $this->language->get('error_conversion_id');
		}

		if (!$this->request->post['module_google_ad_conversion_conversion_label']) {
			$this->error['conversion_label'] = $this->language->get('error_conversion_label');
		}

		return !$this->error;
	}
}