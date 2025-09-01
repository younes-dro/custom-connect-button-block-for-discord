import { test, expect } from '@playwright/test';
import { getScreenshotPath } from './helpers/screenshot';

test.describe('WordPress Authentication', () => {
	test('Subscriber can log in to WordPress', async ({ page }) => {
		console.log('🔍 Testing with BASE_URL:', process.env.BASE_URL);
		console.log('🔍 Testing with user:', process.env.SUBSCR_USER);
		await page.goto(`${process.env.BASE_URL}/wp-login.php`);

		// Take screenshot to see login page
		await page.screenshot({ path: getScreenshotPath('login-page.png') });

		// Fill login form
		await page.fill('#user_login', process.env.SUBSCR_USER!);
		await page.fill('#user_pass', process.env.SUBSCR_PASS!);

		// Click login button
		await page.click('#wp-submit');

		// Wait for navigation - could be to admin or frontend
		await page.waitForLoadState('networkidle');

		// Take screenshot after login attempt
		await page.screenshot({ path: getScreenshotPath('after-login.png') });

		// Check if login was successful - look for admin bar or profile link
		const loggedInIndicators = [
			'#wpadminbar', // Admin bar
			'a[href*="wp-admin/profile.php"]', // Profile link
			'.ab-item[href*="wp-admin/profile.php"]', // Admin bar profile
			'body.logged-in' // WordPress logged-in body class
		];

		let loginSuccessful = false;
		for (const selector of loggedInIndicators) {
			try {
				await expect(page.locator(selector)).toBeVisible({ timeout: 5000 });
				console.log(`✅ Login successful - found: ${selector}`);
				loginSuccessful = true;
				break;
			} catch (e) {
				console.log(`❌ Not found: ${selector}`);
			}
		}

		// If none of the indicators work, check URL
		if (!loginSuccessful) {
			const currentUrl = page.url();
			console.log('🔍 Current URL after login:', currentUrl);

			// Login successful if not on login page and no error messages
			const isOnLoginPage = currentUrl.includes('wp-login.php');
			const hasErrorMessage = await page.locator('.login #login_error').isVisible().catch(() => false);

			if (!isOnLoginPage && !hasErrorMessage) {
				console.log('✅ Login appears successful based on URL redirect');
				loginSuccessful = true;
			}
		}

		expect(loginSuccessful, 'Login should be successful').toBe(true);
	});

	test('Admin can log in to WordPress', async ({ page }) => {
		console.log('🔍 Testing admin login with user:', process.env.ADMIN_USER);

		await page.goto('/wp-login.php');

		await page.fill('#user_login', process.env.ADMIN_USER!);
		await page.fill('#user_pass', process.env.ADMIN_PASS!);
		await page.click('#wp-submit');

		// Admin should redirect to dashboard
		await page.waitForURL('**/wp-admin/**', { timeout: 10000 });

		// Take screenshot to see what's on the page
		await page.screenshot({ path: getScreenshotPath('admin-dashboard.png') });

		// Should see admin bar (this is the most reliable indicator)
		await expect(page.locator('#wpadminbar')).toBeVisible();

		// Close any modal dialogs that might be open
		const modalClose = page.locator('#link-modal-title').locator('..').locator('button[data-dismiss="modal"], .media-modal-close, .mce-close');
		if (await modalClose.isVisible()) {
			console.log('🔄 Closing modal dialog...');
			await modalClose.click();
			await page.waitForTimeout(1000); // Wait for modal to close
		}

		// More specific h1 selector - look for dashboard-specific h1
		const dashboardHeading = page.locator('#wpbody-content h1, .wrap h1').first();
		await expect(dashboardHeading).toContainText(/Dashboard|Welcome/);

		// Alternative: Check for specific dashboard elements instead of h1
		const dashboardElements = [
			'.welcome-panel', // WordPress welcome panel
			'#dashboard-widgets', // Dashboard widgets container
			'#wpbody-content .wrap', // Main admin content wrapper
		];

		let foundDashboardElement = false;
		for (const selector of dashboardElements) {
			if (await page.locator(selector).isVisible()) {
				console.log(`✅ Found dashboard element: ${selector}`);
				foundDashboardElement = true;
				break;
			}
		}

		// If h1 check fails, at least verify we're in admin area
		if (!foundDashboardElement) {
			// Just verify we're in wp-admin and admin bar is visible
			expect(page.url()).toContain('/wp-admin/');
			console.log('✅ Admin login successful - verified by URL and admin bar');
		}

		console.log('✅ Admin login successful');
	});
});
