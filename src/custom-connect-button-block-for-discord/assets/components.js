import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/icons';
const textDomain = 'custom-connect-button-block-for-discord';

const DiscordIcon = () => (
	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.211.375-.445.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" fill="currentColor" />
	</svg>
);

const PlayIcon = () => (
	<Icon
		icon={
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<radialGradient id="playRadial" cx="30%" cy="30%" r="70%">
						<stop offset="0%" stopColor="#34d399" stopOpacity="1" />
						<stop offset="50%" stopColor="#10b981" stopOpacity="1" />
						<stop offset="100%" stopColor="#047857" stopOpacity="0.9" />
					</radialGradient>
					<linearGradient id="playShine" x1="0%" y1="0%" x2="100%" y2="100%">
						<stop offset="0%" stopColor="#ffffff" stopOpacity="0.9" />
						<stop offset="100%" stopColor="#ffffff" stopOpacity="0.1" />
					</linearGradient>
				</defs>

				<circle cx="12" cy="12" r="10" fill="url(#playRadial)" />
				<circle cx="12" cy="12" r="10" fill="url(#playShine)" opacity="0.4" />
				<path d="M9 7 L9 17 L17 12 Z" fill="white" opacity="0.95" />
				<path d="M9 7 L9 17 L17 12 Z" fill="rgba(255,255,255,0.3)" />
			</svg>
		}
	/>
);

const StopIcon = () => (
	<Icon
		icon={
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<radialGradient id="stopRadial" cx="30%" cy="30%" r="70%">
						<stop offset="0%" stopColor="#f87171" stopOpacity="1" />
						<stop offset="50%" stopColor="#ef4444" stopOpacity="1" />
						<stop offset="100%" stopColor="#b91c1c" stopOpacity="0.9" />
					</radialGradient>
					<linearGradient id="stopShine" x1="0%" y1="0%" x2="100%" y2="100%">
						<stop offset="0%" stopColor="#ffffff" stopOpacity="0.9" />
						<stop offset="100%" stopColor="#ffffff" stopOpacity="0.1" />
					</linearGradient>
				</defs>

				<circle cx="12" cy="12" r="10" fill="url(#stopRadial)" />
				<circle cx="12" cy="12" r="10" fill="url(#stopShine)" opacity="0.4" />
				<rect x="8" y="8" width="8" height="8" rx="1" fill="white" opacity="0.95" />
				<rect x="8" y="8" width="8" height="8" rx="1" fill="rgba(255,255,255,0.3)" />
			</svg>
		}
	/>
);

const LivePreviewBadge = () => (
	<div className="live-preview-badge" style={{
		position: 'absolute',
		top: '8px',
		right: '8px',
		backgroundColor: '#007cba',
		color: 'white',
		padding: '4px 8px',
		borderRadius: '4px',
		fontSize: '12px',
		fontWeight: 'bold',
		zIndex: 10,
		pointerEvents: 'none'
	}}>
		{__('Live Preview', textDomain)}
	</div>
);

export { DiscordIcon, PlayIcon, StopIcon, LivePreviewBadge };

