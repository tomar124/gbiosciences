<?php
/* * * Using dompdf for generating pdf files ** */
require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
/* * * Ending dompdf ** */

class ControllerCatalogGmcSpecialProduct extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('catalog/gmc_special_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/gmc_special_product');

		$this->getList();
	}

	public function add()
	{
		$this->load->language('catalog/gmc_special_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/gmc_special_product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gmc_special_product->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_gpt'])) {
				$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('catalog/gmc_special_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/gmc_special_product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gmc_special_product->editProduct($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_gpt'])) {
				$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/gmc_special_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/gmc_special_product');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_gmc_special_product->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_gpt'])) {
				$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy()
	{
		$this->load->language('catalog/gmc_special_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/gmc_special_product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_gmc_special_product->copyProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_gpt'])) {
				$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList()
	{

		if (isset($this->request->get['filter_gpt'])) {
			$filter_gpt = $this->request->get['filter_gpt'];
		} else {
			$filter_gpt = null;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/gmc_special_product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['add_gp_grouped'] = $this->url->link('catalog/gmc_special_product/add', 'user_token=' . $this->session->data['user_token'] . '&is_gp=grouped', true);
		$data['export'] = $this->url->link('catalog/gmc_special_product/export', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['copy'] = $this->url->link('catalog/gmc_special_product/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/gmc_special_product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['upload'] = $this->url->link('catalog/gmc_special_product/upload', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
		$data['download'] = $this->url->link('catalog/gmc_special_product/download', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
		$data['datasheets'] = $this->url->link('catalog/gmc_special_product/datasheets', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);

		$data['products'] = array();

		$filter_data = array(

			'filter_gpt'      => $filter_gpt,

			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		// $product_total = $this->model_catalog_gmc_special_product->getTotalProducts($filter_data);

		$product_total = $this->model_catalog_gmc_special_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_gmc_special_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_gmc_special_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}


			if ($is_gp = $this->model_catalog_gmc_special_product->getGroupedProductGrouped($result['product_id'])) {
				$data['is_gp_product'] = true;

				if ($is_gp['gp_price_min']) {
					$result['price'] .= '<br />From: ' . $is_gp['gp_price_min'];
				}

				if ($is_gp['gp_price_max']) {
					$result['price'] .= '<br />To: ' . $is_gp['gp_price_max'];
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/gmc_special_product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['text_valid_product_id'] = sprintf($this->language->get('text_valid_product_id'), $this->model_catalog_gmc_special_product->getLastInsertId());

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

		$data['export_catalog'] = $this->url->link('catalog/import/catalog', 'user_token=' . $this->request->get['user_token'], TRUE);

		$data['sort_product_id'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.product_id' . $url, true);

		$data['sort_name'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_gpt'])) {
			$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$this->load->model('catalog/category');
                
        $data['categories'] = $this->model_catalog_category->getCategories(array('datatables_design' => 1));

		// echo "<pre>"; print_r($data['categories']); die;

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/gmc_special_product_list', $data));
	}

	protected function getForm()
	{

		// echo "<pre>"; print_r($this->model_catalog_gmc_special_product->getGroupedProductGrouped($this->request->get['product_id'])); die;

		if (isset($this->request->get['is_gp']) && $this->request->get['is_gp'] == 'grouped' || isset($this->request->post['is_gp']) && $this->request->post['is_gp'] == 'grouped' || isset($this->request->get['product_id']) && $this->model_catalog_gmc_special_product->getGroupedProductGrouped($this->request->get['product_id'])) {
			$data['is_gp'] = 'grouped';
			$this->load->language('catalog/gp_grouped');

			// GP Parent data
			if (isset($this->request->get['product_id'])) {
				$gp_data = $this->model_catalog_gmc_special_product->getGroupedProductGrouped($this->request->get['product_id']);
			}

			// GP Parent available layouts

			if (isset($this->request->post['gp_price_min'])) {
				$data['gp_price_min'] = $this->request->post['gp_price_min'];
			} else if (isset($this->request->get['product_id'])) {
				$data['gp_price_min'] = $gp_data['gp_price_min'];
			} else {
				$data['gp_price_min'] = '';
			}

			if (isset($this->request->post['gp_price_max'])) {
				$data['gp_price_max'] = $this->request->post['gp_price_max'];
			} else if (isset($this->request->get['product_id'])) {
				$data['gp_price_max'] = $gp_data['gp_price_max'];
			} else {
				$data['gp_price_max'] = '';
			}

			// GP Options and Childs
			if (isset($this->request->post['gp_child'])) {
				$gps = $this->request->post['gp_child'];
			} else if (isset($this->request->get['product_id'])) {
				$gps = $this->model_catalog_gmc_special_product->getGroupedProductGroupedChilds($this->request->get['product_id']);
			} else {
				$gps = array();
			}

			$data['gps'] = array();

			
			// echo "<pre>"; print_r($gps); 

			foreach ($gps as $child_id => $child) {
				$child_info = $this->model_catalog_gmc_special_product->getProduct($child_id);

				// echo "<pre>"; print_r($child_id); die;

				if ($child_info) {
					if ($this->model_catalog_gmc_special_product->getProductOptions($child_info['product_id'])) {
						$child_has_options = $this->language->get('text_gp_child_has_options');
					} else {
						$child_has_options = '';
					}

					$child_info_special = false;
					$child_specials = $this->model_catalog_gmc_special_product->getProductSpecials($child_info['product_id']);
					foreach ($child_specials as $child_special) {
						if (($child_special['date_start'] == '0000-00-00' || $child_special['date_start'] < date('Y-m-d')) && ($child_special['date_end'] == '0000-00-00' || $child_special['date_end'] > date('Y-m-d'))) {
							$child_info_special = $child_special['price'];
							break;
						}
					}

					$data['gps'][] = array(
						'child_id' => $child_info['product_id'],
						'name' => $child_info['name'] . $child_has_options,
						'model' => $child_info['model'],
						'price' => $child_info['price'],
						'special' => $child_info_special,
						'href' => $this->url->link('catalog/gmc_special_product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $child_id, 'SSL'),
						'child_sort_order' => $child['child_sort_order']
					);
				}
			}

			
			// echo "<pre>"; print_r($data['gps']); die;
		}
		$data['entry_gp_parent'] = 'Parent Product';
		$data['help_gp_parent'] = 'Prevent to see this product individually in order to go in a Grouped Product.';
		$data['entry_cart_comment'] = $this->language->get('entry_cart_comment');
		$data['entry_size'] = $this->language->get('entry_size');

		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_gpt'])) {
			$url .= '&filter_gpt=' . urlencode(html_entity_decode($this->request->get['filter_gpt'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

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
			'href' => $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('catalog/gmc_special_product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/gmc_special_product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_gmc_special_product->getProduct($this->request->get['product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();


		if (isset($this->request->post['reference'])) {
			$data['reference'] = $this->request->post['reference'];
		} else if (isset($product_info['reference'])) {
			$data['reference'] = $product_info['reference'];
		} else {
			$data['reference'] = '';
		}

		if (isset($this->request->post['cart_comment'])) {
			$data['cart_comment'] = $this->request->post['cart_comment'];
		} else if (isset($product_info['reference'])) {
			$data['cart_comment'] = $product_info['cart_comment'];
		} else {
			$data['cart_comment'] = '';
		}

		if (isset($this->request->post['size'])) {
			$data['size'] = $this->request->post['size'];
		} else if (isset($product_info['size'])) {
			$data['size'] = $product_info['size'];
		} else {
			$data['size'] = '';
		}

		if (isset($this->request->post['shipping_code'])) {
			$data['shipping_code'] = $this->request->post['shipping_code'];
		} else if (isset($product_info['shipping_code'])) {
			$data['shipping_code'] = $product_info['shipping_code'];
		} else {
			$data['shipping_code'] = '';
		}

		if (isset($this->request->post['hazardous'])) {
			$data['hazardous'] = $this->request->post['hazardous'];
		} else if (isset($product_info['hazardous'])) {
			$data['hazardous'] = $product_info['hazardous'];
		} else {
			$data['hazardous'] = '';
		}

		if (isset($this->request->post['is_ground_hazmat'])) {
			$data['is_ground_hazmat'] = $this->request->post['is_ground_hazmat'];
		} else if (isset($product_info['is_ground_hazmat'])) {
			$data['is_ground_hazmat'] = $product_info['is_ground_hazmat'];
		} else {
			$data['is_ground_hazmat'] = '';
		}

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_gmc_special_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_gmc_special_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info)) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_catalog_gmc_special_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_gmc_special_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['product_filter'])) {
			$filters = $this->request->post['product_filter'];
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_catalog_gmc_special_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_catalog_gmc_special_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_catalog_gmc_special_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_catalog_gmc_special_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_catalog_gmc_special_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}


		if (isset($this->request->post['alt_text'])) {
			$data['alt_text'] = $this->request->post['alt_text'];
		} elseif (!empty($product_info)) {
			$data['alt_text'] = $product_info['alt_text'];
		} else {
			$data['alt_text'] = '';
		}

		if (isset($this->request->post['caption'])) {
			$data['caption'] = $this->request->post['caption'];
		} elseif (!empty($product_info)) {
			$data['caption'] = $product_info['caption'];
		} else {
			$data['caption'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Referennces
		if (isset($this->request->post['product_references'])) {
			$product_references = $this->request->post['product_references'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_references = $this->model_catalog_gmc_special_product->getProductReferences($this->request->get['product_id']);
		} else {
			$product_references = array();
		}

		$data['product_references'] = $product_references;


		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_catalog_gmc_special_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),

				'sort_order'    => $product_image['sort_order'],
				'caption' => $product_image['image_caption'],
				'alt_text'      => $product_image['alt_text'],
			);
		}

		if (isset($product_info)) {
			$sds = $this->model_catalog_gmc_special_product->getProductsSds($product_info['product_id']);
		}

		$languageTechnical = $this->model_catalog_gmc_special_product->getLanguageTechnicalForProducts();

		$data['languageTechnical'] = $data['sds'] = array();

		foreach ($languageTechnical as $LT) {
			$data['languageTechnical'][] = array(
				'language_technical_id' => $LT['language_technical_id'],
				'name' => $LT['name'] . (($LT['is_default']) ? $this->language->get('text_default_language') : null),
				'sort_order' => $LT['sort_order']
			);

			if (isset($this->request->post['sds']) && $this->request->post['sds']) {
				$sds = $this->request->post['sds'];
			} else if (!isset($product_info)) {
				$sds = array();
			}

			if (isset($sds[$LT['language_technical_id']]) && is_file(DIR_IMAGE . $sds[$LT['language_technical_id']]['pdf'])) {
				$data['sds'][$LT['language_technical_id']] = array(
					'thumb' => $this->model_tool_image->resize('pdf_icon.png', 100, 100),
					'pdf' => $sds[$LT['language_technical_id']]['pdf'],
					'sort_order' => $sds[$LT['language_technical_id']]['sort_order']
				);
			} else {
				$data['sds'][$LT['language_technical_id']] = array(
					'thumb' => $this->model_tool_image->resize('no_image.png', 100, 100),
					'pdf' => '',
					'sort_order' => ''
				);
			}
		}

		if (isset($product_info)) {
			$technicals = $this->model_catalog_gmc_special_product->getProductsTechnicals($product_info['product_id']);
		}

		if (isset($this->request->post['technical']) && $this->request->post['technical']) {
			foreach ($this->request->post['technical'] as $technical) {
				$data['technicals'][] = array(
					'title' => $technical['title'],
					'link' => $technical['link'],
					'description' => $technical['description']
				);
			}
		} else if (!empty($technicals)) {
			foreach ($technicals as $technical) {
				$data['technicals'][] = array(
					'title' => $technical['title'],
					'link' => $technical['link'],
					'description' => $technical['description']
				);
			}
		} else {
			$data['technicals'] = array();
		}

		//coa

		if (isset($product_info)) {
			$coas = $this->model_catalog_gmc_special_product->getProductsCoa($product_info['product_id']);
		}

		if (isset($this->request->post['coa']) && $this->request->post['coa']) {
			foreach ($this->request->post['coa'] as $coa) {
				if (is_file(DIR_IMAGE . $coa['pdf'])) {
					$data['coas'][] = array(
						'thumb' => $this->model_tool_image->resize('pdf_icon.png', 100, 100),
						'pdf' => $coa['pdf'],
						'description' => $coa['description'],
						'sort_order' => $coa['sort_order']
					);
				}
			}
		} else if (!empty($coas)) {
			foreach ($coas as $coa) {
				if (is_file(DIR_IMAGE . $coa['pdf'])) {
					$data['coas'][] = array(
						'thumb' => $this->model_tool_image->resize('pdf_icon.png', 100, 100),
						'pdf' => $coa['pdf'],
						'description' => $coa['description'],
						'sort_order' => $coa['sort_order']
					);
				}
			}
		} else {
			$data['coas'] = array();
		}

		//Protocol
		if (isset($product_info)) {
			$protocol = $this->model_catalog_gmc_special_product->getProductsProtocol($product_info['product_id']);
		}

		if (isset($this->request->post['protocol'])) {
			$data['protocol'] = $this->request->post['protocol'];
		} else if (!empty($protocol)) {
			$data['protocol'] = $protocol['pdf'];
		} else {
			$data['protocol'] = '';
		}

		if (isset($this->request->post['protocol']) && is_file(DIR_IMAGE . $this->request->post['protocol'])) {
			$data['thumb_protocol'] = $this->model_tool_image->resize('pdf_icon.png', 100, 100);
		} else if (!empty($protocol) && is_file(DIR_IMAGE . $protocol['pdf'])) {
			$data['thumb_protocol'] = $this->model_tool_image->resize('pdf_icon.png', 100, 100);
		} else {
			$data['thumb_protocol'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_catalog_gmc_special_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_gmc_special_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_gmc_special_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_reward'])) {
			$data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_catalog_gmc_special_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->post['product_seo_url'])) {
			$data['product_seo_url'] = $this->request->post['product_seo_url'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_seo_url'] = $this->model_catalog_gmc_special_product->getProductSeoUrls($this->request->get['product_id']);
		} else {
			$data['product_seo_url'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_catalog_gmc_special_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// echo "<pre>"; print_r($data); die;

		$this->response->setOutput($this->load->view('catalog/gmc_special_product_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if (utf8_strlen($this->request->post['model']) < 1) {

				$this->error['model'] = $this->language->get('error_model');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete()
	{

		if (isset($this->request->get['filter_gpt'])) {
			$filter_gpt = $this->request->get['filter_gpt'];
		} else {
			$filter_gpt = '';
		}

		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/gmc_special_product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(

				'filter_gpt'      => $filter_gpt,

				'filter_name'  => $filter_name,

				'filter_gpt'   => $filter_gpt,

				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_gmc_special_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_gmc_special_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function export()
	{
		$this->load->model('catalog/import');

		$this->model_catalog_import->downloadGroupProucts();
	}

	public function download()
	{
		$this->load->language('catalog/gmc_special_product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDownloadForm()) {
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('catalog/gmc_special_product');

			// THIS WAS CASE OF SPECIAL PRODUCT
			// $this->model_catalog_gmc_special_product->download((int)$this->request->post['category'], $this->request->post['option']);

			$this->model_catalog_gmc_special_product->download_gbm_gpproduct((int)$this->request->post['category'], $this->request->post['option']);
		} else {
			$this->session->data['warning'] = $this->language->get('error_warning');
		}

		if (isset($this->error['category'])) {
			$this->session->data['category'] = $this->error['category'];
		}

		if (isset($this->error['option'])) {
			$this->session->data['option'] = $this->error['option'];
		}

		$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->request->get['user_token'], TRUE));
	}

	public function datasheets()
	{
		ini_set('max_execution_time', 3600000);

		$json = array();

		$this->load->language('catalog/gmc_special_product');
		$this->load->model('catalog/gmc_special_product');

		$products = $this->model_catalog_gmc_special_product->getProducts();

		foreach ($products as $product_info) {
			$create = 0;

			if ($this->request->get['update'] == 1) {
				$create = 1;
			} elseif ($this->request->get['update'] == 0) {
				if (!file_exists(DIR_IMAGE . 'pdfs/datasheets/' . $product_info['name'] . '.pdf')) {
					$create = 1;
				}
			}

			if ($create && !empty($product_info['name'])) {
				$product_info['options'] = array();

				$product_options = $this->model_catalog_gmc_special_product->getProductOptionsForDatasheets($product_info['product_id']);

				foreach ($product_options as $product_option) {
					$product_info['options'][$product_option['name']] = '';
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$option_name = strstr($product_option_value['name'], '(', TRUE);
						$text = trim(($option_name != FALSE) ? $option_name : $product_option_value['name']);
						$product_info['options'][$product_option['name']][] = $text;
					}
					$product_info['options'][$product_option['name']] = implode(', ', $product_info['options'][$product_option['name']]);
				}

				$product_info['attributes'] = $this->model_catalog_gmc_special_product->getProductAttributesForDatasheets($product_info['product_id']);
				$product_info['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
				$product_info['store_url'] = $this->config->get('config_url');
				$product_info['store_name'] = $this->config->get('config_name');
				$product_info['store_title'] = $this->config->get('config_title');
				$product_info['logo'] = DIR_IMAGE . $this->config->get('config_logo');

				$html = $this->load->view('catalog/gmc_special_product_datasheets', $product_info);

				$dompdf = new Dompdf();
				$dompdf->set_option('enable_html5_parser', TRUE);
				$dompdf->loadHtml($html, 'UTF-8');
				$dompdf->setPaper('A4', 'portrait');
				$dompdf->render();

				$output = $dompdf->output();
				$file_to_save = DIR_IMAGE . 'pdfs/datasheets/' . $product_info['name'] . '.pdf';
				file_put_contents($file_to_save, $output);
			}
		}

		if ($this->request->get['update'] == 1) {
			$json['success'] = $this->language->get('text_datasheets_update_success');
		} else {
			$json['success'] = $this->language->get('text_datasheets_missing_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDownloadForm()
	{
		if (!$this->user->hasPermission('access', 'catalog/gmc_special_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		if (!isset($this->request->post['category']) || empty($this->request->post['category'])) {
			$this->error['category'] = $this->language->get('error_download_category');
		}

		if (!isset($this->request->post['option']) || empty($this->request->post['option'])) {
			$this->error['option'] = $this->language->get('error_product_download_option');
		}

		return !$this->error;
	}

	// gpProductUpload used for grouped product.
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

	public function upload()
	{
		$this->load->language('catalog/gmc_special_product');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/gmc_special_product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
			if ((isset($this->request->files['Grouped'])) && (is_uploaded_file($this->request->files['Grouped']['tmp_name']))) {
				$file = $this->request->files['Grouped']['tmp_name'];
				$incremental = true;

				// if ($this->model_catalog_gmc_special_product->upload($file, $incremental) == true) {
				if ($this->model_catalog_gmc_special_product->uploadGroupedProduct($file, $incremental) == true) {
					$this->session->data['success'] = $this->language->get('text_success_import');
					$this->response->redirect($this->url->link('catalog/gmc_special_product', 'user_token=' . $this->session->data['user_token'], $this->ssl));
				} else {
					$this->error['warning'] = $this->language->get('error_upload');
					if (defined('VERSION')) {
						$this->error['warning'] .= "<br />\n" . $this->language->get('text_log_details_2_0_x');
					} else {
						$this->error['warning'] .= "<br />\n" . $this->language->get('text_log_details');
					}
				}
			}
		}

		$this->getList();
	}

	protected function validateUploadForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/gmc_special_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->files['Grouped']['name'])) {
			if (isset($this->error['warning'])) {
				$this->error['warning'] .= "<br /\n" . $this->language->get('error_upload_name');
			} else {
				$this->error['warning'] = $this->language->get('error_upload_name');
			}
		} else {
			$ext = strtolower(pathinfo($this->request->files['Grouped']['name'], PATHINFO_EXTENSION));
			if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get('error_upload_ext');
				} else {
					$this->error['warning'] = $this->language->get('error_upload_ext');
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function export_catalog() {
		$this->load->language( 'catalog/import' );
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model( 'catalog/import' );
		$this->model_catalog_import->downloadCatalogs();
		$this->response->redirect( $this->url->link( 'catalog/gmc_special_product', 'user_token='.$this->request->get['user_token'], TRUE) );
	}
}
