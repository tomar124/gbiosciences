<?php
class ControllerCommonFileManager extends Controller {

            private $ROOT_DIRECTORY = '';

            public function __construct($registry) {
                    parent:: __construct($registry);
            }
            
	public function index() {
		$this->load->language('common/filemanager');

		// Find which protocol to use to pass the full image link back
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $this->request->get['filter_name']), '/');
		} else {
			$filter_name = '';
		}

		// Make sure we have the correct directory
		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			
            $directory = rtrim($this->ROOT_DIRECTORY . str_replace('*', '', $this->request->get['directory']), '/') . '/';
            
		} else {
			
            $directory = $this->ROOT_DIRECTORY;
            
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		
            $this->load->model('tool/image');

            //check if directory exists or not
            if (!empty($directory) && !$this->s3->getObject($directory, '', '', true)) {
                $data['error_warning'] = $this->language->get('error_directory');
            }
            
            // Get data
            $allObjects= $this->s3->getAllObjects($directory, $filter_name);

            $directories = $allObjects['folders'];
            $files = $allObjects['files'];

            // Merge directories and files
            $images = array_merge($directories, $files);

            // Get total number of files and directories
            $image_total = count($images);

            // Split the array based on current page number and max number of items per page of 10
            $images = array_splice($images, ($page - 1) * 16, 16);

            foreach ($images as $image) {
                    if (in_array($image, $directories)) {
                            $name = str_split(basename($image), 14);

                            $url = '';

                            if (isset($this->request->get['target'])) {
                                    $url .= '&target=' . $this->request->get['target'];
                            }

                            if (isset($this->request->get['thumb'])) {
                                    $url .= '&thumb=' . $this->request->get['thumb'];
                            }

                            $data['images'][] = array(
                                    'thumb' => '',
                                    'name'  => implode(' ', $name),
                                    'type'  => 'directory',
                                    'path'  => $directory . $image,
                                    'href'  => $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . '&directory=' . urlencode(str_replace($this->ROOT_DIRECTORY, '', $directory) . $image) . $url, true)
                            );
                    } elseif (in_array($image, $files)) {
                            $name = str_split(basename($image), 14);

                            // Find which protocol to use to pass the full image link back
                            if ($this->request->server['HTTPS']) {
                                    $server = HTTPS_CATALOG;
                            } else {
                                    $server = HTTP_CATALOG;
                            }

                            $path = $directory . $image;

                            $data['images'][] = array(
                                    'thumb' => (strrchr($image, '.') != ".pdf") ? $this->model_tool_image->resize($path, 100, 100) : $this->model_tool_image->resize('pdf_icon.png', 100, 100),
                                    'name'  => implode(' ', $name),
                                    'type'  => 'image',
                                    'path'  => $path,
                                    'href'  => $server . 'image/' . $path
                            );
                    }
            }
            
		$data['user_token'] = $this->session->data['user_token'];

		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			$data['directory'] = urlencode($this->request->get['directory']);
		} else {
			$data['directory'] = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($this->request->get['target'])) {
			$data['target'] = $this->request->get['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->get['thumb'])) {
			$data['thumb'] = $this->request->get['thumb'];
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$url = '';

		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			$pos = strrpos($this->request->get['directory'], '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
			}
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['parent'] = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Refresh
		$url = '';

		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			$url .= '&directory=' . urlencode($this->request->get['directory']);
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['refresh'] = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$url = '';

		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			$url .= '&directory=' . urlencode(html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 16;
		$pagination->url = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('common/filemanager', $data));
	}

	public function upload() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			
            $directory = rtrim($this->ROOT_DIRECTORY . str_replace('*', '', $this->request->get['directory']), '/') . '/';
            
		} else {
			
            $directory = $this->ROOT_DIRECTORY;
            
		}

		
            /*// Get infor
            $info = $this->s3->getObject($directory, '', '', true);

            // Check its a directory
            if (!$info) {
                    $json['error'] = $this->language->get('error_directory');
            }*/
            

		if (!$json) {
			// Check if multiple files are uploaded or just one
			$files = array();

			if (!empty($this->request->files['file']['name']) && is_array($this->request->files['file']['name'])) {
				foreach (array_keys($this->request->files['file']['name']) as $key) {
					$files[] = array(
						'name'     => $this->request->files['file']['name'][$key],
						'type'     => $this->request->files['file']['type'][$key],
						'tmp_name' => $this->request->files['file']['tmp_name'][$key],
						'error'    => $this->request->files['file']['error'][$key],
						'size'     => $this->request->files['file']['size'][$key]
					);
				}
			}

			foreach ($files as $file) {
				if (is_file($file['tmp_name'])) {
					// Sanitize the filename
					$filename = basename(html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8'));

					// Validate the filename length
					if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
						$json['error'] = $this->language->get('error_filename');
					}

					// Allowed file extension types
					$allowed = array(
						'jpg',
						'jpeg',
						'gif',
						
            'png',
            'pdf',
            'PDF'
            
					);

					if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Allowed file mime types
					$allowed = array(
						'image/jpeg',
						'image/pjpeg',
						'image/png',
						'image/x-png',
						
            'image/gif',
            'application/pdf'
            
					);

					if (!in_array($file['type'], $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Return any upload error
					if ($file['error'] != UPLOAD_ERR_OK) {
						$json['error'] = $this->language->get('error_upload_' . $file['error']);
					}
				} else {
					$json['error'] = $this->language->get('error_upload');
				}

				if (!$json) {
					
            $this->s3->putObject($directory, $file, $filename);

            $info = pathinfo($file['name']);

            $filename = $info['filename'] . '-100x100.' . $info['extension'];
            $image = new Image($file['tmp_name']);
            $image->resize(100, 100);
            $image->save(DIR_IMAGE . $filename);

            $this->s3->putObject('cache/' . $directory, [
                'type' => $image->getMime(),
                'tmp_name' => DIR_IMAGE . $filename
            ], $filename);

            unlink(DIR_IMAGE . $filename);
            
				}
			}
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_uploaded');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function folder() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		
            if (isset($this->request->get['directory']) && !empty($this->request->get['directory'])) {
            
			
            $directory = rtrim($this->ROOT_DIRECTORY . str_replace('*', '', $this->request->get['directory']), '/') . '/';
            
		} else {
			
            $directory = $this->ROOT_DIRECTORY;
            
		}

		
            /*// Get infor
            $info = $this->s3->getObject($directory, '', '', true);

            // Check its a directory
            if (!$info) {
                    $json['error'] = $this->language->get('error_directory');
            }*/
            

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			// Sanitize the folder name
			
            $folder = basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8')) . '/';

            $folder_info = $this->s3->getObject($directory . $folder, '', '', true);

            // Check if directory already exists or not
            if ($folder_info) {
                    $json['error'] = $this->language->get('error_exists');
            }
            
		}

		if (!isset($json['error'])) {
			
            $this->s3->createDirectoryObject($directory, $folder);
            
			$json['success'] = $this->language->get('text_directory');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['path'])) {
			$paths = $this->request->post['path'];
		} else {
			$paths = array();
		}

		
            $this->s3->deleteObjects($paths);

            $json['success'] = $this->language->get('text_delete');
            

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}