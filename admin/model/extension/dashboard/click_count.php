<?php
class ModelExtensionDashboardClickCount extends Model {
	public function getTotalClickCounts($page, $section) {
		$sql = "SELECT count AS total FROM `" . DB_PREFIX . "click_count` WHERE page = '" . $page . "' AND section = '" . $section . "'";

		$query = $this->db->query($sql);

		return isset($query->row['total']) ? $query->row['total'] : 0;
	}
}