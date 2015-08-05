<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Controller extends CI_Controller
{
    /* Output control constants */
    const OUTPUT_TEMPLATE = 10;
    const OUTPUT_NORMAL = 11;

    /* Private properties */

    
    //the default public folder this means that content inside this folder will be accessed directly
    //without using the routing.
    //Note!!! This folder must be enabled in the .htaccess file.
    private $_public_folder = '';
    
	private $_template_folder = 'sites/';
	private $_template_view = 'default';
	
    
    //the default css location for the css files inside the $public_folder("public/" by default) (public/css/)
    private $_css_folder = 'css/';
    
    //the default javascript location for the css files inside the $public_folder("public/" by default) (public/js/)
    private $_javascript_folder = 'js/';
    
    //Inline scripting (Javascript)
    private $_inline_scripting = '';

    private $_modules = array(); //An array that contains the modules output.

    private $_charset = ''; //The page charset

    private $_title = ''; //The page Title
    private $_title_long = ''; // Site long title
	
	private $_fav_icon = '';
	
    //Media files and data
    private $_media = array('css'=>array(),
                            'javascript'=>array(),
                             //meta tags
                            'meta'=>array(),
                             //RDF are 3rd genreration meta tags
                            'rdf'=>array());

	
    //The requested controller
    protected $controller = '';
    //The requested method to be called
    protected $method        = '';
    
    private $_output_data = array(); 
    
    private $_autoload_language_folder = '';


    /**
     * The MY_Controller constructor method.
     */
    function __construct(){
        parent::__construct();
        //Initializing the controller
        //Get the default charset from the config file.
        $this->_charset = $this->config->item('charset');

        //Set the default mode to use a template view
        $this->set_output_mode(self::OUTPUT_TEMPLATE);

        //Passing as properties the controller and the method that should be called.
        $this->controller = $this->uri->rsegment(1);
        $this->method      = $this->uri->rsegment(2);
        
		date_default_timezone_set('Asia/Singapore');
    }

    /**
     * CodeIgniter magic method that controls the output of a controller.
     * You can't call it directly.
     *
     * @see http://codeigniter.com/user_guide/general/controllers.html#output
     * @final this method cannot be overloaded
     * @param string $output the controller output
     * @return void
     */
    public function _output($output)
	{
        switch($this->_mode){
            //Use the template
            case self::OUTPUT_TEMPLATE:
                $data = array(    'meta'=>$this->_media['meta'],
                                'rdf'=>$this->_media['rdf'],
                                'javascript'=>$this->_media['javascript'],
                                'css'=>$this->_media['css'],
								'fav_icon'=>$this->_fav_icon,
                                'title'=>$this->_title,
                                'charset'=>$this->_charset,
                                'output'=>$output,
                                'modules'=>(object)$this->_modules,
                                'inline_scripting'=>$this->_inline_scripting,
								'error_message'=>$this->get_error_message()
							);
                
                //Merge the data arrays
                $data = array_merge($data, $this->_output_data);
                
                //Load the final output
                $out = $this->load->view($this->_template_folder . $this->_template_view, $data, TRUE);
                //The End
				//$out = preg_replace('/{title}/', $this->_title, $out);
				//$out = preg_replace('/{title_long}/', $this->config->item('title_long'), $out);
                echo $out;
            break;
            //or just echo output.
            case self::OUTPUT_NORMAL:
            default:
                echo $output;
            break;
        }
    }
    
    /**
     * Pass extra data on the final output.
     *
     * @param string $paramName the parameter name.
     * @param mixed $value $the value of the parameter
     */
    
    protected function set_output_data($paramName, $value){
        $this->_output_data[$paramName] = $value;
    }

    /**
     * This method sets the output mode. That the controller should use to display the content
     *
     * @access protected
     * @param int $mode One of the constants self::OUTPUT_TEMPLATE, self::OUTPUT_NORMAL
     * @return void
     */
    protected function set_output_mode($mode){
        $this->_mode = $mode;
    }
    
    /**
     * Sets the template that will be used at the final output.
     *
     * @access protected
     * @param string $template
     * @return bool
     */
    public function set_template($template){
        $filepath = APPPATH . "views/"  . $this->_template_folder . str_replace('.php','',$template) . ".php";
                
        if(!$this->_is_file_exists($filepath)){
            show_error("Cannot locate template file <tt>$template</tt>");
            return false;
        }

        $this->_template_view = $template;
        
        return true;
    }

    /**
     * Adds a Javascript file into the template.
     *
     * @access protected
     * @param string $file a js file located inside the public/js/ folder or an url.
     * @param boolean $custom_url Default FALSE. A flag to determine if the given $file is an url or just a file inside the public/js/ folder.
     * @return bool
     */
    public function add_javascript_file($file, $custom_url=false){
        
        if($custom_url===false){
            if ( is_array($file) ) {
				foreach ( $file as $a_file) {
					$this->add_javascript_file($a_file);
				}
				return true;
			}
            $filepath = $this->_public_folder . $this->_javascript_folder . str_replace('.js','',$file) . ".js";
            if(!$this->_is_file_exists($filepath)){
                show_error('Cannot locate javascript file <tt><a href="'.$filepath.'">'.$filepath.'</a></tt>');
                return false;
            }
            
            $filepath = base_url() . $filepath;
            
        }else{
            $filepath = $file;
        }
        
        if (array_search($filepath, $this->_media['javascript']) === false)
            $this->_media['javascript'][] = $filepath;
        else
            return false;
            
        return true;
    }
    
    /**
     * Adds a CSS file into the template.
     *
     * @access protected
     * @param string $file a css file located inside the public/js/ folder or an url.
     * @param boolean $custom_url Default FALSE. A flag to determine if the given $file is an url or just a file insite the public/js/ folder.
     * @return bool
     */
    public function add_css_file($file, $custom_url=false){
        if (!$custom_url) {
            if ( is_array($file) ) {
				foreach ( $file as $a_file) {
					$this->add_css_file($a_file);
				}
				return true;
			}
            $filepath = $this->_public_folder . $this->_css_folder . str_replace('.css','',$file) . ".css";
            
            if(!$this->_is_file_exists($filepath)){
                show_error('Cannot locate css file: <tt><a href="'.$filepath.'">'.$filepath.'</a></tt>');
                return false;
            }
            
            $filepath = base_url() . $filepath;
        }
        else {
            $filepath = $file;
        }

        if(array_search($filepath, $this->_media['css']) === false)
            $this->_media['css'][] = $filepath;
        else return false;
        
        return true;
    }

    /**
     * Sets the default charset
     *
     * @access protected
     * @param string $charset
     * @return void
     */
    public function set_chartset($charset){
        $this->_charset = $charset;
    }

    /**
     * Sets the page title
     *
     * @access protected
     * @param string $new_title
     * @return void
     */
    public function set_title($new_title){
        $this->_title = $new_title;
        
    }
	

    /**
     * Appends a string at the title text
     *
     * @access protected
     * @param string $title
     * @return void
     */
    public function append_title($title){
        $this->_title .= " - $title";
    }
	
	public function set_public_folder($folder)
	{
		if ( strlen($folder) > 0 ) {
			if ( !preg_match('/\/$/', $folder ) ) {
				$folder .= '/';
			}
		}
		$this->_public_folder = $folder;
	}

    /**
     * Adds meta tags.
     *
     * @access protected
     * @param string $name the name of the meta tag
     * @param string $content the content of the mneta tag
     * @return bool
     */
    public function add_meta($name, $content){
        if(array_key_exists($name, $this->_media['meta']))
            show_error("Duplicate usage of meta tag file <tt>$name</tt>.");

        $this->_media['meta'][$name] = $content;
        return true;
    }

    /**
     * Adds RDF meta tags (3rd generation meta tags).
     *
     * @access protected
     * @param string $name the name of the meta tag
     * @param string $content the content of the mneta tag
     * @return bool
     */
    public function add_RDF($name, $content){
        if(array_key_exists($name, $this->_media['rdf']))
            show_error("Duplicate usage of meta tag file <tt>$name</tt>.");

        $this->_media['rdf'][$name] = $content;
        return true;
    }

    /**
     * Registers module positions
     *
     * @access protected
     * @param string $position_name the name of the position (no special chars or spaces are allowed)
     * @return bool
     */
    public function register_module_position($position_name){
        if(array_key_exists($position_name, $this->_modules))
            show_error("Module position failed because position <tt>$position_name</tt> has already been registered.");

        //Check for illegal characters.
        if(!preg_match("/[a-zA-Z0-9]*/", $position_name))
            show_error("Position name <tt>$position_name</tt> contains some illegal characters. Only letters or numbers are allowed.");

        $this->_modules[$position_name] = array();

        return true;
    }

    /**
     *    Loads a view file (module) in a certain position.
     *
     * @access protected
     * @param string $position the module position
     * @param string $view_file the view file path.
     * @param array $params    the parameter to be passed in the view file.
     * @return bool
     */
    public function load_module($position, $view_file, $params = array()){
        if(!array_key_exists($position, $this->_modules))
            show_error("Module position <tt>$position</tt> hasn't ever been registered.");
        
        $this->_modules[$position][] = $this->load->view($view_file, $params, TRUE);
        
        return true;
    }

    /**
     * Marks the begining of inline scripting.
     * Example:
     *     <?php ....
     *
     *             $this->_start_inline_scripting();
     *     ?>
     *     [removed] .... [removed]
     *  <?php
     *         $this->_end_inline_scripting();
     *     .....
     *!!!Note that the <scrpt*>[removed] tags will be removed!!!
     *
     */
    public function start_inline_scripting()
	{
        ob_start();
    }

    /**
     * Marks the end of the inline scripting.
     */
    public function end_inline_scripting()
	{
         $s = ob_get_clean();
         
         $s = preg_replace("/<script*>/", '', $s);
         $s = preg_replace("/<\/script>/", '', $s);
              
         $this->_inline_scripting .= $s;
    }
    

	public function add_java_script($script)
	{
		$this->_inline_scripting .= $script;
	}
    /**
     * Checks if the given file exists in the filesystem.
     *
     * @access private
     * @param string $filepath The file path, using a physical relative path
     * @return bool
     */
    private function _is_file_exists($filepath)
	{
        return file_exists($filepath);
    }
	
	public function set_error_message($text)
	{
		$this->session->set_flashdata('error_message', $text);
	}
	public function get_error_message()
	{
		return $this->session->flashdata('error_message');
	}
	
	public function set_fav_icon($value)
	{
		$this->_fav_icon = $value;
	}
	
	public function get_fav_icon()
	{
		return $this->_fav_icon;
	}

	public function get_checkid($name = '')
	{
		if ( $name == '' ) {
			$name = 'checkid';
		}
		$ids = $this->input->post($name);
		if ( $ids and is_array($ids)) {
			return reset($ids);
		}
		return 0;
	}
	public function get_checkids($name = '')
	{
		if ( $name == '' ) {
			$name = 'checkid';
		}
		$ids = $this->input->post($name);
		if ( $ids and is_array($ids) ) {
			return $ids;
		}
		return array();
	}

	public function set_autoload_language_folder($folder)
	{
		$name = strtolower(get_class($this));
		
		$this->_autoload_language_folder = $folder;
		$language_file = $name;
		if ( $folder ) {
			$language_file = $folder.'/'.$name;
		}
		if ( file_exists( APPPATH.'language/english/'.$language_file.'_lang'.EXT ) ) {	
			$this->load->language($language_file);
		}
	}

}

?>
