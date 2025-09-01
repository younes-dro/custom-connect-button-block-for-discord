import path from 'path';
import fs from 'fs';

// Ensure screenshots directory exists
const screenshotsDir = path.join('tests', 'e2e', 'screenshots');
if (!fs.existsSync(screenshotsDir)) {
	fs.mkdirSync(screenshotsDir, { recursive: true });
}

export function getScreenshotPath(filename: string): string {
	return path.join('tests', 'e2e', 'screenshots', filename);
}

// Usage in your tests:
// import { getScreenshotPath } from '../helpers/screenshot';
// await page.screenshot({ path: getScreenshotPath('login-page.png') });
