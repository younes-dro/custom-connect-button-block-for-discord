import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch'
import { useBlockProps, InspectorControls, RichText, BlockControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

import {
	TabPanel,
	Panel,
	PanelBody,
	PanelRow,
	TextControl,
	ColorPicker,
	Icon,
	ToolbarGroup,
	ToolbarButton,
	Spinner,
} from '@wordpress/components';
import { brush, overlayText, cautionFilled } from '@wordpress/icons';
import { DiscordIcon, PlayIcon, StopIcon, LivePreviewBadge } from './assets/components';
import { ServiceIconSVG } from './assets/CustomServiceIcon';
import './editor.scss';

const textDomain = 'custom-connect-button-block-for-discord';

/**
 * Edit function for the Custom connect button block for Discord.
 *
 * @param {Object} props - The block properties.
 * @param {Object} props.attributes - The block attributes.
 * @param {Function} props.setAttributes - Function to update block attributes.
 * @returns {JSX.Element} The block edit interface.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
export default function Edit({ attributes, setAttributes }) {
	const {
		connectButtonTextColor,
		connectButtonBgColor,
		disconnectButtonTextColor,
		disconnectButtonBgColor,
		loggedInText,
		loggedOutText,
		roleWillAssignText,
		roleAssignedText,
		discordConnectedAccountText,
		isLivePreview = false,
	} = attributes;

	// const blockProps = { ...useBlockProps(), className: 'dro-ccbb-connect-block' };
	const blockProps = useBlockProps({
		className: 'dro-ccbb-connect-block',
	});

	const [serviceIconURL, setServiceIconURL] = useState(null);
	const [serviceName, setServiceIconName] = useState(null);
	const [isServiceIconLoading, setServiceIconLoading] = useState(true);
	const [isServiceIconError, setServiceIconError] = useState(null);

	useEffect(() => {
		const fetchServiceIcon = async () => {
			try {
				setServiceIconLoading(true);
				setServiceIconError(null);
				const response = await apiFetch({
					path: droDiscordApi.endpoints.icons
				});

				if (response.success && response.data.icon_url) {
					setServiceIconURL(response.data.icon_url);
					setServiceIconName(response.data.service_name);

				} else {
					throw new Error(__('No icon URL found in response', textDomain));
				}

			} catch (err) {
				console.info('Error fetching Discord service icon:', err);
				setServiceIconError(err.message || __('Failed to fetch service icon', textDomain));
			} finally {
				setServiceIconLoading(false);
			}
		};

		fetchServiceIcon();

	}, []); // Empty dependency array means this runs once on mount

	const renderLivePreview = () => (

		<div className="live-preview-wrapper" style={{ position: 'relative' }}>
			<LivePreviewBadge />
			<ServerSideRender
				block="dro-block/custom-connect-button-block-for-discord"
				attributes={attributes}
				LoadingResponseComponent={() => (
					<div style={{ textAlign: 'center', padding: '20px' }}>
						<Spinner />
						<p>{__('Loading preview...', textDomain)}</p>
					</div>
				)}
				ErrorResponseComponent={({ response }) => (
					<div style={{
						padding: '20px',
						backgroundColor: '#f8d7da',
						color: '#721c24',
						borderRadius: '4px'
					}}>
						<p><strong>{__('Preview Error:', textDomain)}</strong></p>
						<p>{response?.message || __('Could not load preview', textDomain)}</p>
					</div>
				)}
			/>
		</div>

	);


	const renderEditUI = () => (
		<>
			{/* <img src={serviceIconURL} /> */}
			<div className='dro-ccbb-connect-buttons'>

				<button
					style={{
						backgroundColor: connectButtonBgColor,
						color: connectButtonTextColor
					}}
					className="dro-ccbb-connect-button"
				>
					<RichText
						tagName="span"
						value={loggedInText}
						onChange={(value) => setAttributes({ loggedInText: value })}
						placeholder={__('Connect to Discord...', textDomain)}
					/>
					<Icon icon={DiscordIcon} />
				</button>
				<button
					style={{
						backgroundColor: disconnectButtonBgColor,
						color: disconnectButtonTextColor
					}}
					className="dro-ccbb-connect-button"
				>
					<RichText
						tagName="span"
						value={loggedOutText}
						onChange={(value) => setAttributes({ loggedOutText: value })}
						placeholder={__('Disconnect from Discord?', textDomain)}
					/>
					<Icon icon={DiscordIcon} />
				</button>
			</div>
			<RichText
				tagName="p"
				value={discordConnectedAccountText}
				onChange={(value) => setAttributes({ discordConnectedAccountText: value })}
				placeholder={__('Connected account text...', textDomain)}
			/>
			<RichText
				tagName="p"
				value={roleWillAssignText}
				onChange={(value) => setAttributes({ roleWillAssignText: value })}
				placeholder={__('Role will assign text...', textDomain)}
			/>
			<RichText
				tagName="p"
				value={roleAssignedText}
				onChange={(value) => setAttributes({ roleAssignedText: value })}
				placeholder={__('Role assigned text...', textDomain)}
			/>
		</>
	);

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={isLivePreview ? StopIcon : PlayIcon}
						label={isLivePreview ? __('Back to Edit', textDomain) : __('Live Preview', textDomain)}
						isPressed={isLivePreview}
						className="dro-ccbb-preview-toggle"
						onClick={() => setAttributes({ isLivePreview: !isLivePreview })}
					/>
					<ToolbarButton
						icon={isServiceIconLoading
							? (<Spinner />) : isServiceIconError ? (
								<Icon icon={cautionFilled} />
							) :
								(
									<Icon icon={() => <ServiceIconSVG iconURL={serviceIconURL} size={20} />} />
								)

						}

						onClick={() => alert(__('Discord active service: ' + serviceName, textDomain))}
					/>

				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<div className="dro-native-tab-wrapper">
					<TabPanel
						className="dro-native-tabs"
						activeClass="is-active"
						tabs={[
							{
								name: 'settings',
								title: __('Settings', textDomain),
								icon: overlayText,
							},
							{
								name: 'style',
								title: __('Style', textDomain),
								icon: brush,
							}
						]}
					>
						{(tab) => (
							<>
								{tab.name === 'settings' && (
									<>
										<PanelBody
											title={__('Button Text Settings', textDomain)}
											initialOpen={true}
										>
											<TextControl
												label={__('Connect Button Text', textDomain)}
												value={loggedInText}
												onChange={(value) => setAttributes({ loggedInText: value })}
												placeholder={__('Connect to Discord', textDomain)}
											/>
											<TextControl
												label={__('Disconnect Button Text', textDomain)}
												value={loggedOutText}
												onChange={(value) => setAttributes({ loggedOutText: value })}
												placeholder={__('Disconnect from Discord', textDomain)}
											/>
										</PanelBody>

										<PanelBody
											title={__('Discord Account Text', textDomain)}
											initialOpen={false}
										>
											<TextControl
												label={__('Connected Account Text', textDomain)}
												value={discordConnectedAccountText}
												onChange={(value) => setAttributes({ discordConnectedAccountText: value })}
												placeholder={__('Connected account: {discord_username}', textDomain)}
												help={__('Use {discord_username} as placeholder for the actual username', textDomain)}
											/>
										</PanelBody>

										<PanelBody
											title={__('Role Assignment Text', textDomain)}
											initialOpen={false}
										>
											<TextControl
												label={__('Role Will Assign Text', textDomain)}
												value={roleWillAssignText}
												onChange={(value) => setAttributes({ roleWillAssignText: value })}
												placeholder={__('You will be assigned the following Discord roles:', textDomain)}
											/>
											<TextControl
												label={__('Role Assigned Text', textDomain)}
												value={roleAssignedText}
												onChange={(value) => setAttributes({ roleAssignedText: value })}
												placeholder={__('You have been assigned the following Discord roles:', textDomain)}
											/>
										</PanelBody>
									</>
								)}

								{tab.name === 'style' && (
									<>
										<PanelBody
											title={__('Connect Button Colors', textDomain)}
											initialOpen={true}
										>
											<PanelRow>
												<label>{__('Background Color', textDomain)}</label>
											</PanelRow>
											<ColorPicker
												color={connectButtonBgColor}
												onChangeComplete={(color) => setAttributes({ connectButtonBgColor: color.hex })}
											/>
											<PanelRow>
												<label>{__('Text Color', textDomain)}</label>
											</PanelRow>
											<ColorPicker
												color={connectButtonTextColor}
												onChangeComplete={(color) => setAttributes({ connectButtonTextColor: color.hex })}
											/>
										</PanelBody>

										<PanelBody
											title={__('Disconnect Button Colors', textDomain)}
											initialOpen={false}
										>
											<PanelRow>
												<label>{__('Background Color', textDomain)}</label>
											</PanelRow>
											<ColorPicker
												color={disconnectButtonBgColor}
												onChangeComplete={(color) => setAttributes({ disconnectButtonBgColor: color.hex })}
											/>
											<PanelRow>
												<label>{__('Text Color', textDomain)}</label>
											</PanelRow>
											<ColorPicker
												color={disconnectButtonTextColor}
												onChangeComplete={(color) => setAttributes({ disconnectButtonTextColor: color.hex })}
											/>
										</PanelBody>
									</>
								)}
							</>
						)}
					</TabPanel>
				</div>
			</InspectorControls>


			<div {...blockProps}>
				<div style={{ display: isLivePreview ? 'block' : 'none' }}>
					{renderLivePreview()}
				</div>
				<div style={{ display: isLivePreview ? 'none' : 'block' }}>
					{renderEditUI()}
				</div>
			</div>

		</>
	);
}
