<?php
class ControllerExtensionModuleInformationDistributors extends Controller {
	public function index() {
		$this->load->language('extension/module/information/distributors');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/information/distributors')
		);

		$data['heading_title'] = $this->language->get('heading_title');

                $data['text_name'] = $this->language->get('text_name');
                $data['text_email'] = $this->language->get('text_email');
                $data['text_website'] = $this->language->get('text_website');
                $data['text_country'] = $this->language->get('text_country');
                $data['text_address'] = $this->language->get('text_address');
                $data['text_phone'] = $this->language->get('text_phone');

		$this->load->model('extension/module/catalog/distributor');
                
                $data['country'] = $this->model_extension_module_catalog_distributor->getCountry();
                
                $data['countries'] = $this->model_extension_module_catalog_distributor->getCountries();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
                
                $this->response->setOutput($this->load->view('extension/module/information/distributors', $data));
	}
}