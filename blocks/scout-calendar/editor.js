/**
 * Scout Calendar block — editor UI (no JSX, no build step).
 *
 * Renders a placeholder in the block editor with an InspectorControls
 * panel for choosing between month grid and upcoming events list views.
 * Actual calendar output is server-side rendered via the PHP render callback.
 */
( function () {
	var registerBlockType  = wp.blocks.registerBlockType;
	var el                 = wp.element.createElement;
	var __                 = wp.i18n.__;
	var useBlockProps      = wp.blockEditor.useBlockProps;
	var InspectorControls  = wp.blockEditor.InspectorControls;
	var PanelBody          = wp.components.PanelBody;
	var SelectControl      = wp.components.SelectControl;

	registerBlockType( 'scout-starter/calendar', {

		edit: function ( props ) {
			var view       = props.attributes.view || 'dayGridMonth';
			var viewLabel  = view === 'listYear'
				? __( 'Upcoming Events List', 'scout-starter' )
				: __( 'Month Grid View', 'scout-starter' );

			var blockProps = useBlockProps( {
				style: {
					background:    '#f0f0f1',
					border:        '2px dashed #c3c4c7',
					borderRadius:  '4px',
					padding:       '32px 24px',
					textAlign:     'center',
				},
			} );

			return [
				el(
					InspectorControls,
					{ key: 'inspector' },
					el(
						PanelBody,
						{ title: __( 'Calendar Settings', 'scout-starter' ), initialOpen: true },
						el( SelectControl, {
							label:    __( 'View', 'scout-starter' ),
							value:    view,
							options:  [
								{ label: __( 'Month Grid', 'scout-starter' ),          value: 'dayGridMonth' },
								{ label: __( 'Upcoming Events List', 'scout-starter' ), value: 'listYear' },
							],
							onChange: function ( val ) {
								props.setAttributes( { view: val } );
							},
						} )
					)
				),
				el(
					'div',
					Object.assign( { key: 'preview' }, blockProps ),
					el( 'div', { style: { fontSize: '36px', marginBottom: '8px' } }, '📅' ),
					el( 'strong', { style: { display: 'block', marginBottom: '4px' } },
						__( 'Scout Calendar', 'scout-starter' )
					),
					el( 'span', { style: { color: '#757575', fontSize: '13px' } }, viewLabel )
				),
			];
		},

		save: function () {
			return null; // Dynamic block — rendered server-side via PHP callback.
		},

	} );
} )();
