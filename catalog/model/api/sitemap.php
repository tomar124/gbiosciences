<?php
class ModelApiSitemap extends Model {
	public function getCustomLinks() {
		return $this->db->query("SELECT url FROM `" . DB_PREFIX . "google_sitemap_pro_custom_url`")->rows;
	}
}