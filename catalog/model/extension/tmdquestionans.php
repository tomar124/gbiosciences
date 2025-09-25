<?php
class ModelExtensionTmdquestionans extends Model {
	public function addQuestion($product_id,$data){
		$customername = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
		$customeremail = $this->customer->getEmail();
		$autoapprove = $this->config->get('questions_autoapprove');
		$this->db->query("INSERT INTO " . DB_PREFIX . "user_questions SET name = '" . $customername . "', email = '" . $customeremail . "',question = '" .  $this->db->escape($data['qa_question']). "',date_added = NOW(),product_id='".(int) $product_id."',showquestion=1, approved = '".$autoapprove."'");
		$this->language->load('product/product');
		$subject = $this->language->get('text_subject');

		$subject =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['sub_askquestion_admin'];
		if (empty($subject)) {
			$subject = $this->language->get('text_subject');
		}

		$admnmessage =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['message_askquestion_admin'];
		if (!empty($admnmessage)) {
			$admnmessage = $admnmessage;
		}else{
			$admnmessage = $this->language->get('text_question');
		}

		$find = array(
			'{name}',
			'{email}',
			'{question}',
		);
		$replace = array(
			'name' 			=> $customername,
			'email' 		=> $customeremail,
			'question' 		=> $data['qa_question'],
		);

		$message = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $admnmessage))));
		if(!empty($message)){
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($this->config->get('config_email'));
		
		$mail->setFrom($customeremail);
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode(sprintf($subject, $customeremail), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml(html_entity_decode($message));
		$mail->send();
        }
	}
		
	public function addAnswer($user_question_id, $data){
		$customername = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
		$customeremail = $this->customer->getEmail();
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "user_questions_answer SET name = '" . $customername . "', email = '" . $customeremail . "', answer = '" .  $this->db->escape($data['answer']). "',date_added = NOW(),user_question_id='".(int) $user_question_id."'");
	
		$question_info = $this->getAllQuestion($user_question_id);

		if(isset($question_info['question'])){
			$question = $question_info['question'];
		}else{
			$question = '';
		}
        if(isset($question_info['name'])){
			$name = $question_info['name'];
		}else{
			$name = '';
		}
		if(isset($question_info['email'])){
			$email = $question_info['email'];
		}else{
			$email = '';
		}
		if(isset($data['answer'])){
			$answer = $data['answer'];
		}else{
			$answer = '';
		}

		$find = array(
			'{name}',
			'{email}',
			'{question}',
			'{name2}',
			'{email2}',
			'{answer}',
		);

		$replace = array(
			'name' 			=> $customername,
			'email' 		=> $customeremail,
			'question' 		=> $question,
			'name2' 		=> $name,
			'email2' 		=> $email,
			'answer' 		=> $answer,
		);
           
		/// mail to admin///
		$subject =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['sub_answer_admin'];
		if (empty($subject)) {
			$subject = $this->language->get('text_subject');
		}

		$admnmessage =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['msg_answer_admin'];
		if (!empty($admnmessage)) {
			$admnmessage = $admnmessage;
		}else{
			$admnmessage = $this->language->get('text_question');
		}

		$message = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $admnmessage))));
          
          if(!empty($message)){
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($this->config->get('config_email'));
		$mail->setFrom($customeremail);
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode(sprintf($subject, $customeremail), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml(html_entity_decode($message));
		
		$mail->send(); 
	   }
		/// mail to admin///

		/// mail to customer ///
		$customersubject =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['sub_answer_customer'];
		if (empty($customersubject)) {
			$customersubject = $this->language->get('text_subject');
		}

		$customermessage =  $this->config->get('questions_emailtemp')[$this->config->get('config_language_id')]['msg_answer_customer'];
		if (!empty($customermessage)) {
			$customermessage = $customermessage;
		}else{
			$customermessage = $this->language->get('text_question');
		}

		$customermessage = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $customermessage))));
      
       if(!empty($customermessage)){
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($email);
		$mail->setFrom($customeremail);
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode(sprintf($customersubject, $customeremail), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml(html_entity_decode($customermessage));
		$mail->send();
	 }
	}
		
		public function getQuestions($data, $product_id){
				$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
			$sql = "SELECT * FROM " . DB_PREFIX . "user_questions  WHERE product_id= '". (int) $product_id ."' AND showquestion=1 AND approved = '1'";
			
			
			if (!empty($data['filter_search'])) {
			$sql .= " AND question LIKE '" . $this->db->escape($data['filter_search']) . "%'";
			}
			$sql .= " ORDER BY user_question_id DESC";
			$query = $this->db->query($sql);
			$data=array();
			
			foreach($query->rows as $result){
				
				$answers = $this->getAnswers($result['user_question_id']);				
				$totalanswer = $this->TotalAnswer($result['user_question_id']);				
				$ansdata=array();

				foreach($answers as $answer){
					
					$user_info = $this->getusers();
					
					if(!empty($answer['name'])){
					$answeusername = $answer['name'];
					} else {
					$answeusername = $user_info['username'];	
					}
					
					$ansdata[]=array(
						'answer_add' => date( $this->language->get('date_format_short') ,strtotime($answer['date_added'])),
						'username' => $answeusername,
						'user_question_id' => $answer['user_question_id'],					
						'answer' => $answer['answer'],
						
						);
				}			
				
				$data[]=array(
					'name'  => $result['name'],
					'queestion' => $result['question'],
					'user_question_id' => $result['user_question_id'],					
					'answer' => $ansdata,
					'totalanswer' => $totalanswer,
				);
			}

			return $data;

		}
			
			
		}
		
		public function getAnswers($user_question_id){
			
			$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "user_questions_answer  WHERE  user_question_id='".(int) $user_question_id."' ORDER BY date_added DESC");
			
			return $query->rows;
		}
		
		public function getQuestion($user_question_id){
			
			$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "user_questions  WHERE  user_question_id='".(int) $user_question_id."' AND approved = '1'");
			
			return $query->row;
		}
		public function getAllQuestion($user_question_id){
			
			$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "user_questions WHERE user_question_id='".(int)$user_question_id."'");
			
			return $query->row;
		}
		public function getQuestionByProductId($product_id){
				$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){		
			$query=$this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_questions  WHERE product_id='".(int) $product_id."' AND showquestion=1 AND approved = '1'");
			return $query->row['total'];
		}
		}
		
		public function TotalAnswers($product_id) {
				$tmdquestions_status = $this->config->get('module_tmdquestions_status');
			if(!empty($tmdquestions_status)){
			$query=$this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_questions_answer uqa LEFT JOIN  " . DB_PREFIX . "user_questions uq ON(uqa.user_question_id = uq.user_question_id) WHERE uq.product_id='".(int) $product_id."' AND uq.approved = '1'");
			return $query->row['total'];
		}
		}
		
		public function TotalAnswer($user_question_id) {
			
			$query=$this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_questions_answer WHERE user_question_id='".(int) $user_question_id."'");
			return $query->row['total'];
		}
		
		public function getusers(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user where user_id<>0");		
		return $query->row;
	}
}
