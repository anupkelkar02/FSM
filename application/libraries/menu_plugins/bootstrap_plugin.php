<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// ddsmooth menu plugin

class bootstrap_plugin extends menu_plugin
{


	public function get_default_config()
	{
		$result = array(
					'is_vertical_view'=>false,
					
						);
		return $result;		
	}
	public function setup()
	{


	}
	
	public function show($items)
	{
		$result = '<ul class="nav navbar-nav">';
		
		$result .= $this->_show_menu_items($items);
		$result .= '</ul>';
		return $result;
	}
	
	protected function _show_menu_items($items)
	{
		$result = '';
		if ( $items and count($items) > 0 ) {			
			foreach ( $items as $item ) {
				$dropdown_class = '';
				$dropdown_caret = '';
				if ( $item->child_items ) {
					$result .= '<li class="dropdown">';
					$dropdown_class='class="dropdown-toggle" data-toggle="dropdown"';
					$dropdown_caret=' <b class="caret"></b>';
				}
				elseif ( $item->title == '-' ) {
					$result .= '<li class="divider">';
				}
				else {
					$result .= '<li>';
				}
				if ( $item->link ) {
					$title  = "<a $dropdown_class href='".$item->link."'>".$item->title.$dropdown_caret;
					$title .= "</a>";
				}
				else {
					$title = $item->title;
				}
				
				$result .= $title;
				if ( $item->child_items ) {
					$result .= '<ul class="dropdown-menu" >';
					$result .= "\n\t".$this->_show_menu_items($item->child_items)."\n";
					$result .= '</ul>';
				}
				$result .= "</li>\n";
			}
		}
		return $result;
	}

}




?>
