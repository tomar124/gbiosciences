<?php
class ControllerApiSitemap extends Controller {
	private $startStringSitemap;
        private $maxUrlCount;
        private $fileCount;
        private $directory;
        private $initialHit;
        private $urlSet;

        public function __construct($registry) {
                parent::__construct($registry);

                $this->startStringSitemap = '<?xml version="1.0" encoding="UTF-8"?>'
                    . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
                    . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
                $this->endStringSitemap = '</urlset>';
                $this->maxUrlCount = $this->config->get('feed_google_sitemap_pro_max_url_in_single_sitemap');
                $this->fileCount = 1;
                $this->directory = 'sitemaps';
                $this->initialHit = true;
                $this->urlSet = array();
        }

        public function generateSitemap() {
                $this->load->language('api/sitemap');

                $json = array();

                if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (!$this->config->get('feed_google_sitemap_pro_status')) {
                        $json['error'] = $this->language->get('error_status');
                } else {
                        ini_set('max_execution_time', -1);
                        ini_set('memory_limit', '4096M');

                        if ($this->config->get('feed_google_sitemap_pro_products')) {
                                $this->load->model('catalog/product');
                                $this->load->model('tool/image');

                                $groupProducts = $this->model_catalog_product->getGroupProducts();
                                $specialProducts = $this->model_catalog_product->getSpecialProducts();

                                $products = array_merge($groupProducts, $specialProducts);

                                foreach ($products as $product) {
                                        $this->urlSet[] = array(
                                                'loc' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                                                'lastmod' => ($product['date_modified'] !== '0000-00-00 00:00:00' ? date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) : $product['date_added'] !== '0000-00-00 00:00:00' ? date('Y-m-d\TH:i:sP', strtotime($product['date_added'])) : date('Y-m-d\TH:i:sP')),
                                                'priority' => '1.0',
                                                'image' => $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
                                                'image:caption' => ($product['caption'] !== '' ?
                                                                    htmlspecialchars(htmlentities($product['caption'])) :
                                                                    htmlspecialchars(htmlentities($product['name']))),
                                                'image:title' => ($product['alt_text'] !== '' ?
                                                                    htmlspecialchars(htmlentities($product['alt_text'])) :
                                                                    htmlspecialchars(htmlentities($product['name'])))
                                        );
                                }
                        }

                        if ($this->config->get('feed_google_sitemap_pro_categories')) {
                                $this->load->model('catalog/category');

                                $this->getCategories(0);
                        }

                        if ($this->config->get('feed_google_sitemap_pro_informations')) {
                                $this->load->model('catalog/information');

                                $informations = $this->model_catalog_information->getInformations();

                                foreach ($informations as $information) {
                                        $this->urlSet[] = array(
                                                'loc' => $this->url->link('information/information', 'information_id=' . $information['information_id']),
                                                'priority' => '0.5'
                                        );
                                }
                        }

                        if ($this->config->get('feed_google_sitemap_pro_protocols')) {
                                $documentProtocol = $this->model_catalog_product->getAllProtocol();

                                if ($documentProtocol) {
                                        foreach ($documentProtocol as $protocol) {
                                                $this->urlSet[] = array(
                                                        'loc' => CDN_SERVER . $protocol['pdf'],
                                                        'lastmod' => date('Y-m-d\TH:i:sP'),
                                                        'priority' => '1.0',
                                                );
                                        }
                                }
                        }

                        if ($this->config->get('feed_google_sitemap_pro_sds')) {
                                $documentSds = $this->model_catalog_product->getAllSds();

                                if ($documentSds) {
                                        foreach ($documentSds as $sds) {
                                                $this->urlSet[] = array(
                                                        'loc' => CDN_SERVER . $sds['pdf'],
                                                        'lastmod' => date('Y-m-d\TH:i:sP'),
                                                        'priority' => '1.0',
                                                );
                                        }
                                }
                        }

                        if ($this->config->get('feed_google_sitemap_pro_custom')) {
                                $this->load->model('api/sitemap');

                                $customLinks = $this->model_api_sitemap->getCustomLinks();

                                foreach ($customLinks as $customLink) {
                                        $this->urlSet[] = array(
                                                'loc' => $customLink['url'],
                                                'lastmod' => date('Y-m-d\TH:i:sP'),
                                                'priority' => '1.0',
                                        );
                                }
                        }

                        $output  = $this->startStringSitemap;

                        $count = 0; $totalUrlSet = count($this->urlSet);
                        foreach ($this->urlSet as $index => $urlSet) {
                                $output .= $this->getURLXML($urlSet);

                                if ($count < $this->maxUrlCount && $index != ($totalUrlSet - 1)) {
                                        $count++;
                                } else {
                                        $count = 0;

                                        $this->putToFile($output . $this->endStringSitemap);

                                        $output = $this->startStringSitemap;
                                }
                        }

                        $this->updateSitemapFile();

                        $json['success'] = $this->language->get('text_success');
                }

                $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
        }

	protected function getURLXML($data) {
                $url = '<url>'
                        . '  <loc>' . $data['loc'] . '</loc>'
                        . '  <changefreq>' . $this->config->get('feed_google_sitemap_pro_change_frequency') . '</changefreq>'
                        . ((isset($data['lastmod']) && !empty($data['lastmod'])) ? '  <lastmod>' . $data['lastmod'] . '</lastmod>' : '')
                        . '  <priority>' . $data['priority'] . '</priority>';
                
                if ($this->config->get('feed_google_sitemap_pro_product_images') 
                        && isset($data['image']) && !empty($data['image'])) {
                        $url .= '  <image:image>'
                                . '  <image:loc>' . $data['image'] . '</image:loc>'
                                . '  <image:caption>' . $data['image:caption'] . '</image:caption>'
                                . '  <image:title>' . $data['image:title'] . '</image:title>'
                                . '  </image:image>';
                }

                $url .= '</url>';

                return $url;
        }

        protected function getDirectory() {
                return DIR_ROOT . $this->directory . '/';
        }

        protected function getNextFileName(){
                $fileName = "sitemap_$this->fileCount.xml";
                
                $this->fileCount++;
                return $this->getDirectory() . $fileName;
        }

        protected function getFilesInDirectory() {
                return glob($this->getDirectory() . '*');
        }

        protected function cleanDirectory() {
                $files = $this->getFilesInDirectory();

                if (count($files) > 0) {
                        foreach ($files as $file) {
                                unlink($file);
                        }
                }
        }

        protected function putToFile ($sitemap, $filename = '') {
                if ($this->initialHit) {
                        $directory = $this->getDirectory();
                        if (!is_dir($directory)) {
                                mkdir($directory);
                        } else {
                                $this->cleanDirectory();
                        }

                        $this->initialHit = false;
                }

                $fileName = $filename ? $filename : $this->getNextFileName();
                if (file_exists($fileName)) {
                        unlink($fileName);
                }

                $handle = fopen($fileName, "w");
                fwrite($handle, $sitemap);

                fclose($handle);
        }

        protected function getCategories($parent_id, $current_path = '') {
		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

                        $this->urlSet[] = array(
                                'loc' => $this->url->link('product/category', 'path=' . $new_path),
                                'priority' => '0.7'
                        );

                        if ($this->config->get('feed_google_sitemap_pro_products')) {
                                $groupProducts = $this->model_catalog_product->getGroupProducts(array('filter_category_id' => $result['category_id']));
                                $specialProducts = $this->model_catalog_product->getSpecialProducts(array('filter_category_id' => $result['category_id']));

                                $products = array_merge($groupProducts, $specialProducts);


                                foreach ($products as $product) {
                                        $this->urlSet[] = array(
                                                'loc' => $this->url->link('product/product', 'path=' . $new_path . '&product_id=' . $product['product_id']),
                                                'priority' => '1.0'
                                        );
                                }
                        }

			$this->getCategories($result['category_id'], $new_path);
		}
	}

        function updateSitemapFile () {
                $output  = '<?xml version="1.0" encoding="UTF-8"?>';
                $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

                $files = glob($this->getDirectory() . '*.xml');

                foreach ($files as $file) {
                    $output .= '<sitemap>'
                            . '  <loc>' . $this->config->get('config_url') . 'sitemaps/' . basename($file) . '</loc>'
                            . '  <lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>'
                            . '</sitemap>';
                }

                $output .= '</sitemapindex>';

                $this->putToFile($output, DIR_ROOT . 'sitemap.xml');

                return true;
        }
}
