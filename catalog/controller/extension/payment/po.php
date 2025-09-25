<?php
class ControllerExtensionPaymentPo extends Controller {
	public function index() {
                $this->load->language('extension/payment/po');
                
		$data['button_confirm'] = $this->language->get('button_confirm');
                
                $data['text_title'] = $this->language->get('text_title');
                
                $data['entry_po'] = $this->language->get('entry_po');
                
		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/po', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'po') {
                        $this->load->language('extension/payment/po');
                        
                        $json = array();
                        
                        if(isset($this->request->get['po_number']) && !empty($this->request->get['po_number'])){
                            $this->load->model('checkout/order');

                            $this->session->data['po_number'] = $this->request->get['po_number'];

                            $message = 'Purchase Order #: ' . $this->request->get['po_number'];
                            
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_po_order_status_id'), $message, false);

                            //Update PO Number to order table
                            $this->model_checkout_order->updatePONumber($this->session->data['order_id'], $this->request->get['po_number']);

                            $json['success'] = $this->url->link('checkout/success');
                        }else{
                            $json['error'] = $this->language->get('error_po');
                        }
                        
                        $this->response->addHeader('Content-Type: application/json');
                        $this->response->setOutput(json_encode($json));
		}
	}
}
