import { test, expect } from '@playwright/test';
import { getScreenshotPath } from './helpers/screenshot';

test('Debug environment and site access', async ({ page }) => {
	// Log all environment variables
	console.log('=== Environment Variables ===');
	console.log('BASE_URL:', process.env.BASE_URL);
	console.log('ADMIN_USER:', process.env.ADMIN_USER);
	console.log('ADMIN_PASSWORD:', process.env.ADMIN_PASSWORD ? '***SET***' : 'NOT SET');
	console.log('Current working directory:', process.cwd());
	console.log('Node environment:', process.env.NODE_ENV);
	console.log('================================');

	// Validate required environment variables
	if (!process.env.BASE_URL) {
		throw new Error('BASE_URL environment variable is not set');
	}

	// Try to access the homepage first
	try {
		console.log(`🌐 Attempting to navigate to: ${process.env.BASE_URL}/wp-login.php`);

		await page.goto(`${process.env.BASE_URL}/wp-login.php`, {
			waitUntil: 'networkidle', // Wait for network to be idle
			timeout: 30000 // 30 second timeout
		});

		console.log('✅ Site is accessible');

		// Take a screenshot
		await page.screenshot({
			path: getScreenshotPath('debug-login-page.png'),
			fullPage: true // Capture entire page
		});

		// Check page title
		const title = await page.title();
		console.log('📄 Page title:', title);

		// Additional WordPress-specific checks
		const isWordPress = await page.locator('body').getAttribute('class');
		if (isWordPress && isWordPress.includes('wp-')) {
			console.log('✅ WordPress detected');
		}

		// Check if login form exists
		const loginForm = page.locator('#loginform, form[name="loginform"]');
		if (await loginForm.isVisible()) {
			console.log('✅ Login form is visible');
		} else {
			console.log('⚠️ Login form not found');
		}

		// Check for any obvious errors on the page
		const errorMessages = await page.locator('.error, .wp-die-message').count();
		if (errorMessages > 0) {
			console.log('⚠️ Found', errorMessages, 'error message(s) on page');
		}

		// Test basic navigation
		console.log('🔍 Testing basic site navigation...');
		await page.goto(process.env.BASE_URL!);
		const homepageTitle = await page.title();
		console.log('🏠 Homepage title:', homepageTitle);

		await page.screenshot({ path: getScreenshotPath('debug-homepage.png') });

	} catch (error) {
		console.log('❌ Site access failed:', error.message);

		// Take screenshot even on failure for debugging
		try {
			await page.screenshot({ path: getScreenshotPath('debug-error.png') });
			console.log('📷 Error screenshot saved');
		} catch (screenshotError) {
			console.log('📷 Could not save error screenshot:', screenshotError.message);
		}

		// Re-throw the error so the test fails
		throw error;
	}
});
