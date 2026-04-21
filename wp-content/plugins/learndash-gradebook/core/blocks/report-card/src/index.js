import { registerBlockType } from '@wordpress/blocks';
import { __, _x } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Disabled, PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';
import metadata from '../block.json';

import { GradebookDropdown } from '../../../../src/assets/js/admin/gutenberg/gradebook-dropdown';
import { UserDropdown } from '../../../../src/assets/js/admin/gutenberg/user-dropdown';

registerBlockType( metadata, {
	title: __( 'Report Card', 'learndash-gradebook' ),
	description: __( 'Display a student report card for one or more Gradebooks.', 'learndash-gradebook' ),
	edit: ( props ) => {

		const blockProps = useBlockProps();

		const {
			attributes: { gradebook, user },
			setAttributes,
		} = props;

		return (
			<div { ...blockProps }>
				<InspectorControls key="setting">
					<PanelBody>

						<GradebookDropdown  
							label={ _x( 'Gradebook', 'Gutenberg Block Gradebook dropdown label', 'learndash-gradebook' ) }
							value={ gradebook }
							description={ _x( 'Leave empty to show a Report Card for each Gradebook the User is in', 'Report Card Gutenberg Block Gradebook field description', 'learndash-gradebook' ) }
							placeholder={ _x( 'Type to search for a Gradebook...', 'Gutenberg Block type to search for a Gradebook', 'learndash-gradebook' ) }
							onChange={ ( option ) => {
								return setAttributes( { gradebook: ( option ) ? option.value.toString() : null } );
							} }
						/>
						<UserDropdown  
							label={ _x( 'User', 'Gutenberg Block User dropdown label', 'learndash-gradebook' ) }
							value={ user }
							description={ _x( 'Leave empty to show Report Card data for the currently logged-in User', 'Report Card Gutenberg Block User field description', 'learndash-gradebook' ) }
							placeholder={ _x( 'Type to search for a User...', 'Gutenberg Block type to search for a User', 'learndash-gradebook' ) }
							onChange={ ( option ) => {
								return setAttributes( { user: ( option ) ? option.value.toString() : null } );
							} }
						/>

					</PanelBody>
				</InspectorControls>
				<Disabled>
					<ServerSideRender block="realbigplugins/report-card-block" attributes={ props.attributes } />
				</Disabled>
			</div>
		);
	}
} );