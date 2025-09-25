<?php
class ControllerCatalogImport extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/import');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/import');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = (isset($this->request->get['section']) && $this->request->get['section'] == 'category') ? $this->language->get('text_category') : $this->language->get('text_product');
        $data['text_import'] = $this->language->get('text_import');
        $data['text_valid_product_id'] = sprintf($this->language->get('text_valid_product_id'), $this->model_catalog_import->getLastInsertId());
        
        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];

            unset($this->session->data['warning']);
        } else {
            $data['warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
        } else {
                $data['error_warning'] = '';
        }
        
        if (isset($this->request->get['section']) && !empty($this->request->get['section'])) {
            $data['section'] = $this->request->get['section'];
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );

        if (isset($this->request->get['section']) && $this->request->get['section'] == 'category') {
            $data['action_category'] = $this->url->link('catalog/import/category', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
        }

        if (isset($this->request->get['section']) && $this->request->get['section'] == 'product') {
            $data['action_product'] = $this->url->link('catalog/import/product', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
            $data['action_catalog'] = $this->url->link('catalog/import/catalog', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
            $data['action_update_meta_title'] = $this->url->link('catalog/import/update_meta', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
            $data['action_merge_sds'] = $this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'] . '&type=msds' . $url, TRUE);
            $data['action_merge_protocol'] = $this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'] . '&type=protocol' . $url, TRUE);
            $data['action_merge_coa'] = $this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'] . '&type=coa' . $url, TRUE);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/import', $data));
    }

    public function category() {

        $this->load->language('catalog/import');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/import');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $csv = new CSV();
            
            $data['category'] = '';
            $root_directory = $_SERVER['DOCUMENT_ROOT'];

            if ($_SERVER['SERVER_NAME'] == 'gbiosciences.local') {
                $uploads_dir = $root_directory . '/upload/category/'; // set you upload path here
            } else {
                $uploads_dir = $root_directory . '/gbiosciences/upload/category/'; // set you upload path here
            }
            
            if (is_uploaded_file($this->request->files['category']['tmp_name'])) {
                move_uploaded_file($this->request->files['category']['tmp_name'], $uploads_dir . $this->request->files['category']['name']);
                $data['category'] = $this->request->files['category']['name'];
            }

            $csv_file = $uploads_dir . $data['category'];

            $csv->category($csv_file);
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = $this->language->get('text_category');
        $data['text_import'] = $this->language->get('text_import');

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );

        $data['success'] = $this->language->get('text_success_category');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/import', $data));
    }

    public function product() {
        $this->load->language('catalog/import');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/import');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $data['product'] = '';
            $root_directory = $_SERVER['DOCUMENT_ROOT'];
            
            if ($_SERVER['SERVER_NAME'] == 'gbiosciences.local') {
                $uploads_dir = $root_directory . '/upload/product/'; // set you upload path here
            } else {
                $uploads_dir = $root_directory . '/gbiosciences/upload/product/'; // set you upload path here
            }
            if (is_uploaded_file($this->request->files['product']['tmp_name'])) {
                move_uploaded_file($this->request->files['product']['tmp_name'], $uploads_dir . $this->request->files['product']['name']);
                $data['product'] = $this->request->files['product']['name'];
            }

            $csv_file = $uploads_dir . $data['product'];

            $this->model_catalog_import->product($csv_file);
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = $this->language->get('text_product');
        $data['text_import'] = $this->language->get('text_import');

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );

        $data['success'] = $this->language->get('text_success_product');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/import', $data));
    }

    public function merge() {
        $this->load->language('catalog/import');

        $this->load->model('catalog/import');
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateMergeForm()) {            
                if ((isset( $this->request->files['merge'] )) && (is_uploaded_file($this->request->files['merge']['tmp_name']))) {
                        $file = $this->request->files['merge']['tmp_name'];
                        if ($this->model_catalog_import->merge($file, $this->request->post['language'])==true) {
                                $this->session->data['success'] = "You have successfully merged files";
                                $this->response->redirect($this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'], TRUE));
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
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        $this->document->setTitle($this->language->get('heading_title_merge_sds'));
        
        $data['heading_title'] = $this->language->get('heading_title_merge_sds');
        
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_merge_sds'),
            'href' => $this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'], TRUE)
        );                
        
        if (isset($this->error['error_file'])) {
                $data['error_file'] = $this->error['error_file'];
        } else {
                $data['error_file'] = '';
        }
        
        if (isset($this->error['error_merge_language'])) {
                $data['error_merge_language'] = $this->error['error_merge_language'];
        } else {
                $data['error_merge_language'] = '';
        }
        
        $data['entry_merge_sds_file'] = $this->language->get('entry_merge_sds_file');
        $data['entry_merge_sds_language'] = $this->language->get('entry_merge_sds_language');
        $data['text_form'] = $this->language->get('heading_title_merge_sds');
        $data['text_import'] = $this->language->get('text_import');
        
        $data['language_technicals'] = $this->model_catalog_import->getAllLanguageTechnicals();
        
        $data['action'] = $this->url->link('catalog/import/merge', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['merge_sample'] = $this->url->link('catalog/import/merge_sample', 'user_token=' . $this->session->data['user_token'], TRUE);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/merge', $data));
    }
    
    public function catalog() {
            $this->load->language('catalog/import');

            $this->document->setTitle($this->language->get('text_catalog'));

            $this->load->model('catalog/import');

            $data['heading_title'] = $this->language->get('text_catalog');

            $data['text_form'] = $this->language->get('text_catalog');
            $data['text_import'] = $this->language->get('text_catalog');
            $data['text_valid_product_id'] = sprintf($this->language->get('text_valid_product_id'), $this->model_catalog_import->getLastInsertId());

            $data['entry_catalog_file'] = $this->language->get('entry_catalog_file');
            $data['button_import'] = $this->language->get('button_import');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_catalog'),
                'href' => $this->url->link('catalog/import/catalog', 'user_token=' . $this->session->data['user_token'], TRUE)
            );
        
            $data['action'] = $this->url->link('catalog/import/catalog', 'user_token=' . $this->session->data['user_token'], TRUE);
        
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
                    if ((isset( $this->request->files['catalog'] )) && (is_uploaded_file($this->request->files['catalog']['tmp_name']))) {
                            $file = $this->request->files['catalog']['tmp_name'];
                            $incremental = true;
                            if ($this->model_catalog_import->uploadCatalog($file,$incremental)==true) {
                                    $this->session->data['success'] = $this->language->get('text_success_import');
                                    $this->response->redirect($this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'], $this->ssl));
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

            if (isset($this->session->data['warning'])) {
                $data['warning'] = $this->session->data['warning'];

                unset($this->session->data['warning']);
            } else {
                $data['warning'] = '';
            }

            if (isset($this->session->data['success'])) {
                $data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $data['success'] = '';
            }

            if (isset($this->error['warning'])) {
                    $data['error_warning'] = $this->error['warning'];
            } else {
                    $data['error_warning'] = '';
            }
            
            $data['user_token'] = $this->session->data['user_token'];

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
        
            $this->response->setOutput($this->load->view('catalog/import', $data));
    }    
    protected function validateUploadForm() {
            if (!$this->user->hasPermission('modify', 'catalog/import')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }
            if (!isset($this->request->files['catalog']['name'])) {
                    if (isset($this->error['warning'])) {
                            $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
                    } else {
                            $this->error['warning'] = $this->language->get( 'error_upload_name' );
                    }
            } else {
                    $ext = strtolower(pathinfo($this->request->files['catalog']['name'], PATHINFO_EXTENSION));
                    if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
                            if (isset($this->error['warning'])) {
                                    $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
                            } else {
                                    $this->error['warning'] = $this->language->get( 'error_upload_ext' );
                            }
                    }
            }
            
            return !$this->error;
    }
    protected function validateUploadGrouped() {
            if (!$this->user->hasPermission('modify', 'catalog/import')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }
            if (!isset($this->request->files['Grouped']['name'])) {
                    if (isset($this->error['warning'])) {
                            $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
                    } else {
                            $this->error['warning'] = $this->language->get( 'error_upload_name' );
                    }
            } else {
                    $ext = strtolower(pathinfo($this->request->files['Grouped']['name'], PATHINFO_EXTENSION));
                    if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
                            if (isset($this->error['warning'])) {
                                    $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
                            } else {
                                    $this->error['warning'] = $this->language->get( 'error_upload_ext' );
                            }
                    }
            }
            
            return !$this->error;
    }
    protected function validateMergeForm() {
            if (!$this->user->hasPermission('modify', 'catalog/import')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }
            
            if (!isset($this->request->files['merge']['name'])) {
                    if (isset($this->error['error_file'])) {
                            $this->error['error_file'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
                    } else {
                            $this->error['error_file'] = $this->language->get( 'error_upload_name' );
                    }
            } else {
                    $ext = strtolower(pathinfo($this->request->files['merge']['name'], PATHINFO_EXTENSION));
                    if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
                            if (isset($this->error['error_file'])) {
                                    $this->error['error_file'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
                            } else {
                                    $this->error['error_file'] = $this->language->get( 'error_upload_ext' );
                            }
                    }
            }
            
            if (!isset($this->request->post['language']) || empty($this->request->post['language']) || !$this->model_catalog_import->getLanguageTechnicalIdByLanguageTechnicalID($this->request->post['language'])) {
                   $this->error['error_merge_language'] = $this->language->get( 'error_merge_language' );
            }
            
            return !$this->error;
    } 
    public function update_meta() { 
        $this->load->model('catalog/import');

        $this->model_catalog_import->update_meta();
        
        $this->load->language('catalog/import');

        $this->document->setTitle($this->language->get('heading_title'));        

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = $this->language->get('text_product');
        $data['text_import'] = $this->language->get('text_import');

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );

        $data['success'] = $this->language->get('text_success_meta_update');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/import', $data));
    }
    
    public function export_catalog() {
            $this->load->language( 'catalog/import' );
            $this->document->setTitle($this->language->get('heading_title'));
            $this->load->model( 'catalog/import' );
            $this->model_catalog_import->downloadCatalogs();
            $this->response->redirect( $this->url->link( 'catalog/import/catalog', 'user_token='.$this->request->get['user_token'], TRUE) );
    }
    
    public function export_grouped_product() {
            $this->load->language( 'catalog/import' );
            $this->document->setTitle($this->language->get('heading_title'));
            $this->load->model( 'catalog/import' );
            $this->model_catalog_import->export_grouped_product();
            $this->response->redirect( $this->url->link( 'catalog/import/gpProductUpload', 'user_token='.$this->request->get['user_token'], TRUE) );
    }
    
    public function merge_sample() {
            $this->load->language( 'catalog/import' );
            $this->document->setTitle($this->language->get('heading_title'));
            $this->load->model( 'catalog/import' );
            $this->model_catalog_import->downloadMergeSample();
            $this->response->redirect( $this->url->link( 'catalog/import', 'user_token='.$this->request->get['user_token'], TRUE) );
    }
    
    public function gpProductUpload() {
        $this->load->language('catalog/import');

            $this->document->setTitle($this->language->get('text_import_grouped_product'));

            $this->load->model('catalog/import');

            $data['heading_title'] = $this->language->get('text_import_grouped_product');

            $data['text_form'] = $this->language->get('text_import_grouped_product');
            $data['text_import'] = $this->language->get('text_import_grouped_product');
            $data['text_valid_grouped_product_id'] = sprintf($this->language->get('text_valid_grouped_product_id'), $this->model_catalog_import->getLastInsertId());

            $data['entry_catalog_file'] = $this->language->get('entry_catalog_file');
            $data['button_import'] = $this->language->get('button_import');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_import_grouped_product'),
                'href' => $this->url->link('catalog/import/gpProductUpload', 'user_token=' . $this->session->data['user_token'], TRUE)
            );
        
            $data['action'] = $this->url->link('catalog/import/gpProductUpload', 'user_token=' . $this->session->data['user_token'], TRUE);

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadGrouped())) {
                    if ((isset( $this->request->files['Grouped'] )) && (is_uploaded_file($this->request->files['Grouped']['tmp_name']))) {
                            $file = $this->request->files['Grouped']['tmp_name'];
                            $incremental = true;
                            if ($this->model_catalog_import->uploadGroupedProduct($file,$incremental)==true) {
                                    $this->session->data['success'] = $this->language->get('text_success_import');
                                    $this->response->redirect($this->url->link('catalog/import/gpProductUpload', 'user_token=' . $this->session->data['user_token'], $this->ssl));
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

            if (isset($this->session->data['warning'])) {
                $data['warning'] = $this->session->data['warning'];

                unset($this->session->data['warning']);
            } else {
                $data['warning'] = '';
            }

            if (isset($this->session->data['success'])) {
                $data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $data['success'] = '';
            }

            if (isset($this->error['warning'])) {
                    $data['error_warning'] = $this->error['warning'];
            } else {
                    $data['error_warning'] = '';
            }
            
            $data['user_token'] = $this->session->data['user_token'];

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
        
            $this->response->setOutput($this->load->view('catalog/import_gpproduct', $data));
    }
}
