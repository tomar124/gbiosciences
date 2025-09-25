<?php
class ControllerExtensionTotalEei extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/eei');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('total_eei', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_total'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/eei', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/total/eei', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', 'SSL');

		if (isset($this->request->post['total_eei_total'])) {
			$data['total_eei_total'] = $this->request->post['total_eei_total'];
		} else {
			$data['total_eei_total'] = $this->config->get('total_eei_total');
		}

		if (isset($this->request->post['total_eei_fee'])) {
			$data['total_eei_fee'] = $this->request->post['total_eei_fee'];
		} else {
			$data['total_eei_fee'] = $this->config->get('total_eei_fee');
		}

		if (isset($this->request->post['total_eei_status'])) {
			$data['total_eei_status'] = $this->request->post['total_eei_status'];
		} else {
			$data['total_eei_status'] = $this->config->get('total_eei_status');
		}

		if (isset($this->request->post['total_eei_sort_order'])) {
			$data['total_eei_sort_order'] = $this->request->post['total_eei_sort_order'];
		} else {
			$data['total_eei_sort_order'] = $this->config->get('total_eei_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/eei', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/eei')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}