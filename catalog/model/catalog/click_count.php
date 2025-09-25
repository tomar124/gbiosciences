<?php
class ModelCatalogClickCount extends Model {
        public function updateCount($page, $section) {
                $this->db->query("UPDATE `" . DB_PREFIX . "click_count` SET `count` = (`count` + 1) WHERE page = '" . $page . "' AND section = '" . $section . "'");
        }

        public function getAvailableSections() {
                $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "click_count` WHERE Field = 'section'");

                if ($query->num_rows) {
                        $result = ltrim(rtrim($query->row['Type'], "')"), "enum('");
                        
                        return explode("','", $result);
                }

                return array();
        }
}
