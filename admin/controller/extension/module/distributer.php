<?php 
    class ControllerExtensionModuledistributer extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/module/distributer');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('extension/module/distributer');

                $this->getList();
        }
        
        public function add() {
                $this->load->language('extension/module/distributer');
                
                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('extension/module/distributer');
              
                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                        $this->model_extension_module_distributer->addDistributer($this->request->post);

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

                        $this->response->redirect($this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
                }
                $this->getForm();
	}
        
        public function edit() {
                $this->load->language('extension/module/distributer');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('extension/module/distributer');

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                        $this->model_extension_module_distributer->editDistributer($this->request->get['distributer_id'], $this->request->post);

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
                        
                        $this->response->redirect($this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
                }
                $this->getForm();
	}
        
        public function delete() {
                $this->load->language('extension/module/distributer');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('extension/module/distributer');

                if (isset($this->request->post['selected'])) {
                        foreach ($this->request->post['selected'] as $distributer_id) {
                                $this->model_extension_module_distributer->deleteDistributer($distributer_id);
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
                        
                        $this->response->redirect($this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
                }
                $this->getList();
	}
        
        public function getList() {
                if (isset($this->request->get['sort'])) {
                        $sort = $this->request->get['sort'];
                } else {
                        $sort = '';
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

                if ($order == 'ASC') {
                        $url .= '&order=DESC';
                } else {
                        $url .= '&order=ASC';
                }

                $data['distributers'] = array();

                $filter_data = array(
                        'sort'  => $sort,
                        'order' => $order,
                        'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                        'limit' => $this->config->get('config_limit_admin')
                );

                $results = $this->model_extension_module_distributer->getDistributers($filter_data);

                foreach ($results as $result) {
                        $data['distributers'][] = array(
                                'distributer_id'=> $result['distributer_id'],
                                'companyname'   => $result['companyname'],
                                'email'         => $result['email'],
                                'phone'         => $result['phone'],
                                'country'       => $result['countryname'],
                                'edit'          => $this->url->link('extension/module/distributer/edit', 'user_token=' . $this->session->data['user_token'] . '&distributer_id=' . $result['distributer_id'] . $url, 'SSL')
                        );
                }

                $distributer_total = $this->model_extension_module_distributer->getTotalDistributer();

                $data['heading_title'] = $this->language->get('heading_title');

                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
                );

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('heading_title'),
                        'href' => $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
                );

                $data['insert'] = $this->url->link('extension/module/distributer/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
                $data['copy'] = $this->url->link('extension/module/distributer/copy', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
                $data['delete'] = $this->url->link('extension/module/distributer/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

                $data['button_edit'] = $this->language->get('button_edit');
                $data['button_add'] = $this->language->get('button_add');
                $data['button_delete'] = $this->language->get('button_delete');

                $data['text_list'] = $this->language->get('text_list');
                $data['text_no_results'] = $this->language->get('text_no_results');
                $data['text_confirm'] = $this->language->get('text_confirm');

                $data['column_name'] = $this->language->get('column_name');
                $data['column_email'] = $this->language->get('column_email');
                $data['column_phone'] = $this->language->get('column_phone');
                $data['column_country'] = $this->language->get('column_country');
                $data['column_action'] = $this->language->get('column_action');

                $data['sort_name'] = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, 'SSL');
                $data['sort_email'] = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, 'SSL');
                $data['sort_phone'] = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, 'SSL');
                $data['sort_country'] = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . '&sort=countryname' . $url, 'SSL');

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
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

                $pagination = new Pagination();
                $pagination->total = $distributer_total;
                $pagination->page = $page;
                $pagination->limit = $this->config->get('config_limit_admin');
                $pagination->url = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

                $data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($distributer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($distributer_total - $this->config->get('config_limit_admin'))) ? $distributer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $distributer_total, ceil($distributer_total / $this->config->get('config_limit_admin')));

                $data['sort'] = $sort;
                $data['order'] = $order;

                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');
                $this->response->setOutput($this->load->view('extension/module/distributer', $data));
                
        }
        
        protected function getForm() {
                $data['heading_title'] = $this->language->get('heading_title');

                $data['text_form'] = !isset($this->request->get['distributer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

                $data['entry_fristname'] = $this->language->get('entry_fristname');
                $data['entry_lastname'] = $this->language->get('entry_lastname');
                $data['entry_address'] = $this->language->get('entry_address');
                $data['entry_city'] = $this->language->get('entry_city');
                $data['entry_country'] = $this->language->get('entry_country');
                $data['entry_pincode'] = $this->language->get('entry_pincode');
                $data['entry_officenumber'] = $this->language->get('entry_officenumber');
                $data['entry_mobilenumber'] = $this->language->get('entry_mobilenumber');
                $data['entry_email'] = $this->language->get('entry_email');
                $data['entry_secoundary_email'] = $this->language->get('entry_secoundary_email');
                $data['entry_companyname'] = $this->language->get('entry_companyname');
                $data['entry_website'] = $this->language->get('entry_website');
                $data['entry_faxno'] = $this->language->get('entry_faxno');
                $data['entry_storagenumber'] = $this->language->get('entry_storagenumber');
                $data['entry_status'] = $this->language->get('entry_status');
                
                $data['tab_general'] = $this->language->get('tab_general');
                $data['tab_data'] = $this->language->get('tab_data');

                $data['text_enabled'] = $this->language->get('text_enabled');
                $data['text_disabled'] = $this->language->get('text_disabled');
                $data['text_select'] = $this->language->get('text_select');

                $data['button_edit'] = $this->language->get('button_edit');
                $data['button_delete'] = $this->language->get('button_delete');
                $data['button_save'] = $this->language->get('button_save');
                $data['button_cancel'] = $this->language->get('button_cancel');

                if (isset($this->error['warning'])) {
                        $data['error_warning'] = $this->error['warning'];
                } else {
                        $data['error_warning'] = '';
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
               
                if (isset($this->error['error_mobilenumbers'])) {
                        $data['error_mobilenumbers'] = $this->error['error_mobilenumbers'];
                }
                
                if (isset($this->error['error_countrys'])) {
                        $data['error_countrys'] = $this->error['error_countrys'];
                }
                
                if (isset($this->error['error_email'])) {
                        $data['error_email'] = $this->error['error_email'];
                }

                if (isset($this->error['error_companyname'])) {
                        $data['error_companyname'] = $this->error['error_companyname'];
                }
                
                $data['breadcrumbs'] = array();

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
                );

                $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('heading_title'),
                        'href' => $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
                );
                
                $this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();    
                
                if (!isset($this->request->get['distributer_id'])) {
                        $data['action'] = $this->url->link('extension/module/distributer/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
                } else {
                        $data['action'] = $this->url->link('extension/module/distributer/edit', 'user_token=' . $this->session->data['user_token'] . '&distributer_id=' . $this->request->get['distributer_id'] . $url, 'SSL');
                }

                $data['cancel'] = $this->url->link('extension/module/distributer', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

                if (isset($this->request->get['distributer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                        $distributer_info = $this->model_extension_module_distributer->getDistributer($this->request->get['distributer_id']);
                }
                
                $this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
                
                if (isset($this->request->post['distributor_discription'])) {
			$data['distributor_discription'] = $this->request->post['distributor_discription'];
		} elseif (isset($this->request->get['distributer_id'])) {
			$data['distributor_discription'] = $this->model_extension_module_distributer->getDistributorDescriptions($this->request->get['distributer_id']);
		} else {
			$data['distributor_discription'] = array();
		}
               
                if (isset($this->request->post['country'])) {
                        $data['country'] = $this->request->post['country'];
                } elseif (!empty($distributer_info)) {
                        $data['country'] = $distributer_info['country'];
                } else {
                        $data['country'] = '';
                }
                
                if (isset($this->request->post['pincode'])) {
                        $data['pincode'] = $this->request->post['pincode'];
                } elseif (!empty($distributer_info)) {
                        $data['pincode'] = $distributer_info['postalcode'];
                } else {
                        $data['pincode'] = '';
                }

                if (isset($this->request->post['officenumber'])) {
                        $data['officenumber'] = $this->request->post['officenumber'];
                } elseif (!empty($distributer_info)) {
                        $data['officenumber'] = $distributer_info['officenumber'];
                } else {
                        $data['officenumber'] = '';
                }

                if (isset($this->request->post['mobilenumber'])) {
                        $data['mobilenumber'] = $this->request->post['mobilenumber'];
                } elseif (!empty($distributer_info)) {
                        $data['mobilenumber'] = $distributer_info['mobilenumber'];
                } else {
                        $data['mobilenumber'] = '';
                }

                if (isset($this->request->post['faxno'])) {
                        $data['faxno'] = $this->request->post['faxno'];
                } elseif (!empty($distributer_info)) {
                        $data['faxno'] = $distributer_info['faxno'];
                } else {
                        $data['faxno'] = '';
                }

                if (isset($this->request->post['storage'])) {
                        $data['storage'] = $this->request->post['storage'];
                } elseif (!empty($distributer_info)) {
                        $data['storage'] = $distributer_info['storagespace'];
                } else {
                        $data['storage'] = '';
                }

                if (isset($this->request->post['status'])) {
                        $data['status'] = $this->request->post['status'];
                } elseif (!empty($distributer_info)) {
                        $data['status'] = $distributer_info['status'];
                } else {
                        $data['status'] = '';
                }

                $this->load->model('localisation/country');

                $data['countries'] = $this->model_localisation_country->getCountries();

                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');
                
                $this->response->setOutput($this->load->view('extension/module/distributor_form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/distributer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                foreach ($this->request->post['distributor_discription'] as $language_id => $value) {
                    if ((utf8_strlen($value['companyname']) < 2) || (utf8_strlen($value['companyname']) > 64)) {
                        $this->error['error_companyname'][$language_id] = $this->language->get('error_companyname');
                    }
                    
                    if ((utf8_strlen($value['email']) < 7) || (utf8_strlen($value['email']) > 64)) {
                        $this->error['error_email'][$language_id] = $this->language->get('error_email');
                    }
                }
                
                if ((utf8_strlen($this->request->post['country']) < 1) || (utf8_strlen($this->request->post['country']) > 3)) {
                        $this->error['error_countrys'] = $this->language->get('error_country');
                }
                
                if ((utf8_strlen($this->request->post['mobilenumber']) < 8) || (utf8_strlen($this->request->post['mobilenumber']) > 30)) {
                        $this->error['error_mobilenumbers'] = $this->language->get('error_mobilenumber');
                }
                
                if ($this->error && !isset($this->error['warning'])) {
                        $this->error['warning'] = $this->language->get('error_warning');
		}
            
		return !$this->error;
	}
    }
?>