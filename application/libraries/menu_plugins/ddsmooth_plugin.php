<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// ddsmooth menu plugin

class ddsmooth_plugin extends menu_plugin
{


	public function get_default_config()
	{
		$result = array(
					'image_folder'=> BASE_URL().'images/admin/ddsmooth_menu',
					'icon_folder'=>BASE_URL().'images/admin/menu_icons',
					'show_icons'=>false,
					'is_vertical_view'=>false,
					'image_class'=>'',
					
						);
		return $result;		
	}
	public function setup()
	{
		// setup using the jquery_helper
		$this->CI->load->helper('jquery');
		jquery_setup();
		if ( $this->_config['is_vertical_view'] ) {
			$this->CI->add_css_file("ddsmoothmenu/ddsmoothmenu-v.css");
		}
		else {
			$this->CI->add_css_file("ddsmoothmenu/ddsmoothmenu.css");
		}

		$this->CI->add_javascript_file("ddsmoothmenu/ddsmoothmenu.js");

		$script = <<<__EOM
<script type="text/javascript">

ddsmoothmenu.init({
	mainmenuid: "smoothmenu", //menu DIV id
	orientation: '{ORIENTATION}', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'ddsmoothmenu', //class added to menu's outer DIV
//	customtheme: ["#805a4a", "#18374a"],
	contentsource: "markup", // "markup" or ["container_id", "path_to_menu_file"]
	arrowimages: {down:['downarrowclass', '{IMAGE_FOLDER}/down.gif', 23], 
					right:['rightarrowclass', '{IMAGE_FOLDER}/right.gif']
				}

})


</script>
__EOM;

		$orientation = 'h';
		if ( $this->_config['is_vertical_view'] )  {
			$orientation = 'v';
		}
		$script = preg_replace('/{ORIENTATION}/', $orientation, $script);

		if ( isset($this->_config['image_folder']) ) {
			$script = preg_replace('/{IMAGE_FOLDER}/', $this->_config['image_folder'], $script);
		}
		
		$this->CI->add_java_script($script);

	}
	
	public function show($items)
	{
		$result = '<div id="smoothmenu" class="ddsmoothmenu">';
		$result .= $this->_show_menu_items($items);
		$result .= '</div>';
		return $result;
	}
	
	protected function _show_menu_items($items)
	{
		$result = '';
		if ( $items and count($items) > 0 ) {
			$result = "<ul>";
			foreach ( $items as $item ) {
				if ( $item->link ) {
					$title  = "<a href='".$item->link."'>".$item->title;
					if ( $item->image_name and $this->_config['show_icons'] ) {
						$title .= '<img src="'.$this->_config['icon_folder'].'/'.$item->image_name.'"';
						if ( $this->_config['icon_class'] ) {
							$title .= ' class="'.$this->_config['icon_class'].'"';
						}
						$title .= '>';
					}
					$title .= "</a>";
				}
				else {
					$title = $item->title;
				}
				
				$result .= "<li>$title";
				if ( $item->child_items ) {
					$result .= "\n\t".$this->_show_menu_items($item->child_items)."\n";
				}
				$result .= "</li>";
			}
			$result .= "</ul>";
		}
		return $result;
	}

}




?>
