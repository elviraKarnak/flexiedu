import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Disabled } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';
import metadata from '../block.json';

registerBlockType( metadata, {
	title: __( 'Frontend Gradebook', 'learndash-gradebook' ),
	description: __( 'Display a Frontend Gradebook for your users.', 'learndash-gradebook' ),
	edit: ( props ) => {
		const blockProps = useBlockProps();

		return (
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender block="realbigplugins/frontend-gradebook-block" />
				</Disabled>					
			</div>
		);
	}
} );