/**
 * jQuery script for the Theme Customizer color with alpha control.
 *
 * @package    variations-template-theme
 * @author     Pluto <steven@plutomedia.co.nz>
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @link       http://pluto.kiwi.nz/2014/07/how-to-add-a-color-control-with-alphaopacity-to-the-wordpress-theme-customizer/
 * @version    1.1
 */
jQuery(document).ready( function($)
{
	/**
	 * Override the default toString function of Color, which gets the CSS value of the color.
	 * @param  bool  remove_alpha  True if alpha value should be removed, otherwise False.
	 * @return  string  The modified CSS color.
	 */
	Color.prototype.toString = function(remove_alpha) {
		
		if( remove_alpha == 'no-alpha' )
		{
			return this.toCSS('rgba', '1').replace(/\s+/g, '');
		}
		
		if( this._alpha < 1 )
		{
			return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
		}
		
		var hex = parseInt(this._color, 10).toString(16);
		
		if (this.error) return '';
		
		if( hex.length < 6 )
		{
			for( var i = 6 - hex.length - 1; i >= 0; i-- )
			{
				hex = '0' + hex;
			}
		}
		
		return '#' + hex;
	};
	
	
	/**
	 * Apply jQuery plugin to each pluto-color-control.
	 */
	$('.pluto-color-control').each( function() {
		
		var $control = $(this),
			value = $control.val().replace(/\s+/g, '');
		

		// Manage Palettes
		var palette_input = $control.attr('data-palette');
		
		var palette = null;
		switch( palette_input )
		{
			case 'false': case false: palette = false; break;
			case 'true': case true: palette = true; break;
			default: palette = $control.attr('data-palette').split(","); break;
		}
		

		// Modify the existing wpColorPicker.
		$control.wpColorPicker({ 

			clear: function(event, ui) {
				// TODO reset Alpha Slider to 100

				// Added by Crystal Barton.
				// Fixes the theme customizer not detecting a change when Clear is clicked.
				var key = $control.attr('data-customize-setting-link');
				wp.customize(key, function(obj) {
					obj.set('');
				});
			},
			change: function(event, ui) {
			
				// send ajax request to wp.customizer to enable Save & Publish button
				// var _new_value = $control.val();
				var key = $control.attr('data-customize-setting-link');

				// Added by Crystal Barton.
				// Fixes the color preview being a selection behind.
				var blue  = ui.color._color & 255;
				var green = (ui.color._color >> 8) & 255;
				var red   = (ui.color._color >> 16) & 255;
				var _new_value = 'rgba('+red+','+green+','+blue+','+ui.color._alpha+')';
			
				wp.customize(key, function(obj) {
					obj.set(_new_value);
				});
			
				// change the background color of our transparency container whenever a color is updated
				var $transparency = $control.parents('.wp-picker-container:first').find('.transparency');
			
				// we only want to show the color at 100% alpha
				$transparency.css('backgroundColor', ui.color.toString('no-alpha'));
			},
			
			// Remove the color palettes
			palettes: palette
		});
		

		// Create alpha-slider container.
		$('<div class="pluto-alpha-container"><div class="slider-alpha"></div><div class="transparency"></div></div>').appendTo($control.parents('.wp-picker-container'));
	
		var $alpha_slider = $control.parents('.wp-picker-container:first').find('.slider-alpha');
		
		// Determine the color alpha value.
		// if in format RGBA - grab A channel value
		var alpha_val = null;
		if( value.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/) )
		{
			alpha_val = parseFloat(value.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)[1]) * 100;
			alpha_val = parseInt(alpha_val);
		}
		else
		{
			alpha_val = 100;
		}
		
		$('.iris-palette').css({'height':'20px','width':'20px', 'margin-left':'','margin-right':'3px','margin-top':'3px'});
		$('.iris-strip').css('height','185px');
		paletteCount = $('.iris-palette').length
		paletteRowCount = Math.ceil(paletteCount / 8);
		$('.iris-picker').css({'height': 150 + (paletteRowCount * 23)+'px', 'padding-bottom':'15px'});
	
		// Alpha Slider
		$alpha_slider.slider({
			slide: function(event, ui) {
			
				// show value on slider handle
				$(this).find('.ui-slider-handle').text(ui.value);

				// send ajax request to wp.customizer to enable Save & Publish button
				var _new_value = $control.val();
				var key = $control.attr('data-customize-setting-link');
			
				wp.customize(key, function(obj) {
					obj.set(_new_value);
				});
			},
			create: function(event, ui) {
				var v = $(this).slider('value');
				$(this).find('.ui-slider-handle').text(v);
			},
			value: alpha_val,
			range: "max",
			step: 1,
			min: 1,
			max: 100
		});
		
		$alpha_slider.slider().on('slidechange', function(event, ui) {
		
			var new_alpha_val = parseFloat(ui.value),
				iris = $control.data('a8cIris'),
				color_picker = $control.data('wpWpColorPicker');
			iris._color._alpha = new_alpha_val / 100.0;
			$control.val(iris._color.toString());
			color_picker.toggler.css({
				backgroundColor: $control.val()
			});
		
			// fix relationship between alpha slider and the 'side slider not updating.
			var get_val = $control.val();
			$($control).wpColorPicker('color', get_val);
		});
	}); // $('.pluto-color-control').each( function() {
 
});

