<?php
class ControllerExtensionFeedGoogleSitemapPro extends Controller {
	public function index() {
		if ($this->config->get('feed_google_sitemap_pro_status')) {
                        $output  = '<?xml version="1.0" encoding="UTF-8"?>';
                        $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

                        $files = glob(DIR_IMAGE . 'sitemap/*.xml');

                        foreach ($files as $file) {
                            $output .= '<sitemap>'
                                    . '  <loc>' . $this->config->get('config_url') . 'image/sitemap/' . basename($file) . '</loc>'
                                    . '  <lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>'
                                    . '</sitemap>';
                        }

                        $output .= '</sitemapindex>';

                        $this->response->addHeader('Content-Type: application/xml');
                        $this->response->setOutput($output);
		}
	}
}
