<?php
/**
 * wc-cli-import-csv
 *
 * @package           Myridia
 * @author            Myridia
 * @copyright         2024 Myridia
 * @license           GPL
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce cli Import CSV
 * Plugin URI:        https://myridia.com/
 * Description:       Adds wp-cli CLI commands to WordPress.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Myridia
 * Author URI:        https://myridia.com/
 * Text Domain:       wc-cli-import-csv
 * License:           GPL
 */





// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

define('WC_ABSPATH', 'wp-content/plugins/woocommerce/');


//use WC_Product;
require_once WP_PLUGIN_DIR . '/woocommerce/includes/import/class-wc-product-csv-importer.php';

class Commands {
	/**
	 * Run the "do something" command
	 */
	public static function doSomething() {
		//echo "hello";
        //echo ABSPATH;
        echo WC_ABSPATH;
	}
}


    class WP_CLI_WC_Import_CSV {
       
        protected $_mappings = [
            'from'=>[],
            'to'=>[]
        ];

        public function __invoke($args, $assoc_args) {


            $filename = realpath($args[0]);

            if(!file_exists($filename)) {
                WP_CLI::error('File not found : '.$filename);
                return;
            }
            
            if ( ! current_user_can( 'manage_product_terms' ) ) {
                WP_CLI::error('Current user cannot manage categories, ensure you set the --user parameter');
                return;
            }
            
            $params =  [
                'mapping'=>$this->_readMappings(realpath($assoc_args['mappings'])),
                'update_existing'=>array_key_exists('update', $assoc_args) ? true : false,
                'prevent_timeouts'=>false,
                'parse'=>true,
            ];

            $importer = new WC_Product_CSV_Importer($filename, $params);
            $result = $importer->import();
            WP_CLI::success( print_r($result, true) );
        }

        protected function _readMappings($filename) {
            $mappings = [];

            $row = 1;
            
            if(($fh = fopen($filename, 'r')) !== FALSE) {;
                while(($data = fgetcsv($fh)) !== FALSE) {

                    if($row > 1) {
                        $mappings['from'][] = $data[0];
                        $mappings['to'][] = $data[1]; 
                    }
                    $row++;
                }
            }

            fclose($fh);
            var_dump("hello");
            var_dump($mappings);            
            return $mappings;
        }
    }
    


$instance = new WP_CLI_WC_Import_CSV();

WP_CLI::add_command('wc import-csv', $instance,[
        'shortdesc'=>'Import woocommerce products using the standard CSV import',
        'synopsis'=>[
            [
                'type'=>'positional', 
                'name'=>'file',
                'optional'=>false, 
                'repeating'=>false,
            ],
            [
                'type'=>'assoc',
                'name'=>'mappings', 
                'description'=>'Mappings csv file, with "from" and "to" column headers.  The "to" column matches to "Maps to product property" on the import schema.  More details here at https://github.com/woocommerce/woocommerce/wiki/Product-CSV-Import-Schema',
                'optional'=>true,
            ],
            [
                'type'=>'assoc', 
                'name'=>'delimiter',
                'description'=>'Delimeter for csv file (defaults to comma)', 
                'default'=>',',
                'optional'=>true
            ],
            [
                'type'=>'flag', 
                'name'=>'update', 
                'description'=>'Update existing products matched on ID or SKU, omit this flag to insert new products.  Those which exist will be skipped.',
                'optional'=>true 

            ]
        ]
    ]);

/*
WP_CLI::add_command( 'wc import-csv',
	function () {
		try {
              Commands::doSomething();
		} catch ( \Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
			throw $e;
		}

	}
);

*/
