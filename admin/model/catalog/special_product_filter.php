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

                if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!='tool/export_import/download'))  {
                        if ($config->get('config_error_display')) {
                                echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
                        }
                } else {
                        $session->data['export_import_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
                        $token = $request->get['token'];
                        $link = $url->link( 'catalog/special_product_filter', 'token='.$token, 'SSL' );
                        header('Status: ' . 302);
                        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
                        exit();
                }

                return true;
}
function fatal_error_shutdown_handler_for_export_import() {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
                // fatal error
                error_handler_for_export_import(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
        }
}
ini_set('max_execution_time', 3600000);
ini_set('memory_limit','2048M');
class ModelCatalogSpecialProductFilter extends Model {
        private $error = array();
        protected $null_array = array();
	public function addSpecialProductFilter($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "special_product_filter_group` SET sort_order = '" . (int)$data['sort_order'] . "'");

		$special_product_filter_group_id = $this->db->getLastId();

		foreach ($data['special_product_filter_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter_group_description SET special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		if (isset($data['special_product_filter'])) {
			foreach ($data['special_product_filter'] as $filter) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter SET special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");

				$special_product_filter_id = $this->db->getLastId();

				foreach ($filter['special_product_filter_description'] as $language_id => $filter_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter_description SET special_product_filter_id = '" . (int)$special_product_filter_id . "', language_id = '" . (int)$language_id . "', special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', name = '" . $this->db->escape($filter_description['name']) . "'");
				}
			}
		}

		return $special_product_filter_group_id;
	}

	public function editSpecialProductFilter($special_product_filter_group_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "special_product_filter_group` SET sort_order = '" . (int)$data['sort_order'] . "' WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "special_product_filter_group_description WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");

		foreach ($data['special_product_filter_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter_group_description SET special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "special_product_filter WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "special_product_filter_description WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");

		if (isset($data['special_product_filter'])) {
			foreach ($data['special_product_filter'] as $filter) {
				if ($filter['special_product_filter_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter SET special_product_filter_id = '" . (int)$filter['special_product_filter_id'] . "', special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter SET special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");
				}

				$special_product_filter_id = $this->db->getLastId();

				foreach ($filter['special_product_filter_description'] as $language_id => $filter_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "special_product_filter_description SET special_product_filter_id = '" . (int)$special_product_filter_id . "', language_id = '" . (int)$language_id . "', special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "', name = '" . $this->db->escape($filter_description['name']) . "'");
				}
			}
		}
	}

	public function deleteSpecialProductFilter($special_product_filter_group_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "special_product_filter_group` WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "special_product_filter_group_description` WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "special_product_filter` WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "special_product_filter_description` WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");
	}

	public function getSpecialProductFilterGroup($special_product_filter_group_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "special_product_filter_group` fg LEFT JOIN " . DB_PREFIX . "special_product_filter_group_description fgd ON (fg.special_product_filter_group_id = fgd.special_product_filter_group_id) WHERE fg.special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "' AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getSpecialProductFilterGroups($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "special_product_filter_group` spfg LEFT JOIN " . DB_PREFIX . "special_product_filter_group_description spfgd ON (spfg.special_product_filter_group_id = spfgd.special_product_filter_group_id) WHERE spfgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'spfgd.name',
			'spfg.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY spfgd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getSpecialProductFilterGroupDescriptions($special_product_filter_group_id) {
		$special_product_filter_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_product_filter_group_description WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");

		foreach ($query->rows as $result) {
			$special_product_filter_group_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $special_product_filter_group_data;
	}

	public function getSpecialProductFilter($special_product_filter_id) {
		$query = $this->db->query("SELECT *, (SELECT name FROM " . DB_PREFIX . "special_product_filter_group_description spfgd WHERE spf.special_product_filter_group_id = spfgd.special_product_filter_group_id AND spfgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group` FROM " . DB_PREFIX . "special_product_filter spf LEFT JOIN " . DB_PREFIX . "special_product_filter_description spfd ON (spf.special_product_filter_id = spfd.special_product_filter_id) WHERE spf.special_product_filter_id = '" . (int)$special_product_filter_id . "' AND spfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

        public function getSpecialProductFilters($data = array()) {
                $sql = "SELECT * FROM `" . DB_PREFIX . "special_product_filter_group` spfg LEFT JOIN `" . DB_PREFIX . "special_product_filter_group_description` spfgd"
                    . " ON (spfg.special_product_filter_group_id = spfgd.special_product_filter_group_id) WHERE spfgd.language_id = '" . $this->config->get('config_language_id') . "'";

                if (!empty($data['filter_name'])) {
                        $sql .= " AND spfgd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
                }

                $sql .= " ORDER BY spfg.sort_order";

                if (isset($data['start']) || isset($data['limit'])) {
                        if ($data['start'] < 0) {
                                $data['start'] = 0;
                        }

                        if ($data['limit'] < 1) {
                                $data['limit'] = 20;
                        }

                        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
                }

                $query = $this->db->query($sql);

                return $query->rows;
        }

	public function getSpecialProductFilterDescriptions($special_product_filter_group_id) {
		$filter_data = array();

		$filter_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_product_filter WHERE special_product_filter_group_id = '" . (int)$special_product_filter_group_id . "'");

		foreach ($filter_query->rows as $filter) {
			$filter_description_data = array();

			$filter_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_product_filter_description WHERE special_product_filter_id = '" . (int)$filter['special_product_filter_id'] . "'");

			foreach ($filter_description_query->rows as $filter_description) {
				$filter_description_data[$filter_description['language_id']] = array('name' => $filter_description['name']);
			}

			$filter_data[] = array(
				'special_product_filter_id'             => $filter['special_product_filter_id'],
				'special_product_filter_description'    => $filter_description_data,
				'sort_order'                            => $filter['sort_order']
			);
		}

		return $filter_data;
	}

	public function getTotalSpecialProductFilterGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "special_product_filter_group`");

		return $query->row['total'];
	}

        public function getSpecialProductFilterOptions($spfg_id, $filter_row) {
                $sql = "SELECT * FROM " . DB_PREFIX . "special_product_filter spf LEFT JOIN " . DB_PREFIX . "special_product_filter_description spfd ON (spf.special_product_filter_id = spfd.special_product_filter_id) WHERE spf.special_product_filter_group_id = '" . (int)$spfg_id . "' ORDER BY spf.sort_order ASC";

                $query = $this->db->query($sql);

                foreach ($query->rows as $result) {
                        $filter_data['options'][] = array(
                            'value'    => $result['special_product_filter_id'],
                            'text'     => $result['name']
                        );
                }

                $filter_data['filter_row'] = $filter_row;

                return $filter_data;
        }

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

        public function getLastInsertId(){
                $data = array();
                $query = $this->db->query("SELECT MAX(special_product_filter_id) as special_product_filter_id, MAX(special_product_filter_group_id) as special_product_filter_group_id FROM " . DB_PREFIX . "special_product_filter");
                
                $data['last_special_product_filter_id'] = (int)$query->row['special_product_filter_id'] + 1;
                $data['last_special_product_filter_group_id'] = (int)$query->row['special_product_filter_group_id'] + 1;
                
                return $data;
        }

        public function download() {
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

                        // Creating the FilterGroups worksheet
                        //$workbook->createSheet();
                        $workbook->setActiveSheetIndex($worksheet_index++);
                        $worksheet = $workbook->getActiveSheet();
                        $worksheet->setTitle( 'FilterGroups' );
                        $this->populateFilterGroupsWorksheet( $worksheet, $languages, $box_format, $text_format );
                        $worksheet->freezePaneByColumnAndRow( 1, 2 );

                        // Creating the filters worksheet
                        $workbook->createSheet();
                        $workbook->setActiveSheetIndex($worksheet_index++);
                        $worksheet = $workbook->getActiveSheet();
                        $worksheet->setTitle( 'Filters' );
                        $this->populateFiltersWorksheet( $worksheet, $languages, $box_format, $text_format );
                        $worksheet->freezePaneByColumnAndRow( 1, 2 );

			$workbook->setActiveSheetIndex(0);

			// redirect output to client browser
			$datetime = date('Y-m-d');
                        $filename = 'special_product_filters-'.$datetime.'.xlsx';

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

        protected function isInteger($input){
		return(ctype_digit(strval($input)));
	}

        protected function getFilterGroupDescriptions( &$languages ) {
		// query the special_product_filter_group_description table for each language
		$special_product_filter_group_descriptions = array();
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$language_code = $language['code'];
			$sql  = "SELECT spfg.special_product_filter_group_id, spfgd.* ";
			$sql .= "FROM `" . DB_PREFIX . "special_product_filter_group` spfg ";
			$sql .= "LEFT JOIN `".DB_PREFIX."special_product_filter_group_description` spfgd ON spfgd.special_product_filter_group_id=spfg.special_product_filter_group_id AND spfgd.language_id='".(int)$language_id."' ";
			$sql .= "GROUP BY spfg.special_product_filter_group_id ";
			$sql .= "ORDER BY spfg.special_product_filter_group_id ASC ";
			$query = $this->db->query( $sql );
			$special_product_filter_group_descriptions[$language_code] = $query->rows;
		}
		return $special_product_filter_group_descriptions;
	}

        protected function getFilterGroups( &$languages ) {
		$results = $this->db->query( "SELECT * FROM `".DB_PREFIX."special_product_filter_group` ORDER BY special_product_filter_group_id ASC" );
		$special_product_filter_group_descriptions = $this->getFilterGroupDescriptions( $languages );
		foreach ($languages as $language) {
			$language_code = $language['code'];
			foreach ($results->rows as $key=>$row) {
				if (isset($special_product_filter_group_descriptions[$language_code][$key])) {
					$results->rows[$key]['name'][$language_code] = $special_product_filter_group_descriptions[$language_code][$key]['name'];
				} else {
					$results->rows[$key]['name'][$language_code] = '';
				}
			}
		}
		return $results->rows;
	}

        protected function populateFilterGroupsWorksheet( &$worksheet, $languages, &$box_format, &$text_format ) {
                    // Set the column widths
                    $j = 0;
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_group_id'),30)+1);
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order')+1);
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name'),30)+1);

                    // The heading row and column styles
                    $styles = array();
                    $data = array();
                    $i = 1;
                    $j = 0;

                    $data[$j++] = 'special_product_filter_group_id';
                    $data[$j++] = 'sort_order';
                    foreach ($languages as $language) {
			$styles[$j] = &$text_format;
			$data[$j++] ='name('.$language['code'].')';
                    }

                    $worksheet->getRowDimension($i)->setRowHeight(30);
                    $this->setCellRow( $worksheet, $i, $data, $box_format );

                    // The actual product specials data
                    $i += 1;
                    $j = 0;
                    //$special_product_filter_groups = $this->getSpecialProductFilterGroupsValues();
                    $special_product_filter_groups = $this->getFilterGroups( $languages );
                    
                    foreach ($special_product_filter_groups as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(13);
                            $data = array();
                            $data[$j++] = $row['special_product_filter_group_id'];
                            $data[$j++] = $row['sort_order'];
                            foreach ($languages as $language) {
				$data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
                            }
                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
            }

        protected function getFilterDescriptions( &$languages ) {
		// query the special_product_filter_description table for each language
		$special_product_filter_descriptions = array();
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$language_code = $language['code'];
			$sql  = "SELECT spf.special_product_filter_group_id, spf.special_product_filter_id, spfd.* ";
			$sql .= "FROM `" . DB_PREFIX . "special_product_filter` spf ";
			$sql .= "LEFT JOIN `" . DB_PREFIX . "special_product_filter_description` spfd ON spfd.special_product_filter_id=spf.special_product_filter_id AND spfd.language_id='".(int)$language_id."' ";
			$sql .= "GROUP BY spf.special_product_filter_group_id, spf.special_product_filter_id ";
			$sql .= "ORDER BY spf.special_product_filter_group_id ASC, spf.special_product_filter_id ASC ";
			$query = $this->db->query( $sql );
			$special_product_filter_descriptions[$language_code] = $query->rows;
		}
		return $special_product_filter_descriptions;
	}


	protected function getFilters( &$languages ) {
		$results = $this->db->query( "SELECT * FROM `".DB_PREFIX."special_product_filter` ORDER BY special_product_filter_id ASC, special_product_filter_group_id ASC" );
		$filter_descriptions = $this->getFilterDescriptions( $languages );
		foreach ($languages as $language) {
			$language_code = $language['code'];
			foreach ($results->rows as $key=>$row) {
				if (isset($filter_descriptions[$language_code][$key])) {
					$results->rows[$key]['name'][$language_code] = $filter_descriptions[$language_code][$key]['name'];
				} else {
					$results->rows[$key]['name'][$language_code] = '';
				}
			}
		}
		return $results->rows;
	}

        protected function populateFiltersWorksheet( &$worksheet, $languages, &$box_format, &$text_format ) {
                    // Set the column widths
                    $j = 0;
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('special_product_filter_id'),30)+1);
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('special_product_filter_group_id')+1);
                    $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'),30)+1);
                    foreach ($languages as $language) {
			$worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name')+4,30)+1);
                    }

                    // The heading row and column styles
                    $styles = array();
                    $data = array();
                    $i = 1;
                    $j = 0;

                    $data[$j++] = 'special_product_filter_id';
                    $data[$j++] = 'special_product_filter_group_id';
                    $data[$j++] = 'sort_order';
                    foreach ($languages as $language) {
			$styles[$j] = &$text_format;
			$data[$j++] = 'name('.$language['code'].')';
                    }

                    $worksheet->getRowDimension($i)->setRowHeight(30);
                    $this->setCellRow( $worksheet, $i, $data, $box_format );

                    // The actual product specials data
                    $i += 1;
                    $j = 0;
                    //  $special_product_filter_values = $this->getSpecialProductFiltersValues();
                    $special_product_filter_values = $this->getFilters( $languages );
                    
                    foreach ($special_product_filter_values as $row) {
                            $worksheet->getRowDimension($i)->setRowHeight(13);
                            $data = array();
                            $data[$j++] = $row['special_product_filter_id'];
                            $data[$j++] = $row['special_product_filter_group_id'];
                            $data[$j++] = $row['sort_order'];
                            foreach ($languages as $language) {
				$data[$j++] = html_entity_decode($row['name'][$language['code']],ENT_QUOTES,'UTF-8');
			}

                            $this->setCellRow( $worksheet, $i, $data, $this->null_array, $styles );
                            $i += 1;
                            $j = 0;
                    }
            }
 
        public function upload( $filename, $incremental=true ) {
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
                        $cacheSettings = array( ' memoryCacheSize '  => '16MB'  );
                        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

			// parse uploaded spreadsheet file
			$inputFileType = PHPExcel_IOFactory::identify($filename);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setReadDataOnly(true);
			$reader = $objReader->load($filename);

			// read the various worksheets and load them to the database			
			if (!$this->validateUpload( $reader )) {
				return false;
			}

			$this->clearCache();
			$this->session->data['export_import_nochange'] = 0;
			$this->uploadFilterGroups( $reader, $incremental );
			$this->uploadFilters( $reader, $incremental );
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

        // USE only required function param in below function
        protected function uploadFilters( &$reader, $incremental ) {
		// get worksheet, if not there return immediately
		$data = $reader->getSheetByName( 'Filters' );
		if ($data==null) {
			return;
		}

		// find the installed languages
		$languages = $this->getLanguages();

		// if not incremental then delete all old filters
		if (!$incremental) {
			$this->deleteFilters();
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
			$special_product_filter_id = trim($this->getCell($data,$i,$j++));
			if ($special_product_filter_id=='') {
				continue;
			}
			$special_product_filter_group_id = trim($this->getCell($data,$i,$j++));
			if ($special_product_filter_group_id=='') {
				continue;
			}
			$sort_order = $this->getCell($data,$i,$j++,'0');
			$names = array();
			while (($j<=$max_col) && $this->startsWith($first_row[$j-1],"name(")) {
				$language_code = substr($first_row[$j-1],strlen("name("),strlen($first_row[$j-1])-strlen("name(")-1);
				$name = $this->getCell($data,$i,$j++);
				$name = htmlspecialchars( $name );
				$names[$language_code] = $name;
			}
			$filter = array();
			$filter['special_product_filter_id'] = $special_product_filter_id;
			$filter['special_product_filter_group_id'] = $special_product_filter_group_id;
			$filter['sort_order'] = $sort_order;
			$filter['names'] = $names;
			if ($incremental) {
				$this->deleteFilter( $special_product_filter_id );
			}
			$this->moreFilterCells( $i, $j, $data, $filter );
			$this->storeFilterIntoDatabase( $filter, $languages );
		}
	}

        protected function uploadFilterGroups( &$reader, $incremental ) {
		// get worksheet, if not there return immediately
		$data = $reader->getSheetByName( 'FilterGroups' );
		if ($data==null) {
			return;
		}

		// find the installed languages
		$languages = $this->getLanguages();

		// if not incremental then delete all old filter groups
		if (!$incremental) {
			$this->deleteFilterGroups();
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
			$special_product_filter_group_id = trim($this->getCell($data,$i,$j++));
			if ($special_product_filter_group_id=='') {
				continue;
			}
			$sort_order = $this->getCell($data,$i,$j++,'0');
			$names = array();
			while (($j<=$max_col) && $this->startsWith($first_row[$j-1],"name(")) {
				$language_code = substr($first_row[$j-1],strlen("name("),strlen($first_row[$j-1])-strlen("name(")-1);
				$name = $this->getCell($data,$i,$j++);
				$name = htmlspecialchars( $name );
				$names[$language_code] = $name;
			}
			$filter_group = array();
			$filter_group['special_product_filter_group_id'] = $special_product_filter_group_id;
			$filter_group['sort_order'] = $sort_order;
			$filter_group['names'] = $names;
			if ($incremental) {
				$this->deleteFilterGroup( $special_product_filter_group_id );
			}
			$this->moreFilterGroupCells( $i, $j, $data, $filter_group );
			$this->storeFilterGroupIntoDatabase( $filter_group, $languages );
		}
	}

        protected function storeFilterIntoDatabase( &$filter, &$languages ) {
		$special_product_filter_id = $filter['special_product_filter_id'];
		$special_product_filter_group_id = $filter['special_product_filter_group_id'];
		$sort_order = $filter['sort_order'];
		$names = $filter['names'];
		$sql  = "INSERT INTO `".DB_PREFIX."special_product_filter` (`special_product_filter_id`,`special_product_filter_group_id`,`sort_order`) VALUES ";
		$sql .= "( $special_product_filter_id, $special_product_filter_group_id, $sort_order );"; 
		$this->db->query( $sql );
		foreach ($languages as $language) {
			$language_code = $language['code'];
			$language_id = $language['language_id'];
			$name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
			$sql  = "INSERT INTO `".DB_PREFIX."special_product_filter_description` (`special_product_filter_id`, `language_id`, `special_product_filter_group_id`, `name`) ";
			$sql .= "VALUES ( $special_product_filter_id, $language_id, $special_product_filter_group_id, '$name' );";
			$this->db->query( $sql );
		}
	}

        protected function storeFilterGroupIntoDatabase( &$filter_group, &$languages ) {
		$special_product_filter_group_id = $filter_group['special_product_filter_group_id'];
		$sort_order = $filter_group['sort_order'];
		$names = $filter_group['names'];
		$sql  = "INSERT INTO `".DB_PREFIX."special_product_filter_group` (`special_product_filter_group_id`,`sort_order`) VALUES ";
		$sql .= "( $special_product_filter_group_id, $sort_order );"; 
		$this->db->query( $sql );
		foreach ($languages as $language) {
			$language_code = $language['code'];
			$language_id = $language['language_id'];
			$name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
			$sql  = "INSERT INTO `".DB_PREFIX."special_product_filter_group_description` (`special_product_filter_group_id`, `language_id`, `name`) VALUES ";
			$sql .= "( $special_product_filter_group_id, $language_id, '$name' );";
			$this->db->query( $sql );
		}
	}

        protected function getLanguages() {
		$query = $this->db->query( "SELECT * FROM `".DB_PREFIX."language` WHERE `status`=1 ORDER BY `code`" );

		return $query->rows;
	}

        protected function deleteFilters() {
		$sql = "TRUNCATE TABLE `".DB_PREFIX."special_product_filter`";
		$this->db->query( $sql );
		$sql = "TRUNCATE TABLE `".DB_PREFIX."special_product_filter_description`";
		$this->db->query( $sql );
	}

        protected function deleteFilter( $special_product_filter_id ) {
		$sql = "DELETE FROM `".DB_PREFIX."special_product_filter` WHERE special_product_filter_id='".(int)$special_product_filter_id."'";
		$this->db->query( $sql );
		$sql = "DELETE FROM `".DB_PREFIX."special_product_filter_description` WHERE special_product_filter_id='".(int)$special_product_filter_id."'";
		$this->db->query( $sql );
	}

        protected function deleteFilterGroups() {
		$sql = "TRUNCATE TABLE `".DB_PREFIX."special_product_filter_group`";
		$this->db->query( $sql );
		$sql = "TRUNCATE TABLE `".DB_PREFIX."special_product_filter_group_description`";
		$this->db->query( $sql );
	}

        protected function startsWith( $haystack, $needle ) {
		if (strlen( $haystack ) < strlen( $needle )) {
			return false;
		}
		return (substr( $haystack, 0, strlen($needle) ) == $needle);
	}

	protected function deleteFilterGroup( $special_product_filter_group_id ) {
		$sql = "DELETE FROM `".DB_PREFIX."special_product_filter_group` WHERE special_product_filter_group_id='".(int)$special_product_filter_group_id."'";
		$this->db->query( $sql );
		$sql = "DELETE FROM `".DB_PREFIX."special_product_filter_group_description` WHERE special_product_filter_group_id='".(int)$special_product_filter_group_id."'";
		$this->db->query( $sql );
	}

        // function for reading additional cells in class extensions
	protected function moreFilterGroupCells( $i, &$j, &$worksheet, &$filter_group ) {
		return;
	}

        // function for reading additional cells in class extensions
	protected function moreFilterCells( $i, &$j, &$worksheet, &$filter ) {
		return;
	}

        protected function validateUpload( &$reader ) {
		$ok = true;
		// worksheets must have correct heading rows
		if (!$this->validateFilterGroups( $reader )) {
			$this->log->write( $this->language->get('error_filter_group') );
			$ok = false;
		}
		if (!$this->validateFilters( $reader )) {
			$this->log->write( $this->language->get('error_filter_option') );
			$ok = false;
		}

		// certain worksheets rely on the existence of other worksheets
		$names = $reader->getSheetNames();
		$exist_filter_groups = false;
		$exist_filters = false;
		foreach ($names as $name) {
			if ($name=='FilterGroups') {
				$exist_filter_groups = true;
				continue;
			}
                        if ($name=='Filters') {
                                if (!$exist_filter_groups) {
                                        $this->log->write( $this->language->get('error_filter_group') );
                                        $ok = false;
                                }
                                $exist_filters = true;
                                continue;
                        }
		}

		if (!$ok) {
			return false;
		}

		if (!$this->validateSpecialProductFilterGroupIdColumns( $reader )) {
			$ok = false;
		}

		return $ok;

	}

        protected function validateFilterGroups( &$reader ) {
		$data = $reader->getSheetByName( 'FilterGroups' );
		if ($data==null) {
			return true;
		}
		if (!$this->existFilter()) {
			throw new Exception( $this->language->get( 'error_filter_not_supported' ) );
		}
		$expected_heading = array( "special_product_filter_group_id", "sort_order", "name" );
		$expected_multilingual = array( "name" );

		return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
	}

        protected function validateFilters( &$reader ) {
		$data = $reader->getSheetByName( 'Filters' );
		if ($data==null) {
			return true;
		}
		if (!$this->existFilter()) {
			throw new Exception( $this->language->get( 'error_filter_not_supported' ) );
		}
		$expected_heading = array( "special_product_filter_id", "special_product_filter_group_id", "sort_order", "name" );
		$expected_multilingual = array( "name" );

		return $this->validateHeading( $data, $expected_heading, $expected_multilingual );
	}

        protected function validateHeading( &$data, &$expected, &$multilingual ) {
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

        protected function validateSpecialProductFilterGroupIdColumns( &$reader ) {
		$data = $reader->getSheetByName( 'FilterGroups' );
		if ($data==null) {
			return true;
		}
		$ok = true;

		// only unique numeric filter_group_ids can be used, in ascending order, in worksheet 'FilterGroups'
		$previous_special_product_group_id = 0;
		$has_missing_special_product_filter_group_ids = false;
		$special_product_filter_group_ids = array();
		$k = $data->getHighestRow();
		for ($i=1; $i<$k; $i+=1) {
			$special_product_filter_group_id = $this->getCell($data,$i,1);
			if ($special_product_filter_group_id=="") {
				if (!$has_missing_special_product_filter_group_ids) {
					$msg = str_replace( '%1', 'FilterGroups', $this->language->get( 'error_missing_group_id' ) );
					$this->log->write( $msg );
					$has_missing_special_product_filter_group_ids = true;
				}
				$ok = false;
				continue;
			}
			if (!$this->isInteger($special_product_filter_group_id)) {
				$msg = str_replace( '%2', $special_product_filter_group_id, str_replace( '%1', 'FilterGroups', $this->language->get( 'error_invalid_group_id' ) ) );
				$this->log->write( $msg );
				$ok = false;
				continue;
			}
			if (in_array( $special_product_filter_group_id, $special_product_filter_group_ids )) {
				$msg = str_replace( '%2', $special_product_filter_group_id, str_replace( '%1', 'FilterGroups', $this->language->get( 'error_duplicate_group_id' ) ) );
				$this->log->write( $msg );
				$ok = false;
			}
			$special_product_filter_group_ids[] = $special_product_filter_group_id;
			if ($special_product_filter_group_id < $previous_special_product_group_id) {
				$msg = str_replace( '%2', $special_product_filter_group_id, str_replace( '%1', 'FilterGroups', $this->language->get( 'error_wrong_order_group_id' ) ) );
				$this->log->write( $msg );
				$ok = false;
			}
			$previous_special_product_group_id = $special_product_filter_group_id;
		}

		// make sure filter_ids are numeric entries and are in ascending order
		$worksheets = array( 'Filters' );
		foreach ($worksheets as $worksheet) {
			$data = $reader->getSheetByName( $worksheet );
			if ($data==null) {
				continue;
			}
			$previous_filter_id = 0;
			$has_missing_filter_ids = false;
                        $unlisted_special_product_filter_group_ids = array();
			$k = $data->getHighestRow();
			for ($i=1; $i<$k; $i+=1) {
				$special_product_filter_id = $this->getCell($data,$i,1);
                                $special_product_filter_group_id = $this->getCell($data,$i,2);
				if ($special_product_filter_id=="") {
					if (!$has_missing_filter_ids) {
						$msg = str_replace( '%1', $worksheet, $this->language->get( 'error_missing_filter_id' ) );
						$this->log->write( $msg );
						$has_missing_filter_ids = true;
					}
					$ok = false;
					continue;
				}
				if (!$this->isInteger($special_product_filter_id)) {
					$msg = str_replace( '%2', $special_product_filter_id, str_replace( '%1', $worksheet, $this->language->get( 'error_invalid_filter_id' ) ) );
					$this->log->write( $msg );
					$ok = false;
					continue;
				}
				if (!in_array( $special_product_filter_group_id, $special_product_filter_group_ids )) {
					if (!in_array( $special_product_filter_group_id, $unlisted_special_product_filter_group_ids )) {
						$unlisted_special_product_filter_group_ids[] = $special_product_filter_group_id;
						$msg = str_replace( '%2', $special_product_filter_group_id, str_replace( '%1', $worksheet, $this->language->get( 'error_unlisted_special_product_filter_group_id' ) ) );
						$this->log->write( $msg );
						$ok = false;
					}
				}
				if ($special_product_filter_id < $previous_filter_id) {
					$msg = str_replace( '%2', $special_product_filter_id, str_replace( '%1', $worksheet, $this->language->get( 'error_wrong_order_filter_id' ) ) );
					$this->log->write( $msg );
					$ok = false;
				}
				$previous_filter_id = $special_product_filter_id;
			}
		}

		return $ok;
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

        protected function getCell(&$worksheet,$row,$col,$default_val='') {
		$col -= 1; // we use 1-based, PHPExcel uses 0-based column index
		$row += 1; // we use 0-based, PHPExcel uses 1-based row index
		$val = ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
		if ($val===null) {
			$val = $default_val;
		}
		return $val;
	}

        protected function clearCache() {
		$this->cache->delete('*');
	}

        public function existFilter() {
		// only newer OpenCart versions support filters
		$query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."special_product_filter'" );
		$exist_table_special_product_filter = ($query->num_rows > 0);
		$query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."special_product_filter_group'" );
		$exist_table_special_product_filter_group = ($query->num_rows > 0);
		$query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."special_product_filter_description'" );
		$exist_table_special_product_filter_description = ($query->num_rows > 0);
		$query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."special_product_filter_group_description'" );
		$exist_table_special_product_filter_group_description = ($query->num_rows > 0);

		if (!$exist_table_special_product_filter) {
			return false;
		}
		if (!$exist_table_special_product_filter_group) {
			return false;
		}
		if (!$exist_table_special_product_filter_description) {
			return false;
		}
		if (!$exist_table_special_product_filter_group_description) {
			return false;
		}

		return true;
	}
}