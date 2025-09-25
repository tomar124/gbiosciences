<?php
class ModelExtensionTotalEei extends Model {
	public function getTotal($total) {
		if (($this->cart->getSubTotal() > $this->config->get('total_eei_total')) && ($this->cart->getSubTotal() > 0)) {
			$this->load->language('extension/total/eei');
                        
                        if(isset($this->session->data['shipping_address']['country_id']) && $this->config->get('config_country_id') != $this->session->data['shipping_address']['country_id']){
                            $total['totals'][] = array(
                                    'code'       => 'eei',
                                    'title'      => $this->language->get('text_eei'),
                                    'value'      => $this->config->get('total_eei_fee'),
                                    'sort_order' => $this->config->get('total_eei_sort_order')
                            );

                            $total['total'] += $this->config->get('total_eei_fee');
                        }
                }
	}
}