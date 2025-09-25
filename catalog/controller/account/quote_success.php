<?php
class ControllerAccountQuoteSuccess extends Controller {
	public function index() {
                $data['quote_id'] = $this->request->get['quote_id'];
                
                $this->load->language('account/quote_success');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

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
                        'href' => $this->url->link('account/quote_success')
                );

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->config->get('config_customer_group_id'));

		$data['text_message'] = $this->language->get('text_message');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('account/account', '', 'TRUE');
		
                $data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
                
		$this->response->setOutput($this->load->view('common/quote_success', $data));
	}
}