<?php
class ControllerCatalogEmail extends Controller {
	private $error = array();

        public function index() {
		$this->load->language('catalog/email');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/email');

		$this->getList();
	}
        
        protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id';
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
		);

		$data['add'] = $this->url->link('catalog/email/add', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
		$data['delete'] = $this->url->link('catalog/email/delete', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
		$data['repair'] = $this->url->link('catalog/email/repair', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);

		$data['emails'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$email_total = $this->model_catalog_email->getTotalEmails();

		$results = $this->model_catalog_email->getEmails($filter_data);

		foreach ($results as $result) {
			$data['emails'][] = array(
				'id' => $result['id'],
				'name'        => $result['name'],
				'status'  => $result['status'],
				'edit'        => $this->url->link('catalog/email/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, TRUE),
				'delete'      => $this->url->link('catalog/email/delete', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, TRUE)
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
			$data['selected'] = (array)$this->request->post['selected'];
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

		$data['sort_name'] = $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, TRUE);
		$data['sort_sort_order'] = $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, TRUE);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $email_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', TRUE);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($email_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($email_total - $this->config->get('config_limit_admin'))) ? $email_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $email_total, ceil($email_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
		$this->response->setOutput($this->load->view('catalog/email_list', $data));
	}

	public function add() {
		$this->load->language('catalog/email');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/email');
                
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
			$this->model_catalog_email->addEmail($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
		}

		$this->getForm();
	}
        

	public function edit() {
		$this->load->language('catalog/email');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/email');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_email->editEmail($this->request->get['id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/email');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/email');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_catalog_email->deleteEmail($id);
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

			$this->response->redirect($this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
		}

		$this->getList();
	}
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/email')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

                if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 255)) {
                        $this->error['name'] = $this->language->get('error_name');
                }

                if ((utf8_strlen($this->request->post['email_subject']) < 3) || (utf8_strlen($value['email_subject']) > 255)) {
                        $this->error['subject_title'] = $this->language->get('error_subject_title');
                }
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/email')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        protected function getForm() {

                $data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

                if (isset($this->error['subject_title'])) {
                        $data['error_subject_title'] = $this->error['subject_title'];
                } else {
                        $data['error_subject_title'] = '';
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
                        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
                );

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('heading_title'),
                        'href' => $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
                );

                if (!isset($this->request->get['id'])) {
                        $data['action'] = $this->url->link('catalog/email/add', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
                } else {
                        $data['action'] = $this->url->link('catalog/email/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, TRUE);
                }

                $data['cancel'] = $this->url->link('catalog/email', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);

                if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                        $email_info = $this->model_catalog_email->getEmail($this->request->get['id']);
                }
                
                $data['user_token'] = $this->session->data['user_token'];

                if (isset($this->request->post['name'])) {
                        $data['name'] = $this->request->post['name'];
                } elseif (isset($this->request->get['id'])) {
                        $data['name'] = $email_info['name'];
                } else {
                        $data['name'] = '';
                }

                if (isset($this->request->post['description'])) {
                        $data['description'] = $this->request->post['description'];
                } elseif (isset($this->request->get['id'])) {
                        $data['description'] = $email_info['description'];
                } else {
                        $data['description'] = '';
                }

                if (isset($this->request->post['status'])) {
                        $data['status'] = $this->request->post['status'];
                } elseif (isset($this->request->get['id'])) {
                        $data['status'] = $email_info['status'];
                } else {
                        $data['status'] = 0;
                }
                if (isset($this->request->post['email_subject'])) {
                        $data['email_subject'] = $this->request->post['email_subject'];
                } elseif (isset($this->request->get['id'])) {
                        $data['email_subject'] = $email_info['email_subject'];
                } else {
                        $data['email_subject'] = '';
                }
                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view('catalog/email_form', $data));
        }        
}