<?php
// Heading
$_['heading_title']         = 'IMPORT DATA';
$_['heading_title_merge_sds'] = 'Merge SDS';

// Text
$_['text_success_category'] = 'Success: You have imported Categories!';
$_['text_success_product']  = 'Success: You have imported Products!';
$_['text_success_catalog']  = 'Success: You have imported Catalogs!';
$_['text_success_meta_update']  = 'Success: You have successfully updated Products Meta Titles!';
$_['text_list']             = 'Import List';
$_['text_category']         = 'Import Category Data';
$_['text_product']          = 'Import / Export Product Data';
$_['text_catalog']          = 'Import / Export Catalog Data';
$_['text_customer']         = 'Import / Export Customer Data';
$_['text_default']          = 'Default';
$_['text_success_import']   = 'Success: You have successfully imported your data!';
$_['text_log_details']      = 'See also \'System &gt; Error Logs\' for more details.';
$_['text_log_details_2_0_x']= 'See also \'Tools &gt; Error Logs\' for more details.';
$_['text_valid_product_id']  = '<b>Catalog File:</b> Please use %s or above as product_id while adding new products using excel';
$_['text_valid_grouped_product_id']  = '<b>Grouped Product File:</b> Please use %s or above as product_id while adding new products using excel';
$_['text_help']  = '<b>Notice:</b> You can only update customers details using import fuctionality';

// Column
$_['column_name']           = 'Import Name';
$_['column_sort_order']     = 'Sort Order';
$_['column_action']         = 'Action';

// Entry
$_['entry_category_file']   = 'Select Category file';
$_['entry_product_file']    = 'Select Product file';
$_['entry_catalog_file']    = 'Select Catalog file <br />( You can have as many entries for catalog import excel )';
$_['entry_grouped_file']    = 'Select Grouped product file <br />( You can have as many entries for Grouped product import excel )';
$_['entry_customer_file']   = 'Select Ccustomer file <br />( You can have as many entries for customer import excel )';
$_['entry_update_product_meta_title']    = 'Update Product Meta Title';
$_['entry_merge_sds_file']  = 'Select file to merge sds';
$_['entry_merge_sds_language'] = 'Select language';
$_['entry_merge_protocol_file']  = 'Select file to merge protocol';
$_['entry_merge_coa_file']  = 'Select file to merge coa';
$_['entry_customer_file']   = 'Select customer file';
$_['entry_sort_order']      = 'Sort Order';
$_['entry_type']            = 'Type';
$_['entry_email']           = 'Email';
$_['entry_second_email']    = 'Secondary Email';
$_['entry_third_email']     = 'Tenary Email';
$_['entry_website']         = 'Website';
$_['entry_address']         = 'Address';
$_['entry_country']         = 'Country';
$_['entry_phone1']          = 'Phone1';
$_['entry_phone2']          = 'Phone2';
$_['entry_fax']             = 'Fax';
$_['entry_custom_url']      = 'Custom Url';
$_['entry_meta_title']      = 'Meta Title';
$_['entry_meta_description']= 'Meta Description';
$_['entry_meta_keyword']    = 'Meta Keyword';
$_['entry_status']          = 'Status';

   
//button
$_['button_import']         = 'Import!';

//Help
$_['help_update_product_meta_title']    = 'Products which are having blank meta title will gets updated to the product name itself';

//Error
$_['error_permission']       = 'Warning: You do not have permission to modify import!';
$_['error_upload_ext']       = 'Uploaded file has not one of the \'.xls\', \'.xlsx\' or \'.ods\' file name extensions, it might not be a spreadsheet file!';
$_['error_upload']           = 'Uploaded spreadsheet file has validation errors!';
$_['error_upload_name']      = 'Missing file name for upload';
$_['error_missing_product_id']     = 'Export/Import: Missing product_ids in worksheet \'%1\'!';
$_['error_missing_customer_id']     = 'Export/Import: Missing customer_ids in worksheet \'%1\'!';
$_['error_invalid_product_id']     = 'Export/Import: Invalid product_id \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_customer_id']     = 'Export/Import: Invalid customer_id \'%2\' used in worksheet \'%1\'!';
$_['error_duplicate_product_id']   = 'Export/Import: Duplicate product_id \'%2\' used in worksheet \'%1\'!';
$_['error_wrong_order_product_id'] = 'Export/Import: Worksheet \'%1\' uses product_id \'%2\' in the wrong order. Ascending order expected!';
$_['error_unlisted_product_id']    = 'Export/Import: Worksheet \'%1\' cannot use product_id \'%2\' because it is not listed in worksheet \'Products\'!';
$_['error_duplicate_customer_id']   = 'Export/Import: Duplicate customer_id \'%2\' used in worksheet \'%1\'!';
$_['error_wrong_order_customer_id'] = 'Export/Import: Worksheet \'%1\' uses customer_id \'%2\' in the wrong order. Ascending order expected!';
$_['error_unlisted_customer_id']    = 'Export/Import: Worksheet \'%1\' cannot use customer_id \'%2\' because it is not listed in worksheet \'Products\'!';
$_['error_products_header']        = 'Export/Import: Invalid header in the Products worksheet';
$_['error_merging_header']         = 'Export/Import: Invalid header in the Merging worksheet';
$_['error_rewards_header']         = 'Export/Import: Invalid header in the Rewards worksheet';
$_['error_specials_header']        = 'Export/Import: Invalid header in the Specials worksheet';
$_['error_discounts_header']       = 'Export/Import: Invalid header in the Discounts worksheet';
$_['error_grouped_heading']        = 'Export/Import: Invalid header in the Grouped Products worksheet';
$_['error_gpdata']                 = 'Export/Import: Invalid header in the GP-Data worksheet';
$_['error_grouped_images']         = 'Export/Import: Invalid header in the Grouped Images worksheet';
$_['error_grouped_citations']      = 'Export/Import: Invalid header in the Citation worksheet';
$_['error_protocols_header']       = 'Export/Import: Invalid header in the Protocol worksheet';
$_['error_msds_header']            = 'Export/Import: Invalid header in the Msds worksheet';
$_['error_coa_header']             = 'Export/Import: Invalid header in the Coa worksheet';
$_['error_technical_header']       = 'Export/Import: Invalid header in the Technical worksheet';
$_['error_rewards']                = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Rewards';
$_['error_specials']               = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Specials';
$_['error_discounts']              = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Discounts';
$_['error_rewards_customer']       = 'Export/Import: Missing Customers worksheet, or Customers worksheet not listed before Rewards';
$_['error_protocol']               = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Protocol';
$_['error_msds']                   = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Msds';
$_['error_coa']                    = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Coa';
$_['error_technical']              = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Technical';
$_['error_merge_language']          = 'Please select a valid language to merge SDS file.';