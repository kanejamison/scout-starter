/**
 * Customizer controls: color preset selector.
 *
 * When a preset is chosen, pushes values into the individual color controls.
 * Users can still adjust any color manually after selecting a preset.
 */
( function ( wp ) {
	var presets = {
		cub_pack: {
			scout_color_primary:   '#003F87',
			scout_color_accent:    '#FFCC00',
			scout_color_nav_bg:    '#003F87',
			scout_color_hero_bg:   '#003F87',
			scout_color_footer_bg: '#003F87',
		},
		troop: {
			scout_color_primary:   '#003F87',
			scout_color_accent:    '#CE1126',
			scout_color_nav_bg:    '#003F87',
			scout_color_hero_bg:   '#003F87',
			scout_color_footer_bg: '#003F87',
		},
		venturing: {
			scout_color_primary:   '#006338',
			scout_color_accent:    '#FFCC00',
			scout_color_nav_bg:    '#006338',
			scout_color_hero_bg:   '#006338',
			scout_color_footer_bg: '#006338',
		},
		sea_scouts: {
			scout_color_primary:   '#003F87',
			scout_color_accent:    '#FFFFFF',
			scout_color_nav_bg:    '#003F87',
			scout_color_hero_bg:   '#003F87',
			scout_color_footer_bg: '#003F87',
		},
	};

	wp.customize( 'scout_color_preset', function ( value ) {
		value.bind( function ( preset ) {
			if ( ! presets[ preset ] ) {
				return;
			}
			Object.keys( presets[ preset ] ).forEach( function ( key ) {
				wp.customize( key, function ( setting ) {
					setting.set( presets[ preset ][ key ] );
				} );
			} );
		} );
	} );
} )( wp );
