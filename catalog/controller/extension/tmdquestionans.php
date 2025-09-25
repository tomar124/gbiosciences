<?php
class ControllerExtensionTmdquestionans extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/tmdquestionans');
		$this->load->model('extension/tmdquestionans');
		
		$this->document->addStyle('catalog/view/theme/default/stylesheet/tmdquestionans.css');
        $data['askbtnbg']=$this->config->get('questions_askbg');
        $data['askbtncolor']=$this->config->get('questions_ask_color');
        $data['btnbg']=$this->config->get('questions_btnbg');
        $data['btncolor']=$this->config->get('questions_btn_color');
        $data['qsbg']=$this->config->get('questions_qsbg');
        $data['qscolor']=$this->config->get('questions_qscolor');
        $data['ansbg']=$this->config->get('questions_ansbg');
        $data['anscolor']=$this->config->get('questions_anscolor');
		$data['captchastatus'] = $this->config->get('questions_captchastatus');

	
           $tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
			$answer_texts = $this->config->get('questions_answertext');

            if (!empty($answer_texts[$this->config->get('config_language_id')]['name'])) {
                $data['text_answerss'] = $answer_texts[$this->config->get('config_language_id')]['name'];
            } else {
                $data['text_answerss'] = $this->language->get('text_answers');
            }


            $language_id=$this->config->get('config_language_id');
	    	
	    	$questions_qatab=$this->config->get('questions_qatab');
			
	    	if(!empty($questions_qatab[$language_id]['askquestion'])){
				$data['text_askquestion'] = $questions_qatab[$language_id]['askquestion'];
			}else{
				$data['text_askquestion'] = $this->language->get('text_askquestion');
			}

			if(!empty($questions_qatab[$language_id]['askanswer'])){
				$data['text_answers'] = $questions_qatab[$language_id]['askanswer'];
			}else{
				$data['text_answers'] = $this->language->get('text_answers');
			}




			if(!empty($questions_qatab[$language_id]['your_question'])){
				$data['text_question'] = $questions_qatab[$language_id]['your_question'];
			}else{
				$data['text_question'] = $this->language->get('text_question');
			}

			if(!empty($questions_qatab[$language_id]['questions'])){
				$data['text_quest'] = $questions_qatab[$language_id]['questions'];
			}else{
				$data['text_quest'] = $this->language->get('text_quest');
			}

			if(!empty($questions_qatab[$language_id]['submit'])){
				$data['text_save'] = $questions_qatab[$language_id]['submit'];
			}else{
				$data['text_save'] = $this->language->get('text_save');
			}


			}


        $data['logged'] = $this->customer->isLogged();
        $data['login'] = $this->url->link('account/login', '', true);

		$data['product_id'] = (int)$this->request->get['product_id'];

        $this->load->model('catalog/product');


        $data['question_popup']=$this->url->link('extension/tmdquestionans/questionpoup','product_id=' . $this->request->get['product_id']);

        $data['answer_popup']=$this->url->link('extension/tmdquestionans/answerpoup','product_id=' . $this->request->get['product_id']);
        
        $data['login_popup']=$this->url->link('extension/tmdquestionans/loginpoup');

	    $product_info = $this->model_catalog_product->getProduct($data['product_id']);

        if(!empty($product_info['name'])){
          $data['product_title'] = $product_info['name'];
        }else{
        	$data['product_title'] = '';
        }

		$data['customer_email'] = $this->customer->getEmail();

		if($this->request->get['product_id']){
		   $product_id = (int)$this->request->get['product_id'];
	    }else{
		   $product_id = 0;
	    }

		if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
		} else {
			$filter_search = '';
		}

		$url = '';

		if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
		}

		$filter_data = array(
			'filter_search' => $filter_search
		);

		$data['hasquestions'] = $this->model_extension_tmdquestionans->getQuestions($filter_data, $product_id);

		$data['filter_search'] = $filter_search;
		
		$data['hasTotalquestions'] =  $this->model_extension_tmdquestionans->getQuestionByProductId($product_id);

		$data['totalanswers'] =  $this->model_extension_tmdquestionans->TotalAnswers($product_id);

		$this->session->data['redirect'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}
		if(!empty($this->request->get['loadstatus'])) {
			$this->response->setOutput($this->load->view('extension/tmdquestionans', $data));
		} else{
			return $this->load->view('extension/tmdquestionans', $data);
		}
	}


     public function loginpoup() {
     	$this->load->language('extension/tmdquestionans');
			$this->load->model('extension/tmdquestionans');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/tmdquestionans.css');
			$data['askbtnbg']=$this->config->get('questions_askbg');
            $data['askbtncolor']=$this->config->get('questions_ask_color');
            $data['btnbg']=$this->config->get('questions_btnbg');
            $data['btncolor']=$this->config->get('questions_btn_color');
            $data['qsbg']=$this->config->get('questions_qsbg');
            $data['qscolor']=$this->config->get('questions_qscolor');
            $data['ansbg']=$this->config->get('questions_ansbg');
            $data['anscolor']=$this->config->get('questions_anscolor');
			$data['captchastatus'] = $this->config->get('questions_captchastatus');

			$data['text_loginplz'] = $this->language->get('text_loginplz');
            $data['logged'] = $this->customer->isLogged();

            $this->response->setOutput($this->load->view('extension/tmdlogin_popup', $data));

     }


		public function questionpoup() {
       		$this->load->language('extension/tmdquestionans');
			$this->load->model('extension/tmdquestionans');
			
			$this->document->addStyle('catalog/view/theme/default/stylesheet/tmdquestionans.css');
            $data['askbtnbg']=$this->config->get('questions_askbg');
            $data['askbtncolor']=$this->config->get('questions_ask_color');
            $data['btnbg']=$this->config->get('questions_btnbg');
            $data['btncolor']=$this->config->get('questions_btn_color');
            $data['qsbg']=$this->config->get('questions_qsbg');
            $data['qscolor']=$this->config->get('questions_qscolor');
            $data['ansbg']=$this->config->get('questions_ansbg');
            $data['anscolor']=$this->config->get('questions_anscolor');
			$data['captchastatus'] = $this->config->get('questions_captchastatus');

		$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
			$answer_texts = $this->config->get('questions_answertext');

            if (!empty($answer_texts[$this->config->get('config_language_id')]['name'])) {
                $data['text_answerss'] = $answer_texts[$this->config->get('config_language_id')]['name'];
            } else {
                $data['text_answerss'] = $this->language->get('name');
            }


            $language_id=$this->config->get('config_language_id');
	    	
	    	$questions_qatab=$this->config->get('questions_qatab');
			
	    	if(!empty($questions_qatab[$language_id]['askquestion'])){
				$data['text_askquestion'] = $questions_qatab[$language_id]['askquestion'];
			}else{
				$data['text_askquestion'] = $this->language->get('text_askquestion');
			}

			if(!empty($questions_qatab[$language_id]['askanswer'])){
				$data['text_answers'] = $questions_qatab[$language_id]['askanswer'];
			}else{
				$data['text_answers'] = $this->language->get('text_answers');
			}
			

			if(!empty($questions_qatab[$language_id]['your_question'])){
				$data['text_question'] = $questions_qatab[$language_id]['your_question'];
			}else{
				$data['text_question'] = $this->language->get('text_question');
			}

			if(!empty($questions_qatab[$language_id]['questions'])){
				$data['text_quest'] = $questions_qatab[$language_id]['questions'];
			}else{
				$data['text_quest'] = $this->language->get('text_quest');
			}

			if(!empty($questions_qatab[$language_id]['submit'])){
				$data['text_save'] = $questions_qatab[$language_id]['submit'];
			}else{
				$data['text_save'] = $this->language->get('text_save');;
			}


			}
			
			
		

            $data['logged'] = $this->customer->isLogged();



            $data['login'] = $this->url->link('account/login', '', true);

			$data['product_id'] = (int)$this->request->get['product_id'];

            $this->load->model('catalog/product');



		    $product_info = $this->model_catalog_product->getProduct($data['product_id']);
            if(!empty($product_info['name'])){
             $data['product_title'] = $product_info['name'];
            }else{
            	$data['product_title'] = '';
            }
			$data['customer_email'] = $this->customer->getEmail();
             if($this->request->get['product_id']){
			   $product_id = (int)$this->request->get['product_id'];
		    }else{
			   $product_id = 0;
		    }

			if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
			} else {
			$filter_search = '';
			}

			$url = '';

			if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
			}

			$filter_data = array(
			'filter_search' => $filter_search
			);

			

			$this->session->data['redirect'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
                $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
            } else {
                $data['captcha'] = '';
            }

        $this->response->setOutput($this->load->view('extension/tmdquestion_popup', $data));

    }

	 public function askquestion() {
		$this->load->language('extension/tmdquestionans');
		$this->load->model('extension/tmdquestionans');

		$chkcaptchastatus = $this->config->get('questions_captchastatus');

		$json = array();


			if($this->request->post){
			if ((utf8_strlen($this->request->post['qa_question']) < 10) || (utf8_strlen($this->request->post['qa_question']) > 1000)) {
				$json['error'] = $this->language->get('error_qa_question');
			}

		// Captcha
		$storecaptcha = $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status');
			 if ($storecaptcha==1) {
			 		if($chkcaptchastatus==1){
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				if ($captcha) {
					$json['captchaerror'] = $this->language->get('error_captcha');
				}
		    }
		    }
		}

				if (!$json) {

					 $language_id=$this->config->get('config_language_id');
	    	
	    	$questions_qatab=$this->config->get('questions_qatab');
			
	    	if(!empty($questions_qatab[$language_id]['your_question'])){
				$text_qa_success = $questions_qatab[$language_id]['your_question'];
			}else{
				$text_qa_success = $this->language->get('text_qa_success');
			}
				/* update line */
			$this->model_extension_tmdquestionans->addQuestion($this->request->get['product_id'],$this->request->post);
				$json['success'] = $text_qa_success;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


      public function answerpoup() {
       $this->load->language('extension/tmdquestionans');
			$this->load->model('extension/tmdquestionans');
			
			$this->document->addStyle('catalog/view/theme/default/stylesheet/tmdquestionans.css');
            $data['askbtnbg']=$this->config->get('questions_askbg');
            $data['askbtncolor']=$this->config->get('questions_ask_color');
            $data['btnbg']=$this->config->get('questions_btnbg');
            $data['btncolor']=$this->config->get('questions_btn_color');
            $data['qsbg']=$this->config->get('questions_qsbg');
            $data['qscolor']=$this->config->get('questions_qscolor');
            $data['ansbg']=$this->config->get('questions_ansbg');
            $data['anscolor']=$this->config->get('questions_anscolor');
			$data['captchastatus'] = $this->config->get('questions_captchastatus');

			$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
			$answer_texts = $this->config->get('questions_answertext');

            if (!empty($answer_texts[$this->config->get('config_language_id')]['name'])) {
                $data['text_answerss'] = $answer_texts[$this->config->get('config_language_id')]['name'];
            } else {
                $data['text_answerss'] = $this->language->get('name');
            }


            $language_id=$this->config->get('config_language_id');
	    	
	    	$questions_qatab=$this->config->get('questions_qatab');
			
	    	if(!empty($questions_qatab[$language_id]['askquestion'])){
				$data['text_askquestion'] = $questions_qatab[$language_id]['askquestion'];
			}else{
				$data['text_askquestion'] =  $this->language->get('text_askquestion');
			}

			if(!empty($questions_qatab[$language_id]['askanswer'])){
				$data['text_answers'] = $questions_qatab[$language_id]['askanswer'];
			}else{
				$data['text_answers'] = $this->language->get('text_answers');
			}
			

			if(!empty($questions_qatab[$language_id]['your_question'])){
				$data['text_question'] = $questions_qatab[$language_id]['your_question'];
			}else{
				$data['text_question'] = $this->language->get('text_question');
			}

			if(!empty($questions_qatab[$language_id]['questions'])){
				$data['text_quest'] = $questions_qatab[$language_id]['questions'];
			}else{
				$data['text_quest'] = $this->language->get('text_quest');
			}

			if(!empty($questions_qatab[$language_id]['submit'])){
				$data['text_save'] = $questions_qatab[$language_id]['submit'];
			}else{
				$data['text_save'] = $this->language->get('text_save');
			}


			}
			
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_captcha'] = 'Captcha';
			$data['text_note'] = $this->language->get('text_note');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['text_loading'] = $this->language->get('text_loading');


			$data['text_ansme'] = $this->language->get('text_ansme');
			$data['text_loginplz'] = $this->language->get('text_loginplz');

            $data['logged'] = $this->customer->isLogged();

            $data['login'] = $this->url->link('account/login', '', true);

			if($this->request->get['product_id']){
			   $data['product_id'] = (int)$this->request->get['product_id'];
		    }else{
			   $data['product_id'] = 0;
		    }

		    if($this->request->get['user_question_id']){
			   $data['user_question_id'] = (int)$this->request->get['user_question_id'];
		    }else{
			   $data['user_question_id'] = 0;
		    }

            $this->load->model('catalog/product');



		    $product_info = $this->model_catalog_product->getProduct($data['product_id']);

             if(!empty($product_info['name'])){
             $data['product_title'] = $product_info['name'];
            }else{
            	$data['product_title'] = '';
            }
			$data['customer_email'] = $this->customer->getEmail();
             if($this->request->get['product_id']){
			   $product_id = (int)$this->request->get['product_id'];
		    }else{
			   $product_id = 0;
		    }

			$data['customer_email'] = $this->customer->getEmail();


			if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
			} else {
			$filter_search = '';
			}

			$url = '';

			if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
			}

			$filter_data = array(
			'filter_search' => $filter_search
			);


			$this->session->data['redirect'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
                $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
            } else {
                $data['captcha'] = '';
            }


        $this->response->setOutput($this->load->view('extension/tmdanswer_popup', $data));

    }
	public function addanswer() {
		$this->load->language('extension/tmdquestionans');
		$this->load->model('extension/tmdquestionans');

		$chkcaptchastatus = $this->config->get('questions_captchastatus');
		$json = array();


			if($this->request->post){
		if (isset($this->request->get['user_question_id'])) {
			$data['user_question_id'] = $this->request->get['user_question_id'];
		} else {
			$data['user_question_id'] = '';
		}

			if ((utf8_strlen($this->request->post['answer']) < 10) || (utf8_strlen($this->request->post['answer']) > 1000)) {
				$json['error'] = $this->language->get('error_answer');
			}

			// Captcha
			$storecaptcha = $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status');
			 if ($storecaptcha==1) {
			 		if($chkcaptchastatus==1){
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				if ($captcha) {
					$json['captchaerror'] = $this->language->get('error_captcha');
				}
		    }
		    }
		}


				if (!$json) {
				 $language_id=$this->config->get('config_language_id');
	    	
	    	$questions_qatab=$this->config->get('questions_qatab');
			
	    	if(!empty($questions_qatab[$language_id]['your_question'])){
				$text_qa_success = $questions_qatab[$language_id]['your_question'];
			}else{
				$text_qa_success = $this->language->get('text_qa_success');
			}
			$this->model_extension_tmdquestionans->addAnswer($this->request->post['user_question_id'],$this->request->post);
				$json['success'] = $text_qa_success;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		}

}
