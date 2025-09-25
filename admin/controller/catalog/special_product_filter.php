<?php
class ControllerCatalogSpecialProductFilter extends Controller {
        private $error = array();

        public function index() {
                $this->load->language('catalog/special_product_filter');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('catalog/special_product_filter');

                $this->getList();
        }

        public function add() {
                $this->load->language('catalog/special_product_filter');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('catalog/special_product_filter');

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                        $this->model_catalog_special_product_filter->addSpecialProductFilter($this->request->post);

                        $this->session->data['success'] = $this->language->get('text_success');

                        $url = '';

                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }

                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }

                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

                        $this->response->redirect($this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }

                $this->getForm();
        }

        public function edit() {
                $this->load->language('catalog/special_product_filter');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('catalog/special_product_filter');

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                        $this->model_catalog_special_product_filter->editSpecialProductFilter($this->request->get['special_product_filter_group_id'], $this->request->post);

                        $this->session->data['success'] = $this->language->get('text_success');

                        $url = '';

                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }

                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }

                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

                        $this->response->redirect($this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }

                $this->getForm();
        }

        public function delete() {
                $this->load->language('catalog/special_product_filter');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('catalog/special_product_filter');

                if (isset($this->request->post['selected']) && $this->validateDelete()) {
                        foreach ($this->request->post['selected'] as $special_product_filter_group_id) {
                                $this->model_catalog_special_product_filter->deleteSpecialProductFilter($special_product_filter_group_id);
                        }

                        $this->session->data['success'] = $this->language->get('text_success');

                        $url = '';

                        if (isset($this->request->get['sort'])) {
                                $url .= '&sort=' . $this->request->get['sort'];
                        }

                        if (isset($this->request->get['order'])) {
                                $url .= '&order=' . $this->request->get['order'];
                        }

                        if (isset($this->request->get['page'])) {
                                $url .= '&page=' . $this->request->get['page'];
                        }

                        $this->response->redirect($this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                }

                $this->getList();
        }

        protected function getList() {
                if (isset($this->request->get['sort'])) {
                        $sort = $this->request->get['sort'];
                } else {
                        $sort = 'fgd.name';
                }

                if (isset($this->request->get['order'])) {
                        $order = $this->request->get['order'];
                } else {
                        $order = 'ASC';
                }

                if (isset($this->request->get['page'])) {
                        $page = $this->request->get['page'];
                } else {
                        $page = 1;
                }

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                }

                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
                );

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('heading_title'),
                        'href' => $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true)
                );
                $data['upload'] = $this->url->link('catalog/special_product_filter/upload', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
                $data['download'] = $this->url->link('catalog/special_product_filter/download', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
                $data['add'] = $this->url->link('catalog/special_product_filter/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
                $data['delete'] = $this->url->link('catalog/special_product_filter/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

                $data['special_product_filters'] = array();

                $filter_data = array(
                        'sort' => $sort,
                        'order' => $order,
                        'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                        'limit' => $this->config->get('config_limit_admin')
                );

                $filter_total = $this->model_catalog_special_product_filter->getTotalSpecialProductFilterGroups();

                $results = $this->model_catalog_special_product_filter->getSpecialProductFilterGroups($filter_data);

                $special_product_filter_last_id = array();
                $special_product_filter_last_id = $this->model_catalog_special_product_filter->getLastInsertId();

                $data['text_valid_special_product_filter_group_id'] = sprintf($this->language->get('text_valid_special_product_filter_group_id'), $special_product_filter_last_id['last_special_product_filter_group_id']);
                $data['text_valid_special_product_filter_id'] = sprintf($this->language->get('text_valid_special_product_filter_id'), $special_product_filter_last_id['last_special_product_filter_id']);
                
                foreach ($results as $result) {
                        $data['special_product_filters'][] = array(
                                'special_product_filter_group_id' => $result['special_product_filter_group_id'],
                                'name' => $result['name'],
                                'sort_order' => $result['sort_order'],
                                'edit' => $this->url->link('catalog/special_product_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&special_product_filter_group_id=' . $result['special_product_filter_group_id'] . $url, true)
                        );
                }

                if (isset($this->error['warning'])) {
                        $data['error_warning'] = $this->error['warning'];
                } else {
                        $data['error_warning'] = '';
                }

                if (isset($this->session->data['success'])) {
                        $data['success'] = $this->session->data['success'];

                    unset($this->session->data['success']);
                } else {
                        $data['success'] = '';
                }

                if (isset($this->request->post['selected'])) {
                        $data['selected'] = (array) $this->request->post['selected'];
                } else {
                        $data['selected'] = array();
                }

                $url = '';

                if ($order == 'ASC') {
                        $url .= '&order=DESC';
                } else {
                        $url .= '&order=ASC';
                }

                if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                }

                $data['sort_name'] = $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=fgd.name' . $url, true);
                $data['sort_sort_order'] = $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=fg.sort_order' . $url, true);

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

                $pagination = new Pagination();
                $pagination->total = $filter_total;
                $pagination->page = $page;
                $pagination->limit = $this->config->get('config_limit_admin');
                $pagination->url = $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

                $data['pagination'] = $pagination->render();

                $data['results'] = sprintf($this->language->get('text_pagination'), ($filter_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($filter_total - $this->config->get('config_limit_admin'))) ? $filter_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $filter_total, ceil($filter_total / $this->config->get('config_limit_admin')));

                $data['sort'] = $sort;
                $data['order'] = $order;

                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view('catalog/special_product_filter_list', $data));
        }

        protected function getForm() {
                $data['text_form'] = !isset($this->request->get['special_product_filter_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

                if (isset($this->error['warning'])) {
                        $data['error_warning'] = $this->error['warning'];
                } else {
                        $data['error_warning'] = '';
                }

                if (isset($this->error['group'])) {
                        $data['error_group'] = $this->error['group'];
                } else {
                        $data['error_group'] = array();
                }

                if (isset($this->error['filter'])) {
                        $data['error_filter'] = $this->error['filter'];
                } else {
                        $data['error_filter'] = array();
                }

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                }

                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
                );

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('heading_title'),
                        'href' => $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true)
                );

                if (!isset($this->request->get['special_product_filter_group_id'])) {
                        $data['action'] = $this->url->link('catalog/special_product_filter/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
                } else {
                        $data['action'] = $this->url->link('catalog/special_product_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&special_product_filter_group_id=' . $this->request->get['special_product_filter_group_id'] . $url, true);
                }

                $data['cancel'] = $this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'] . $url, true);

                if (isset($this->request->get['special_product_filter_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                        $filter_group_info = $this->model_catalog_special_product_filter->getSpecialProductFilterGroup($this->request->get['special_product_filter_group_id']);
                }

                $data['user_token'] = $this->session->data['user_token'];

                $this->load->model('localisation/language');

                $data['languages'] = $this->model_localisation_language->getLanguages();

                if (isset($this->request->post['special_product_filter_group_description'])) {
                        $data['special_product_filter_group_description'] = $this->request->post['special_product_filter_group_description'];
                } elseif (isset($this->request->get['special_product_filter_group_id'])) {
                        $data['special_product_filter_group_description'] = $this->model_catalog_special_product_filter->getSpecialProductFilterGroupDescriptions($this->request->get['special_product_filter_group_id']);
                } else {
                        $data['special_product_filter_group_description'] = array();
                }

                if (isset($this->request->post['sort_order'])) {
                        $data['sort_order'] = $this->request->post['sort_order'];
                } elseif (!empty($filter_group_info)) {
                        $data['sort_order'] = $filter_group_info['sort_order'];
                } else {
                        $data['sort_order'] = '';
                }

                if (isset($this->request->post['special_product_filter'])) {
                        $data['special_product_filters'] = $this->request->post['special_product_filter'];
                } elseif (isset($this->request->get['special_product_filter_group_id'])) {
                        $data['special_product_filters'] = $this->model_catalog_special_product_filter->getSpecialProductFilterDescriptions($this->request->get['special_product_filter_group_id']);
                } else {
                        $data['special_product_filters'] = array();
                }

                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view('catalog/special_product_filter_form', $data));
        }

        protected function validateForm() {
                if (!$this->user->hasPermission('modify', 'catalog/special_product_filter')) {
                        $this->error['warning'] = $this->language->get('error_permission');
                }

                foreach ($this->request->post['special_product_filter_group_description'] as $language_id => $value) {
                        if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
                                $this->error['group'][$language_id] = $this->language->get('error_group');
                        }
                }

                if (isset($this->request->post['special_product_filter'])) {
                        foreach ($this->request->post['special_product_filter'] as $special_product_filter_id => $filter) {
                                foreach ($filter['special_product_filter_description'] as $language_id => $special_product_filter_description) {
                                        if ((utf8_strlen($special_product_filter_description['name']) < 1) || (utf8_strlen($special_product_filter_description['name']) > 64)) {
                                                $this->error['filter'][$special_product_filter_id][$language_id] = $this->language->get('error_name');
                                        }
                                }
                        }
                }

                return !$this->error;
        }

        protected function validateDelete() {
                if (!$this->user->hasPermission('modify', 'catalog/special_product_filter')) {
                        $this->error['warning'] = $this->language->get('error_permission');
                }

                return !$this->error;
        }

        public function getSpecialProductFilterOptions() {
                $filter_html = '';

                if (isset($this->request->get['spfg_id']) && $this->request->get['spfg_id']) {
                        $this->load->model('catalog/special_product_filter');
                        $filter_data = $this->model_catalog_special_product_filter->getSpecialProductFilterOptions($this->request->get['spfg_id'], $this->request->get['filter_row']);

                        $filter_html = $this->load->view('common/select_builder', $filter_data, TRUE);
                }

                $this->response->addHeader('Content-Type: text/html');
                $this->response->setOutput($filter_html);
        }

        public function autocomplete() {
                $json = array();

                if (isset($this->request->get['filter_name'])) {
                        $this->load->model('catalog/special_product_filter');

                        $filter_data = array(
                                'filter_name' => $this->request->get['filter_name'],
                                'start' => 0,
                                'limit' => 5
                        );

                        $results = $this->model_catalog_special_product_filter->getSpecialProductFilters($filter_data);

                        foreach ($results as $result) {
                                $json[] = array(
                                    'special_product_filter_group_id' => $result['special_product_filter_group_id'],
                                    'name' => $result['name']
                                );
                        }
                }

                $sort_order = array();

                foreach ($json as $key => $value) {
                        $sort_order[$key] = $value['name'];
                }

                array_multisort($sort_order, SORT_ASC, $json);

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }

        public function download() {
                $this->load->language( 'catalog/special_product_filter' );

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDownloadForm()) {
                        $this->document->setTitle($this->language->get('heading_title'));
                        $this->load->model( 'catalog/special_product_filter' );

                        $this->model_catalog_special_product_filter->download();
                } else {
                        $this->session->data['warning'] = $this->language->get('error_warning');
                }

                $this->response->redirect( $this->url->link( 'catalog/special_product_filter', 'user_token='.$this->request->get['user_token'], TRUE) );
	}

        public function upload() {
		$this->load->language('catalog/special_product_filter');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/special_product_filter');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
			if ((isset( $this->request->files['file'] )) && (is_uploaded_file($this->request->files['file']['tmp_name']))) {
				$file = $this->request->files['file']['tmp_name'];
				$incremental = true;
				if ($this->model_catalog_special_product_filter->upload($file,$incremental)==true) {
					$this->session->data['success'] = $this->language->get('text_success_import');
					$this->response->redirect($this->url->link('catalog/special_product_filter', 'user_token=' . $this->session->data['user_token'], $this->ssl));
				}
				else {
					$this->error['warning'] = $this->language->get('error_upload');
					if (defined('VERSION')) {
						$this->error['warning'] .= "<br />\n".$this->language->get( 'text_log_details_2_0_x' );
					} else {
						$this->error['warning'] .= "<br />\n".$this->language->get( 'text_log_details' );
					}
				}
			}
		}

		$this->getList();
	}
        protected function validateUploadForm() {
		if (!$this->user->hasPermission('modify', 'catalog/special_product_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} 

		if (!isset($this->request->files['file']['name'])) {
			if (isset($this->error['warning'])) {
				$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
			} else {
				$this->error['warning'] = $this->language->get( 'error_upload_name' );
			}
		} else {
			$ext = strtolower(pathinfo($this->request->files['file']['name'], PATHINFO_EXTENSION));
			if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
				} else {
					$this->error['warning'] = $this->language->get( 'error_upload_ext' );
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
        protected function validateDownloadForm() {
		if (!$this->user->hasPermission('access', 'catalog/special_product_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		return !$this->error;
	}
        
}