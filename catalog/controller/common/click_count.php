<?php
class ControllerCommonClickCount extends Controller {
	public function index() {
		if ($this->config->get('dashboard_click_count_status')) {
			$this->load->model('catalog/click_count');

                        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                                $this->model_catalog_click_count->updateCount($this->request->post['page'], $this->request->post['section']);

                                return true;
                        } else {
                                return false;
                        }
                } else {
                        return false;
                }
	}

	protected function validate() {
                $availabelSections = $this->model_catalog_click_count->getAvailableSections();

		if (!isset($this->request->post['page']) && !in_array($this->request->post['page'], $availabelSections)) {
			return false;
		}

		if (!isset($this->request->post['section']) && !in_array($this->request->post['section'], $availabelSections)) {
			return false;
		}

                return true;
	}
}