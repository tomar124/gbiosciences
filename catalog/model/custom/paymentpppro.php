<?php
class ModelCustomPaymentpppro extends Model
{
    public function pymntppprovalidation() {
        $is_error = false;

        $order_id = $this->session->data['order_id'];

        if($order_id) {
            $result = $this->db->query("SELECT `order_id`, `attempt` FROM ". DB_PREFIX ."transaction_verify WHERE `order_id` =".$order_id);

            if($result->num_rows) {
                $attempt = (int) $result->row['attempt'];

                if($attempt > 3) {
                    $is_error = true;
                    $this->cancel_order($order_id);
                } else if($attempt <= 3) {
                    $attempt += 1;
                    $this->db->query("UPDATE " . DB_PREFIX . "transaction_verify SET `attempt` = '" . (int)$attempt . "' WHERE `order_id` ='". $order_id."'");
                }

            } else {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "transaction_verify` SET `order_id` = '" . (int)$order_id . "', `date_added` = NOW()");
            }
        } else {
            $is_error = true;
        }
            
        $this->log->write("pp_pro model validation ". $is_error);
        return $is_error;
     
    }

    public function save_response($response, $order_id, $attempt = null) {
        if($attempt){
            $this->db->query("UPDATE " . DB_PREFIX . "transaction_verify SET `attempt` = '2' WHERE `order_id` ='". $order_id."' ");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "transaction_verify SET `response` = '" . $response . "' WHERE `order_id` ='". $order_id."'");
        }
    }

    public function cancel_order($order_id) {
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);
        $canceled_order = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE name = 'Canceled' ");

        $canceled_order_id = $canceled_order->row['order_status_id'];

        if ($order_info && $canceled_order_id &&  ($order_info['order_status_id'] != $canceled_order_id) ) {
            
            // THIS way user will get an email confirmation order even if you are cancelling the order.
                // $this->model_checkout_order->addOrderHistory($order_id, $canceled_order_id);

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$canceled_order_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$canceled_order_id . "', notify = '0', comment = 'Transaction Error', date_added = NOW()");
    

            $this->cart->clear();

            unset($this->session->data['order_id']);
            unset($this->session->data['payment_address']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['comment']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
        }
    }

}
