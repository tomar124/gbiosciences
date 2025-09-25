<?php
class ControllerS3Sync extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('s3/sync');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('s3/sync', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['sync'] = $this->url->link('s3/sync', 'user_token=' . $this->session->data['user_token'] . '&is_sync=1', true);

                if (isset($this->request->get['is_sync']) && $this->request->get['is_sync'] == 1) {
                        $source_directory_to_sync = DIR_IMAGE . $this->config->get('config_s3_source_directory_to_sync');
                        $destination_directory_to_sync = "s3://{$this->config->get('config_s3_bucket')}/{$this->config->get('config_s3_destination_directory_to_sync')}";

                        //$script = "aws s3 sync $source_directory_to_sync $destination_directory_to_sync"; // commented Halim
						$aws = '"C:\\Program Files\\Amazon\\AWSCLI\\bin\\aws.exe"';
						$script = "$aws s3 sync $source_directory_to_sync $destination_directory_to_sync 2>&1";

                        $output = shell_exec($script);
                        echo "<pre>$output</pre>";
                        if (!empty($output)) {
                                $data['output'] = $output;
                        } else {
                                $data['output'] = $this->language->get('text_already_synced');
                        }
                } else {
                        $data['output'] = '';
                }

                $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('s3/sync', $data));
	}
}