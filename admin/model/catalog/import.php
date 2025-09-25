<?php
// Error Handler
function error_handler_for_export_import($errno, $errstr, $errfile, $errline) {
    global $registry;

    switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                    $errors = "Notice";
                    break;
            case E_WARNING:
            case E_USER_WARNING:
                    $errors = "Warning";
                    break;
            case E_ERROR:
            case E_USER_ERROR:
                    $errors = "Fatal Error";
                    break;
            default:
                    $errors = "Unknown";
                    break;
    }

    $config = $registry->get('config');
    $url = $registry->get('url');
    $request = $registry->get('request');
    $session = $registry->get('session');
    $log = $registry->get('log');

    if ($config->get('config_error_log')) {
            $log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
    }

    if (($errors=='Warning') || ($errors=='Unknown')) {
            return true;
    }

    if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='catalog/import'))  {
            if ($config->get('config_error_display')) {
                    echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
            }
    } else {
            $session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
            $token = $request->get['token'];
            $link = $url->link( 'catalog/import', 'token='.$token, 'SSL' );
            header('Status: ' . 302);
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
            exit();
    }

return true;
}
function error_handler_for_merge($errno, $errstr, $errfile, $errline) {
    global $registry;

    switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                    $errors = "Notice";
                    break;
            case E_WARNING:
            case E_USER_WARNING:
                    $errors = "Warning";
                    break;
            case E_ERROR:
            case E_USER_ERROR:
                    $errors = "Fatal Error";
                    break;
            default:
                    $errors = "Unknown";
                    break;
    }

    $config = $registry->get('config');
    $url = $registry->get('url');
    $request = $registry->get('request');
    $session = $registry->get('session');
    $log = $registry->get('log');

    if ($config->get('config_error_log')) {
            $log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
    }

    if (($errors=='Warning') || ($errors=='Unknown')) {
            return true;
    }

    if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='catalog/import/merge'))  {
            if ($config->get('config_error_display')) {
                    echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
            }
    } else {
            $session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
            $token = $request->get['token'];
            $link = $url->link( 'catalog/import/merge', 'token='.$token, 'SSL' );
            header('Status: ' . 302);
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
            exit();
    }

return true;
}

function error_handler_for_export_import_customer($errno, $errstr, $errfile, $errline) {
    global $registry;

    switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                    $errors = "Notice";
                    break;
            case E_WARNING:
            case E_USER_WARNING:
                    $errors = "Warning";
                    break;
            case E_ERROR:
            case E_USER_ERROR:
                    $errors = "Fatal Error";
                    break;
            default:
                    $errors = "Unknown";
                    break;
    }

    $config = $registry->get('config');
    $url = $registry->get('url');
    $request = $registry->get('request');
    $session = $registry->get('session');
    $log = $registry->get('log');

    if ($config->get('config_error_log')) {
            $log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
    }

    if (($errors=='Warning') || ($errors=='Unknown')) {
            return true;
    }

    if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='sale/customer/import'))  {
            if ($config->get('config_error_display')) {
                    echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
            }
    } else {
            $session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
            $token = $request->get['token'];
            $link = $url->link( 'sale/customer/import', 'token='.$token, 'SSL' );
            header('Status: ' . 302);
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
            exit();
    }

return true;
}
function error_handler_for_merge_customer($errno, $errstr, $errfile, $errline) {
    global $registry;

    switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                    $errors = "Notice";
                    break;
            case E_WARNING:
            case E_USER_WARNING:
                    $errors = "Warning";
                    break;
            case E_ERROR:
            case E_USER_ERROR:
                    $errors = "Fatal Error";
                    break;
            default:
                    $errors = "Unknown";
                    break;
    }

    $config = $registry->get('config');
    $url = $registry->get('url');
    $request = $registry->get('request');
    $session = $registry->get('session');
    $log = $registry->get('log');

    if ($config->get('config_error_log')) {
            $log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
    }

    if (($errors=='Warning') || ($errors=='Unknown')) {
            return true;
    }

    if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='sale/customer/import'))  {
            if ($config->get('config_error_display')) {
                    echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
            }
    } else {
            $session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
            $token = $request->get['token'];
            $link = $url->link( 'sale/customer/import', 'token='.$token, 'SSL' );
            header('Status: ' . 302);
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
            exit();
    }

return true;
}

function fatal_error_shutdown_handler_for_export_import(){
    $last_error = error_get_last();
    if ($last_error['type'] === E_ERROR) {
            // fatal error
            error_handler_for_export_import(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
    }
}
ini_set('max_execution_time', 36000);
ini_set('memory_limit','512M');
class ModelCatalogImport extends Model {
    private $error = array();
    protected $null_array = array();
    private $child_id;
    private $category_path;
    private $pdf_catalog_ids;
    private $newPdfFileName;
    private $coverPageFile;
    
    function __construct($registry) {
        parent::__construct($registry);

        $this->getAllCategoriesPath();
    }

    public function product($csv_file) {
        $csv = array_map('str_getcsv', file($csv_file));

        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });

        array_shift($csv); # remove column header  

        $datas = $csv;

        foreach ($csv as $data) {
            $result = FALSE;
            $product_id = 0;

            $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE name = '" . $this->db->escape(htmlentities($data['product'])) . "'");

            if ($query->num_rows) {
                $result = TRUE;
                $product_id = $query->row['product_id'];
            } else {
                $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE name = '" . $this->db->escape(html_entity_decode($data['product'])) . "'");

                if ($query->num_rows) {
                    $result = TRUE;
                    $product_id = $query->row['product_id'];
                }
            }

            if ($result && $product_id != 0) {
                $this->productUpdate($data, $product_id);
            } else {
                $this->productInsert($data);
            }
        }

        /* foreach ($this->child_id as $product_id => $child_ids) {
          $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "gp_grouped WHERE product_id = '" . $product_id . "'");

          if (!$query->num_rows) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped SET product_id = '" . $product_id . "'");
          }

          foreach ($child_ids as $child_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped_child SET product_id = '" . $product_id . "', child_id = '" . $child_id . "'");
          }
          } */
    }

    public function productUpdate($data, $product_id) {
        echo "update <pre>";
        print_r($data);
        echo "</pre>";
        die;

        $sql = "UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape(htmlentities($data['product'])) . "'";

        if (!empty($data['image'])) {
            $sql .= ", image = '" . $this->db->escape($data['image']) . "'";
        }

        if (!empty($data['product status'])) {
            $sql .= ", status = '" . $data['product status'] . "'";
        }

        $sql .= " WHERE product_id = '" . $product_id . "'";
        $this->db->query($sql);

        $sql = "UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape(htmlentities($data['product'])) . "', language_id = 1";

        if (!empty($data['product description'])) {
            $sql .= ", description = '" . $this->db->escape(htmlentities(html_entity_decode($data['product description']))) . "'";
        }

        if (!empty($data['meta title'])) {
            $sql .= ", meta_title = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta title']))) . "'";
        }

        if (!empty($data['meta description'])) {
            $sql .= ", meta_description = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta description']))) . "'";
        }

        if (!empty($data['meta keyword'])) {
            $sql .= ", meta_keyword = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta keyword']))) . "'";
        }

        $sql .= " WHERE product_id = '" . $product_id . "'";
        $this->db->query($sql);

        if (!empty($data['category'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . $product_id . "'");

            $categories = $this->getcategoryids($data['category']);

            foreach ($categories as $category) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET category_id = '" . $category . "', product_id = '" . $product_id . "'");
            }
        }

        if (!empty($data['catalog'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "gp_grouped_child WHERE product_id = '" . $product_id . "'");

            $catalogs = $this->getcatalogids($data['catalog']);

            foreach ($catalogs as $catalog) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped_child SET product_id = '" . $product_id . "', child_id = '" . $catalog . "'");
            }
        }
    }

    public function productInsert($data) {
        echo "Insert <pre>";
        print_r($data);
        echo "</pre>";
        die;

        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "', status = 1");

        $product_id = $this->db->getLastId();

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . $product_id . "', name = '" . $this->db->escape(htmlentities(html_entity_decode($data['product']))) . "', language_id = 1, description = '" . $this->db->escape(htmlentities(html_entity_decode($data['product description']))) . "', meta_title = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta title']))) . "', meta_description = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta description']))) . "', meta_keyword = '" . $this->db->escape(htmlentities(html_entity_decode($data['meta keyword']))) . "'");

        foreach ($categories as $category) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET category_id = '" . $category . "', product_id = '" . $product_id . "'");
        }

        if (!empty($data['category'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . $product_id . "'");

            $categories = $this->getcategoryids($data['category']);

            foreach ($categories as $category) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET category_id = '" . $category . "', product_id = '" . $product_id . "'");
            }
        }

        if (!empty($data['catalog'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "gp_grouped_child WHERE product_id = '" . $product_id . "'");

            $this->db->query("INSERT INTO FROM " . DB_PREFIX . "gp_grouped SET product_id = '" . $product_id . "'");

            $catalogs = $this->getcatalogids($data['catalog']);

            foreach ($catalogs as $catalog) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "gp_grouped_child SET product_id = '" . $product_id . "', child_id = '" . $catalog . "'");
            }
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . $product_id . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . $product_id . "'");
    }

    public function getcategoryids($categories) {

        $categoryIds = array();

        $categories = explode(',#', $categories);

        foreach ($categories as $category) {

            if (array_search($category, $this->category_path)) {
                $categoryIds[] = array_search($category, $this->category_path);
            }
        }

        return $categoryIds;
    }

    public function getcatalogids($catalogs) {

        $catalogsIds = array();

        $catalogs = explode(',#', $catalogs);

        foreach ($catalogs as $catalog) {
            $sql = "SELECT product_id FROM " . DB_PREFIX . "product_description WHERE name = '" . $this->db->escape($catalog) . "'";

            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $catalogsIds[] = $query->row['product_id'];
            }
        }

        return $catalogsIds;
    }

    public function getAllCategoriesPath() {
        $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category");

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $paths = $this->getlist($row['category_id']);
                $paths = array_reverse($paths);
                $link = '';

                foreach ($paths as $path) {
                    $query2 = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . $path . "'");

                    if ($query2->num_rows) {
                        $result3 = $query2->row;
                        $link .= htmlentities($result3['name']) . '/';
                    }
                }

                if (!empty($link)) {
                    $final[$row['category_id']] = rtrim($link, '/');
                }
            }

            $this->category_path = $final;
        } else {
            return array();
        }
    }

    public function getlist($category_links_id) {
        $path = array();

        $path[] = $category_links_id;
        $parent = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int) $category_links_id . "'");
        $result = $parent->row;

        while ($result['parent_id'] != 0) {
            $path[] = $result['parent_id'];
            $result = $this->getParents($result['parent_id']);
        }
        return $path;
    }

    public function getParents($category_id) {
        $parent = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int) $category_id . "'");
        return $parent->row;
    }

    public function merge($filename, $language_technical_id) {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_merge',E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            try {
                    
                $this->coverPageFile = DIR_IMAGE . 'pdfs/coverpage.pdf';

                // exec('aws s3 cp s3://' . DEFAULT_BUCKET . '/pdfs/coverpage.pdf ' . $this->coverPageFile);
                shell_exec('aws s3 cp s3://' . DEFAULT_BUCKET . '/catalog/coverpageheader.gif ' . DIR_IMAGE . 'pdfs/coverpageheader.gif');
                shell_exec('aws s3 cp s3://' . DEFAULT_BUCKET . '/catalog/coverpagefooter.jpg ' . DIR_IMAGE . 'pdfs/coverpagefooter.jpg');

                    // we use the PHPExcel package from http://phpexcel.codeplex.com/
                    $cwd = getcwd();
                    chdir( DIR_SYSTEM.'PHPExcel' );
                    require_once( 'Classes/PHPExcel.php' );
                    chdir( $cwd );

                    // Memory Optimization
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array( ' memoryCacheSize '  => '128MB'  );
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    // parse uploaded spreadsheet file
                    $inputFileType = PHPExcel_IOFactory::identify($filename);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $reader = $objReader->load($filename);
                    
                    // read the various worksheets and load them to the database			
                    if (!$this->validateMerging( $reader )) {
                            return false;
                    }//die('So far so good');
                    $this->clearCache();
                    $this->mergeAndUploadSDS( $reader, $language_technical_id );
                    return true;
            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return false;
            }
    }

    protected function mergeAndUploadSDS( &$reader, $language_technical_id ){
        // get worksheet, if not there return immediately
        $data = $reader->getSheetByName( 'Merging' );
        if ($data==null) {
                return;
        }

        // load the worksheet cells and store them to the database
        $first_row = $merge = array();
        $i = 0;
        $k = $data->getHighestRow();
        for ($i=0; $i<$k; $i+=1) {
                if ($i==0) {
                        $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                        for ($j=1; $j<=$max_col; $j+=1) {
                                $first_row[] = $this->getCell($data,$i,$j);
                        }
                        continue;
                }
                $j = 1;
                $product_id = trim($this->getCell($data,$i,$j++));
                if ($product_id=="") {
                        continue;
                }
                $catalog = trim($this->getCell($data,$i,$j++));
                $document = trim($this->getCell($data,$i,$j++));
                $merge[$product_id][] = str_replace('%20', ' ', $document);
        }

        if($merge) {
            foreach ($merge as $product_id => $document) {
                if($document){
                    $query = $this->db->query("SELECT name FROM ".DB_PREFIX."product_description WHERE product_id='".(int)$product_id."'");

                    if ($query->num_rows && $language = $this->getLanguageTechnicalIdByLanguageTechnicalID($language_technical_id)) {
                        $this->newPdfFile($query->row['name'], $language['code']);

                        require_once DIR_SYSTEM . 'library/PDFMerger.php';
                        $pdf = new PDFMerger();
                        $this->addCoverPage($product_id);
                        $pdf->addPDF($this->coverPageFile);

                        $this->s3->copyFilesToTemp($document);
                        $this->s3->addFilesToQueue($pdf, $document);

                        $merge_return = $this->s3->mergeFilesInQueue($pdf, $this->newPdfFileName);

                        $this->s3->removeFilesFromTemp($document);

                        if ($merge_return !== FALSE) {
                                $this->db->query("DELETE FROM ".DB_PREFIX."product_sds WHERE language_technical_id = '".(int)$language_technical_id."' AND product_id = '".(int)$product_id."'");
                                $this->db->query("INSERT INTO ".DB_PREFIX."product_sds SET language_technical_id = '".(int)$language_technical_id."', product_id = '".(int)$product_id."', pdf = 'pdfs/msds/".$this->newPdfFileName."'");
                        }
                    }
                }
            }

            $this->s3->emptyTempDirectory();
        }
    }

    public function newPdfFile($catalog_name, $language_code) {
        $catalog_name = strip_tags($catalog_name);
        $product_name = preg_replace('/[^A-Za-z0-9\-]/', '-', $catalog_name);
        $catalog_name = preg_replace('/[-]{1,}/', '-', $catalog_name);
        $catalog_name = rtrim($catalog_name, '-');
        if($language_code == ''){
            $catalog_name = $catalog_name . '_msds.pdf';
        } else {
            $catalog_name = $catalog_name . '_msds_' . $language_code . '.pdf';
        }

        $this->newPdfFileName = $catalog_name;
    }

    public function catalog($excelData) {
        array_walk($excelData, function(&$a) use ($excelData) {
            $a = array_combine($excelData[1], $a);
        });

        array_shift($excelData); # remove column header          

        foreach ($excelData as $data) {
            $sql = "SELECT product_id FROM " . DB_PREFIX . "product_description WHERE name = '" . $this->db->escape($data['Cat. #']) . "'";

            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $catalog_id = $query->row['product_id'];

                $sql = "UPDATE " . DB_PREFIX . "product SET model = '" . $data['Product Description'] . "', shipping = 1, shipping_code = '" . $data['Shipping Class'] . "', status = 1, minimum = 1";

                if (!empty($data['Hazardous'])) {
                    switch ($data['Hazardous']) {
                        case 'NO': $sql .= ", hazardous = 0";
                            break;
                        case 'ACCESSIBLE': $sql .= ", hazardous = 1";
                            break;
                        case 'INACCESSIBLE': $sql .= ", hazardous = 2";
                            break;
                    }
                }

                if (!empty($data['Weight'])) {
                    $sql .= ", weight = '" . $data['Weight'] . "', weight_class_id = 6";
                }

                if (!empty($data['Size'])) {
                    $sql .= ", size = '" . $data['Size'] . "'";
                }

                if (!empty($data['Price'])) {
                    $sql .= ", price = '" . $data['Price'] . "'";
                }

                $sql .= ", date_modified = NOW() WHERE product_id = '" . $catalog_id . "'";

                $this->db->query($sql);

                $this->db->query("UPDATE " . DB_PREFIX . "product_description SET language_id = 1, name = '" . $data['Cat. #'] . "', meta_title = '" . $data['Product Description'] . "' WHERE product_id = '" . $catalog_id . "'");
            } else {
                $sql = "INSERT INTO " . DB_PREFIX . "product SET model = '" . $data['Product Description'] . "', shipping = 1, shipping_code = '" . $data['Shipping Class'] . "', status = 1, minimum = 1";

                if (!empty($data['Hazardous'])) {
                    switch ($data['Hazardous']) {
                        case 'NO': $sql .= ", hazardous = 0";
                            break;
                        case 'ACCESSIBLE': $sql .= ", hazardous = 1";
                            break;
                        case 'INACCESSIBLE': $sql .= ", hazardous = 2";
                            break;
                    }
                }

                if (!empty($data['Weight'])) {
                    $sql .= ", weight = '" . $data['Weight'] . "', weight_class_id = 6";
                }

                if (!empty($data['Size'])) {
                    $sql .= ", size = '" . $data['Size'] . "'";
                }

                if (!empty($data['Price'])) {
                    $sql .= ", price = '" . $data['Price'] . "'";
                }

                $sql .= ", date_added = NOW()";

                $query = $this->db->query($sql);

                $catalog_id = $this->db->getLastId();

                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET language_id = 1, name = '" . $data['Cat. #'] . "', meta_title = '" . $data['Product Description'] . "', product_id = '" . $catalog_id . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . $catalog_id . "', layout_id = 0, store_id = 0");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . $catalog_id . "', store_id = 0");
            }
        }

        return TRUE;
    }

    public function update_meta() {
        $sql = "SELECT pd.product_id, pd.name FROM " . DB_PREFIX . "gp_grouped gp LEFT JOIN " . DB_PREFIX . "product_description pd on pd.product_id = gp.product_id"
                . " WHERE pd.meta_title = ''";

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            foreach ($query->rows as $data) {
                $sql = "UPDATE " . DB_PREFIX . "product_description SET meta_title = '" . $this->db->escape($data['name']) . "' WHERE product_id = '" . $data['product_id'] . "'";
                $this->db->query($sql);
            }
        }

        return TRUE;
    }

    public function addCoverPage($catalog_id) {
            $catalog = $catalog_model = $size = '';

            $sql = "SELECT pd.name as catalog, CASE WHEN special_product = 1 THEN pd.description ELSE p.model END as model, p.size FROM " . DB_PREFIX . "product_description pd"
                    . " LEFT JOIN " . DB_PREFIX . "product p on p.product_id = pd.product_id"
                    . " WHERE pd.product_id = '" . $catalog_id . "'";

            $query = $this->db->query($sql);

            if ($query->num_rows) {
                    $catalog = $query->row['catalog'];
                    $catalog_model = $query->row['model'];
                    $size = $query->row['size'];
            }

            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'letter');
            // Add a Unicode font (uses UTF-8)
            $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor(54, 95, 145);
            $mid_x = 135;
            $text = 'Safety Data Sheet';
            $pdf->MultiCell(0, 4, $text, 0, 'C');
            $pdf->Ln(60);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(79, 129, 189);        
            $pdf->MultiCell(0, 7, 'Cat. # ' . $catalog, 0, 'C');
            $pdf->Ln(17);
            $pdf->SetFont('DejaVu', '', 17);
            $pdf->SetTextColor(54, 95, 145);
            $pdf->MultiCell(0, 7, html_entity_decode($catalog_model), 0, 'C');
            $pdf->Ln(17);
            $pdf->SetFont('DejaVu', '', 12);
            $pdf->SetTextColor(79, 129, 189);        
            $pdf->MultiCell(0, 7, 'Size: ' . $size, 0, 'C');
            
            $pdf->Output($this->coverPageFile, 'F');
    }
    
    public function get_all_weights(){
        $sql = "SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wcd.weight_class_id = wc.weight_class_id)";
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    public function get_all_lengths(){
        $sql = "SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lcd.length_class_id = lc.length_class_id)";
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    /***
     * Import / Export functions
     ***/
    protected function setCellRow( $worksheet, $row/*1-based*/, $data, &$default_style=null, &$styles=null ) {
            if (!empty($default_style)) {
                    $worksheet->getStyle( "$row:$row" )->applyFromArray( $default_style, false );
            }
            if (!empty($styles)) {
                    foreach ($styles as $col=>$style) {
                            $worksheet->getStyleByColumnAndRow($col,$row)->applyFromArray($style,false);
                    }
            }
            $worksheet->fromArray( $data, null, 'A'.$row, true );
    }
    protected function getDefaultLanguageId() {
            $code = $this->config->get('config_language');
            $sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = '$code'";
            $result = $this->db->query( $sql );
            $language_id = 1;
            if ($result->rows) {
                    foreach ($result->rows as $row) {
                            $language_id = $row['language_id'];
                            break;
                    }
            }
            return $language_id;
    }
    protected function getLanguages() {
            $query = $this->db->query( "SELECT * FROM `".DB_PREFIX."language` WHERE `status`=1 ORDER BY `code`" );
            return $query->rows;
    }
    public function downloadCatalogs(){
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import', E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            // Use the PHPExcel package from http://phpexcel.codeplex.com/
            $cwd = getcwd();
            chdir( DIR_SYSTEM.'PHPExcel' );
            require_once( 'Classes/PHPExcel.php' );
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
            chdir( $cwd );

            // Memory Optimization
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            
            try {
                    // set appropriate timeout limit
                    set_time_limit( 1800 );

                    $languages = $this->getLanguages();
                    $default_language_id = $this->getDefaultLanguageId();

                    // create a new workbook
                    $workbook = new PHPExcel();

                    // set some default styles
                    $workbook->getDefaultStyle()->getFont()->setName('Verdana');
                    $workbook->getDefaultStyle()->getFont()->setSize(10);
                    //$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
                    $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

                    // pre-define some commonly used styles
                    $box_format = array(
                            'fill' => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array( 'rgb' => '275E6E')
                            ),
                            'font' => array(
                                    'color'     => array( 'rgb' => 'FFFFFF')
                            )
                    );
                    $text_format = array(
                            'numberformat' => array(
                                    'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true,
                            )
                    );
                    $price_format = array(
                            'numberformat' => array(
                                    'code' => '######0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );
                    $weight_format = array(
                            'numberformat' => array(
                                    'code' => '##0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );

                    // create the worksheets
                    $worksheet_index = 0;
                    // creating the Catalogs worksheet
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Catalogs' );
                    $this->populateCatalogsWorksheet( $worksheet, $languages, $default_language_id, $price_format, $box_format, $weight_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // Protocols worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Protocol' );
                    $this->populateCatalogProtocolsWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // Msds worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Msds' );
                    $this->populateCatalogMsdsWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // Coas worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Coa' );
                    $this->populateCatalogCoasWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // Technicals worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Technicals' );
                    $this->populateCatalogTechnicalsWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // creating the Catalog, Rewards worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Rewards' );
                    $this->populateCatalogRewardsWorksheet( $worksheet, $default_language_id, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    // creating the Specials worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Specials' );
                    $this->populateCatalogSpecialsWorksheet( $worksheet, $default_language_id, $price_format, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    // creating the Discounts worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Discounts' );
                    $this->populateCatalogDiscountsWorksheet( $worksheet, $default_language_id, $price_format, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    // creating the Valid Values worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Data Terminology' );
                    $this->populateValidValuesWorksheet( $worksheet, $default_language_id, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    $workbook->setActiveSheetIndex(0);

                    // redirect output to client browser
                    $datetime = date('m-d-Y');

                    $filename = 'catalogs-'.$datetime.'.xlsx';

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
                    $objWriter->setPreCalculateFormulas(false);
                    $objWriter->save('php://output');

                    // Clear the spreadsheet caches
                    $this->clearSpreadsheetCache();
                    exit();

            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return;
            }
    }
    public function downloadMergeSample(){
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_merge', E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            // Use the PHPExcel package from http://phpexcel.codeplex.com/
            $cwd = getcwd();
            chdir( DIR_SYSTEM.'PHPExcel' );
            require_once( 'Classes/PHPExcel.php' );
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
            chdir( $cwd );

            // Memory Optimization
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            
            try {
                    // set appropriate timeout limit
                    set_time_limit( 1800 );

                    // create a new workbook
                    $workbook = new PHPExcel();

                    // set some default styles
                    $workbook->getDefaultStyle()->getFont()->setName('Verdana');
                    $workbook->getDefaultStyle()->getFont()->setSize(10);
                    //$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
                    $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

                    // pre-define some commonly used styles
                    $box_format = array(
                            'fill' => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array( 'rgb' => '275E6E')
                            ),
                            'font' => array(
                                    'color'     => array( 'rgb' => 'FFFFFF')
                            )
                    );
                    $text_format = array(
                            'numberformat' => array(
                                    'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                            ),

                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true,
                            )
                    );

                    // create the worksheets
                    $worksheet_index = 0;
                    // creating the Catalogs worksheet
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Merging' );
                    $this->populateMergingsWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    // creating the Valid Values worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Data Terminology' );
                    $this->populateValidValuesMergingWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    $workbook->setActiveSheetIndex(0);

                    // redirect output to client browser
                    $datetime = date('m-d-Y');

                    $filename = 'merging-'.$datetime.'.xlsx';

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
                    $objWriter->setPreCalculateFormulas(false);
                    $objWriter->save('php://output');

                    // Clear the spreadsheet caches
                    $this->clearSpreadsheetCache();
                    exit();

            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return;
            }
    }
    protected function populateGroupedProductsImagesWorksheet( $worksheet, $box_format, $text_format ) {
        // Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('alt_text'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_caption'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order')+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
		$styles[$j] = &$text_format;
		$data[$j++] = 'image';
		$styles[$j] = &$text_format;
		$data[$j++] = 'alt_text';
		$styles[$j] = &$text_format;
                $data[$j++] = 'image_caption';
                $data[$j++] = 'sort_order';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product rewards data
		$i += 1;
		$j = 0;
                
                $sql = "SELECT pi.product_id As product_id, pi.image As image, pi.alt_text As alt_text, pi.image_caption As image_caption, pi.sort_order As sort_order "
                        . "FROM " . DB_PREFIX . "product_image pi "
                        . "LEFT JOIN " . DB_PREFIX . "product p ON pi.product_id = p.product_id "
                        . "LEFT JOIN " . DB_PREFIX . "gp_grouped gp ON p.product_id = gp.product_id "
                        . "WHERE p.special_product = 0 AND gp.product_id IS NOT NULL ORDER BY pi.product_id ASC, pi.sort_order ASC";
                $query = $this->db->query($sql);
                
                if($query->num_rows){
                    foreach ($query->rows as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(26);
                            $data = array();
                            $data[$j++] = $row['product_id'];
                            if ($row['image'] == "") {
                                continue;
                            }
                            $data[$j++] = str_replace(['///', '//'], '/', $row['image']);
                            $data[$j++] = $row['alt_text'];
                            $data[$j++] = $row['image_caption'];
                            $data[$j++] = $row['sort_order'];
                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
                }
    }
    protected function populateGroupedProductsRefrenceWorksheet( $worksheet, $box_format, $text_format ) {
        // Set the column widths
		$j = 0;
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('islink'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text'),30)+1);
		$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('link'),30)+1);
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('year')+1);

		// The heading row and column styles
		$styles = array();
		$data = array();
		$i = 1;
		$j = 0;
		$data[$j++] = 'product_id';
                $data[$j++] = 'islink';
		$styles[$j] = &$text_format;
		$data[$j++] = 'text';
		$styles[$j] = &$text_format;
		$data[$j++] = 'link';
                $data[$j++] = 'year';
		$worksheet->getRowDimension($i)->setRowHeight(30);
		$this->setCellRow( $worksheet, $i, $data, $box_format );

		// The actual product rewards data
		$i += 1;
		$j = 0;
                
                $sql = "SELECT pr.product_id As product_id,pr.islink, pr.text As text, pr.link As link, pr.year As year "
                        . "FROM " . DB_PREFIX . "product_references pr "
                        . "LEFT JOIN " . DB_PREFIX . "product p ON pr.product_id = p.product_id "
                        . "LEFT JOIN " . DB_PREFIX . "gp_grouped gp ON p.product_id = gp.product_id "
                        . "WHERE p.special_product = 0 AND gp.product_id IS NOT NULL ORDER BY pr.product_id ASC, pr.year DESC";
                $query = $this->db->query($sql);
                
                if($query->num_rows){
                    foreach ($query->rows as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(26);
                            $data = array();
                            $data[$j++] = $row['product_id'];
                            $data[$j++] = $row['islink'];
                            $data[$j++] = $row['text'];
                            $data[$j++] = $row['link'];
                            $data[$j++] = $row['year'];
                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
                }
    }
    protected function populateCatalogsWorksheet( &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog')+4,20)+1);
            }
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Description'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('shipping'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('hazardous'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('is_ground_hazmat'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('size'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('cart_comment'),10)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'),6)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_unit'),3)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length'),8)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('width'),8)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('height'),8)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length_unit'),3)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'),8)+1);

            // The product headings row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'catalog('.$language['code'].')';
            }
            $styles[$j] = &$text_format;
            $data[$j++] = 'description';
            $data[$j++] = 'shipping';
            $styles[$j] = &$price_format;
            $data[$j++] = 'price';
            $data[$j++] = 'hazardous';
            $data[$j++] = 'is_ground_hazmat';
            $styles[$j] = &$text_format;
            $data[$j++] = 'size';
            $styles[$j] = &$text_format;
            $data[$j++] = 'cart_comment';
            $styles[$j] = &$weight_format;
            $data[$j++] = 'weight';
            $data[$j++] = 'weight_unit';
            $data[$j++] = 'length';
            $data[$j++] = 'width';
            $data[$j++] = 'height';
            $data[$j++] = 'length_unit';
            $data[$j++] = 'status';		
            $data[$j++] = 'points';
            $data[$j++] = 'sort_order';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual products data
            $i += 1;
            $j = 0;
            $store_ids = $this->getStoreIdsForProducts();
            $products = $this->getImportExportCatalogs( $languages, $default_language_id, $product_fields );
            $len = count($products);
            foreach ($products as $row) {
                    $data = array();
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $product_id = $row['product_id'];
                    $data[$j++] = $product_id;
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
                    }
                    $data[$j++] = $row['model'];
                    $data[$j++] = $row['shipping_code'];
                    $data[$j++] = $row['price'];
                    $data[$j++] = $row['hazardous'];
                    $data[$j++] = $row['is_ground_hazmat'];
                    $data[$j++] = $row['size'];
                    $data[$j++] = $row['cart_comment'];
                    $data[$j++] = $row['weight'];
                    $data[$j++] = $row['weight_unit'];
                    $data[$j++] = $row['length'];
                    $data[$j++] = $row['width'];
                    $data[$j++] = $row['height'];
                    $data[$j++] = $row['length_unit'];
                    $data[$j++] = ($row['status']==0) ? 'false' : 'true';			
                    $data[$j++] = $row['points'];
                    $data[$j++] = $row['sort_order'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    } 
    protected function populateGPDataWorksheet( $worksheet, $box_format, $text_format ){
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog_id'),4)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('child_sort_order'),4)+1);
            
            // The product headings row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $data[$j++] = 'catalog_id';
            $data[$j++] = 'child_sort_order';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );
            // The actual products data
            $i += 1;
            $j = 0;

            $sql = "SELECT product_id, child_id, child_sort_order from `".DB_PREFIX."gp_grouped_child` WHERE product_id <> 0 ORDER BY product_id ASC;";
            $query = $this->db->query( $sql );
            foreach ($query->rows as $row) {
                    $data = array();
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $product_id = $row['product_id'];
                    $data[$j++] = $product_id;
                    $data[$j++] = $row['child_id'];
                    $data[$j++] = $row['child_sort_order'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function populateGroupedProductWorksheet( &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Product_Name')+4,20)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Description')+4,20)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Title')+4,30)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Description')+4,30)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Meta_Tag_Keywords')+4,30)+1);
            }
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image_Alt'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Image_caption'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Sort_order'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Status'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Categories'),30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Related_Products'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Reference'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('SEO_Keywords'),20)+1);

            // The product headings row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'Product_Name('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'Description('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'Meta_Tag_Title('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'Meta_Tag_Description('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'Meta_Tag_Keywords('.$language['code'].')';
            }
            $styles[$j] = &$text_format;
            $data[$j++] = 'Image';
            $data[$j++] = 'alt_text';
            $data[$j++] = 'caption';
            $data[$j++] = 'Sort_order';
            $data[$j++] = 'Status';
            $styles[$j] = &$price_format;
            $data[$j++] = 'Categories';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Related_Products';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Reference';
            $styles[$j] = &$text_format;
            $data[$j++] = 'SEO_Keywords';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual products data
            $i += 1;
            $j = 0;
            $store_ids = $this->getStoreIdsForProducts();
            $products = $this->getImportExportGroupedProducts( $languages, $default_language_id, $product_fields );
            $len = count($products);
            foreach ($products as $row) {
                    $data = array();
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $product_id = $row['product_id'];
                    $data[$j++] = $product_id;
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['name'][$language['code']],  ENT_QUOTES, 'UTF-8');
                    }
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['description'][$language['code']], ENT_QUOTES, 'UTF-8');
                    }
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['meta_title'][$language['code']], ENT_QUOTES, 'UTF-8');
                    }
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['meta_description'][$language['code']], ENT_QUOTES, 'UTF-8');
                    }
                    foreach ($languages as $language) {
                            $data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']] ,ENT_QUOTES, 'UTF-8');
                    }
                    $data[$j++] = $row['image'];
                    $data[$j++] = $row['alt_text'];
                    $data[$j++] = $row['caption'];
                    $data[$j++] = $row['sort_order'];
                    $data[$j++] = $row['status'];
                    $data[$j++] = $row['categories'];
                    $data[$j++] = $row['relateds'];
                    $data[$j++] = html_entity_decode($row['reference'], ENT_QUOTES, 'UTF-8');
                    $data[$j++] = $row['keyword'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    } 
    protected function getImportExportCatalogs( &$languages, $default_language_id, $product_fields ) {
            $sql  = "SELECT ";
            $sql .= "  p.product_id,";
            $sql .= "  p.model,";
            $sql .= "  p.shipping_code,";
            $sql .= "  p.price,";
            $sql .= "  p.points,";
            $sql .= "  p.weight,";
            $sql .= "  wc.unit AS weight_unit,";
            $sql .= "  p.length,";
            $sql .= "  p.width,";
            $sql .= "  p.height,";
            $sql .= "  p.hazardous,";
            $sql .= "  p.is_ground_hazmat,";
            $sql .= "  p.size,";
            $sql .= "  p.cart_comment,";
            $sql .= "  p.status,";
            $sql .= "  p.sort_order,";
            $sql .= "  mc.unit AS length_unit ";
            $sql .= "FROM `".DB_PREFIX."product` p ";
            $sql .= "LEFT JOIN `".DB_PREFIX."weight_class_description` wc ON wc.weight_class_id = p.weight_class_id ";
            $sql .= "AND wc.language_id=$default_language_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."length_class_description` mc ON mc.length_class_id=p.length_class_id WHERE p.special_product<>'1' ";
            $sql .= "AND mc.language_id=$default_language_id AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";		
            $sql .= "GROUP BY p.product_id ";
            $sql .= "ORDER BY p.product_id ";
            $sql .= "; ";

            $results = $this->db->query( $sql );
            $product_descriptions = $this->getImportExportCatalogDescriptions( $languages );
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    foreach ($results->rows as $key=>$row) {
                            if (isset($product_descriptions[$language_code][$key])) {
                                    $results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
                            } else {
                                    $results->rows[$key]['name'][$language_code] = '';
                            }
                    }
            }
            return $results->rows;
    }
    protected function getImportExportGroupedProducts( &$languages, $default_language_id, $product_fields ) {
            $sql  = "SELECT p.product_id, p.image, p.alt_text, p.caption, p.reference, ua.keyword,p.sort_order, p.status, ";
            $sql .= "GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ) AS categories, ";
            $sql .= "GROUP_CONCAT( DISTINCT CAST(pr.product_id AS CHAR(11)) SEPARATOR \",\" ) AS relateds ";
            $sql .= "FROM `".DB_PREFIX."product` p ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_related` pr ON p.product_id=pr.product_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."seo_url` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
            $sql .= "WHERE p.special_product <> '1' ";
            $sql .= "AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id <> '' ";		
            $sql .= "GROUP BY p.product_id ";
            $sql .= "ORDER BY p.product_id";
            $sql .= "; ";
            
            $results = $this->db->query( $sql );
            $product_descriptions = $this->getExportGroupProductDescriptions( $languages );
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    foreach ($results->rows as $key => $row) {
                            if (isset($product_descriptions[$language_code][$key])) {
                                    $results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
                                    $results->rows[$key]['description'][$language_code] = $product_descriptions[$language_code][$key]['description'];
                                    $results->rows[$key]['meta_title'][$language_code] = $product_descriptions[$language_code][$key]['meta_title'];
                                    $results->rows[$key]['meta_description'][$language_code] = $product_descriptions[$language_code][$key]['meta_description'];
                                    $results->rows[$key]['meta_keyword'][$language_code] = $product_descriptions[$language_code][$key]['meta_keyword'];
                            } else {
                                    $results->rows[$key]['name'][$language_code] = '';
                                    $results->rows[$key]['description'][$language_code] = '';
                                    $results->rows[$key]['meta_title'][$language_code] = '';
                                    $results->rows[$key]['meta_description'][$language_code] = '';
                                    $results->rows[$key]['meta_keyword'][$language_code] = '';
                            }
                    }
            }
            return $results->rows;
    }
    protected function getImportExportCatalogDescriptions( &$languages ) {
            // query the product_description table for each language
            $product_descriptions = array();
            foreach ($languages as $language) {
                    $language_id = $language['language_id'];
                    $language_code = $language['code'];
                    $sql  = "SELECT p.product_id, pd.name ";
                    $sql .= "FROM `".DB_PREFIX."product` p ";
                    $sql .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id=p.product_id AND pd.language_id='".(int)$language_id."' ";
                    $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";			
                    $sql .= "GROUP BY p.product_id ";
                    $sql .= "ORDER BY p.product_id ";
                    $sql .= "; ";

                    $query = $this->db->query( $sql );
                    $product_descriptions[$language_code] = $query->rows;
            }
            return $product_descriptions;
    }
    protected function populateCatalogProtocolsWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document')+30);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'document';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
            $protocols = $this->getCatalogProtocols();
            foreach ($protocols as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['document'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCatalogProtocols() {
            // get the product protocols
            $sql  = "SELECT pp.product_id, pp.pdf as document ";
            $sql .= "FROM `".DB_PREFIX."product_protocol` pp ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pp.product_id=p.product_id) ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY pp.product_id";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function populateCatalogMsdsWorksheet( &$worksheet, &$box_format, &$text_format ) {
            //fetching all technical languages
            $language_technicals = $this->getAllLanguageTechnicals();
            
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            foreach ($language_technicals as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document('.$language['name'].')')+30);
            }

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            foreach ($language_technicals as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'document('.$language['name'].')';
            }
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
            $msds = $this->getCatalogMsds( $language_technicals );
            foreach ($msds as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    foreach ($language_technicals as $language) {
                            $data[$j++] = html_entity_decode($row[$language['name']],ENT_QUOTES,'UTF-8');
                    }
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCatalogMsds( $language_technicals ) {
            // get the product msds
            $sql  = "SELECT DISTINCT ps.product_id ";
            $sql .= "FROM `".DB_PREFIX."product_sds` ps ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (ps.product_id=p.product_id) ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY ps.product_id";
            $results = $this->db->query( $sql );            
            $product_descriptions = $this->getCatalogMsdsDocuments( $language_technicals );
            foreach ($language_technicals as $language) {
                    $language_name = $language['name'];
                    foreach ($results->rows as $key => $row) {
                            if (isset($product_descriptions[$language_name][$row['product_id']])) {
                                    $results->rows[$key][$language_name] = $product_descriptions[$language_name][$row['product_id']];
                            } else {
                                    $results->rows[$key][$language_name] = '';
                            }
                    }
            }
            return $results->rows;
    }
    protected function getCatalogMsdsDocuments( &$language_technicals ) {
            // query the catalogs msds table for each language
            $result = array();
            foreach ($language_technicals as $language) {
                    $language_id = $language['language_technical_id'];
                    $language_name = $language['name'];
                    $sql  = "SELECT ps.product_id, ps.pdf as document ";
                    $sql .= "FROM `".DB_PREFIX."product_sds` ps ";
                    $sql .= "WHERE ps.language_technical_id='".(int)$language_id."' ORDER BY ps.product_id";
                    $sql .= "; ";
                    $query = $this->db->query( $sql );
                    if($query->rows){
                            foreach($query->rows as $row){
                                    $result[$language_name][$row['product_id']] = $row['document'];
                            }
                    } else {
                            $result[$language_name] = array();
                    }
            }
            return $result;
    }
    protected function populateCatalogCoasWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document')+15);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('description')+15);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order')+15);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'document';
            $styles[$j] = &$text_format;
            $data[$j++] = 'description';
            $data[$j++] = 'sort_order';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
            $coas = $this->getCatalogCoas();
            foreach ($coas as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['document'];
                    $data[$j++] = $row['description'];
                    $data[$j++] = $row['sort_order'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCatalogCoas() {
            // get the product coas
            $sql  = "SELECT pc.product_id, pc.pdf as document, pc.description, pc.sort_order ";
            $sql .= "FROM `".DB_PREFIX."product_coa` pc ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pc.product_id=p.product_id) ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY pc.product_id, pc.sort_order";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function populateCatalogTechnicalsWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('title')+15);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('link')+30);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('description')+30);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'title';
            $styles[$j] = &$text_format;
            $data[$j++] = 'link';
            $styles[$j] = &$text_format;
            $data[$j++] = 'description';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
            $technicals = $this->getCatalogTechnicals();
            foreach ($technicals as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['title'];
                    $data[$j++] = $row['link'];
                    $data[$j++] = $row['description'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCatalogTechnicals() {
            // get the product coas
            $sql  = "SELECT pt.product_id, pt.title, pt.link, pt.description ";
            $sql .= "FROM `".DB_PREFIX."product_technical` pt ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pt.product_id=p.product_id) ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY pt.product_id";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function populateCatalogRewardsWorksheet( &$worksheet, $language_id, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('points')+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'customer_group';
            $data[$j++] = 'points';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
            $rewards = $this->getCatalogRewards( $language_id );//echo "<pre>"; print_r($rewards); echo "</pre>"; die;
            foreach ($rewards as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['name'];
                    $data[$j++] = $row['points'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCatalogRewards( $language_id ) {
            // get the product rewards
            $sql  = "SELECT pr.*, cgd.name ";
            $sql .= "FROM `".DB_PREFIX."product_reward` pr ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pr.product_id=p.product_id) ";
            $sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=pr.customer_group_id AND cgd.language_id=$language_id ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY pr.product_id, name";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function getSpecials( $language_id ) {
            // get the product specials
            $sql  = "SELECT ps.*, cgd.name ";
            $sql .= "FROM `".DB_PREFIX."product_special` ps ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (ps.product_id=p.product_id) ";
            $sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=ps.customer_group_id AND cgd.language_id=$language_id ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY ps.product_id, name, ps.priority";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function populateCatalogSpecialsWorksheet( &$worksheet, $language_id, &$price_format, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'),19)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'),19)+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'customer_group';
            $data[$j++] = 'priority';
            $styles[$j] = &$price_format;
            $data[$j++] = 'price';
            $styles[$j] = &$text_format;
            $data[$j++] = 'date_start';
            $styles[$j] = &$text_format;
            $data[$j++] = 'date_end';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product specials data
            $i += 1;
            $j = 0;
            $specials = $this->getSpecials( $language_id, $min_id, $max_id );
            foreach ($specials as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(13);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['name'];
                    $data[$j++] = $row['priority'];
                    $data[$j++] = $row['price'];
                    $data[$j++] = $row['date_start'];
                    $data[$j++] = $row['date_end'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getDiscounts( $language_id ) {
            // get the product discounts
            $sql  = "SELECT pd.*, cgd.name ";
            $sql .= "FROM `".DB_PREFIX."product_discount` pd ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product` p ON (pd.product_id=p.product_id) ";
            $sql .= "LEFT JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=pd.customer_group_id AND cgd.language_id=$language_id ";
            $sql .= "WHERE p.special_product<>'1' AND p.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ORDER BY pd.product_id ASC, name ASC, pd.quantity ASC";

            $result = $this->db->query( $sql );
            return $result->rows;
    }
    protected function populateCatalogDiscountsWorksheet( &$worksheet, $language_id, &$price_format, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('quantity')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'),10)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'),19)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'),19)+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] =  'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] =  'customer_group';
            $data[$j++] =  'quantity';
            $data[$j++] =  'priority';
            $styles[$j] = &$price_format;
            $data[$j++] =  'price';
            $styles[$j] = &$text_format;
            $data[$j++] =  'date_start';
            $styles[$j] = &$text_format;
            $data[$j++] =  'date_end';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product discounts data
            $i += 1;
            $j = 0;
            $discounts = $this->getDiscounts( $language_id, $min_id, $max_id );
            foreach ($discounts as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(13);
                    $data = array();
                    $data[$j++] =$row['product_id'];
                    $data[$j++] =$row['name'];
                    $data[$j++] =$row['quantity'];
                    $data[$j++] =$row['priority'];
                    $data[$j++] =$row['price'];
                    $data[$j++] =$row['date_start'];
                    $data[$j++] =$row['date_end'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    public function getAllLanguageTechnicals(){
            $sql = "SELECT * FROM " . DB_PREFIX . "language_technical lt";
            
            return $this->db->query($sql)->rows;
    }
    protected function getStoreIdsForProducts() {
            $sql =  "SELECT product_id, store_id FROM `".DB_PREFIX."product_to_store` ps;";
            $store_ids = array();
            $result = $this->db->query( $sql );
            foreach ($result->rows as $row) {
                    $productId = $row['product_id'];
                    $store_id = $row['store_id'];
                    if (!isset($store_ids[$productId])) {
                            $store_ids[$productId] = array();
                    }
                    if (!in_array($store_id,$store_ids[$productId])) {
                            $store_ids[$productId][] = $store_id;
                    }
            }
            return $store_ids;
    }
    protected function populateValidValuesWorksheet( &$worksheet, $language_id, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Attribute Name')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Valid Values (Use these values)')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Valid Values Name')+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'Attribute Name';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Valid Values (Use these values)';
            $data[$j++] = 'Valid Values Name';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;

            $weightUnits = $weightTitles = $lengthUnits = $lengthTitles = array();

            foreach($this->getAllWeightUnit() as $weight){
                $weightUnits[] = $weight['unit'];

                $weightTitles[] = $weight['title'];
            }

            foreach($this->getAllMeasurementUnit() as $length){
                $lengthUnits[] = $length['unit'];

                $lengthTitles[] = $length['title'];
            }

            $values = array(
                0 => array(
                    'Attribute Name' => 'hazardous',
                    'Valid Value' => '0, 1, 2',
                    'Valid Value Name' => 'NO, ACCESSIBLE, INACCESSIBLE',
                ),
                1 => array(
                    'Attribute Name' => 'shipping',
                    'Valid Value' => '2DAY, GROUND, STANDARD, BLUE, DRY',
                    'Valid Value Name' => 'Ambient, Ambient (GROUND), Standard, Blue Ice, Dry Ice',
                ),
                2 => array(
                    'Attribute Name' => 'status',
                    'Valid Value' => '1, 0',
                    'Valid Value Name' => 'Enabled, Disabled',
                ),
                3 => array(
                    'Attribute Name' => 'weight_unit',
                    'Valid Value' => implode(', ', $weightUnits),
                    'Valid Value Name' => implode(', ', $weightTitles),
                ),
                4 => array(
                    'Attribute Name' => 'length_unit',
                    'Valid Value' => implode(', ', $lengthUnits),
                    'Valid Value Name' => implode(', ', $lengthTitles),
                )
            );
            foreach ($values as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(13);
                    $data = array();
                    $data[$j++] = $row['Attribute Name'];
                    $data[$j++] = $row['Valid Value'];
                    $data[$j++] = $row['Valid Value Name'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function populateMergingsWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id')+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('catalog')+15);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('document')+15);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'catalog';
            $styles[$j] = &$text_format;
            $data[$j++] = 'document';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;

            $values = array(
                0 => array(
                    'product_id' => 1,
                    'catalog' => '786',
                    'document' => 'document1.pdf'
                ),
                1 => array(
                    'product_id' => 1,
                    'catalog' => '786',
                    'document' => 'document2.pdf'
                ),
                2 => array(
                    'product_id' => 1,
                    'catalog' => '786',
                    'document' => 'document3.pdf'
                ),
                3 => array(
                    'product_id' => 2,
                    'catalog' => '787',
                    'document' => 'document4.pdf'
                ),
                4 => array(
                    'product_id' => 3,
                    'catalog' => '788',
                    'document' => 'document5.pdf'
                ),
                5 => array(
                    'product_id' => 3,
                    'catalog' => '788',
                    'document' => 'document6.pdf'
                )
            );
            
            foreach ($values as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['catalog'];
                    $data[$j++] = $row['document'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function populateValidValuesMergingWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Product IDs')+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('Catalog')+15);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'Product IDs';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Catalog';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;

            $sql = "SELECT pd.product_id, pd.name ";
            $sql .= "FROM `".DB_PREFIX."product_description` pd ";
            $sql .= "WHERE pd.product_id NOT IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND pd.product_id<>'' ORDER BY pd.product_id";	
            
            $query = $this->db->query($sql);
            
            foreach ($query->rows as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['product_id'];
                    $data[$j++] = $row['name'];
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getAllWeightUnit() {
            $language_id = $this->getDefaultLanguageId();
            $sql = "SELECT title, unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    return $query->rows;
            }
            return 'lb';
    }
    protected function getAllMeasurementUnit() {
            $language_id = $this->getDefaultLanguageId();
            $sql = "SELECT title, unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    return $query->rows;
            }
            return 'in';
    }
    protected function clearSpreadsheetCache() {
            $files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');

            if ($files) {
                    foreach ($files as $file) {
                            if (file_exists($file)) {   
                                    @unlink($file);
                                    clearstatcache();
                            }
                    }
            }
    }
    public function uploadCatalog( $filename, $incremental=true ) {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import',E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            try {
                    $this->session->data['export_import_nochange'] = 1;

                    // we use the PHPExcel package from http://phpexcel.codeplex.com/
                    $cwd = getcwd();
                    chdir( DIR_SYSTEM.'PHPExcel' );
                    require_once( 'Classes/PHPExcel.php' );
                    chdir( $cwd );

                    // Memory Optimization
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array( ' memoryCacheSize '  => '128MB'  );
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    // parse uploaded spreadsheet file
                    $inputFileType = PHPExcel_IOFactory::identify($filename);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $reader = $objReader->load($filename);
                    
                    // read the various worksheets and load them to the database			
                    if (!$this->validateCatalogUpload( $reader )) {
                            return false;
                    }
                    $this->clearCache();
                    $this->session->data['export_import_nochange'] = 0;
                    $available_product_ids = array();
                    $this->uploadCatalogs( $reader, $incremental, $available_product_ids );
                    $this->uploadCatalogProtocols( $reader, $incremental );
                    $this->uploadCatalogMsds( $reader, $incremental );
                    $this->uploadCatalogCoa( $reader, $incremental );
                    $this->uploadCatalogTechnical( $reader, $incremental );
                    $this->uploadRewards( $reader, $incremental, $available_product_ids );
                    $this->uploadSpecials( $reader, $incremental, $available_product_ids );
                    $this->uploadDiscounts( $reader, $incremental, $available_product_ids );
                    return true;
            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return false;
            }
    }
    protected function validateMerging( &$reader ){
            $ok = true;

            // worksheets must have correct heading rows
            $data = $reader->getSheetByName( 'Merging' );
            if ($data==null) {
                    return true;
            }

            $expected_heading = array
            ( "product_id", "catalog", "document" );

            $expected_multilingual = array(  );

            
            if (!$this->validateHeading( $data, $expected_heading, $expected_multilingual )) {
                    $this->log->write( $this->language->get('error_merging_header') );
                    $ok = false;
            }

            // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
            $previous_product_id = 0;
            $has_missing_product_ids = false;
            $product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $product_id = $this->getCell($data,$i,1);
                    if ($product_id=="") {
                            if (!$has_missing_product_ids) {
                                    $msg = str_replace( '%1', 'Products', $this->language->get( 'error_missing_product_id' ) );
                                    $this->log->write( $msg );
                                    $has_missing_product_ids = true;
                            }
                            $ok = false;
                            continue;
                    }
                    if (!$this->isInteger($product_id)) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_invalid_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                            continue;
                    }
                    /*if (in_array( $product_id, $product_ids )) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_duplicate_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }*/
                    $product_ids[] = $product_id;
                    if ($product_id < $previous_product_id) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_wrong_order_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $previous_product_id = $product_id;
            }


            return $ok;
    }
    protected function validateCatalogUpload( &$reader ){
            $ok = true;

            // worksheets must have correct heading rows
            if (!$this->validateCatalogs( $reader )) {
                    $this->log->write( $this->language->get('error_products_header') );
                    $ok = false;
            }
            if (!$this->validateCatalogProtocols( $reader )) {
                    $this->log->write( $this->language->get('error_protocols_header') );
                    $ok = false;
            }            
            if (!$this->validateCatalogMsds( $reader )) {
                    $this->log->write( $this->language->get('error_msds_header') );
                    $ok = false;
            }            
            if (!$this->validateCatalogCoas( $reader )) {
                    $this->log->write( $this->language->get('error_coa_header') );
                    $ok = false;
            }            
            if (!$this->validateCatalogTechnicals( $reader )) {
                    $this->log->write( $this->language->get('error_technical_header') );
                    $ok = false;
            }
            if (!$this->validateRewards( $reader )) {
                    $this->log->write( $this->language->get('error_rewards_header') );
                    $ok = false;
            }
            if (!$this->validateSpecials( $reader )) {
                    $this->log->write( $this->language->get('error_specials_header') );
                    $ok = false;
            }
            if (!$this->validateDiscounts( $reader )) {
                    $this->log->write( $this->language->get('error_discounts_header') );
                    $ok = false;
            }

            // certain worksheets rely on the existence of other worksheets
            $names = $reader->getSheetNames();
            $exist_products = false;
            $exist_rewards = false;
            $exist_specials = false;
            $exist_discounts = false;
            foreach ($names as $name) {
                    if ($name=='Catalogs') {
                            $exist_products = true;
                            continue;
                    }
                    if ($name=='Protocol') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_protocol') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Msds') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_msds') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Coa') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_coa') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Technicals') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_technical') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Rewards') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_rewards') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Specials') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Specials
                                    $this->log->write( $this->language->get('error_specials') );
                                    $ok = false;
                            }
                            $exist_specials = true;
                            continue;
                    }
                    if ($name=='Discounts') {
                            if (!$exist_products) {
                                    // Missing Products worksheet, or Products worksheet not listed before Discounts
                                    $this->log->write( $this->language->get('error_discounts') );
                                    $ok = false;
                            }
                            $exist_discounts = true;
                            continue;
                    }
            }

            if (!$ok) {
                    return false;
            }

            if (!$this->validateProductIdColumns( $reader )) {
                    $ok = false;
            }

            return $ok;
    }
    protected function validateUploadGroupedProduct( &$reader ){
            $ok = true;

            // worksheets must have correct heading rows
            if (!$this->validateGrouped( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_heading') );
                    $ok = false;
            }
            if (!$this->validateGPData( $reader )) {
                    $this->log->write( $this->language->get('error_gpdata') );
                    $ok = false;
            }            
            if (!$this->validateGroupedImages( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_images') );
                    $ok = false;
            }  

            if (!$this->validateGroupedReferences( $reader )) {
                    $this->log->write( $this->language->get('error_grouped_citations') );
                    $ok = false;
            }

            // certain worksheets rely on the existence of other worksheets
            $names = $reader->getSheetNames();
            $exist_products = false;
            $exist_rewards = false;
            foreach ($names as $name) {
                    if ($name=='Products') {
                            $exist_products = true;
                            continue;
                    }
                    if ($name=='GP_Data') {
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Images') {
                            $exist_rewards = true;
                            continue;
                    }
                    if ($name=='Citations') {
                            $exist_rewards = true;
                            continue;
                    }
            }

            if (!$ok) {
                    return false;
            }

            if (!$this->validateGroupedIdColumns( $reader )) {
                    $ok = false;
            }

            return $ok;
    }
    protected function validateCatalogs( &$reader ) {
            $data = $reader->getSheetByName( 'Catalogs' );
            if ($data==null) {
                    return true;
            }

            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            $expected_heading = array
            ( "product_id", "catalog", "description", "shipping", "price", "hazardous", "is_ground_hazmat", "size", "cart_comment", "weight", "weight_unit", "length", "width", "height", "length_unit", "status", "points", "sort_order" );

            $expected_multilingual = array( "catalog" );

            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateGrouped( &$reader ) {
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return true;
            }

            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            $expected_heading = array
            ( "product_id", "product_name", "description", "meta_tag_title", "meta_tag_description", "meta_tag_keywords", "image", "alt_text", "caption", "sort_order", "status", "categories", "related_products", "reference", "seo_keywords" );

            $expected_multilingual = array( "product_name", "description", "meta_tag_title", "meta_tag_description", "meta_tag_keywords" );

            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
            
    }
    protected function validateCatalogProtocols( &$reader ) {
            $data = $reader->getSheetByName( 'Protocol' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "document" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateCatalogMsds( &$reader ) {
            $data = $reader->getSheetByName( 'Msds' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "document" );
            $expected_multilingual = array( "document" );
            return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateGPData( &$reader ) {
            $data = $reader->getSheetByName( 'GP_Data' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "catalog_id" );
            $expected_multilingual = array( );
            return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateGroupedImages( &$reader ) {
            $data = $reader->getSheetByName( 'Images' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "image", "alt_text", "image_caption", "sort_order" );
            $expected_multilingual = array( );
            return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateGroupedReferences( &$reader ) {
            $data = $reader->getSheetByName( 'Citations' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "islink", "text", "link", "year" );
            $expected_multilingual = array( );
            return $this->validateMsdsHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateCatalogCoas( &$reader ) {
            $data = $reader->getSheetByName( 'Coa' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "document", "description", "sort_order" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateCatalogTechnicals( &$reader ) {
            $data = $reader->getSheetByName( 'Technical' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array ( "product_id", "title", "link", "description" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateRewards( &$reader ) {
            $data = $reader->getSheetByName( 'Rewards' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array( "product_id", "customer_group", "points" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateSpecials( &$reader ) {
            $data = $reader->getSheetByName( 'Specials' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array( "product_id", "customer_group", "priority", "price", "date_start", "date_end" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateDiscounts( &$reader ) {
            $data = $reader->getSheetByName( 'Discounts' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array( "product_id", "customer_group", "quantity", "priority", "price", "date_start", "date_end" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    function validateHeading( &$data, &$expected, &$multilingual ) {
            $default_language_code = $this->config->get('config_language');
            $heading = array();
            $k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
            $i = 0;
            for ($j=1; $j <= $k; $j+=1) {
                    $entry = $this->getCell($data,$i,$j);
                    $bracket_start = strripos( $entry, '(', 0 );
                    if ($bracket_start === false) {
                            if (in_array( $entry, $multilingual )) {
                                    return false;
                            }
                            $heading[] = strtolower($entry);
                    } else {
                            $name = strtolower(substr( $entry, 0, $bracket_start ));
                            if (!in_array( $name, $multilingual )) {
                                    return false;
                            }
                            $bracket_end = strripos( $entry, ')', $bracket_start );
                            if ($bracket_end <= $bracket_start) {
                                    return false;
                            }
                            if ($bracket_end+1 != strlen($entry)) {
                                    return false;
                            }
                            $language_code = strtolower(substr( $entry, $bracket_start+1, $bracket_end-$bracket_start-1 ));
                            if (count($heading) <= 0) {
                                    return false;
                            }
                            if ($heading[count($heading)-1] != $name) {
                                    $heading[] = $name;
                            }
                    }
            }
            for ($i=0; $i < count($expected); $i+=1) {
                    if (!isset($heading[$i])) {
                            return false;
                    }
                    if ($heading[$i] != $expected[$i]) {
                            return false;
                    }
            }
            return true;
    }
    function validateMsdsHeading( &$data, &$expected, &$multilingual ) {
            $language_technical = $this->getAllLanguageTechnicals();
            $default_language_code = 'English';            
            $heading = array();
            $k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
            
            $i = 0;
            for ($j=1; $j <= $k; $j+=1) {
                    $entry = $this->getCell($data,$i,$j);
                    $bracket_start = strripos( $entry, '(', 0 );
                    if ($bracket_start === false) {
                            if (in_array( $entry, $multilingual )) {
                                    return false;
                            }
                            $heading[] = strtolower($entry);
                    } else {
                            $name = strtolower(substr( $entry, 0, $bracket_start ));
                            if (!in_array( $name, $multilingual )) {
                                    return false;
                            }
                            $bracket_end = strripos( $entry, ')', $bracket_start );
                            if ($bracket_end <= $bracket_start) {
                                    return false;
                            }
                            if ($bracket_end+1 != strlen($entry)) {
                                    return false;
                            }
                            $language_code = strtolower(substr( $entry, $bracket_start+1, $bracket_end-$bracket_start-1 ));
                            if (count($heading) <= 0) {
                                    return false;
                            }
                            if ($heading[count($heading)-1] != $name) {
                                    $heading[] = $name;
                            }
                    }
            }
            for ($i=0; $i < count($expected); $i+=1) {
                    if (!isset($heading[$i])) {
                            return false;
                    }
                    if ($heading[$i] != $expected[$i]) {
                            return false;
                    }
            }
            return true;
    }
    function getCell(&$worksheet,$row,$col,$default_val='') {
            $col -= 1; // we use 1-based, PHPExcel uses 0-based column index
            $row += 1; // we use 0-based, PHPExcel uses 1-based row index
            $val = ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
            if ($val===null) {
                    $val = $default_val;
            }
            return $val;
    }
    protected function validateGroupedIdColumns( &$reader ) {
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return true;
            }
            $ok = true;

            // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
            $previous_product_id = 0;
            $has_missing_product_ids = false;
            $product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $product_id = $this->getCell($data,$i,1);
                    if ($product_id=="") {
                            if (!$has_missing_product_ids) {
                                    $msg = str_replace( '%1', 'Products', $this->language->get( 'error_missing_product_id' ) );
                                    $this->log->write( $msg );
                                    $has_missing_product_ids = true;
                            }
                            $ok = false;
                            continue;
                    }
                    if (!$this->isInteger($product_id)) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_invalid_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                            continue;
                    }
                    if (in_array( $product_id, $product_ids )) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_duplicate_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $product_ids[] = $product_id;
                    if ($product_id < $previous_product_id) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_wrong_order_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $previous_product_id = $product_id;
            }

            // make sure product_ids are numeric entries and are also mentioned in worksheet 'Products'
            $worksheets = array( 'GP_Data', 'Images','Citations' );
            foreach ($worksheets as $worksheet) {
                    $data = $reader->getSheetByName( $worksheet );
                    if ($data==null) {
                            continue;
                    }
                    $ok = true;
                    $previous_product_id = 0;
                    $has_missing_product_ids = false;
                    $unlisted_product_ids = array();
                    $k = $data->getHighestRow();
                    for ($i=1; $i<$k; $i+=1) {
                            $product_id = $this->getCell($data,$i,1);
                            if ($product_id=="") {
                                    if (!$has_missing_product_ids) {
                                            $msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_product_id' ) );
                                            $this->log->write( $msg );
                                            $has_missing_product_ids = true;
                                    }
                                    $ok = false;
                                    continue;
                            }
                            if (!$this->isInteger($product_id)) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                                    continue;
                            }
                            if (!in_array( $product_id, $product_ids )) {
                                    if (!in_array( $product_id, $unlisted_product_ids )) {
                                            $unlisted_product_ids[] = $product_id;
                                            $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_product_id' ) ) );
                                            $this->log->write( $msg );
                                            $ok = false;
                                    }
                            }
                            if ($product_id < $previous_product_id) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                            }
                            $previous_product_id = $product_id;
                    }
            }

            return $ok;
    }
    protected function validateProductIdColumns( &$reader ) {
            $data = $reader->getSheetByName( 'Catalogs' );
            if ($data==null) {
                    return true;
            }
            $ok = true;

            // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
            $previous_product_id = 0;
            $has_missing_product_ids = false;
            $product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $product_id = $this->getCell($data,$i,1);
                    if ($product_id=="") {
                            if (!$has_missing_product_ids) {
                                    $msg = str_replace( '%1', 'Products', $this->language->get( 'error_missing_product_id' ) );
                                    $this->log->write( $msg );
                                    $has_missing_product_ids = true;
                            }
                            $ok = false;
                            continue;
                    }
                    if (!$this->isInteger($product_id)) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_invalid_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                            continue;
                    }
                    if (in_array( $product_id, $product_ids )) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_duplicate_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $product_ids[] = $product_id;
                    if ($product_id < $previous_product_id) {
                            $msg = str_replace( '%2', $product_id, str_replace( '%1', 'Products', $this->language->get( 'error_wrong_order_product_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $previous_product_id = $product_id;
            }

            // make sure product_ids are numeric entries and are also mentioned in worksheet 'Products'
            $worksheets = array( 'Protocol', 'Msds', 'Coa', 'Technicals', 'Rewards', 'Specials', 'Discounts' );
            foreach ($worksheets as $worksheet) {
                    $data = $reader->getSheetByName( $worksheet );
                    if ($data==null) {
                            continue;
                    }
                    $previous_product_id = 0;
                    $has_missing_product_ids = false;
                    $unlisted_product_ids = array();
                    $k = $data->getHighestRow();
                    for ($i=1; $i<$k; $i+=1) {
                            $product_id = $this->getCell($data,$i,1);
                            if ($product_id=="") {
                                    if (!$has_missing_product_ids) {
                                            $msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_product_id' ) );
                                            $this->log->write( $msg );
                                            $has_missing_product_ids = true;
                                    }
                                    $ok = false;
                                    continue;
                            }
                            if (!$this->isInteger($product_id)) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                                    continue;
                            }
                            if (!in_array( $product_id, $product_ids )) {
                                    if (!in_array( $product_id, $unlisted_product_ids )) {
                                            $unlisted_product_ids[] = $product_id;
                                            $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_product_id' ) ) );
                                            $this->log->write( $msg );
                                            $ok = false;
                                    }
                            }
                            if ($product_id < $previous_product_id) {
                                    $msg = str_replace( '%2', $product_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_product_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                            }
                            $previous_product_id = $product_id;
                    }
            }

            return $ok;
    }
    protected function isInteger($input){
            return(ctype_digit(strval($input)));
    }
    protected function clearCache() {
            $this->cache->delete('*');
    }
    protected function uploadCatalogs( &$reader, $incremental, &$available_product_ids=array() ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Catalogs' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old products
            $available_product_ids = array();
            if ($incremental) {
                    $old_product_ids = $this->getAvailableProductIds($data);
            }
            
            // get pre-defined store_ids
            $available_store_ids = $this->getAvailableStoreIds();

            // find the installed languages
            $languages = $this->getLanguages();

            // find the default units
            $default_weight_unit = $this->getDefaultWeightUnit();
            $default_measurement_unit = $this->getDefaultMeasurementUnit();
            $default_stock_status_id = $this->config->get('config_stock_status_id');

            // get weight classes
            $weight_class_ids = $this->getWeightClassIds();

            // get length classes
            $length_class_ids = $this->getLengthClassIds();

            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }

            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $names = array();
                    while ($this->startsWith($first_row[$j-1],"catalog(")) {
                            $language_code = substr($first_row[$j-1],strlen("catalog("),strlen($first_row[$j-1])-strlen("catalog(")-1);
                            $name = $this->getCell($data,$i,$j++);
                            $name = htmlspecialchars( $name );
                            $names[$language_code] = $name;
                    }
                    $model = $this->getCell($data,$i,$j++,'   ');
                    $shipping = $this->getCell($data,$i,$j++);
                    $price = $this->getCell($data,$i,$j++,'0.00');
                    $hazardous = $this->getCell($data,$i,$j++,'0');
                    $is_ground_hazmat = $this->getCell($data,$i,$j++,'0');
                    $size = $this->getCell($data,$i,$j++);
                    $cart_comment = $this->getCell($data,$i,$j++);			
                    $weight = $this->getCell($data,$i,$j++,'0');
                    $weight_unit = $this->getCell($data,$i,$j++,$default_weight_unit);
                    $length = $this->getCell($data,$i,$j++,'0');
                    $width = $this->getCell($data,$i,$j++,'0');
                    $height = $this->getCell($data,$i,$j++,'0');
                    $measurement_unit = $this->getCell($data,$i,$j++,$default_measurement_unit);
                    $status = $this->getCell($data,$i,$j++,'true');
                    $points = $this->getCell($data,$i,$j++,'0');
                    $sort_order = $this->getCell($data,$i,$j++,'0');
                    
                    $product = array();
                    $product['product_id'] = $product_id;
                    $product['names'] = $names;
                    $product['model'] = $model;
                    $product['shipping'] = $shipping;
                    $product['price'] = $price;
                    $product['hazardous'] = $hazardous;
                    $product['is_ground_hazmat'] = $is_ground_hazmat;
                    $product['size'] = $size;
                    $product['cart_comment'] = $cart_comment;
                    $product['weight'] = $weight;
                    $product['weight_unit'] = $weight_unit;
                    $product['length'] = $length;
                    $product['width'] = $width;
                    $product['height'] = $height;
                    $product['measurement_unit'] = $measurement_unit;
                    $product['status'] = $status;
                    $product['points'] = $points;
                    $product['store_ids'] = array(0);
                    $product['stock_status_id'] = $default_stock_status_id;
                    $product['sort_order'] = $sort_order;
                    if ($incremental) {
                            $this->deleteImportExportCatalog( $product_id );
                    }
                    $available_product_ids[$product_id] = $product_id;
                    $this->moreProductCells( $i, $j, $data, $product );
                    $this->storeCatalogIntoDatabase( $product, $languages, $product_fields, $available_store_ids, $weight_class_ids, $length_class_ids );
            }
    }
    protected function startsWith( $haystack, $needle ) {
            if (strlen( $haystack ) < strlen( $needle )) {
                    return false;
            }
            return (substr( $haystack, 0, strlen($needle) ) == $needle);
    }
    protected function getAvailableProductIds(&$data) {
            $available_product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $j = 1;
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $available_product_ids[$product_id] = $product_id;
            }
            return $available_product_ids;
    }
    protected function getAvailableProductChildIds(&$data) {
            $available_product_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $j = 1;
                    $product_id = trim($this->getCell($data,$i,$j++));
                    $child_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $available_product_ids[$product_id] = $product_id;
                    $available_product_ids[$child_id] = $child_id;
            }
            return $available_product_ids;
    }
    protected function getAvailableStoreIds() {
            $sql = "SELECT store_id FROM `".DB_PREFIX."store`;";
            $result = $this->db->query( $sql );
            $store_ids = array(0);
            foreach ($result->rows as $row) {
                    if (!in_array((int)$row['store_id'],$store_ids)) {
                            $store_ids[] = (int)$row['store_id'];
                    }
            }
            return $store_ids;
    }
    protected function getDefaultWeightUnit() {
            $weight_class_id = $this->config->get( 'config_weight_class_id' );
            $language_id = $this->getDefaultLanguageId();
            $sql = "SELECT unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    return $query->row['unit'];
            }
            $sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = 'en'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    $language_id = $query->row['language_id'];
                    $sql = "SELECT unit FROM `".DB_PREFIX."weight_class_description` WHERE language_id='".(int)$language_id."'";
                    $query = $this->db->query( $sql );
                    if ($query->num_rows > 0) {
                            return $query->row['unit'];
                    }
            }
            return 'kg';
    }
    protected function getDefaultMeasurementUnit() {
            $length_class_id = $this->config->get( 'config_length_class_id' );
            $language_id = $this->getDefaultLanguageId();
            $sql = "SELECT unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    return $query->row['unit'];
            }
            $sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = 'en'";
            $query = $this->db->query( $sql );
            if ($query->num_rows > 0) {
                    $language_id = $query->row['language_id'];
                    $sql = "SELECT unit FROM `".DB_PREFIX."length_class_description` WHERE language_id='".(int)$language_id."'";
                    $query = $this->db->query( $sql );
                    if ($query->num_rows > 0) {
                            return $query->row['unit'];
                    }
            }
            return 'cm';
    }
    protected function getWeightClassIds() {
            // find the default language id
            $language_id = $this->getDefaultLanguageId();

            // find all weight classes already stored in the database
            $weight_class_ids = array();
            $sql = "SELECT `weight_class_id`, `unit` FROM `".DB_PREFIX."weight_class_description` WHERE `language_id`=$language_id;";
            $result = $this->db->query( $sql );
            if ($result->rows) {
                    foreach ($result->rows as $row) {
                            $weight_class_id = $row['weight_class_id'];
                            $unit = $row['unit'];
                            if (!isset($weight_class_ids[$unit])) {
                                    $weight_class_ids[$unit] = $weight_class_id;
                            }
                    }
            }

            return $weight_class_ids;
    }
    protected function getLengthClassIds() {
            // find the default language id
            $language_id = $this->getDefaultLanguageId();

            // find all length classes already stored in the database
            $length_class_ids = array();
            $sql = "SELECT `length_class_id`, `unit` FROM `".DB_PREFIX."length_class_description` WHERE `language_id`=$language_id;";
            $result = $this->db->query( $sql );
            if ($result->rows) {
                    foreach ($result->rows as $row) {
                            $length_class_id = $row['length_class_id'];
                            $unit = $row['unit'];
                            if (!isset($length_class_ids[$unit])) {
                                    $length_class_ids[$unit] = $length_class_id;
                            }
                    }
            }

            return $length_class_ids;
    }
    protected function deleteImportExportCatalog( $product_id ) {
            $sql  = "DELETE FROM `".DB_PREFIX."product` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_description` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_to_store` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_reward` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_discount` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_special` WHERE `product_id` = '$product_id';\n";
            $this->multiquery( $sql );
    }
    protected function deleteGroupedProduct( $product_id ) {
            $sql  = "DELETE FROM `".DB_PREFIX."gp_grouped` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_description` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_related` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."product_to_category` WHERE `product_id` = '$product_id';\n";
            $sql .= "DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE 'product_id=".(int)$product_id."';\n";
            $this->multiquery( $sql );
    }
    protected function multiquery( $sql ) {
            foreach (explode(";\n", $sql) as $sql) {
                    $sql = trim($sql);
                    if ($sql) {
                            $this->db->query($sql);
                    }
            }
    }
    protected function moreProductCells( $i, &$j, &$worksheet, &$product ) {
            return;
    }
    protected function storeCatalogIntoDatabase( &$product, &$languages, &$product_fields, &$available_store_ids, &$weight_class_ids, &$length_class_ids ) {
            // extract the product details
            $product_id = $product['product_id'];
            $names = $product['names'];
            $model = $this->db->escape($product['model']);
            $shipping = $this->db->escape($product['shipping']);
            $price = trim($product['price']);
            $points = $this->db->escape($product['points']);
            $weight = ($product['weight']=="") ? 0 : $product['weight'];
            $weight_unit = $product['weight_unit'];
            $weight_class_id = (isset($weight_class_ids[$weight_unit])) ? $weight_class_ids[$weight_unit] : 0;
            $status = $product['status'];
            $status = ((strtoupper($status)=="TRUE") || (strtoupper($status)=="YES") || (strtoupper($status)=="ENABLED") || (strtoupper($status)==1) || (strtoupper($status)=="1")) ? 1 : 0;
            $hazardous = $product['hazardous'];
            $is_ground_hazmat = $product['is_ground_hazmat'];
            $size = $this->db->escape($product['size']);
            $cart_comment = $this->db->escape($product['cart_comment']);
            $stock_status_id = $product['stock_status_id'];
            $length = $product['length'];
            $width = $product['width'];
            $height = $product['height'];
            $length_unit = $product['measurement_unit'];
            $length_class_id = (isset($length_class_ids[$length_unit])) ? $length_class_ids[$length_unit] : 0;
            $sort_order = $product['sort_order'];

            // generate and execute SQL for inserting the product
            $sql = "INSERT INTO `".DB_PREFIX."product` SET product_id = '".$product_id."', stock_status_id = '".$stock_status_id."', model = '".$model."', shipping_code = '".$shipping."', price = '".$price."',"
                    . " points = '".$points."', date_modified = NOW(), weight = '".$weight."', weight_class_id = '".$weight_class_id."', length = '".$length."', width = '".$width."', height = '".$height."',"
                    . " length_class_id = '".$length_class_id."', status = '".$status."', hazardous = '".$hazardous."', is_ground_hazmat = '".$is_ground_hazmat."', size = '".$size."', cart_comment = '".$cart_comment."',"
                    . " images_processed = 1, sort_order = '".$sort_order."';\n";
            $this->multiquery($sql);
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    $language_id = $language['language_id'];
                    $name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';                    	
                    $sql  = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`, `language_id`, `name`) VALUES  ( $product_id, $language_id, '$name' );";
                    $this->db->query( $sql );
            }
            $sql = "INSERT INTO `".DB_PREFIX."product_to_store` (`product_id`,`store_id`) VALUES ($product_id,0);";
            $this->db->query($sql);
    }
    protected function storeGroupedProductIntoDatabase( &$product, &$languages, &$product_fields, &$available_store_ids, &$seo_url_ids ) {
            // extract the product details
            $product_id = $product['product_id'];
            $name = $product['name'];
            $description = $product['description'];
            $meta_title = $product['meta_title'];
            $meta_description = $product['meta_description'];
            $meta_keyword = $product['meta_keyword'];
            $image = isset($product['image']) ? $this->db->escape($product['image']) : '';
            $alt_text = isset($product['image']) ? $this->db->escape($product['alt_text']) : '';
            $caption = isset($product['image']) ? $this->db->escape($product['caption']) : '';
            $sort_order = $product['sort_order'];
            $status = $product['status'];
            $categories = $product['categories'];
            $related_products = $product['related_products'];
            $reference = isset($product['reference']) ? $this->db->escape($product['reference']) : '';
            $keyword = isset($product['keyword']) ? $this->db->escape($product['keyword']) : '';

            $sql = "INSERT INTO `".DB_PREFIX."gp_grouped` SET product_id = '".$product_id."';";
            $this->db->query($sql);
            // generate and execute SQL for inserting the product
            $sql = "INSERT INTO `".DB_PREFIX."product` SET product_id = '".$product_id."', image = '".$this->db->escape($image)."', alt_text = '".$this->db->escape($alt_text)."', caption = '".$this->db->escape($caption)."',sort_order = '".$sort_order."', status = '".$status."', images_processed = 0, reference = '".$this->db->escape($reference)."';";
            $this->db->query($sql);
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    $language_id = $language['language_id'];
                    $name = isset($name[$language_code]) ? $this->db->escape($name[$language_code]) : '';
                    $descriptions = isset($description[$language_code]) ? $this->db->escape($description[$language_code]) : '';
                    $meta_titles = isset($meta_title[$language_code]) ? $this->db->escape($meta_title[$language_code]) : '';
                    $meta_descriptions = isset($meta_description[$language_code]) ? $this->db->escape($meta_description[$language_code]) : '';
                    $meta_keywords = isset($meta_keyword[$language_code]) ? $this->db->escape($meta_keyword[$language_code]) : '';
                    $sql  = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES  ( $product_id, $language_id, '$name', '$descriptions', '$meta_titles', '$meta_descriptions', '$meta_keywords' );";
                    $this->db->query( $sql );
            }
            if ($keyword) {
                    if (isset($seo_url_ids[$product_id])) {
                            $seo_url_id = $seo_url_ids[$product_id];
                            $sql = "INSERT INTO `".DB_PREFIX."seo_url` (`seo_url_id`,`language_id`,`query`,`keyword`) VALUES ($seo_url_id,'1','product_id=$product_id','$keyword');";
                            
                            unset($seo_url_ids[$product_id]);
                    } else {
                            $sql = "INSERT INTO `".DB_PREFIX."seo_url` (`language_id`,`query`,`keyword`) VALUES ('1','product_id=$product_id','$keyword');";
                    }
                    $this->db->query($sql);
            }
            if (count($categories) > 0) {
                    $sql = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
                    $first = true;
                    foreach ($categories as $category_id) {
                            $sql .= ($first) ? "\n" : ",\n";
                            $first = false;
                            $sql .= "($product_id,$category_id)";
                    }
                    $sql .= ";";
                    $this->db->query($sql);
            }
            if (count($related_products) > 0) {
			$sql = "INSERT INTO `".DB_PREFIX."product_related` (`product_id`,`related_id`) VALUES ";
			$first = true;
			foreach ($related_products as $related_id) {
				$sql .= ($first) ? "\n" : ",\n";
				$first = false;
				$sql .= "($product_id,$related_id)";
			}
			$sql .= ";";
			$this->db->query($sql);
		}
    }
    protected function uploadCatalogProtocols( &$reader, $incremental ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Protocol' );
            if ($data==null) {
                    return;
            }

            // load the worksheet cells and store them to the database            
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $document = $this->getCell($data,$i,$j++,'');
                    $protocol = array();
                    $protocol['product_id'] = $product_id;
                    $protocol['pdf'] = $document;
                    if ($incremental) {
                            $this->deleteCatalogProtocol( $product_id );
                    }
                    $this->storeCatalogProtocolIntoDatabase( $protocol );
            }
    }
    protected function uploadCatalogMsds( &$reader, $incremental ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Msds' );
            if ($data==null) {
                    return;
            }

            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $pdfs = array();
                    while (isset($first_row[$j-1]) && $this->startsWith($first_row[$j-1],"document(")) {
                            $language_code = substr($first_row[$j-1],strlen("document("),strlen($first_row[$j-1])-strlen("document(")-1);
                            $language_technical_id = $this->getLanguageTechnicalIdByLanguageTechnicalName($language_code);
                            $pdf = $this->getCell($data,$i,$j++);
                            $pdf = htmlspecialchars( $pdf );
                            if($language_technical_id)
                                $pdfs[$language_technical_id] = $pdf;
                    }
                    $msds = array();
                    $msds['product_id'] = $product_id;
                    $msds['pdfs'] = $pdfs;
                    if ($incremental) {
                            $this->deleteCatalogMsds( $product_id );
                    }
                    $this->storeCatalogMsdsIntoDatabase( $msds );
            }
    }
    protected function uploadCatalogCoa( &$reader, $incremental ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Coa' );
            if ($data==null) {
                    return;
            }

            // load the worksheet cells and store them to the database
            $i = 0;
            $k = $data->getHighestRow();
            $coas = array();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $pdf = trim($this->getCell($data,$i,$j++,''));
                    $description = $this->getCell($data,$i,$j++,'');                    
                    $sort_order = $this->getCell($data,$i,$j++,'');                    
                    $coas[$product_id][] = array(
                        'pdf' => $pdf,
                        'description' => $description,
                        'sort_order' => $sort_order
                    );
            }
            $this->storeCatalogCoaIntoDatabase( $coas, $incremental );
    }
    protected function uploadCatalogTechnical( &$reader, $incremental ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Technicals' );
            if ($data==null) {
                    return;
            }

            // load the worksheet cells and store them to the database
            $technicals = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $title = trim($this->getCell($data,$i,$j++,''));                    
                    $link = $this->getCell($data,$i,$j++,'');
                    $description = $this->getCell($data,$i,$j++,'');
                    $technicals[$product_id][] = array(
                        'title' => $title,
                        'link' => $link,
                        'description' => $description
                    );
            }
            $this->storeCatalogTechnicalIntoDatabase( $technicals, $incremental );
    }
    protected function uploadRewards( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Rewards' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old rewards
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            } 

            // get existing customer groups
            $customer_group_ids = $this->getCustomerGroupIds();

            // load the worksheet cells and store them to the database
            $old_product_reward_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $customer_group = trim($this->getCell($data,$i,$j++));
                    if ($customer_group=="") {
                            continue;
                    }
                    $points = $this->getCell($data,$i,$j++,'0');
                    $reward = array();
                    $reward['product_id'] = $product_id;
                    $reward['customer_group'] = $customer_group;
                    $reward['points'] = $points;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $old_product_reward_ids = $this->deleteReward( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->moreRewardCells( $i, $j, $data, $reward );
                    $this->storeRewardIntoDatabase( $reward, $old_product_reward_ids, $customer_group_ids );
                    $previous_product_id = $product_id;
            }
            /*if ($incremental) {
                    $this->deleteUnlistedRewards( $unlisted_product_ids );
            }*/
    }
    protected function uploadSpecials( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Specials' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old specials
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            }

            // get existing customer groups
            $customer_group_ids = $this->getCustomerGroupIds();

            // load the worksheet cells and store them to the database
            $old_product_special_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $customer_group = trim($this->getCell($data,$i,$j++));
                    if ($customer_group=="") {
                            continue;
                    }
                    $priority = $this->getCell($data,$i,$j++,'0');
                    $price = $this->getCell($data,$i,$j++,'0');
                    $date_start = $this->getCell($data,$i,$j++,'0000-00-00');
                    $date_end = $this->getCell($data,$i,$j++,'0000-00-00');
                    $special = array();
                    $special['product_id'] = $product_id;
                    $special['customer_group'] = $customer_group;
                    $special['priority'] = $priority;
                    $special['price'] = $price;
                    $special['date_start'] = $date_start;
                    $special['date_end'] = $date_end;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $old_product_special_ids = $this->deleteSpecial( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->moreSpecialCells( $i, $j, $data, $special );
                    $this->storeSpecialIntoDatabase( $special, $old_product_special_ids, $customer_group_ids );
                    $previous_product_id = $product_id;
            }
    }
    protected function uploadDiscounts( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Discounts' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old discounts
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            }

            // get existing customer groups
            $customer_group_ids = $this->getCustomerGroupIds();

            // load the worksheet cells and store them to the database
            $old_product_discount_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j = 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $customer_group = trim($this->getCell($data,$i,$j++));
                    if ($customer_group=="") {
                            continue;
                    }
                    $quantity = $this->getCell($data,$i,$j++,'0');
                    $priority = $this->getCell($data,$i,$j++,'0');
                    $price = $this->getCell($data,$i,$j++,'0');
                    $date_start = $this->getCell($data,$i,$j++,'0000-00-00');
                    $date_end = $this->getCell($data,$i,$j++,'0000-00-00');
                    $discount = array();
                    $discount['product_id'] = $product_id;
                    $discount['customer_group'] = $customer_group;
                    $discount['quantity'] = $quantity;
                    $discount['priority'] = $priority;
                    $discount['price'] = $price;
                    $discount['date_start'] = $date_start;
                    $discount['date_end'] = $date_end;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $old_product_discount_ids = $this->deleteDiscount( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->moreDiscountCells( $i, $j, $data, $discount );
                    $this->storeDiscountIntoDatabase( $discount, $old_product_discount_ids, $customer_group_ids );
                    $previous_product_id = $product_id;
            }
    }
    protected function getCustomerGroupIds() {
            $sql = "SHOW TABLES LIKE \"".DB_PREFIX."customer_group_description\"";
            $query = $this->db->query( $sql );
            if ($query->num_rows) {
                    $language_id = $this->getDefaultLanguageId();
                    $sql  = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group_description` ";
                    $sql .= "WHERE language_id=$language_id ";
                    $sql .= "ORDER BY `customer_group_id` ASC";
                    $query = $this->db->query( $sql );
            } else {
                    $sql  = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group` ";
                    $sql .= "ORDER BY `customer_group_id` ASC";
                    $query = $this->db->query( $sql );
            }
            $customer_group_ids = array();
            foreach ($query->rows as $row) {
                    $customer_group_id = $row['customer_group_id'];
                    $name = $row['name'];
                    $customer_group_ids[$name] = $customer_group_id;
            }
            return $customer_group_ids;
    }
    protected function deleteCatalogProtocol( $product_id ) {
            $this->db->query( "DELETE FROM `".DB_PREFIX."product_protocol` WHERE product_id='".(int)$product_id."'" );
    }
    protected function deleteCatalogMsds( $product_id ) {
            $this->db->query( "DELETE FROM `".DB_PREFIX."product_sds` WHERE product_id='".(int)$product_id."'" );
    }
    protected function deleteReward( $product_id ) {
            $sql = "SELECT product_reward_id, product_id, customer_group_id FROM `".DB_PREFIX."product_reward` WHERE product_id='".(int)$product_id."'";
            $query = $this->db->query( $sql );
            $old_product_reward_ids = array();
            foreach ($query->rows as $row) {
                    $product_reward_id = $row['product_reward_id'];
                    $product_id = $row['product_id'];
                    $customer_group_id = $row['customer_group_id'];
                    $old_product_reward_ids[$product_id][$customer_group_id] = $product_reward_id;
            }
            if ($old_product_reward_ids) {
                    $sql = "DELETE FROM `".DB_PREFIX."product_reward` WHERE product_id='".(int)$product_id."'";
                    $this->db->query( $sql );
            }
            return $old_product_reward_ids;
    }
    protected function deleteSpecial( $product_id ) {
            $sql = "SELECT product_special_id, product_id, customer_group_id FROM `".DB_PREFIX."product_special` WHERE product_id='".(int)$product_id."'";
            $query = $this->db->query( $sql );
            $old_product_special_ids = array();
            foreach ($query->rows as $row) {
                    $product_special_id = $row['product_special_id'];
                    $product_id = $row['product_id'];
                    $customer_group_id = $row['customer_group_id'];
                    $old_product_special_ids[$product_id][$customer_group_id] = $product_special_id;
            }
            if ($old_product_special_ids) {
                    $sql = "DELETE FROM `".DB_PREFIX."product_special` WHERE product_id='".(int)$product_id."'";
                    $this->db->query( $sql );
            }
            return $old_product_special_ids;
    }
    protected function deleteDiscount( $product_id ) {
            $sql = "SELECT product_discount_id, product_id, customer_group_id, quantity FROM `".DB_PREFIX."product_discount` WHERE product_id='".(int)$product_id."' ORDER BY product_id ASC, customer_group_id ASC, quantity ASC;";
            $query = $this->db->query( $sql );
            $old_product_discount_ids = array();
            foreach ($query->rows as $row) {
                    $product_discount_id = $row['product_discount_id'];
                    $product_id = $row['product_id'];
                    $customer_group_id = $row['customer_group_id'];
                    $quantity = $row['quantity'];
                    $old_product_discount_ids[$product_id][$customer_group_id][$quantity] = $product_discount_id;
            }
            if ($old_product_discount_ids) {
                    $sql = "DELETE FROM `".DB_PREFIX."product_discount` WHERE product_id='".(int)$product_id."'";
                    $this->db->query( $sql );
            }
            return $old_product_discount_ids;
    }
    protected function moreRewardCells( $i, &$j, &$worksheet, &$reward ) {
            return;
    }
    protected function moreSpecialCells( $i, &$j, &$worksheet, &$special ) {
            return;
    }
    protected function moreDiscountCells( $i, &$j, &$worksheet, &$discount ) {
            return;
    }
    protected function storeCatalogProtocolIntoDatabase( &$protocol ) {
            $this->db->query("INSERT INTO `".DB_PREFIX."product_protocol` SET product_id = '".(int)$protocol['product_id']."', pdf = '".$this->db->escape($protocol['pdf'])."'");
    }
    protected function storeCatalogMsdsIntoDatabase( &$msds ) {
            foreach($msds['pdfs'] as $language_technical_id => $pdf){
                    $this->db->query("INSERT INTO `".DB_PREFIX."product_sds` SET product_id = '".(int)$msds['product_id']."', language_technical_id = '".(int)$language_technical_id."', pdf = '".$this->db->escape($pdf)."'");
            }
    }
    protected function storeCatalogCoaIntoDatabase( $coas, $incremental ) {
        foreach($coas as $product_id => $coa){
                if($incremental){
                        $this->db->query("DELETE FROM " . DB_PREFIX . "product_coa WHERE product_id = '".(int)$product_id."'");
                }
                
                foreach($coa as $value){
                        $this->db->query("INSERT INTO `".DB_PREFIX."product_coa` SET product_id = '".(int)$product_id."', description = '".$value['description']."', pdf = '".$this->db->escape($value['pdf'])."', sort_order = '".$this->db->escape($value['sort_order'])."'");
                }
        }
    }
    protected function storeCatalogTechnicalIntoDatabase( &$technicals, $incremental ) {
        foreach($technicals as $product_id => $technical){
                if($incremental){
                        $this->db->query("DELETE FROM " . DB_PREFIX . "product_technical WHERE product_id = '".(int)$product_id."'");
                }
                
                foreach($technical as $value){
                        $this->db->query("INSERT INTO `".DB_PREFIX."product_technical` SET product_id = '".(int)$product_id."', description = '".$this->db->escape($value['description'])."', title = '".$this->db->escape($value['title'])."', link = '".$this->db->escape($value['link'])."'");
                }
        }
    }
    protected function storeRewardIntoDatabase( &$reward, &$old_product_reward_ids, &$customer_group_ids ) {
            $product_id = $reward['product_id'];
            $name = $reward['customer_group'];
            $customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');
            $points = $reward['points'];
            if (isset($old_product_reward_ids[$product_id][$customer_group_id])) {
                    $product_reward_id = $old_product_reward_ids[$product_id][$customer_group_id];
                    $sql  = "INSERT INTO `".DB_PREFIX."product_reward` (`product_reward_id`,`product_id`,`customer_group_id`,`points` ) VALUES "; 
                    $sql .= "($product_reward_id,$product_id,$customer_group_id,$points)";
                    $this->db->query($sql);
                    unset($old_product_reward_ids[$product_id][$customer_group_id]);
            } else {
                    $sql  = "INSERT INTO `".DB_PREFIX."product_reward` (`product_id`,`customer_group_id`,`points` ) VALUES "; 
                    $sql .= "($product_id,$customer_group_id,$points)";
                    $this->db->query($sql);
            }
    }
    protected function storeSpecialIntoDatabase( &$special, &$old_product_special_ids, &$customer_group_ids ) {
            $product_id = $special['product_id'];
            $name = $special['customer_group'];
            $customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');
            $priority = $special['priority'];
            $price = $special['price'];
            $date_start = $special['date_start'];
            $date_end = $special['date_end'];
            if (isset($old_product_special_ids[$product_id][$customer_group_id])) {
                    $product_special_id = $old_product_special_ids[$product_id][$customer_group_id];
                    $sql  = "INSERT INTO `".DB_PREFIX."product_special` (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 
                    $sql .= "($product_special_id,$product_id,$customer_group_id,$priority,$price,'$date_start','$date_end')";
                    $this->db->query($sql);
                    unset($old_product_special_ids[$product_id][$customer_group_id]);
            } else {
                    $sql  = "INSERT INTO `".DB_PREFIX."product_special` (`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 
                    $sql .= "($product_id,$customer_group_id,$priority,$price,'$date_start','$date_end')";
                    $this->db->query($sql);
            }
    }
    protected function storeDiscountIntoDatabase( &$discount, &$old_product_discount_ids, &$customer_group_ids ) {
            $product_id = $discount['product_id'];
            $name = $discount['customer_group'];
            $customer_group_id = isset($customer_group_ids[$name]) ? $customer_group_ids[$name] : $this->config->get('config_customer_group_id');
            $quantity = $discount['quantity'];
            $priority = $discount['priority'];
            $price = $discount['price'];
            $date_start = $discount['date_start'];
            $date_end = $discount['date_end'];
            if (isset($old_product_discount_ids[$product_id][$customer_group_id][$quantity])) {
                    $product_discount_id = $old_product_discount_ids[$product_id][$customer_group_id][$quantity];
                    $sql  = "INSERT INTO `".DB_PREFIX."product_discount` (`product_discount_id`,`product_id`,`customer_group_id`,`quantity`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 
                    $sql .= "($product_discount_id,$product_id,$customer_group_id,$quantity,$priority,$price,'$date_start','$date_end')";
                    $this->db->query($sql);
                    unset($old_product_discount_ids[$product_id][$customer_group_id][$quantity]);
            } else {
                    $sql  = "INSERT INTO `".DB_PREFIX."product_discount` (`product_id`,`customer_group_id`,`quantity`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 
                    $sql .= "($product_id,$customer_group_id,$quantity,$priority,$price,'$date_start','$date_end')";
                    $this->db->query($sql);
            }
    }
    public function getLastInsertId(){
            $query = $this->db->query("SELECT MAX(product_id) as product_id FROM " . DB_PREFIX . "product");
            return (int)$query->row['product_id'] + 1;
    }
    public function getLanguageTechnicalIdByLanguageTechnicalName($languageTechnicalName){
            $query = $this->db->query("SELECT language_technical_id FROM " . DB_PREFIX . "language_technical WHERE name = '".$this->db->escape($languageTechnicalName)."'");
            if($query->num_rows){
                    return $query->row['language_technical_id'];
            } else {
                    return FALSE;
            }
    }
    public function getLanguageTechnicalIdByLanguageTechnicalID($languageTechnicalId){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language_technical WHERE language_technical_id = '".$this->db->escape($languageTechnicalId)."'");
            return $query->row;
    }        
    public function downloadGroupProucts() {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;

            // Use the PHPExcel package from http://phpexcel.codeplex.com/
            $cwd = getcwd();
            chdir( DIR_SYSTEM.'PHPExcel' );
            require_once( 'Classes/PHPExcel.php' );
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
            chdir( $cwd );

            // find out whether all data is to be downloaded
            //$all = !isset($offset) && !isset($rows) && !isset($min_id) && !isset($max_id);

            // Memory Optimization
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings); 

            try {
                    // set appropriate timeout limit
                    set_time_limit( 1800 );

                    $languages = $this->getLanguages();
                    $default_language_id = $this->getDefaultLanguageId();

                    // create a new workbook
                    $workbook = new PHPExcel();

                    // set some default styles
                    $workbook->getDefaultStyle()->getFont()->setName('Verdana');
                    $workbook->getDefaultStyle()->getFont()->setSize(10);
                    //$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
                    $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

                    // pre-define some commonly used styles
                    $box_format = array(
                            'fill' => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array( 'rgb' => '275E6E')
                            ),
                            'font' => array(
                                    'color'     => array( 'rgb' => 'FFFFFF')
                            )
                    );
                    $text_format = array(
                            'numberformat' => array(
                                    'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true,
                            )
                    );
                    $price_format = array(
                            'numberformat' => array(
                                    'code' => '######0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    /*'wrap'       => false,
                                    'indent'     => 0
                                    */
                            )
                    );
                    $weight_format = array(
                            'numberformat' => array(
                                    'code' => '##0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    /*'wrap'       => false,
                                    'indent'     => 0
                                    */
                            )
                    );

                    // create the worksheets
                    $worksheet_index = 0;
                    // creating the Products worksheet
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Products' );
                    $this->populateGroupProductsWorksheet( $worksheet, $languages, $default_language_id, $price_format, $box_format, $weight_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    $workbook->setActiveSheetIndex(0);

                    // redirect output to client browser
                    $datetime = date('Y-m-d');

                    $filename = 'products-'.$datetime.'.xlsx';

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
                    $objWriter->setPreCalculateFormulas(false);
                    $objWriter->save('php://output');

                    // Clear the spreadsheet caches
                    $this->clearSpreadsheetCache();
                    exit();

            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return;
            }
    }        
    function populateGroupProductsWorksheet( &$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'),4)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('link')+4,30)+1);
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product')+4,30)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('description')+4,32)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_title')+4,20)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_description')+4,32)+1);
            }
            foreach ($languages as $language) {
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_keywords')+4,32)+1);
            }
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('categories'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_name'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_alt'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_caption'),12)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('seo_keyword'),16)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('related')+4,30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('catalog')+4,30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('shipping')+4,15)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('hazardous')+4,15)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('size')+4,15)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight')+4,15)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price')+4,15)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('protocol')+4,30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sds')+4,30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('coa')+4,30)+1);

            // The product headings row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'product_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'plink';
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'product('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'description('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'meta_title('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'meta_description('.$language['code'].')';
            }
            foreach ($languages as $language) {
                    $styles[$j] = &$text_format;
                    $data[$j++] = 'meta_keywords('.$language['code'].')';
            }
            $styles[$j] = &$text_format;
            $data[$j++] = 'categories';
            $styles[$j] = &$text_format;
            $data[$j++] = 'image';
            $data[$j++] = 'alt_text';
            $data[$j++] = 'caption';
            $data[$j++] = 'seo_keyword';
            $data[$j++] = 'status';
            $styles[$j] = &$text_format;
            $data[$j++] = 'related';
            $styles[$j] = &$text_format;
            $data[$j++] = 'catalog';
            $styles[$j] = &$text_format;
            $data[$j++] = 'shipping';
            $styles[$j] = &$text_format;
            $data[$j++] = 'hazardous';
            $styles[$j] = &$text_format;
            $data[$j++] = 'size';
            $styles[$j] = &$text_format;
            $data[$j++] = 'weight';
            $styles[$j] = &$text_format;
            $data[$j++] = 'price';
            $styles[$j] = &$text_format;
            $data[$j++] = 'protocol';
            $styles[$j] = &$text_format;
            $data[$j++] = 'sds';
            $styles[$j] = &$text_format;
            $data[$j++] = 'cao';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual products data
            $i += 1;
            $j = 0;		
            $products = $this->getImportExportGroupProducts( $languages, $default_language_id );

            $len = count($products);
            $this->load->model('catalog/product');
            foreach ($products as $row) {
                    $data = array();
                    $catalog_ids = explode(' || ', $row['catalogs']);
                    if($catalog_ids){
                        foreach($catalog_ids as $catalog_id){                                
                                $this->arrangeRowData($worksheet, $languages, $row, $catalog_id, $i, $j, $styles);
                        }
                    } else {
                        $this->arrangeRowData($worksheet, $languages, $row, 0, $i, $j, $styles);
                    }
            }
    }    
    function arrangeRowData($worksheet, $languages, $row, $catalog_id, &$i, &$j, $styles){
        $worksheet->getRowDimension($i)->setRowHeight(26);
        $product_id = $row['product_id'];
        $data[$j++] = $product_id;
        if(!empty($row['keyword'])){
            $data[$j++] = html_entity_decode(HTTPS_CATALOG.$row['keyword'],ENT_QUOTES,'UTF-8');
        } else {
            $data[$j++] = html_entity_decode(HTTPS_CATALOG.'index.php?route=product/product&product_id='.$product_id,ENT_QUOTES,'UTF-8');
        }
        foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
        }
        foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['description'][$language['code']],ENT_QUOTES,'UTF-8');
        }
        foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_title'][$language['code']],ENT_QUOTES,'UTF-8');
        }
        foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_description'][$language['code']],ENT_QUOTES,'UTF-8');
        }
        foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']],ENT_QUOTES,'UTF-8');
        }
        $data[$j++] = html_entity_decode($row['categories'],ENT_QUOTES,'UTF-8');
        $data[$j++] = $row['image_name'];
        $data[$j++] = $row['alt_text'];
        $data[$j++] = $row['caption'];
        $data[$j++] = ($row['keyword']) ? $row['keyword'] : '';
        $data[$j++] = ($row['status']==0) ? 'false' : 'true';
        $data[$j++] = ($row['relateds']) ? html_entity_decode($row['relateds'],ENT_QUOTES,'UTF-8') : '';

        $catalog_details = $this->model_catalog_product->getProduct($catalog_id);
        
        $data[$j++] = isset($catalog_details['name']) ? html_entity_decode($catalog_details['name'],ENT_QUOTES,'UTF-8') : '';
        $data[$j++] = isset($catalog_details['shipping_code']) ? $catalog_details['shipping_code'] : '';
        $data[$j++] = isset($catalog_details['hazardous']) ? $catalog_details['hazardous'] : '';
        $data[$j++] = isset($catalog_details['size']) ? $catalog_details['size'] : '';
        $data[$j++] = isset($catalog_details['weight']) ? $catalog_details['weight'] : '';
        $data[$j++] = isset($catalog_details['price']) ? $catalog_details['price'] : '';
        $data[$j++] = $this->getProductDocument($catalog_id, 'protocol');
        $data[$j++] = $this->getProductDocument($catalog_id, 'sds');
        $data[$j++] = $this->getProductDocument($catalog_id, 'coa');
        
        $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
        $i += 1;
        $j = 0;
    }    
    function getProductDocument($product_id, $type){
        $query = $this->db->query("SELECT GROUP_CONCAT( DISTINCT CAST($type.pdf AS CHAR(255)) SEPARATOR \" || \" ) as $type FROM " . DB_PREFIX . "product_$type as $type WHERE product_id = '".$product_id."'");

        return isset($query->row[$type]) ? $query->row[$type] : '';
    }    
    protected function getImportExportGroupProducts( &$languages, $default_language_id ) {
            $sql  = "SELECT ";
            $sql .= "  p.product_id,";
            $sql .= "  GROUP_CONCAT( DISTINCT CAST(cd.name AS CHAR(255)) SEPARATOR \" || \" ) AS categories,";	
            $sql .= "  GROUP_CONCAT( DISTINCT CAST(prd.name AS CHAR(255)) SEPARATOR \" || \" ) AS relateds,";	
            $sql .= "  GROUP_CONCAT( DISTINCT CAST(ggcd.product_id AS CHAR(255)) SEPARATOR \" || \" ) AS catalogs,";
            $sql .= "  CONCAT('".CDN_SERVER."image/'".", p.image) AS image_name,";
            $sql .= "  ua.keyword,";
            $sql .= "  p.status, ";
            $sql .= "  p.alt_text, ";
            $sql .= "  p.caption ";
            $sql .= "FROM `".DB_PREFIX."product` p ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."category_description` cd ON pc.category_id=cd.category_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_related` pr ON p.product_id=pr.product_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_description` prd ON prd.product_id=pr.related_id AND prd.language_id=1 ";
            $sql .= "LEFT JOIN `".DB_PREFIX."gp_grouped_child` ggc ON p.product_id=ggc.product_id ";
            $sql .= "LEFT JOIN `".DB_PREFIX."product_description` ggcd ON ggcd.product_id=ggc.child_id AND ggcd.language_id=1 ";
            $sql .= "LEFT JOIN `".DB_PREFIX."seo_url` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
            $sql .= "WHERE p.special_product<>'1' ";
            $sql .= "AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";		
            $sql .= "GROUP BY p.product_id ";
            $sql .= "ORDER BY p.product_id ";
            $sql .= "; ";
            $results = $this->db->query( $sql );
            $product_descriptions = $this->getImportExportGroupProductDescriptions( $languages );
            foreach ($languages as $language) {
                    $language_code = $language['code'];
                    foreach ($results->rows as $key=>$row) {
                            if (isset($product_descriptions[$language_code][$key])) {
                                    $results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
                                    $results->rows[$key]['description'][$language_code] = $product_descriptions[$language_code][$key]['description'];
                                    $results->rows[$key]['meta_title'][$language_code] = $product_descriptions[$language_code][$key]['meta_title'];
                                    $results->rows[$key]['meta_description'][$language_code] = $product_descriptions[$language_code][$key]['meta_description'];
                                    $results->rows[$key]['meta_keyword'][$language_code] = $product_descriptions[$language_code][$key]['meta_keyword'];
                            } else {
                                    $results->rows[$key]['name'][$language_code] = '';
                                    $results->rows[$key]['description'][$language_code] = '';$results->rows[$key]['meta_title'][$language_code] = '';
                                    $results->rows[$key]['meta_description'][$language_code] = '';
                                    $results->rows[$key]['meta_keyword'][$language_code] = '';
                            }
                    }
            }
            return $results->rows;
    }        
    protected function getImportExportGroupProductDescriptions( &$languages ) {
		// query the product_description table for each language
		$product_descriptions = array();
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$language_code = $language['code'];
			$sql  = "SELECT p.product_id, pd.* ";
                        $sql .= "FROM `".DB_PREFIX."product` p ";
                        $sql .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id=p.product_id AND pd.language_id='".(int)$language_id."' ";
                        $sql .= "WHERE p.special_product<>'1' AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";			
                        $sql .= "GROUP BY p.product_id ";
                        $sql .= "ORDER BY p.product_id ";
			$sql .= "; ";
			$query = $this->db->query( $sql );
			$product_descriptions[$language_code] = $query->rows;
		}
		return $product_descriptions;
	}
    protected function getExportGroupProductDescriptions( &$languages ) {
		// query the product_description table for each language
		$product_descriptions = array();
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$language_code = $language['code'];
			$sql  = "SELECT p.product_id, pd.* ";
                        $sql .= "FROM `".DB_PREFIX."product` p ";
                        $sql .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id=p.product_id AND pd.language_id='".(int)$language_id."' ";
                        $sql .= "WHERE p.special_product<>'1' AND p.product_id IN (SELECT product_id FROM `".DB_PREFIX."gp_grouped`) AND p.product_id<>'' ";			
                        $sql .= "GROUP BY p.product_id ";
                        $sql .= "ORDER BY p.product_id ";
			$sql .= "; ";
			$query = $this->db->query( $sql );
			$product_descriptions[$language_code] = $query->rows;
		}
		return $product_descriptions;
	}
    public function downloadCustomers(){
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import_customer', E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            // Use the PHPExcel package from http://phpexcel.codeplex.com/
            $cwd = getcwd();
            chdir( DIR_SYSTEM.'PHPExcel' );
            require_once( 'Classes/PHPExcel.php' );
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
            chdir( $cwd );

            // Memory Optimization
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            
            try {
                    // set appropriate timeout limit
                    set_time_limit( 1800 );

                    // create a new workbook
                    $workbook = new PHPExcel();

                    // set some default styles
                    $workbook->getDefaultStyle()->getFont()->setName('Verdana');
                    $workbook->getDefaultStyle()->getFont()->setSize(10);
                    //$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
                    $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

                    // pre-define some commonly used styles
                    $box_format = array(
                            'fill' => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array( 'rgb' => '275E6E')
                            ),
                            'font' => array(
                                    'color'     => array( 'rgb' => 'FFFFFF')
                            )
                    );
                    $text_format = array(
                            'numberformat' => array(
                                    'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true,
                            )
                    );
                    $price_format = array(
                            'numberformat' => array(
                                    'code' => '######0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );
                    $weight_format = array(
                            'numberformat' => array(
                                    'code' => '##0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );

                    // create the worksheets
                    $worksheet_index = 0;
                    // creating the Customers worksheet
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Customers' );
                    $this->populateCustomersWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );

                    // creating the rewards worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Rewards' );
                    $this->populateCustomersRewardsWorksheet( $worksheet );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    $workbook->setActiveSheetIndex(0);

                    // redirect output to client browser
                    $datetime = date('m-d-Y');

                    $filename = 'customers-'.$datetime.'.xlsx';

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
                    $objWriter->setPreCalculateFormulas(false);
                    $objWriter->save('php://output');

                    // Clear the spreadsheet caches
                    $this->clearSpreadsheetCache();
                    exit();

            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return;
            }
    }
    protected function populateCustomersRewardsWorksheet( &$worksheet, $box_format, $text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('reward_point')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('reward_description')+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'customer_id';
            $data[$j++] = 'reward_point';
            $styles[$j] = &$text_format;
            $data[$j++] = 'reward_description';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual product rewards data
            $i += 1;
            $j = 0;
    }
    protected function populateCustomersWorksheet( &$worksheet, &$box_format, &$text_format ) {
            // Set the column widths
            $j = 0;
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_id')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group')+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('firstname'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('lastname'),20)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('email'),30)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('telephone'),14)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('fax'),14)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('Reward Points'),14)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('newsletter'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'),5)+1);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('approved'),5)+1);

            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'customer_id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'customer_group';
            $styles[$j] = &$text_format;
            $data[$j++] = 'firstname';
            $styles[$j] = &$text_format;
            $data[$j++] = 'lastname';
            $styles[$j] = &$text_format;
            $data[$j++] = 'email';
            $styles[$j] = &$text_format;
            $data[$j++] = 'telephone';
            $styles[$j] = &$text_format;
            $data[$j++] = 'fax';
            $styles[$j] = &$text_format;
            $data[$j++] = 'reward_points';
            $data[$j++] = 'newsletter';
            $data[$j++] = 'status';
            $data[$j++] = 'approved';
            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

            // The actual customers data
            $i += 1;
            $j = 0;
            $customers = $this->getCustomers();

            $len = count($customers);
            foreach ($customers as $row) {
                    $worksheet->getRowDimension($i)->setRowHeight(26);
                    $data = array();
                    $data[$j++] = $row['customer_id'];
                    $data[$j++] = $row['customer_group'];
                    $data[$j++] = $row['firstname'];
                    $data[$j++] = $row['lastname'];
                    $data[$j++] = $row['email'];
                    $data[$j++] = $row['telephone'];
                    $data[$j++] = $row['fax'];
                    $data[$j++] = $row['reward'];
                    $data[$j++] = ($row['newsletter']==0) ? 'no' : 'yes';
                    $data[$j++] = ($row['status']==0) ? 'false' : 'true';
                    $data[$j++] = ($row['approved']==0) ? 'false' : 'true';
                    $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                    $i += 1;
                    $j = 0;
            }
    }
    protected function getCustomers() {
            $language_id = $this->getDefaultLanguageId();

            $sql  = "SELECT c.customer_id, c.firstname, c.lastname, c.email, c.telephone, c.fax, c.newsletter, c.status, c.approved, cgd.name AS customer_group, ";
            $sql .= "(SELECT SUM(points) FROM `" . DB_PREFIX . "customer_reward` cr WHERE cr.customer_id = c.customer_id) as reward ";
            $sql .= "FROM `".DB_PREFIX."customer` c ";
            $sql .= "INNER JOIN `".DB_PREFIX."customer_group_description` cgd ON cgd.customer_group_id=c.customer_group_id AND cgd.language_id='".(int)$language_id."' ";            
            $sql .= "GROUP BY c.`customer_id` ";
            $sql .= "ORDER BY c.`customer_id` ASC ";
            $results = $this->db->query( $sql );
            return $results->rows;
    }
    protected function getAvailableCustomerIds() {
            $sql = "SELECT `customer_id` FROM `".DB_PREFIX."customer`;";
            $result = $this->db->query( $sql );
            $customer_ids = array();
            foreach ($result->rows as $row) {
                    $customer_ids[$row['customer_id']] = $row['customer_id'];
            }
            return $customer_ids;
    }
    public function uploadCustomer( $filename ) {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import_customer',E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            try {
                    $this->session->data['export_import_nochange'] = 1;

                    // we use the PHPExcel package from http://phpexcel.codeplex.com/
                    $cwd = getcwd();
                    chdir( DIR_SYSTEM.'PHPExcel' );
                    require_once( 'Classes/PHPExcel.php' );
                    chdir( $cwd );

                    // Memory Optimization
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array( ' memoryCacheSize '  => '128MB'  );
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    // parse uploaded spreadsheet file
                    $inputFileType = PHPExcel_IOFactory::identify($filename);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $reader = $objReader->load($filename);
                    
                    // read the various worksheets and load them to the database			
                    if (!$this->validateCustomerUpload( $reader )) {
                            return false;
                    }
                    $this->clearCache();
                    $this->session->data['export_import_nochange'] = 0;
                    $available_product_ids = array();
                    $this->uploadCustomers( $reader );
                    $this->uploadCustomersRewards( $reader );
                    return true;
            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return false;
            }
    }
    protected function validateCustomers( &$reader ) {
            $data = $reader->getSheetByName( 'Customers' );
            if ($data==null) {
                    return true;
            }

            $expected_heading = array
            ( "customer_id", "customer_group", "firstname", "lastname", "email", "telephone", "fax", "reward_points", "newsletter", "status", "approved" );

            $expected_multilingual = array();

            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateCustomersRewards( &$reader ) {
            $data = $reader->getSheetByName( 'Rewards' );
            if ($data==null) {
                    return true;
            }
            $expected_heading = array( "customer_id", "reward_point", "reward_description" );
            $expected_multilingual = array();
            return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
    }
    protected function validateCustomerIdColumns( &$reader ) {
            $data = $reader->getSheetByName( 'Customers' );
            if ($data==null) {
                    return true;
            }
            $ok = true;

            // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
            $previous_customer_id = 0;
            $has_missing_customer_ids = false;
            $customer_ids = array();
            $k = $data->getHighestRow();
            for ($i=1; $i<$k; $i+=1) {
                    $customer_id = $this->getCell($data,$i,1);
                    if ($customer_id=="") {
                            if (!$has_missing_customer_ids) {
                                    $msg = str_replace( '%1', 'Customers', $this->language->get( 'error_missing_customer_id' ) );
                                    $this->log->write( $msg );
                                    $has_missing_customer_ids = true;
                            }
                            $ok = false;
                            continue;
                    }
                    if (!$this->isInteger($customer_id)) {
                            $msg = str_replace( '%2', $customer_id, str_replace( '%1', 'Customers', $this->language->get( 'error_invalid_customer_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                            continue;
                    }
                    if (in_array( $customer_id, $customer_ids )) {
                            $msg = str_replace( '%2', $customer_id, str_replace( '%1', 'Customers', $this->language->get( 'error_duplicate_customer_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $customer_ids[] = $customer_id;
                    if ($customer_id < $previous_customer_id) {
                            $msg = str_replace( '%2', $customer_id, str_replace( '%1', 'Customers', $this->language->get( 'error_wrong_order_customer_id' ) ) );
                            $this->log->write( $msg );
                            $ok = false;
                    }
                    $previous_customer_id = $customer_id;
            }

            // make sure customer_ids are numeric entries and are also mentioned in worksheet 'Products'
            $worksheets = array( 'Rewards' );
            foreach ($worksheets as $worksheet) {
                    $data = $reader->getSheetByName( $worksheet );
                    if ($data==null) {
                            continue;
                    }
                    $previous_customer_id = 0;
                    $has_missing_customer_ids = false;
                    $unlisted_customer_ids = array();
                    $k = $data->getHighestRow();
                    for ($i=1; $i<$k; $i+=1) {
                            $customer_id = $this->getCell($data,$i,1);
                            if ($customer_id=="") {
                                    if (!$has_missing_customer_ids) {
                                            $msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_customer_id' ) );
                                            $this->log->write( $msg );
                                            $has_missing_customer_ids = true;
                                    }
                                    $ok = false;
                                    continue;
                            }
                            if (!$this->isInteger($customer_id)) {
                                    $msg = str_replace( '%2', $customer_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_customer_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                                    continue;
                            }
                            if (!in_array( $customer_id, $customer_ids )) {
                                    if (!in_array( $customer_id, $unlisted_customer_ids )) {
                                            $unlisted_customer_ids[] = $customer_id;
                                            $msg = str_replace( '%2', $customer_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_customer_id' ) ) );
                                            $this->log->write( $msg );
                                            $ok = false;
                                    }
                            }
                            if ($customer_id < $previous_customer_id) {
                                    $msg = str_replace( '%2', $customer_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_customer_id' ) ) );
                                    $this->log->write( $msg );
                                    $ok = false;
                            }
                            $previous_customer_id = $customer_id;
                    }
            }

            return $ok;
    }
    protected function validateCustomerUpload( &$reader ){
            $ok = true;

            // worksheets must have correct heading rows
            if (!$this->validateCustomers( $reader )) {
                    $this->log->write( $this->language->get('error_customers_header') );
                    $ok = false;
            }
            if (!$this->validateCustomersRewards( $reader )) {
                    $this->log->write( $this->language->get('error_customers_rewards_header') );
                    $ok = false;
            }

            // certain worksheets rely on the existence of other worksheets
            $names = $reader->getSheetNames();
            $exist_customers = false;
            $exist_rewards = false;
            foreach ($names as $name) {
                    if ($name=='Customers') {
                            $exist_customers = true;
                            continue;
                    }
                    if ($name=='Rewards') {
                            if (!$exist_customers) {
                                    // Missing Products worksheet, or Products worksheet not listed before Rewards
                                    $this->log->write( $this->language->get('error_rewards_customer') );
                                    $ok = false;
                            }
                            $exist_rewards = true;
                            continue;
                    }
            }

            if (!$ok) {
                    return false;
            }

            if (!$this->validateCustomerIdColumns( $reader )) {
                    $ok = false;
            }

            return $ok;
    }
    public function uploadCustomers( &$reader ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Customers' );
            if ($data==null) {
                    return;
            }

            // get customer_group ids indexed by customer group names
            $customer_group_ids = $this->getCustomerGroupIds();            

            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    $customer_id = trim($this->getCell($data,$i,$j++));
                    if ($customer_id=="") {
                            continue;
                    }
                    $customer_group = trim($this->getCell($data,$i,$j++));
                    $customer_group_id = isset($customer_group_ids[$customer_group]) ? $customer_group_ids[$customer_group] : '0';
                    $firstname = trim($this->getCell($data,$i,$j++));
                    $lastname = trim($this->getCell($data,$i,$j++));
                    $email = trim($this->getCell($data,$i,$j++));
                    $telephone = trim($this->getCell($data,$i,$j++));
                    $fax = trim($this->getCell($data,$i,$j++));
                    $newsletter = $this->getCell($data,$i,$j++,'no');
                    $status = $this->getCell($data,$i,$j++,'true');
                    $approved = $this->getCell($data,$i,$j++,'true');

                    $customer = array();
                    $customer['customer_id'] = $customer_id;
                    $customer['customer_group_id'] = $customer_group_id;
                    $customer['firstname'] = $firstname;
                    $customer['lastname'] = $lastname;
                    $customer['email'] = $email;
                    $customer['telephone'] = $telephone;
                    $customer['fax'] = $fax;
                    $customer['newsletter'] = $newsletter;
                    $customer['status'] = $status;
                    $customer['approved'] = $approved;                    
                    
                    $this->moreCustomerCells( $i, $j, $data, $customer );
                    $this->storeCustomerIntoDatabase( $customer );
            }
    }
    // function for reading additional cells in class extensions
    protected function moreCustomerCells( $i, &$j, &$worksheet, &$customer ) {
            return;
    }
    protected function storeCustomerIntoDatabase( &$customer ) {
            $customer_id = $customer['customer_id'];
            $customer_group_id = $customer['customer_group_id'];
            $firstname = $customer['firstname'];
            $lastname = $customer['lastname'];
            $email = $customer['email'];
            $telephone = $customer['telephone'];
            $fax = $customer['fax'];
            $newsletter = $customer['newsletter'];
            $newsletter = ((strtoupper($newsletter)=="TRUE") || (strtoupper($newsletter)=="YES") || (strtoupper($newsletter)=="ENABLED")) ? 1 : 0;
            $status = $customer['status'];
            $status = ((strtoupper($status)=="TRUE") || (strtoupper($status)=="YES") || (strtoupper($status)=="ENABLED")) ? 1 : 0;
            $approved = $customer['approved'];
            $approved = ((strtoupper($approved)=="TRUE") || (strtoupper($approved)=="YES") || (strtoupper($approved)=="ENABLED")) ? 1 : 0;

            $sql  = "UPDATE `".DB_PREFIX."customer` SET customer_group_id = '".$customer_group_id."', firstname = '".$this->db->escape($firstname)."', "
                    . "lastname = '".$this->db->escape($lastname)."', email = '".$this->db->escape($email)."', telephone = '".$this->db->escape($telephone)."', "
                    . "fax = '".$this->db->escape($fax)."', newsletter = '".$newsletter."', status = '".$status."', approved = '".$approved."' "
                    . "WHERE customer_id = '".$customer_id."'";

            $this->db->query( $sql );
    }
    public function uploadCustomersRewards( &$reader ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Rewards' );
            if ($data==null) {
                    return;
            }         

            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    $customer_id = trim($this->getCell($data,$i,$j++));
                    if ($customer_id=="") {
                            continue;
                    }
                    $reward_points = trim($this->getCell($data,$i,$j++));                    
                    $reward_description = trim($this->getCell($data,$i,$j++));

                    $reward = array();
                    $reward['customer_id'] = $customer_id;
                    $reward['reward_points'] = $reward_points;
                    $reward['reward_description'] = $reward_description;                  
                    
                    $this->storeCustomerRewardIntoDatabase( $reward );
            }
    }
    protected function storeCustomerRewardIntoDatabase( &$reward ) {
        $this->log->write("INSERT INTO `" . DB_PREFIX . "customer_reward` SET customer_id = '".$reward['customer_id']."', description = '".$reward['reward_description']."', points = '".$reward['reward_points']."', date_added = NOW()");
            $this->db->query( "INSERT INTO `" . DB_PREFIX . "customer_reward` SET customer_id = '".$reward['customer_id']."', description = '".$reward['reward_description']."', points = '".$reward['reward_points']."', date_added = NOW()" );
    }
    public function export_grouped_product(){
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import', E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            // Use the PHPExcel package from http://phpexcel.codeplex.com/
            $cwd = getcwd();
            chdir( DIR_SYSTEM.'PHPExcel' );
            require_once( 'Classes/PHPExcel.php' );
            PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_ExportImportValueBinder() );
            chdir( $cwd );

            // Memory Optimization
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize'  => '128MB' );  
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            
            try {
                    // set appropriate timeout limit
                    set_time_limit( 1800 );

                    $languages = $this->getLanguages();
                    $default_language_id = $this->getDefaultLanguageId();

                    // create a new workbook
                    $workbook = new PHPExcel();

                    // set some default styles
                    $workbook->getDefaultStyle()->getFont()->setName('Verdana');
                    $workbook->getDefaultStyle()->getFont()->setSize(10);
                    //$workbook->getDefaultStyle()->getAlignment()->setIndent(0.5);
                    $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);

                    // pre-define some commonly used styles
                    $box_format = array(
                            'fill' => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array( 'rgb' => '275E6E')
                            ),
                            'font' => array(
                                    'color'     => array( 'rgb' => 'FFFFFF')
                            )
                    );
                    $text_format = array(
                            'numberformat' => array(
                                    'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true,
                            )
                    );
                    $price_format = array(
                            'numberformat' => array(
                                    'code' => '######0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );
                    $weight_format = array(
                            'numberformat' => array(
                                    'code' => '##0.00'
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            )
                    );

                    // create the worksheets
                    $worksheet_index = 0;
                    // creating the Grouped Product worksheet
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Products' );
                    $this->populateGroupedProductWorksheet( $worksheet, $languages, $default_language_id, $price_format, $box_format, $weight_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // GP-Data worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'GP_Data' );
                    $this->populateGPDataWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                                       
                    // Coas worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Images' );
                    $this->populateGroupedProductsImagesWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    // Reference worksheet
                    $workbook->createSheet();
                    $workbook->setActiveSheetIndex($worksheet_index++);
                    $worksheet = $workbook->getActiveSheet();
                    $worksheet->setTitle( 'Citations' );
                    $this->populateGroupedProductsRefrenceWorksheet( $worksheet, $box_format, $text_format );
                    $worksheet->freezePaneByColumnAndRow( 1, 2 );
                    
                    $workbook->setActiveSheetIndex(0);

                    // redirect output to client browser
                    $datetime = date('m-d-Y');

                    $filename = 'GroupedProducts-'.$datetime.'.xlsx';

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
                    $objWriter->setPreCalculateFormulas(false);
                    $objWriter->save('php://output');

                    // Clear the spreadsheet caches
                    $this->clearSpreadsheetCache();
                    exit();

            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return;
            }
    }
    public function uploadGroupedProduct( $filename, $incremental=true ) {
            // we use our own error handler
            global $registry;
            $registry = $this->registry;
            set_error_handler('error_handler_for_export_import',E_ALL);
            register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

            try {
                    $this->session->data['export_import_nochange'] = 1;

                    // we use the PHPExcel package from http://phpexcel.codeplex.com/
                    $cwd = getcwd();
                    chdir( DIR_SYSTEM.'PHPExcel' );
                    require_once( 'Classes/PHPExcel.php' );
                    chdir( $cwd );

                    // Memory Optimization
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array( ' memoryCacheSize '  => '128MB'  );
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    // parse uploaded spreadsheet file
                    $inputFileType = PHPExcel_IOFactory::identify($filename);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $reader = $objReader->load($filename);
                    
                    // read the various worksheets and load them to the database
                    if (!$this->validateUploadGroupedProduct( $reader )) {
                            return false;
                    }
                    $this->clearCache();
                    $this->session->data['export_import_nochange'] = 0;
                    $available_product_ids = array();
                    $this->uploadGrouped( $reader, $incremental, $available_product_ids );
                    $this->uploadGPData( $reader, $incremental, $available_product_ids );
                    $this->uploadGroupedProductImages( $reader, $incremental, $available_product_ids  );
                    $this->uploadGroupedProductReferences( $reader, $incremental, $available_product_ids  );
                    return true;
            } catch (Exception $e) {
                    $errstr = $e->getMessage();
                    $errline = $e->getLine();
                    $errfile = $e->getFile();
                    $errno = $e->getCode();
                    $this->session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                    if ($this->config->get('config_error_log')) {
                            $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                    }
                    return false;
            }
    }
    protected function deleteAdditionalImage( $product_id ) {
            $this->db->query( "DELETE FROM `".DB_PREFIX."product_image` WHERE product_id='".(int)$product_id."'" );
    }
    protected function deleteAdditionalRefrences( $product_id ) {
            $this->db->query( "DELETE FROM `".DB_PREFIX."product_references` WHERE product_id='".(int)$product_id."'" );
    }
    protected function uploadGroupedProductImages( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Images' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old product images
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            } 

            // load the worksheet cells and store them to the database
            $old_product_image_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j= 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $image_name = $this->getCell($data,$i,$j++,'');
                    if ($image_name=="") {
                            continue;
                    }
                    $alt_text = $this->getCell($data,$i,$j++,'');
                    $image_caption = $this->getCell($data,$i,$j++,'');
                    $sort_order = $this->getCell($data,$i,$j++,0);
                    $image = array();
                    $image['product_id'] = $product_id;
                    $image['image'] = $image_name;
                    $image['alt_text'] = $alt_text;
                    $image['image_caption'] = $image_caption;
                    $image['sort_order'] = $sort_order;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $this->deleteAdditionalImage( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->storeAdditionalImageIntoDatabase( $image );
                    $previous_product_id = $product_id;
            }
    }
    protected function uploadGroupedProductReferences( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Citations' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old product images
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            } 

            // load the worksheet cells and store them to the database
            $old_product_image_ids = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    $j= 1;
                    if ($i==0) {
                            continue;
                    }
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $islink = $this->getCell($data,$i,$j++,'');
                    $text = $this->getCell($data,$i,$j++,'');
                    $link = $this->getCell($data,$i,$j++,'');
                    $year = $this->getCell($data,$i,$j++,0);
                    $reference = array();
                    $reference['product_id'] = $product_id;
                    $reference['islink'] = $islink;
                    $reference['text'] = $text;
                    $reference['link'] = $link;
                    $reference['year'] = $year;
                    if (($incremental) && ($product_id != $previous_product_id)) {
                            $this->deleteAdditionalRefrences( $product_id );
                            if (isset($unlisted_product_ids[$product_id])) {
                                    unset($unlisted_product_ids[$product_id]);
                            }
                    }
                    $this->storeAdditionalRefrencesIntoDatabase( $reference );
                    $previous_product_id = $product_id;
            }
    }
    protected function storeAdditionalImageIntoDatabase( &$image ) {
            $this->db->query("INSERT INTO `".DB_PREFIX."product_image` SET `product_id` = '" . $image['product_id'] . "', `image` = '" . $this->db->escape($image['image']) . "', "
                    . "`alt_text` = '" . $this->db->escape($image['alt_text']) . "', `image_caption` = '" . $this->db->escape($image['image_caption']) . "', "
                    . "`sort_order` = '" . $this->db->escape($image['sort_order']) . "'");
    }
    protected function storeAdditionalRefrencesIntoDatabase( &$reference ) {
            $this->db->query("INSERT INTO `".DB_PREFIX."product_references` SET `product_id` = '" . $reference['product_id'] . "', `islink` = '" . $this->db->escape($reference['islink']) . "', "
                    . "`text` = '" . $this->db->escape($reference['text']) . "', `link` = '" . $this->db->escape($reference['link']) . "', "
                    . "`year` = '" . $this->db->escape($reference['year']) . "'");
    }
    protected function uploadGPData( &$reader, $incremental, &$available_product_ids ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'GP_Data' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old product catalogs
            if ($incremental) {
                    $unlisted_product_ids = $available_product_ids;
            }
           
            // load the worksheet cells and store them to the database
            $first_row = array();
            $previous_product_id = 0;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                if ($i==0) {
                        $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                        for ($j=1; $j<=$max_col; $j+=1) {
                                $first_row[] = $this->getCell($data,$i,$j);
                        }
                        continue;
                }
                $j = 1;
                $product_id = trim($this->getCell($data,$i,$j++,''));
                if ($product_id=="") {
                        continue;
                }
                $child_id = $this->getCell($data,$i,$j++,'');
                $child_sort_order = $this->getCell($data,$i,$j++,0);

                $product = array();
                $product['product_id'] = $product_id;
                $product['child_id'] = $child_id;
                $product['child_sort_order'] = $child_sort_order;
                if (($incremental) && ($product_id != $previous_product_id)) {
                        $this->deleteGPData( $product_id );
                        if (isset($unlisted_product_ids[$product_id])) {
                                unset($unlisted_product_ids[$product_id]);
                        }
                }
                $this->storeGPDataIntoDatabase( $product );
                $previous_product_id = $product_id;
            }
    }
    protected function storeGPDataIntoDatabase( &$product ) {
            // extract the product details
            $product_id = $product['product_id'];
            $child_id = $product['child_id'];
            $child_sort_order = $product['child_sort_order'];
            
            // generate and execute SQL for inserting the product
            $sql = "INSERT INTO `".DB_PREFIX."gp_grouped_child` SET product_id = '".$product_id."', child_id = '".$child_id."', child_sort_order = '".$child_sort_order."'";
            $this->db->query($sql);
            
    }
    protected function deleteGPData( $product_id ) {
            $this->db->query("DELETE FROM `".DB_PREFIX."gp_grouped_child` WHERE `product_id` = '" . (int)$product_id . "'");
    }
    protected function getProductUrlAliasIds() {
            $sql  = "SELECT seo_url_id, SUBSTRING( query, CHAR_LENGTH('product_id=')+1 ) AS product_id ";
            $sql .= "FROM `".DB_PREFIX."seo_url` ";
            $sql .= "WHERE query LIKE 'product_id=%'";
            $query = $this->db->query( $sql );
            $seo_url_ids = array();
            foreach ($query->rows as $row) {
                    $seo_url_id = $row['seo_url_id'];
                    $product_id = $row['product_id'];
                    $seo_url_ids[$product_id] = $seo_url_id;
            }
            return $seo_url_ids;
    }
    protected function uploadGrouped( &$reader, $incremental, &$available_product_ids=array() ) {
            // get worksheet, if not there return immediately
            $data = $reader->getSheetByName( 'Products' );
            if ($data==null) {
                    return;
            }

            // if incremental then find current product IDs else delete all old products
            $available_product_ids = array();
            if ($incremental) {
                    $old_product_ids = $this->getAvailableProductIds($data);
            }
           
            // get pre-defined store_ids
            $available_store_ids = $this->getAvailableStoreIds();
            // find the installed languages
            $languages = $this->getLanguages();

            // find the default units
            $seo_url_ids = $this->getProductUrlAliasIds();
            
            // get list of the field names, some are only available for certain OpenCart versions
            $query = $this->db->query( "DESCRIBE `".DB_PREFIX."product`" );
            $product_fields = array();
            foreach ($query->rows as $row) {
                    $product_fields[] = $row['Field'];
            }
            
            // load the worksheet cells and store them to the database
            $first_row = array();
            $i = 0;
            $k = $data->getHighestRow();
            for ($i=0; $i<$k; $i+=1) {
                    if ($i==0) {
                            $max_col = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
                            for ($j=1; $j<=$max_col; $j+=1) {
                                    $first_row[] = $this->getCell($data,$i,$j);
                            }
                            continue;
                    }
                    $j = 1;
                    $product_id = trim($this->getCell($data,$i,$j++));
                    if ($product_id=="") {
                            continue;
                    }
                    $names = array();
                    while ($this->startsWith($first_row[$j-1],"Product_Name(")) {
                            $language_code = substr($first_row[$j-1],strlen("Product_Name("),strlen($first_row[$j-1])-strlen("Product_Name(")-1);
                            $name = $this->getCell($data,$i,$j++);
                            $name = htmlspecialchars( $name );
                            $names[$language_code] = $name;
                    }
                    while ($this->startsWith($first_row[$j-1],"Description(")) {
                            $language_code = substr($first_row[$j-1],strlen("Description("),strlen($first_row[$j-1])-strlen("Description(")-1);
                            $description = $this->getCell($data,$i,$j++);
                            $description = htmlspecialchars( $description );
                            $descriptions[$language_code] = $description;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Title(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Title("),strlen($first_row[$j-1])-strlen("Meta_Tag_Title(")-1);
                            $meta_tag_title = $this->getCell($data,$i,$j++);
                            $meta_tag_title = htmlspecialchars( $meta_tag_title );
                            $meta_tag_titles[$language_code] = $meta_tag_title;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Description(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Description("),strlen($first_row[$j-1])-strlen("Meta_Tag_Description(")-1);
                            $meta_tag_description = $this->getCell($data,$i,$j++);
                            $meta_tag_description = htmlspecialchars( $meta_tag_description );
                            $meta_tag_descriptions[$language_code] = $meta_tag_description;
                    }
                    while ($this->startsWith($first_row[$j-1],"Meta_Tag_Keywords(")) {
                            $language_code = substr($first_row[$j-1],strlen("Meta_Tag_Keywords("),strlen($first_row[$j-1])-strlen("Meta_Tag_Keywords(")-1);
                            $meta_tag_keyword = $this->getCell($data,$i,$j++);
                            $meta_tag_keyword = htmlspecialchars( $meta_tag_keyword );
                            $meta_tag_keywords[$language_code] = $meta_tag_keyword;
                    }
                   
                    $image = $this->getCell($data,$i,$j++,'');
                    $alt_text = $this->getCell($data,$i,$j++,'');
                    $caption = $this->getCell($data,$i,$j++,'');
                    $sort_order = $this->getCell($data,$i,$j++,'');
                    $status = $this->getCell($data,$i,$j++,'');
                    $catagories = $this->getCell($data,$i,$j++,'');
                    $related_products = $this->getCell($data,$i,$j++,'');
                    $reference = $this->getCell($data,$i,$j++,'');
                    $seo_keywords = $this->getCell($data,$i,$j++,'');
                    $product = array();
                    $product['product_id'] = $product_id;
                    $product['name'] = $names;
                    $product['description'] = $descriptions;
                    $product['meta_title'] = $meta_tag_titles;
                    $product['meta_description'] = $meta_tag_descriptions;
                    $product['meta_keyword'] = $meta_tag_keywords;
                    $product['image'] = $image;
                    $product['alt_text'] = $alt_text;
                    $product['caption'] = $caption;
                    $product['sort_order'] = $sort_order;
                    $product['status'] = $status;
                    $categories = trim( $this->clean($catagories, false) );
                    $product['categories'] = ($categories=="") ? array() : explode( ",", $categories );
                    if ($product['categories']===false) {
                            $product['categories'] = array();
                    }
                    $related_products = trim( $this->clean($related_products, false) );
                    $product['related_products'] = ($related_products=="") ? array() : explode( ",", $related_products );
                    if ($product['related_products']===false) {
                            $product['related_products'] = array();
                    }
                    $product['reference'] = htmlspecialchars( $reference );
                    $product['keyword'] = $seo_keywords;
                    if ($incremental) {
                            $this->deleteGroupedProduct( $product_id );
                    }
                    $available_product_ids[$product_id] = $product_id;
                    $this->moreProductCells( $i, $j, $data, $product );
                    $this->storeGroupedProductIntoDatabase( $product, $languages, $product_fields, $available_store_ids ,$seo_url_ids);
            }
    }
    protected function clean( &$str, $allowBlanks=false ) {
            $result = "";
            $n = strlen( $str );
            for ($m=0; $m<$n; $m++) {
                    $ch = substr( $str, $m, 1 );
                    if (($ch==" ") && (!$allowBlanks) || ($ch=="\n") || ($ch=="\r") || ($ch=="\t") || ($ch=="\0") || ($ch=="\x0B")) {
                            continue;
                    }
                    $result .= $ch;
            }
            return $result;
    }
}
define('FPDF_FONTPATH', DIR_SYSTEM . "library/tfpdf/font/");

require_once DIR_SYSTEM . 'library/fpdf/fpdf.php';
require_once DIR_SYSTEM . 'library/tfpdf/tfpdf.php';

class PDF extends tFPDF {
    function Header() {
        $this->Image(DIR_IMAGE . 'pdfs/coverpageheader.gif', 20, 6, 15);
        $this->SetFont('Arial', 'BI', 7);
        $this->Cell(27);
        $this->Cell(80, 22, 'G-Biosciences, St Louis, MO, USA | 1-800-628-7730 | 1-314-991-6034 | ', 0, 0, 'C');
        $this->SetFont('Arial', 'BI', 7);
        $this->SetTextColor(0, 0, 255);
        $this->Cell(37, 22, 'technical@GBiosciences.com', 0, 0, 'C');
        $this->SetDrawColor(0, 61, 79);
        $this->SetLineWidth(0.9);
        $this->Line(21, 24, 210 - 20, 24); // 20mm from each edge
        $this->Ln(7);
        $this->SetFont('Arial', '', 5);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(56, 22, 'A Geno Technology, Inc. (USA) brand name', 0, 0, 'C');

        $this->Ln(20);
    }

    function Footer() {
        $this->SetY(-27);
        $this->Image(DIR_IMAGE . 'pdfs/coverpagefooter.jpg', 20, null, 15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(0, 61, 79);
        $this->Cell(37);
        $this->Cell(80, -1, 'think proteins! think G-Biosciences!            www.GBiosciences.com ', 0, 0, 'C');
    }
}