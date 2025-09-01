/**
 * SVG wrapper approach - More reliable for toolbar usage
 * This ensures consistent rendering across different contexts
 *
 * @param {string} iconURL - The URL of the image to use as icon
 * @param {number} size - The size of the icon (default: 20)
 * @returns {JSX.Element} SVG-wrapped icon
 */
export const ServiceIconSVG = ({ iconURL, size = 20 }) => {
	if (!iconURL) {
		// Fallback SVG icon
		return (
			<svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor">
				<circle cx="12" cy="12" r="10" />
				<text x="12" y="16" textAnchor="middle" fontSize="12" fill="white">?</text>
			</svg>
		);
	}

	return (
		<svg width={size} height={size} viewBox={`0 0 ${size} ${size}`}>
			<foreignObject width="100%" height="100%">
				<img
					src={iconURL}
					alt="Service Icon"
					style={{
						width: '100%',
						height: '100%',
						objectFit: 'contain'
					}}
				/>
			</foreignObject>
		</svg>
	);
};



