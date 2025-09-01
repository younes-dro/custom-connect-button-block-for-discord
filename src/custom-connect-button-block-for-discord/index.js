
import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import { DiscordIcon } from './assets/components';
import Edit from './edit';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	icon: DiscordIcon,
	edit: Edit,
	save: () => null,
});
