<?php
define('NewLine', '<br />');
class Fedex {
    private $session;
    private $config;
    private $db;
    private $customer;
    private $cart;
    private $response;
    private $international_services;
    protected $address;
    protected $products;

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->session = $registry->get('session');
        $this->customer = $registry->get('customer');
        $this->cart = $registry->get('cart');
        $this->weight = $registry->get('weight');
        $this->length = $registry->get('length');
                
        $this->international_services = ['INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY'];
        $this->response = array();
    }

    function getRates($service, $address) {
        $this->address = $address;
        $this->products = $this->cart->getProducts();
        
        $path_to_wsdl = DIR_SYSTEM . "fedex/wsdl/RateService/RateService_v20.wsdl";        
        
        ini_set("soap.wsdl_cache_enabled", "0");

        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

        $request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                    'Key' => $this->getProperty('key'),
                    'Password' => $this->getProperty('password')
            ),
            'UserCredential' => array(
                    'Key' => $this->getProperty('key'),
                    'Password' => $this->getProperty('password')
            )
        );
        $request['ClientDetail'] = array(
                'AccountNumber' => $this->getProperty('shipaccount'),
                'MeterNumber' => $this->getProperty('meter')
        );
        $request['TransactionDetail'] = array('CustomerTransactionId' => $this->getProperty('customerTransactionId'));
        $request['Version'] = $this->getProperty('RateVersion');
        $request['ReturnTransitAndCommit'] = true;
        $request['RequestedShipment'] = array(
                'DropoffType' => $this->config->get('shipping_fedex_dropoff_type'), // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
                'ShipTimestamp' => $this->getShipmentTimeStamp(),
                'ServiceType' => $service, // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
                'PackagingType' => $this->config->get('shipping_fedex_packaging_type'), // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
                'Shipper' => $this->getProperty('shipper'),
                'Recipient' => $this->getProperty('recipient'),
                'ShippingChargesPayment' => $this->getProperty('ShippingChargesPayment'),
                'PackageCount' => $this->cart->countProducts(),
                'RequestedPackageLineItems' => $this->PackageLineItems($service)
        );
        
        if(in_array($service, $this->international_services)){
            $request['RequestedShipment']['CustomsClearanceDetail'] = array(
                    'DutiesPayment' => array(
                            'PaymentType' => 'RECIPIENT', // valid values RECIPIENT, SENDER and THIRD_PARTY
                    ),//Optional. Descriptive data indicating the method and means of payment to FedEx for providing shipping services.
                    'Commodities' => $this->getCommodities(),
                );
        }
        
        //$request['RequestedShipment']['RateRequestTypes'] = $this->config->get('fedex_rate_type');
        
        try {
            if ($this->setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation($this->setEndpoint('endpoint'));
            }

            $response = $client->getRates($request);

            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $this->response = $response;
            } else {
                $this->writeToLog($client);    // Write to log file
            }
        } catch (SoapFault $exception) {
            $this->writeToLog($client, $exception);    // Write to log file
        }
        
        return $this->response;
    }
    
    function createShipment($data) {
        $path_to_wsdl = DIR_SYSTEM . "fedex/wsdl/ShipService/ShipService_v19.wsdl";
        
        ini_set("soap.wsdl_cache_enabled", "0");

        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

        $request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                'Key' => $this->getProperty('key'),
                'Password' => $this->getProperty('password')
            ),
            'UserCredential' => array(
                'Key' => $this->getProperty('key'),
                'Password' => $this->getProperty('password')
            )
        );
        $request['ClientDetail'] = array(
            'AccountNumber' => $this->getProperty('shipaccount'),
            'MeterNumber' => $this->getProperty('meter')
        );
        $request['TransactionDetail'] = array('CustomerTransactionId' => $this->getProperty('customerTransactionId'));
        $request['Version'] = $this->getProperty('ShipVersion');
        $request['ReturnTransitAndCommit'] = true;
        $request['RequestedShipment']['DropoffType'] = $this->config->get('shipping_fedex_dropoff_type'); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
        $request['RequestedShipment']['ShipTimestamp'] = date('c');
        $request['RequestedShipment']['ServiceType'] = $data['shipping_service']; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
        $request['RequestedShipment']['PackagingType'] = $this->config->get('shipping_fedex_packaging_type'); // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
        $request['RequestedShipment']['TotalWeight'] = array(
            'Value' => $data['weight'],
            'Units' => $data['weight_code']
        );
        $request['RequestedShipment']['Shipper'] = $this->getProperty('shipper');
        $request['RequestedShipment']['Recipient'] = $data['shipping_address'];
        $request['RequestedShipment']['ShippingChargesPayment'] = $this->getProperty('ShippingChargesPayment');
        $request['RequestedShipment']['LabelSpecification'] = $this->getProperty('LabelSpecification');
        $request['RequestedShipment']['PackageCount'] = 1;
        $request['RequestedShipment']['RequestedPackageLineItems'] = array(
            'SequenceNumber' => 1,
            'GroupPackageCount' => 1,
            'Weight' => array(
                'Value' => $data['weight'],
                'Units' => $data['weight_code']
            )
        );
        
        if ($data['shipping_service'] == 'INTERNATIONAL_FIRST' || $data['shipping_service'] == 'INTERNATIONAL_ECONOMY' || $data['shipping_service'] == 'INTERNATIONAL_PRIORITY') {
            $request['RequestedShipment']['CustomsClearanceDetail'] = array(
                'DutiesPayment' => $this->getProperty('DutiesPayment'),
                'DocumentContent' => $this->getProperty('DocumentContent'),
                'CustomsValue' => array(
                    'Currency' => $this->config->get('config_currency'),
                    'Amount' => $data['Total']
                ),
                'Commodities' => $data['Commodities'],
                'ExportDetail' => array(
                    'ExportComplianceStatement' => ($data['aes'] && $data['aes'] != 'undefined') ? 'AES X' . date('Ymd', strtotime(date('c'))) . $data['aes'] : ''
                )
            );
        }        
        
        try {
            if ($this->setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation($this->setEndpoint('endpoint'));
            }
            
            $response = $client->processShipment($request);
            
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $this->response = $response;                
            } else {
                $this->response = $response;
                $this->writeToLog($client);    // Write to log file
            }
        } catch (SoapFault $exception) {
            $this->writeToLog($client, $exception);    // Write to log file
        }
        
        return $this->response;
    }

    function getProperty($var) {
        if ($var == 'key')
            Return $this->config->get('shipping_fedex_key');
        if ($var == 'password')
            Return $this->config->get('shipping_fedex_password');
        if ($var == 'shipaccount')
            Return $this->config->get('shipping_fedex_account');
        if ($var == 'billaccount')
            Return $this->config->get('shipping_fedex_account');
        if ($var == 'dutyaccount')
            Return $this->config->get('shipping_fedex_account');
        if ($var == 'printlabels')
            Return true;
        if ($var == 'printdocuments')
            Return true;
        if ($var == 'meter')
            Return $this->config->get('shipping_fedex_meter');        
//        if ($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m"), date("d") + 1, date("Y")));
//        if ($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m"), date("d") + 1, date("Y"));  
//        if ($var == 'trackingnumber') Return time();
        if ($var == 'customerTransactionId') Return md5(uniqid(time(), true));
//        if ($var == 'hubid') Return '5531';
//        if ($var == 'jobid') Return 'XXX';
        if ($var == 'shipper')
            Return array(
                'Contact' => array(
                    'PersonName' => $this->config->get('config_owner'),
                    'CompanyName' => $this->config->get('config_name'),
                    'PhoneNumber' => $this->config->get('config_telephone')
                ),
                'Address' => array(
                    'StreetLines' => array('9800 Page Avenue'),
                    'City' => 'Saint Louis',
                    'StateOrProvinceCode' => (in_array($this->getCountry($this->config->get('config_country_id'))['iso_code_2'], array('US', 'CA'))) ? ($this->getZone($this->config->get('config_zone_id')) ? $this->getZone($this->config->get('config_zone_id'))['code'] : '') : '',
                    'PostalCode' => $this->config->get('shipping_fedex_postcode'),
                    'CountryCode' => $this->getCountry($this->config->get('config_country_id'))['iso_code_2']
                )
            );
        if ($var == 'recipient')
            Return array(
                'Contact' => array(
                    'PersonName' => $this->address['firstname'] . ' ' . $this->address['lastname'],
                    'CompanyName' => $this->address['company'],
                    'PhoneNumber' => $this->customer->getTelephone() ? $this->customer->getTelephone() : 00
                ),
                'Address' => array(
                    'StreetLines' => array($this->address['address_1'], $this->address['address_2']),
                    'City' => $this->address['city'],
                    'StateOrProvinceCode' => (in_array($this->address['iso_code_2'], array('US', 'CA'))) ? $this->address['zone_code'] : '',
                    'PostalCode' => $this->address['postcode'],
                    'CountryCode' => $this->address['iso_code_2'],
                    'Residential' => false
                )
            );
        if ($var == 'shippingchargespayment')
            Return array(
                'PaymentType' => 'SENDER',
                'Payor' => array(
                    'ResponsibleParty' => array(
                        'AccountNumber' => getProperty('billaccount'),
                        'Contact' => null,
                        'Address' => array('CountryCode' => 'US')
                    )
                )
            );
        if($var == 'RateVersion')
            Return array(
                'ServiceId' => 'crs',
                'Major' => '20',
                'Intermediate' => '0',
                'Minor' => '0'
            );
        if($var == 'ShipVersion')
            Return array(
                'ServiceId' => 'ship', 
                'Major' => '19', 
                'Intermediate' => '0', 
                'Minor' => '0'
            );
        if($var == 'ShippingChargesPayment')
            Return array(
                'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                'Payor' => array(
                    'ResponsibleParty' => array(
                        'AccountNumber' => $this->getProperty('billaccount'),
                        'CountryCode' => $this->getCountry($this->config->get('config_country_id'))['iso_code_2']
                    )
                )
            );
        if($var == 'LabelSpecification')
            Return array(
                'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
                'ImageType' => 'PDF', // valid values DPL, EPL2, PDF, ZPLII and PNG
                'LabelStockType' => 'PAPER_7X4.75'
            );
        if($var == 'DutiesPayment')
            Return array(
                    'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                    'Payor' => array(
                        'ResponsibleParty' => array(
                            'AccountNumber' => $this->getProperty('dutyaccount'),
                            'Contact' => null,
                            'Address' => array('CountryCode' => 'US')
                        )
                    )
                );
        if($var == 'DocumentContent')
            Return 'NON_DOCUMENTS';        
    }
    
    function getShipmentTimeStamp(){
        $date = time();

        $day = date('l', $date);

        if ($day == 'Saturday') {
                $date += 172800;
        } elseif ($day == 'Sunday') {
                $date += 86400;
        }
        
        return date('c', $date);
    }
    
    function getCountry($country_id){
        $country_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '".(int)$country_id."'");
        
        return $country_info->row;
    }
    
    function getZone($zone_id){
        $zone_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '".(int)$zone_id."'");
        
        return $zone_info->row;
    }
    
    function setEndpoint($var){
            if($var == 'changeEndpoint') Return false;
            if($var == 'endpoint') Return 'XXX';
    }
    
    function PackageLineItems($service, $packageLineItem = array(), $sequenceNumber = 1) {
            foreach($this->products as $product){
                    for($i = 1; $i <= $product['quantity']; $i++){
                            $packageLineItem[] = array(
                                    'SequenceNumber' => $sequenceNumber++,
                                    'InsuredValue' => array(
                                            'Currency' => 'USD',
                                            'Amount' => $product['price']
                                    ),
                                    'GroupPackageCount' => 1,
                                    'GroupNumber' => 1,
                                    'Weight' => array(
                                            'Value' => $this->weight->convert($product['weight'], $this->config->get('config_weight_class_id'), $this->config->get('shipping_fedex_weight_class_id')) / $product['quantity'],
                                            'Units' => strtoupper($this->weight->getUnit($this->config->get('shipping_fedex_weight_class_id')))
                                    ),
                                    'Dimensions' => array(
                                            'Length' => $product['length'],
                                            'Width' => $product['width'],
                                            'Height' => $product['height'],
                                            'Units' => strtoupper($this->length->getUnit($this->config->get('shipping_fedex_length_class_id')))
                                    ),
                                    'SpecialServicesRequested' => $this->addSpecialService($service, $product)
                            );
                    }
            }

            return $packageLineItem;
    }
    
    function addSpecialService($service, $product){
            $special_service = array();

            if($service == 'FEDEX_GROUND' && $product['shipping_code'] == 'GROUND' && $this->isAllThroughGround()){
                    if ($product['is_ground_hazmat']) {
                            if ($product['hazardous']) {
                                    $special_service['SpecialServiceTypes'][] = 'DANGEROUS_GOODS';
                                    $special_service['DangerousGoodsDetail']['Options'] = 'HAZARDOUS_MATERIALS';
                            } else {
                                    $special_service['DangerousGoodsDetail']['Regulation'] = 'ORMD';
                                    $special_service['DangerousGoodsDetail']['Options'] = 'ORM_D';
                            }
                    }
            }else{
                    if(in_array($service, $this->international_services) && ($this->hasHazardousAccessible() | $this->hasHazardousInAccessible())){
                            $special_service['SpecialServiceTypes'][] = 'DANGEROUS_GOODS';
                    } elseif($product['hazardous']){
                            $special_service['SpecialServiceTypes'][] = 'DANGEROUS_GOODS';
                    }

                    if($product['shipping_code'] == 'BLUE' || $product['shipping_code'] == 'DRY'){
                            $special_service['SpecialServiceTypes'][] = 'DRY_ICE';
                    }

                    if(in_array($service, $this->international_services) && $this->hasHazardousAccessible()){
                            $special_service['DangerousGoodsDetail']['Accessibility'] = 'ACCESSIBLE';
                    } elseif(in_array($service, $this->international_services) && $this->hasHazardousInAccessible()){
                            $special_service['DangerousGoodsDetail']['Accessibility'] = 'INACCESSIBLE';
                    } elseif($product['hazardous'] == 1) {
                            $special_service['DangerousGoodsDetail']['Accessibility'] = 'ACCESSIBLE';
                    } elseif ($product['hazardous'] == 2){
                            $special_service['DangerousGoodsDetail']['Accessibility'] = 'INACCESSIBLE';
                    }

                    if($product['shipping_code'] == 'BLUE' || $product['shipping_code'] == 'DRY'){
                            $special_service['DryIceWeight'] = array(
                                    'Value' => $this->weight->convert($product['weight'], $this->config->get('config_weight_class_id'), 1) / $product['quantity'],
                                    'Units' => 'KG'
                            );
                    }
            }

            return $special_service;
    }
    
    public function hasHazardousAccessible(){ 
            $hazardous = false;

            foreach($this->products as $product){
                    if ($product['hazardous'] == 1) {
                            $hazardous = true;
                            break;
                    }
            }

            return $hazardous;
    }

    public function hasHazardousInAccessible(){
            $hazardous = false;

            foreach($this->products as $product){
                    if ($product['hazardous'] == 2) {
                            $hazardous = true;
                            break;
                    }
            }

            return $hazardous;
    }
    
    public function isAllThroughGround(){
            $isAllThroughGround = true;

            foreach($this->products as $product){
                    if ($product['shipping_code'] !== 'GROUND') {
                            $isAllThroughGround = false;
                            break;
                    }
            }

            return $isAllThroughGround;
    }
    
    function getCommodities($commodities = array()){
            foreach($this->products as $product){
                    $commodities[] = array(
                            'Name' => $product['name'],
                            'NumberOfPieces' => $product['quantity'],
                            'Description' => $product['name'],
                            'CountryOfManufacture' => 'US',
                            //'HarmonizedCode' => '340290100000',
                            'Weight' => $product['weight'],
                            'Quantity' => $product['quantity'],
                            'QuantityUnits' => 'EA',
                            'UnitPrice' => $product['price'],                            
                            'CustomsValue' => array(
                                    'Currency' => 'USD',
                                    'Amount' => $product['quantity'] * $product['price']
                            )
                    );
            }

            return $commodities;
    }

    function writeToLog($client, $exception = ''){
        if (!$logfile = fopen(DIR_LOGS . '/fedextransactions.log', "a"))
        {
            error_func("Cannot open " . DIR_LOGS . '/fedextransactions.log' . " file.\n", 0);
            exit(1);
        }
        
        fwrite($logfile, sprintf("\r%s:- %s",date("D M j G:i:s T Y"), $client->__getLastRequest() . "\r\n" . $client->__getLastResponse() . "\r\n Exception \n" . $exception ."\r\n\r\n"));
  }
}
