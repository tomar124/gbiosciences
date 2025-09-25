<?php

class Hubspot {
    private $hapikey;
    private $create_endpoint;
    private $update_endpoint;
    private $create_update_endpoint;
    private $email_endpoint;
    private $config;
    
    function __construct($registry) {
        $this->config = $registry->get('config');
        $this->hapikey = $this->config->get('config_hubspot_key');
        
        if(empty($this->hapikey)){
            return FALSE;
        }
        $this->create_endpoint = "http://api.hubapi.com/contacts/v1/contact?hapikey=" . $this->hapikey;
        $this->update_endpoint = "http://api.hubapi.com/contacts/v1/contact/vid/contact_id/profile?hapikey=" . $this->hapikey;
        $this->create_update_endpoint = "http://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/email_id/?hapikey=" . $this->hapikey;
        $this->email_endpoint = "http://api.hubapi.com/contacts/v1/contact/email/email_id/profile?hapikey=" . $this->hapikey;
    }
    
    public function _create($data) {        
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        @curl_setopt($ch, CURLOPT_URL, $this->create_endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        @curl_close($ch);
        $response = json_decode($response);

        $this->_update($data, $response->identityProfile->vid);
    }
    
    public function _update_by_contact($data, $contact_id) {
        $this->update_endpoint = str_replace('contact_id', $contact_id, $this->update_endpoint);
        
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        @curl_setopt($ch, CURLOPT_URL, $this->update_endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        @curl_close($ch);
    }
    
    public function _update_by_email($data, $email_id) {
        $contact_id = $this->_get_by_email($email_id);
        
        if($contact_id !== FALSE){
            $this->update_endpoint = str_replace('contact_id', $contact_id, $this->update_endpoint);
            
            $ch = @curl_init();
            @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            @curl_setopt($ch, CURLOPT_URL, $this->update_endpoint);
            @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = @curl_exec($ch);
            @curl_close($ch);
        }
    }
    
    public function _create_update($data, $email_id) {
        $this->create_update_endpoint = str_replace('email_id', $email_id, $this->create_update_endpoint);
        
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        @curl_setopt($ch, CURLOPT_URL, $this->create_update_endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        @curl_close($ch);
    }
    
    public function _get_by_email($email_id){
        $this->email_endpoint = str_replace('email_id', $email_id, $this->email_endpoint);
        
        $ch = @curl_init();
        
        @curl_setopt($ch, CURLOPT_URL, $this->email_endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        @curl_close($ch);
        $response = json_decode($response);
        
        if(isset($response->vid) && !empty($response->vid)){
            return $response->vid;
        }else{
            return FALSE;
        }        
    }
}
