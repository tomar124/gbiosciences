<?php
class ModelExtensionFeedGoogleSitemapPro extends Model {
	public function install() {
                $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_sitemap_pro_custom_url`");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "google_sitemap_pro_custom_url` (
				`google_sitemap_pro_custom_url_id` INT(11) NOT NULL AUTO_INCREMENT,
				`url` TEXT NOT NULL,
                                `added_on` datetime NOT NULL,
				PRIMARY KEY (`google_sitemap_pro_custom_url_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_sitemap_pro_custom_url`");
	}

	public function getCustomLinks($start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT google_sitemap_pro_custom_url_id, url, added_on FROM " . DB_PREFIX . "google_sitemap_pro_custom_url ORDER BY added_on DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalCustomLinks() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "google_sitemap_pro_custom_url");

		return $query->row['total'];
	}
        
        public function addCustomLink($url) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "google_sitemap_pro_custom_url SET url = '" . $this->db->escape($url) . "', added_on = NOW()");
	}
        
        public function deleteCustomLink($google_sitemap_pro_custom_url_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "google_sitemap_pro_custom_url WHERE google_sitemap_pro_custom_url_id = '" . (int)$google_sitemap_pro_custom_url_id . "'");
	}
        
        public function isUrlExists($url) {
                return $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "google_sitemap_pro_custom_url WHERE url = '" . $this->db->escape($url) . "'")->row['total'];
        }
}
