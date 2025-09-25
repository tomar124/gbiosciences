<?php
use Journal3\Opencart\Controller;

class ControllerJournal3ProductRefrences extends Controller {

	public function index($args) {
                $this->load->model('catalog/product');
                
                $technicals = array();
                
                $product_info = $this->model_catalog_product->getProductReferences($this->request->get['product_id']);
                $data['count'] = $this->model_catalog_product->getReferencesCount($this->request->get['product_id']);
                $data['references'] = $product_info;
                
                return $this->load->view('journal3/module/product_refrences', $data);    
	}
}
