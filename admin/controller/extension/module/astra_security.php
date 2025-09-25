<?php

class ControllerExtensionModuleAstraSecurity extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/astra_security');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/module');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('astra_security', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
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
		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/astra_security', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/astra_security', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/astra_security', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/astra_security', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(DIR_CATALOG){
			$astra_path = DIR_CATALOG . "controller/extension/astra/"; //admin config var
		}else{
			$astra_path = DIR_APPLICATION . "controller/extension/astra/";
		}

		if(HTTPS_CATALOG){
			$assetPath = HTTPS_CATALOG.'catalog/controller/extension/astra/assets/';  //admin config var
		}else{
			$assetPath = HTTPS_SERVER.'catalog/controller/extension/astra/assets/';
		}
		
		if(file_exists($astra_path . 'autoload.php')){
			require_once($astra_path . 'autoload.php');
			$astraContainer = $GLOBALS['astraContainer'];

			$astraSiteId = $astraContainer->get('options')->get('siteId');
    		$astraSiteSettings = $astraContainer->get('options')->get('siteSettings');
			$data['siteId'] = $astraSiteId;
			$data['dashboardUrl'] = getenv('ASTRA_DASHBOARD_URL_HTTPS') ?? 'https://my.getastra.com';
			if (empty($data['dashboardUrl'])) {
				$data['dashboardUrl'] = 'https://my.getastra.com';
			}
			$data['assetPath'] = $assetPath;

			if (!empty($astraSiteId) && !empty($astraSiteSettings)) {
				$data['state'] = 'connected';
				$data['page']['body'] = $this->load->view('extension/module/astra_connected', $data);
			} else {
				$data['siteUrl'] = HTTPS_CATALOG ? HTTPS_CATALOG : HTTPS_SERVER;
				$data['state'] = 'disconnected';
				$data['page']['body'] = $this->load->view('extension/module/astra_disconnected', $data);
			}
		}else{
			//premium plugin not present
			$data['state'] = 'free';
			$data['page']['body'] = $this->load->view('extension/module/astra_free', $data);			
		}
		
		$this->response->setOutput($this->load->view('extension/module/astra_security', $data));

	}

	protected function validate()
	{
		return true;
		if (!$this->user->hasPermission('modify', 'extension/module/astra_security')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		*/

		return !$this->error;
	}

	public function install()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_astra_security', ['module_astra_security_status' => 1]);
	}

	public function uninstall()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_astra_security');
	}
}
