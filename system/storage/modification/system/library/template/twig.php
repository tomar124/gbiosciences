<?php
namespace Template;

final class Twig {
    private $twig;
    private $data = array();
    
    public function __construct() {
        include_once(DIR_SYSTEM . 'library/template/Twig/Autoloader.php');
        \Twig_Autoloader::register();
    }
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function render($template, $cache = false) {
        		
		$loader = new \Twig_Loader_Filesystem();
		
		if (defined('DIR_CATALOG') && is_dir(DIR_MODIFICATION . 'admin/view/template/')) {	
			$loader->addPath(DIR_MODIFICATION . 'admin/view/template/');
		} elseif (is_dir(DIR_MODIFICATION . 'catalog/view/theme/')) {
			$loader->addPath(DIR_MODIFICATION . 'catalog/view/theme/');
		}
		
		$loader->addPath(DIR_TEMPLATE);
        
        $config = array(
            'autoescape' => false,
            'optimizations' => -1,
            'strict_variables' => false,
            'debug' => false,
            'auto_reload' => true,
            'cache' => $cache ? DIR_CACHE : false
        );

        $this->twig = new \Twig_Environment($loader, $config);
        
        try {
            $template = $this->twig->loadTemplate($template . '.twig');
            $output = $template->render($this->data);
            
            // Fix the specific Windows Twig spacing bug
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Fix: block-contentexpand-content -> block-content expand-content
                $output = str_replace('block-contentexpand-content', 'block-content expand-content', $output);
            }
            
            return $output;
            
        } catch (\Exception $e) {
            trigger_error('Error: Could not load template ' . $template . '!');
            exit();    
        }    
    }    
}