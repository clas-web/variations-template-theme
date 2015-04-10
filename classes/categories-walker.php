<?php

/**
 * Create HTML list of categories.
 *
 * @package unc-charlotte-theme
 * @since 
 * @uses Walker
 */
class VTT_Categories_Walker extends Walker 
{
	/**
	 * What the class handles.
	 *
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'category';

	/**
	 * Database fields to use.
	 *
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. Will only append content if style argument value is 'list'.
	 *                       @see wp_list_categories()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() )
	{
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. Will only append content if style argument value is 'list'.
	 *                       @wsee wp_list_categories()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() )
	{
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.1.0
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Depth of category in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_list_categories()
	 * @param int    $id       ID of the current category.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		extract($args);
		
		if( ($flat_list) && ($category->parent) ) return;
		
		$cat_name = esc_attr( $category->name );

		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );

		$link = '<a href="' . esc_url( get_term_link($category) ) . '" ';
		if ( $use_desc_for_title == 0 || empty($category->description) )
		{
			$link .= 'title="' . esc_attr( sprintf(__( 'View all posts filed under %s' ), $cat_name) ) . '"';
		}
		else
		{
			/**
			 * Filter the category description for display.
			 *
			 * @since 1.2.0
			 *
			 * @param string $description Category description.
			 * @param object $category    Category object.
			 */
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		}

		$link .= '>';
		$link .= $cat_name;
		
		if ( !empty($show_count) )
			$link .= '<span class="cat-count"> (' . number_format_i18n( $category->count ) . ')</span>';
		
		$link .= '</a>';

		
		
		if ( !empty($feed_image) || !empty($feed) ) {
			$link .= ' ';

			if ( empty($feed_image) )
				$link .= '(';

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) ) . '"';

			if ( empty($feed) ) {
				$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
			} else {
				$title = ' title="' . $feed . '"';
				$alt = ' alt="' . $feed . '"';
				$name = $feed;
				$link .= $title;
			}

			$link .= '>';

			if ( empty($feed_image) )
				$link .= $name;
			else
				$link .= "<img src='$feed_image'$alt$title" . ' />';

			$link .= '</a>';

			if ( empty($feed_image) )
				$link .= ')';
		}



		if ( 'list' == $args['style'] ) 
		{
			$output .= "\t<li";
			
			$class = 'cat-item cat-item-' . $category->term_id;
			
			if ( !empty($current_category) ) 
			{
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
				{
					$class .=  ' current-cat';
				}
				else
				{
					if( !$flat_list )
					{
						$cc = $_current_category;
						while( $cc->parent )
						{
							if( $category->term_id == $cc->parent )
							{
								$class .= ' current-cat-ancestor';
							}
						
							$cc = get_term( $cc->parent, $category->taxonomy );
						}

						if ( $category->term_id == $_current_category->parent )
							$class .=  ' current-cat-parent';
					}
				}
			}
			
			if( !$flat_list )
			{
				if( $has_children )
					$class .= ' has-children';
			}
			else
			{
				$class .= ' flat-list';
			}

			$output .=  ' class="' . $class . '"';
			$output .= ">$link\n";
		} 
		else 
		{
			$output .= "\t$link<br />\n";
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page   Not used.
	 * @param int    $depth  Depth of category. Not used.
	 * @param array  $args   An array of arguments. Only uses 'list' for whether should append to output. @see wp_list_categories()
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() )
	{
		if ( 'list' != $args['style'] )
			return;

		$output .= "</li>\n";
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( isset( $args[0] ) && is_array( $args[0] ) )
		{
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
			$args[0]['max_depth'] = $max_depth;
			$args[0]['flat_list'] = ($max_depth > 0 && $max_depth <= $depth+1);
		}
		$cb_args = array_merge( array(&$output, $element, $depth), $args);	
		call_user_func_array(array($this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array($this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'end_el'), $cb_args);
	}

}

