<?php
class ModelExtensionTotalHandling extends Model {
	public function getTotal($total) {
		if (($this->cart->getSubTotal() > $this->config->get('total_handling_total')) && ($this->cart->getSubTotal() > 0)) {
			$this->load->language('extension/total/handling');

            /* Functionnality added For ambient, standard, hazardous, blueice and dryice products*/
            //To calculate whether hazamat fee will add or not when fedex ground is selected
            $addHazamtFee = true;
            $selectedShipping = $this->session->data['shipping_method'] ?? [];
            if (isset($selectedShipping['code']) && $selectedShipping['code'] === 'fedex.FEDEX_GROUND') {
                    if ($this->cart->isAllThroughGround() && !$this->cart->hasShipGroundHazmat()) {
                            $addHazamtFee = false;
                    }
            }

            $handling = 0;

            if($this->cart->hasAmbient2Day()){
                $handling += $this->config->get('config_shipping_method_ambient') ? $this->config->get('config_shipping_method_ambient') : 0;
            }

            if($this->cart->hasBlueice()){
                $handling += $this->config->get('config_shipping_method_blue_ice') ? $this->config->get('config_shipping_method_blue_ice') : 0;
            }

            if($this->cart->hasDryice()){
                $handling += $this->config->get('config_shipping_method_dry_ice') ? $this->config->get('config_shipping_method_dry_ice') : 0;
            }

            if($this->cart->hasStandard()){
                $handling += $this->config->get('config_shipping_method_standard') ? $this->config->get('config_shipping_method_standard') : 0;
            }

            if($this->cart->hasHazardousAccessible() && $addHazamtFee){
                $handling += $this->config->get('config_shipping_method_hazardous_accessible') ? $this->config->get('config_shipping_method_hazardous_accessible') : 0;
            }

            if($this->cart->hasHazardousInAccessible() && $addHazamtFee){
                $handling += $this->config->get('config_shipping_method_hazardous_inaccessible') ? $this->config->get('config_shipping_method_hazardous_inaccessible') : 0;
            }

            //Add additional $10 for international orders
            if(isset($this->session->data['shipping_address']['country_id']) && $this->config->get('config_country_id') != $this->session->data['shipping_address']['country_id']){
                $handling += $this->config->get('total_handling_fee_international');
            } else {
                $handling += $this->config->get('total_handling_fee');
            }

            /* ending */
            

			$total['totals'][] = array(
				'code'       => 'handling',
				'title'      => $this->language->get('text_handling'),
				
            'value'      => $handling,
            
				'sort_order' => $this->config->get('total_handling_sort_order')
			);

			if ($this->config->get('total_handling_tax_class_id')) {
				$tax_rates = $this->tax->getRates($this->config->get('total_handling_fee'), $this->config->get('total_handling_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			
            $total['total'] += $handling;
            
		}
	}
}