<?php
/**
 * VTT_ThemeOptionsAdminPageHeaderTabAdminPage
 * 
 * This class controls the admin page "Theme Options > Header".
 * 
 * @package    variations-template-theme
 * @subpackage admin-pages/pages
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('VTT_ThemeOptionsAdminPageHeaderTabAdminPage') ):
class VTT_ThemeOptionsAdminPageHeaderTabAdminPage extends APL_TabAdminPage
{
	
	private $model = null;	
	private $list_table = null;
	
	private $filter_types;
	private $filter;
	private $search;
	private $orderby;
	
	
	/**
	 * Creates an VTT_ThemeOptionsAdminPageHeaderTabAdminPage object.
	 */
	public function __construct( 
		$parent,
		$name = 'header', 
		$tab_title = 'Header', 
		$page_title = 'Header' )
	{
		parent::__construct( $parent, $name, $tab_title, $page_title );
	}
	
	
	/**
	 * Register each individual settings for the Settings API.
	 */
	public function register_settings()
	{
		$this->register_setting( VARIATIONS_TEMPLATE_THEME_OPTIONS );
	}
	
	public function add_head_script()
	{
		?>
		<style>
		
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
	

	/**
	 * Add the sections used for the Settings API. 
	 */
	public function add_settings_sections()
	{
		$this->add_section(
			'vtt-theme-header',
			'Header',
			'print_section_header'
		);

		$this->add_section(
			'vtt-theme-header-image',
			'Image',
			'print_section_header_image'
		);

		$this->add_section(
			'vtt-theme-header-title-box',
			'Title Box',
			'print_section_header_title_box'
		);
	}
	
	
	/**
	 * Add the settings used for the Settings API. 
	 */
	public function add_settings_fields()
	{
		$this->add_field(
			'vtt-theme-header-image',
			'title-image-link',
			'Use as Home Link',
			'print_field_image_title_image_link'
		);

		$this->add_field(
			'vtt-theme-header-title-box',
			'position',
			'Position',
			'print_field_position'
		);

		$this->add_field(
			'vtt-theme-header-title-box',
			'title-text',
			'Title Text',
			'print_field_title_text'
		);

		$this->add_field(
			'vtt-theme-header-title-box',
			'title-link',
			'Title Link',
			'print_field_title_link'
		);

		$this->add_field(
			'vtt-theme-header-title-box',
			'description-text',
			'Description Text',
			'print_field_description_text'
		);

		$this->add_field(
			'vtt-theme-header-title-box',
			'description-link',
			'Description Link',
			'print_field_description_link'
		);
	}
	
	
	/**
	 * Processes the current admin page.
	 */
	public function process()
	{
		if( empty($_REQUEST['action']) ) return;
		
		switch( $_REQUEST['action'] )
		{
// 			case 'refresh':
// 				break;
		}
	}
	
	
	public function print_section_header()
	{
		apl_print( 'print_section_header' );
	}
	
	
	public function print_section_header_image()
	{
		apl_print( 'print_section_header_image' );
	}
	
	
	public function print_section_header_title_box()
	{
		apl_print( 'print_section_header_title_box' );
	}
	
	
	public function print_field_image_title_image_link( $args )
	{
		global $vtt_config;
		$image_link = $vtt_config->get_value( 'header', 'image-link' );
		?>
		
	    <div class="image-link">
	    	<input type="hidden"
	    	       name="<?php vtt_name_e( 'header', 'image-link' ); ?>"
	    	       value="b:false" />
			<input type="checkbox"
			       name="<?php vtt_name_e( 'header', 'image-link' ); ?>"
			       value="b:true"
			       <?php checked( true, $image_link ); ?> />
		</div>
				
		<?php
	}
	
	
	public function print_field_position( $args )
	{
		global $vtt_config;
		$title_position = $vtt_config->get_value( 'header', 'title-position' );
		?>
		
	    <div class="position">
			<input type="text"
			       name="<?php vtt_name_e( 'header', 'title-position' ); ?>"
			       value="<?php echo $title_position; ?>" />
		</div>
				
		<?php
	}
	
	
	public function print_field_title_text( $args )
	{
		global $vtt_config;
		$title = $vtt_config->get_text_data( 'header','title' );
		?>

		<input type="hidden" 
			   name="<?php vtt_name_e( 'header', 'title', 'use-blog-info' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php vtt_name_e( 'header', 'title', 'use-blog-info' ); ?>" 
			   name="<?php vtt_name_e( 'header', 'title', 'use-blog-info' ); ?>" 
			   class="use-blog-info" 
			   value="b:true" 
			   <?php checked( true, $title['use-blog-info'] ); ?>
			   controls="header-title-text" />
		<label for="<?php vtt_name_e( 'header', 'title', 'use-blog-info' ); ?>">use site title</label>

		<div class="header-title-text">
			<input type="text" 
				   id="title-text" 
				   name="<?php vtt_name_e( 'header', 'title', 'text' ); ?>" 
				   value="<?php echo $title['text']; ?>" />		
		</div>
		
		<?php
	}
	
	
	public function print_field_title_link( $args )
	{
		global $vtt_config;
		$title = $vtt_config->get_text_data( 'header','title' );
		?>

		<input type="hidden" 
			   name="<?php vtt_name_e( 'header', 'title', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php vtt_name_e( 'header', 'title', 'use-site-link' ); ?>" 
			   name="<?php vtt_name_e( 'header', 'title', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $title['use-site-link'] ); ?>
			   controls="header-title-link" />
		<label for="<?php vtt_name_e( 'header', 'title', 'use-site-link' ); ?>">use site URL</label>

		<div class="header-title-link">
			<input type="text"
				   name="<?php vtt_name_e( 'header', 'title', 'link' ); ?>" 
				   value="<?php echo $title['link']; ?>" />
		</div>
		
		<?php
	}
	
	
	public function print_field_description_text( $args )
	{
		global $vtt_config;
		$description = $vtt_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php vtt_name_e( 'header', 'description', 'use-blog-info' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php vtt_name_e( 'header', 'description', 'use-blog-info' ); ?>" 
			   name="<?php vtt_name_e( 'header', 'description', 'use-blog-info' ); ?>" 
			   class="use-blog-info" 
			   value="b:true" 
			   <?php checked( true, $description['use-blog-info'] ); ?>
			   controls="header-description-text" />
		<label for="<?php vtt_name_e( 'header', 'description', 'use-blog-info' ); ?>">use site description</label>

		<div class="header-description-text">
			<input type="text" 
				   id="description-text" 
				   name="<?php vtt_name_e( 'header', 'description', 'text' ); ?>" 
				   value="<?php echo $description['text']; ?>" />		
		</div>

		<?php
	}
	
	
	public function print_field_description_link( $args )
	{
		global $vtt_config;
		$title = $vtt_config->get_text_data( 'header','title' );
		$description = $vtt_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php vtt_name_e( 'header', 'description', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php vtt_name_e( 'header', 'description', 'use-site-link' ); ?>" 
			   name="<?php vtt_name_e( 'header', 'description', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $description['use-site-link'] ); ?>
			   controls="header-description-link" />
		<label for="<?php vtt_name_e( 'header', 'description', 'use-site-link' ); ?>">use site URL</label>

		<div class="header-description-link">
			<input type="text"
				   name="<?php vtt_name_e( 'header', 'description', 'link' ); ?>" 
				   value="<?php echo $description['link']; ?>" />
		</div>
				
		<?php
	}
		
	
	/**
	 * Displays the current admin page.
	 */
	public function display()
	{
		$this->print_settings();
	}
	
} // class VTT_ThemeOptionsAdminPageHeaderTabAdminPage extends APL_TabAdminPage
endif; // if( !class_exists('VTT_ThemeOptionsAdminPageHeaderTabAdminPage') )

