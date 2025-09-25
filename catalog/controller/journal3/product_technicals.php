<?php

use Journal3\Opencart\Controller;

class ControllerJournal3ProductTechnicals extends Controller {

	public function index($args) {
                $this->load->model('catalog/product');
                
                $technicals = array();
                $technicals['server_link'] = !defined('CDN_SERVER') ? '/image' : CDN_SERVER;
                
                $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
                
                if (!$product_info['special_product']) {
                        $childs = $this->model_catalog_product->getGroupedProductGroupedChilds($this->request->get['product_id']);
                        
                        foreach ($childs as $child) {                                    
                                $child_info = $this->model_catalog_product->getProduct($child['child_id']);

                                if ($child_info) {
                                        //Technical Documents
                                        if($protocol = $this->model_catalog_product->getProtocol($child['child_id'])){
                                            $technicals['protocol'][$child_info['name']] = $protocol;
                                        }

                                        if($sds = $this->model_catalog_product->getSds($child['child_id'])){
                                            $technicals['sds'][$child_info['name']] = $sds;
                                        }

                                        if($coa = $this->model_catalog_product->getCoa($child['child_id'])){
                                            $technicals['coa'][$child_info['name']] = $coa;
                                        }
                                        
                                        $otherTechnicals[] = $child['child_id'];
                                }
                        }

                        //Technical Documents
                        if(isset($otherTechnicals) && $otherTechnicals && $technical = $this->model_catalog_product->getOtherTechnical($otherTechnicals)){
                                $technicals['technical'] = $technical;
                        }
                } else {
                       if($protocol = $this->model_catalog_product->getProtocol($this->request->get['product_id'])){
                            $technicals['protocol']['protocol'] = $protocol;
                       }

                       if($sds = $this->model_catalog_product->getSds($this->request->get['product_id'])){
                            $technicals['sdss'] = $sds;
                       }

                       if($coa = $this->model_catalog_product->getCoa($this->request->get['product_id'])){
                            $technicals['coa'][$child_info['name']] = $coa;
                       }

                       $technicals['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

                       $attribute_groups = $technicals['attribute_groups'];

                       foreach($attribute_groups as $attribute_group_key => $attribute_group){
                               foreach($attribute_group['attribute'] as $attribute_key => $attribute){
                                       $attribute_groups[$attribute_group_key]['attribute'][$attribute_key]['text'] = html_entity_decode($attribute['text'], ENT_QUOTES, 'UTF-8');
                               }
                       }

                       $technicals['attribute_groups'] = $attribute_groups;
                }
                
                 return $this->load->view('journal3/module/product_technicals', $technicals);    
	}

}
