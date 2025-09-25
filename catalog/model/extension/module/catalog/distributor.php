<?php
    class ModelExtensionModuleCatalogdistributor extends Model {
            public function getCountries() {
                   $sql = "Select *, CONCAT(firstname, ' ', lastname) AS name , website, email , mobilenumber AS phone , c.name AS countryname from " . DB_PREFIX . "distributor AS d LEFT JOIN " .DB_PREFIX . "distributor_discription AS dd ON (d.distributer_id = dd.distributer_id) LEFT JOIN " . DB_PREFIX . "country AS c ON c.country_id = d.country WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
                    $query = $this->db->query($sql);

                    return $query->rows;
            }

            public function getCountry() {
                    $sql = "Select DISTINCT  c.country_id , c.name AS country from " . DB_PREFIX . "distributor AS d LEFT JOIN " . DB_PREFIX . "country AS c ON c.country_id = d.country where d.status = 'Enabled' ORDER BY `c`.`country_id` ASC";

                    $query = $this->db->query($sql);

                    return $query->rows;
            }
    }
?>