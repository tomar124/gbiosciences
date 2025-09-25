<?php

            class ModelExtensionShippingFedex extends Model {
                    function getQuote($address) {
                            $this->load->language('extension/shipping/fedex');

                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('fedex_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

                            if (!$this->config->get('fedex_geo_zone_id')) {
                                    $status = true;
                            } elseif ($query->num_rows) {
                                    $status = true;
                            } else {
                                    $status = false;
                            }

                            $error = '';

                            $quote_data = array();
                
                            $this->load->library('fedex');
                
                            if ($status && $this->fedex) {
                                    $services = array();

                                    if ($this->cart->hasHazardousAccessible()) {
                                            $services[] = 'FEDEX_GROUND';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    } elseif ($this->cart->hasHazardousInAccessible()) {
                                            $services[] = 'FEDEX_GROUND';
                                            $services[] = 'FEDEX_2_DAY';
                                            $services[] = 'STANDARD_OVERNIGHT';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    } elseif ($this->cart->hasDryice() || $this->cart->hasBlueice()) {
                                            //$services[] = 'STANDARD_OVERNIGHT';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    } elseif ($this->cart->hasStandard()) {
                                            $services[] = 'STANDARD_OVERNIGHT';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    } elseif($this->cart->hasAmbient2Day()) {                
                                            $services[] = 'FEDEX_2_DAY';
                                            $services[] = 'STANDARD_OVERNIGHT';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_ECONOMY';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    }else{
                                            $services[] = 'FEDEX_GROUND';
                                            $services[] = 'FEDEX_2_DAY';
                                            $services[] = 'STANDARD_OVERNIGHT';
                                            $services[] = 'PRIORITY_OVERNIGHT';
                                            $services[] = 'INTERNATIONAL_ECONOMY';
                                            $services[] = 'INTERNATIONAL_PRIORITY';
                                    }
                                
                                    foreach ($services as $service) {
                                            if($response = $this->fedex->getRates($service, $address)){
                                                    $code = $response->RateReplyDetails->ServiceType;

                                                    $title = ucwords(strtolower(str_replace('_', ' ', $code)));

                                                    if ($this->config->get('shipping_fedex_display_time') && isset($response->RateReplyDetails->DeliveryTimestamp) && $response->RateReplyDetails->DeliveryTimestamp) {
                                                            $title .= ' (' . $this->language->get('text_eta') . ' ' . date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($response->RateReplyDetails->DeliveryTimestamp)) . ')';
                                                    }

                                                    $RatedShipmentDetails = $response->RateReplyDetails->RatedShipmentDetails;
                                                    $RatedShipmentDetails = is_array($RatedShipmentDetails) ? $RatedShipmentDetails[0] : $RatedShipmentDetails;

                                                    $DANGEROUS_GOODS_CHARGES = 0;
                                                    if(isset($RatedShipmentDetails->ShipmentRateDetail->Surcharges)){
                                                        foreach($RatedShipmentDetails->ShipmentRateDetail->Surcharges as $Surcharges){
                                                            if($Surcharges->SurchargeType == 'DANGEROUS_GOODS')
                                                                $DANGEROUS_GOODS_CHARGES += $Surcharges->Amount->Amount;
                                                        }
                                                    }

                                                    $cost = $RatedShipmentDetails->ShipmentRateDetail->TotalNetFedExCharge->Amount - $DANGEROUS_GOODS_CHARGES;

                                                    $quote_data[$code] = array(
                                                            'code'         => 'fedex.' . $code,
                                                            'title'        => $title,
                                                            'cost'         => $this->currency->convert($cost, $this->config->get('config_currency'), $this->config->get('config_currency')),
                                                            'tax_class_id' => $this->config->get('shipping_fedex_tax_class_id'),
                                                            'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('shipping_fedex_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000),
                                                            'name'         => $service,
                                                            'DANGEROUS_GOODS_CHARGES' => $DANGEROUS_GOODS_CHARGES
                                                    );
                                            }
                                    }
                            }

                            $method_data = array();

                            if ($quote_data || $error) {
                                    $title = $this->language->get('text_title');

                                    if ($this->config->get('shipping_fedex_display_weight')) {
                                            $title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($this->cart->getWeight(), $this->config->get('shipping_fedex_weight_class_id')) . ')';
                                    }

                                    $method_data = array(
                                            'code'       => 'fedex',
                                            'title'      => $title,
                                            'quote'      => $quote_data,
                                            'sort_order' => $this->config->get('shipping_fedex_sort_order'),
                                            'error'      => $error
                                    );
                            }

                            return $method_data;
                    }
            }
            