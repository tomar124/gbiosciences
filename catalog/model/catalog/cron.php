<?php
class ModelCatalogCron extends Model {
        public function getOrders(){
                $marketing_mail_frequency = 30;

                if ($this->config->get('config_marketing_email_frequency')) {
                        $marketing_mail_frequency = $this->config->get('config_marketing_email_frequency');
                }
                
                $sql = "SELECT o.order_id, o.customer_id, o.firstname, o.lastname, o.email, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value "
                        . "FROM `" . DB_PREFIX . "order` o "
                        . "LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) "
                        . "WHERE (CAST(o.date_added as DATE) = CAST(NOW() - INTERVAL " . $marketing_mail_frequency . " DAY as DATE)) "
                        . "AND o.order_status_id NOT IN (0, 7, 11) AND "
                        . "o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                        . "ORDER BY o.order_id DESC";
                
                $query = $this->db->query($sql);

                $this->load->model('account/order');
                $this->load->model('catalog/product');

                foreach ($query->rows as $orderIndex => $order) {
                    $products = $this->model_account_order->getOrderProducts($order['order_id']);

                    foreach ($products as $productIndex => $product) {
                            $parent_id = $this->model_catalog_product->getparent_id($product['product_id']);
                            $products[$productIndex]['parent_id'] = $parent_id;
                            $products[$productIndex]['link'] = $this->url->link('product/product', 'product_id=' . $parent_id) . '?rp=' . md5($order['customer_id']) . '#product';
                            $products[$productIndex]['option'] = $this->model_account_order->getOrderOptions($order['order_id'], $product['order_product_id']);
                    }

                    $query->rows[$orderIndex]['products'] = ($products) ? $products : array();
                }

		return $query->rows;
        }
        
        public function notified($order_id) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order_review_mail_notify` SET `order_id` = '" . (int)$order_id . "'");
        }
        
        public function getAllRewardPointsFroCustomer() {
                $user_point_variable = "REPLACE(IFNULL(SUM(POINTS), 0), '-', '')";
                $added_point_variable = "SUM(cr.POINTS)";
                $sql_used_points = "(SELECT {$user_point_variable} as used_points
                                    FROM `" . DB_PREFIX . "customer_reward` cr1
                                    WHERE cr1.customer_id = c.customer_id
                                    AND cr1.points LIKE '-%')";

                $sql = "SELECT c.customer_id,
                        {$added_point_variable} as added_points,
                        {$sql_used_points} as used_points
                        FROM `" . DB_PREFIX . "customer_reward` cr
                        LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = cr.customer_id)
                        WHERE cr.points NOT LIKE '-%' AND cr.date_added < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                        GROUP BY c.customer_id
                       ";

                return $this->db->query($sql)->rows;
        }

        public function addRewardsExiredEntry($customer_id, $points) {
                $description = "{$points} points expired - Unredeemed points for a year";

                $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_reward` SET `customer_id` = '" . $customer_id . "', `points` = '-" . $points . "', `description` = '" . $this->db->escape($description) . "', date_added = NOW()");
        }
}
