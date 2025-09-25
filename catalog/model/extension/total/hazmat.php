<?php
class ModelExtensionTotalHazmat extends Model {
	public function getTotal($total) {
		if (($this->cart->getSubTotal() > $this->config->get('total_hazmat_total')) && ($this->cart->getSubTotal() > 0)) {
			$this->load->language('extension/total/hazmat');
                            
                        $hazmat = 0;
                            
                        /* Functionality to add FEDEX DANGEROUS_GOODS charges to hazmat */
                        if(isset($this->session->data['shipping_method'], $this->session->data['shipping_method']['DANGEROUS_GOODS_CHARGES'])){
                            $hazmat += $this->session->data['shipping_method']['DANGEROUS_GOODS_CHARGES'];
                        }
                            
                        /* ending */ 
                        $total['totals'][] = array(
                                'code'       => 'hazmat',
                                'title'      => $this->language->get('text_hazmat'),
                                'value'      => $this->config->get('total_hazmat_fee') + $hazmat,
                                'sort_order' => $this->config->get('total_hazmat_sort_order')
                        );

			if ($this->config->get('total_hazmat_tax_class_id')) {
				$tax_rates = $this->tax->getRates($this->config->get('total_hazmat_fee'), $this->config->get('total_hazmat_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}
                        
                        $total['total'] += $this->config->get('total_hazmat_fee') + $hazmat;
		}
	}
}