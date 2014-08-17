<?php


/**
 *
 */
class NH_AdminPage_ThemeOptions extends NH_AdminPage
{

	private static $_instance = null;
	
	
	public $slug = null;
	public $tabs = array();
	public $tab = null;
	

	
	//------------------------------------------------------------------------------------
	// Constructor.
	// Setup the page's slug and tabs.
	//------------------------------------------------------------------------------------
	private function __construct( $slug )
	{
		global $uncc_config;

		$this->slug = $slug;
		
		$this->tabs = array(
			'variations' => 'Variations',
			'header' => 'Header',
		);
		$this->tabs = apply_filters( $this->slug.'-tabs', $this->tabs );
		
        $this->tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'variations' ) );		
	}
	
	
	
	//------------------------------------------------------------------------------------
	// Create or get the current instance of this page.
	//------------------------------------------------------------------------------------
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_ThemeOptions( $slug );
		}
		
		return self::$_instance;
	}



//========================================================================================
//=============================================================== Scripts and Styles =====

	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
		wp_enqueue_script( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js' );
// 		wp_enqueue_media();
	}
	
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_head_script()
	{
		?>
		<style>
		
		.nav-tab.active {
			color:#000;
			background-color:#fff;
		}
		
		.position-controller {
			display:block;
			clear:both;
			text-align:center;
			border:solid 1px #000;
			background-color:#fff;
			padding:0px 5px;
		}
		
		.position-controller > div {
			display:inline-block;
			width:20%;
			height:30px;
			border:solid 1px #ccc;
			background-color:#eee;
			margin:10px 5px;
			cursor:pointer;
		}
		
		.position-controller > div.selected {
			border:solid 1px #000;
		}
		
		.position-controller > div:hover {
			background-color:#ffc;
		}
		
		.position-controller .hleft {
			float:left;
		}

		.position-controller .hright {
			float:right;
		}
		
		.position-controller > div.selected {
			background-color:#000;
		}
		
		.top-submit {
			float:right;
		}
		
		input.no-border {
			border:none;
			outline:none;
			box-shadow:none;
			background:transparent;
		}
		
		</style>
  		<script type="text/javascript">
			jQuery(document).ready( function()
			{
				
				// 
				// Media Library Selector
				// 
				
				var custom_uploader;

				jQuery('.media-select').each( function()
				{
					var media_select = this;
					
					jQuery(media_select).find('.select-image').click( function(e) 
					{
						e.preventDefault();

						//If the uploader object has already been created, reopen the dialog
						if (custom_uploader)
						{
							custom_uploader.open();
							return;
						}

						//Extend the wp.media object
						custom_uploader = wp.media.frames.file_frame = wp.media(
						{
							title: 'Choose Image',
							button: {
								text: 'Choose Image'
							},
							multiple: false
						});

						//When a file is selected, grab the URL and set it as the text field's value
						custom_uploader.on('select', function() 
						{
							attachment = custom_uploader.state().get('selection').first().toJSON();
							jQuery(media_select).find('.image-id').val(attachment.id);
							jQuery(media_select).find('img').attr('src', attachment.url);
						});

						//Open the uploader dialog
						custom_uploader.open();
					});					
				
				
				});
				
				
				// 
				// Slider for number input fields
				// 
				
				jQuery( 'input.number' ).each( function()
				{
					var min = parseInt(jQuery(this).attr('min'));
					var max = parseInt(jQuery(this).attr('max'));
					var step = parseInt(jQuery(this).attr('step'));
					var start = parseInt(jQuery(this).attr('start'));
				
					var values = {};
					
					if( !isNaN(min) ) values['min'] = min;
					if( !isNaN(max) ) values['max'] = max;
					if( !isNaN(step) ) values['step'] = step;
					if( !isNaN(start) ) values['start'] = start;
				
					jQuery(this).spinner( values );//.attr( 'readonly', true );
				});
				
				
				// 
				// Image selector
				// 
				
				jQuery('.image-selector').each( function()
				{
					var selector_id = jQuery(this).attr('selector-id');
					
					jQuery(this).find('.selection-type').change( function()
					{
						if( this.checked )
						{
							switch( jQuery(this).val() )
							{
								case 'relative':
									jQuery('.relative-path').filter('[selector-id='+selector_id+']').parent().parent().show();
									jQuery('.media-select').filter('[selector-id='+selector_id+']').parent().parent().hide();
									break;
								case 'media':
									jQuery('.relative-path').filter('[selector-id='+selector_id+']').parent().parent().hide();
									jQuery('.media-select').filter('[selector-id='+selector_id+']').parent().parent().show();
									break;
							}
						}
					}).change();
				});
				
				
				// 
				// Header title box position
				// 

				jQuery('.position').each( function()
				{
					var self = this;
					
					jQuery(self).find('input').attr('readonly', true).addClass('no-border');
					var position = jQuery(self).find('input').val();
					var container = jQuery('<div class="position-controller"></div>');
					
					var v = [ 'vtop', 'vcenter', 'vbottom' ];
					var h = [ 'hleft', 'hcenter', 'hright' ];
					
					for( var r = 0; r < 3; r++ )
					{
						for( var c = 0; c < 3; c++ )
						{
							var pos = h[c]+' '+v[r];
							var cls = pos;
							if( position == pos ) cls += ' selected';
							jQuery(container).append('<div position="'+pos+'" class="'+cls+'"></div>');
						}
						jQuery(container).append('<br/>');
					}
					
					jQuery(container).find('div').click( function()
					{
						jQuery(container).find('div').removeClass('selected');
						jQuery(this).addClass('selected');
						jQuery(self).find('input').val( jQuery(this).attr('position') );
					});

					jQuery(self).append(container);
				});
				
				
				// 
				// Checkbox controls hiding / showing div area.
				// Used for "Use site link" options.
				// 
				
				jQuery('input[type=checkbox][controls]').change( function()
				{
					var controls_div = '.'+this.attributes.controls.value;
					if( this.checked )
						jQuery(controls_div).hide();
					else
						jQuery(controls_div).show();
				}).change();
				
				
				// 
				// Site layout
				// 
				
				jQuery('input[site-layout]').each( function()
				{
					var self = this;
					var part = jQuery(this).attr('site-layout');
					var id = jQuery(this).attr('id');
					
					var add_highlight = function()
					{
						jQuery('.site-layout .'+part).addClass('highlight');
					}
					var remove_highlight = function()
					{
						jQuery('.site-layout .'+part).removeClass('highlight');
					}
					var show = function()
					{
						jQuery('.site-layout .'+part).removeClass('hide');
						jQuery('.site-layout .'+part).addClass('show');
					}
					var hide = function()
					{
						jQuery('.site-layout .'+part).addClass('hide');
						jQuery('.site-layout .'+part).removeClass('show');
					}
					
					jQuery(this)
						.change( 
							function() { if( this.checked ) show(); else hide(); })
						.change();
					
					jQuery(this).mouseenter( add_highlight );
					jQuery('label[for="'+id+'"]').mouseenter( add_highlight );

					jQuery(this).mouseleave( remove_highlight );
					jQuery('label[for="'+id+'"]').mouseleave( remove_highlight );
				});

			});
		</script>
		<?php
	}



//========================================================================================
//========================================================================= Settings =====


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function register_settings()
	{
		add_filter( $this->slug.'-process-input', array($this, 'process_input'), 99, 5 );
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_settings_sections()
	{

		//
		// Variations
		//
		
		add_settings_section(
			'variations', 'Variations', array( $this, 'print_variations_section' ),
			$this->slug.':variations'
		);

		//
		// Header
		//
		
		add_settings_section(
			'header', 'Header', array( $this, 'print_header_section' ), 
			$this->slug.':header'
		);

		add_settings_section(
			'header-image', 'Image', array( $this, 'print_header_image' ), 
			$this->slug.':header:image'
		);
		
		add_settings_section(
			'header-title-box', 'Title Box', array( $this, 'print_header_title_box' ), 
			$this->slug.':header:title-box'
		);
	
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_settings_fields()
	{
		global $uncc_config;
		
		//
		// Variations
		//
		
		add_settings_field( 
			'variation', 'Current Variation', array( $this, 'print_variation_list' ),
			$this->slug.':variations', 'variations', array(  )
		);

		//
		// Header - Image
		//

		add_settings_field(
			'image-title-image-link', 'Use As Home Link', array( $this, 'print_header_image_link' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);

		//
		// Header - Title Box
		//

		add_settings_field(
			'position', 'Position', array( $this, 'print_header_title_position' ),
			$this->slug.':header:title-box', 'header-title-box', array( 'header', 'title-box' )
		);
		add_settings_field(
			'title-text', 'Title Text', array( $this, 'print_header_title_text' ),
			$this->slug.':header:title-box', 'header-title-box', array( 'header', 'title-box' )
		);
		add_settings_field(
			'title-link', 'Title Link', array( $this, 'print_header_title_link' ),
			$this->slug.':header:title-box', 'header-title-box', array( 'header', 'title-box' )
		);
		add_settings_field(
			'description-text', 'Description Text', array( $this, 'print_header_description_text' ),
			$this->slug.':header:title-box', 'header-title-box', array( 'header', 'title-box' )
		);
		add_settings_field(
			'description-link', 'Description Link', array( $this, 'print_header_description_link' ),
			$this->slug.':header:title-box', 'header-title-box', array( 'header', 'title-box' )
		);
		
	}
	

//========================================================================================
//============================================================================= Save =====

	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function process_input( $options, $page, $tab, $option, $input )
	{
		if( $option !== 'uncc-options' ) return $options;
		
		global $uncc_config;
		
		if( !array_key_exists($tab, $input) ) return $options;
		$tab_input = $input[$tab];
		
		$tab_input = array_map( 'uncc_string_to_value', $tab_input );
// 		uncc_print($tab_input);
	
		switch( $tab )
		{
			case 'variations':
				// [variation]
				if( isset($tab_input['variation']) ):
					$variations = $uncc_config->get_variations();
					$chosen_variation = $tab_input['variation'];
					if( (!array_key_exists($chosen_variation, $variations)) && ($chosen_variation !== 'default') )
					{
						add_settings_error( '', '', 'Invalid variation: '.$chosen_variation );
						return $options;
					}
					$uncc_config->set_variation( $chosen_variation );
				endif;
				
				// [reset-options]
				if( isset($tab_input['reset-options']) ):
					
					add_settings_error( '', '', 'Settings saved.', 'updated' );
					$new_options = array();
					add_settings_error( '', '', 'Reset options for variation: '.$chosen_variation, 'updated' );
					return $new_options;
					
				endif;
				
				break;
			
			case 'header':
				// [image-link]
				// nothing to do

				// [title-position]
				// nothing to do

				// [title][use-blog-info]
				// [title][text]
				// [title][use-site-link]
				// [title][link]
				// nothing to do

				// [description][use-blog-info]
				// [description][text]
				// [description][use-site-link]
				// [description][link]
				// nothing to do

				break;
		}

		return array_merge( $options, array($tab => $tab_input) );
	}
	


//========================================================================================
//========================================================================== Display =====


	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function show()
	{
		global $uncc_admin_pages, $wp_settings_sections;
		?>
		
		<div class="wrap tab-<?php echo $this->tab; ?>">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $uncc_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $this->tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$this->tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<form method="post" action="options.php">
				<div class="top-submit"><?php submit_button(); ?></div>
				<div style="clear:both"></div>
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" />
				
				<?php
				do_settings_sections( $this->slug.':'.$this->tab );
				
				$tab_section = $this->slug.':'.$this->tab.':';
				foreach( array_keys($wp_settings_sections) as $section_name )
				{
					if( substr($section_name, 0, strlen($tab_section)) === $tab_section )
					{
						do_settings_sections( $section_name );
					}
				}
				?>
				
				<div style="clear:both"></div>
				<div class="bottom-submit"><?php submit_button(); ?></div>
			</form>
		 
		</div><!-- /.wrap -->
		
		<?php
	}
	


//========================================================================================
//========================================================= Display Setting Sections =====

	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_variations_section()
	{
		echo '<p>print_variations_section</p>';
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_section()
	{
		echo '<p>print_header_section</p>';
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------	
	public function print_header_image()
	{
		echo 'print_header_image';
		do_settings_sections( 'header-image' );
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_box()
	{
		echo 'print_header_title';
		do_settings_sections( 'header-title-box' );
	}


//========================================================================================
//================================================================== Settings Fields =====



	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_variation_list( $args )
	{
		global $uncc_config;
		
		$current_variation = $uncc_config->get_current_variation();
		$variations = $uncc_config->get_variations();
		?>
		
		<select name="<?php uncc_input_name_e( $this->tab, 'variation' ); ?>">
		
		<?php foreach( $variations as $key => $name ): ?>
			<option value="<?php echo $key; ?>" 
			        <?php selected( $key, $current_variation); ?>>
				<?php echo $name; ?>
			</option>
		<?php endforeach; ?>
		
		</select>
		
		<div>
		<input type="checkbox" 
		       name="<?php uncc_input_name_e( $this->tab, 'reset-options' ); ?>" 
		       value="reset-options" />
		Reset options?
		</div>

		<?php		
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_image_link( $args )
	{
		global $uncc_config;
		$image_link = $uncc_config->get_value( 'header', 'image-link' );
		?>
		
	    <div class="image-link">
	    	<input type="hidden"
	    	       name="<?php uncc_input_name_e( $this->tab, 'image-link' ); ?>"
	    	       value="b:false" />
			<input type="checkbox"
			       name="<?php uncc_input_name_e( $this->tab, 'image-link' ); ?>"
			       value="b:true"
			       <?php checked( true, $image_link ); ?> />
		</div>
				
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_position( $args )
	{
		global $uncc_config;
		$title_position = $uncc_config->get_value( 'header', 'title-position' );
		?>
		
	    <div class="position">
			<input type="text"
			       name="<?php uncc_input_name_e( $this->tab, 'title-position' ); ?>"
			       value="<?php echo $title_position; ?>" />
		</div>
				
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_text( $args )
	{
		global $uncc_config;
		$title = $uncc_config->get_text_data( 'header','title' );
		?>

		<input type="hidden" 
			   name="<?php uncc_input_name_e( $this->tab, 'title', 'use-blog-info' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php uncc_input_name_e( $this->tab, 'title', 'use-blog-info' ); ?>" 
			   name="<?php uncc_input_name_e( $this->tab, 'title', 'use-blog-info' ); ?>" 
			   class="use-blog-info" 
			   value="b:true" 
			   <?php checked( true, $title['use-blog-info'] ); ?>
			   controls="header-title-text" />
		<label for="<?php uncc_input_name_e( $this->tab, 'title', 'use-blog-info' ); ?>">use site title</label>

		<div class="header-title-text">
			<input type="text" 
				   id="title-text" 
				   name="<?php uncc_input_name_e( $this->tab, 'title', 'text' ); ?>" 
				   value="<?php echo $title['text']; ?>" />		
		</div>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_link( $args )
	{
		global $uncc_config;
		$title = $uncc_config->get_text_data( 'header','title' );
		?>

		<input type="hidden" 
			   name="<?php uncc_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php uncc_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   name="<?php uncc_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $title['use-site-link'] ); ?>
			   controls="header-title-link" />
		<label for="<?php uncc_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>">use site URL</label>

		<div class="header-title-link">
			<input type="text"
				   name="<?php uncc_input_name_e( $this->tab, 'title', 'link' ); ?>" 
				   value="<?php echo $title['link']; ?>" />
		</div>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_description_text( $args )
	{
		global $uncc_config;
		$description = $uncc_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php uncc_input_name_e( $this->tab, 'description', 'use-blog-info' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php uncc_input_name_e( $this->tab, 'description', 'use-blog-info' ); ?>" 
			   name="<?php uncc_input_name_e( $this->tab, 'description', 'use-blog-info' ); ?>" 
			   class="use-blog-info" 
			   value="b:true" 
			   <?php checked( true, $description['use-blog-info'] ); ?>
			   controls="header-description-text" />
		<label for="<?php uncc_input_name_e( $this->tab, 'description', 'use-blog-info' ); ?>">use site description</label>

		<div class="header-description-text">
			<input type="text" 
				   id="description-text" 
				   name="<?php uncc_input_name_e( $this->tab, 'description', 'text' ); ?>" 
				   value="<?php echo $description['text']; ?>" />		
		</div>

		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_description_link( $args )
	{
		global $uncc_config;
		$title = $uncc_config->get_text_data( 'header','title' );
		$description = $uncc_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php uncc_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php uncc_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   name="<?php uncc_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $description['use-site-link'] ); ?>
			   controls="header-description-link" />
		<label for="<?php uncc_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>">use site URL</label>

		<div class="header-description-link">
			<input type="text"
				   name="<?php uncc_input_name_e( $this->tab, 'description', 'link' ); ?>" 
				   value="<?php echo $description['link']; ?>" />
		</div>
				
		<?php
	}
	
}


