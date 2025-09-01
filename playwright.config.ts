import { defineConfig, devices } from '@playwright/test';
import * as dotenv from 'dotenv';
import path from 'path';

dotenv.config({ path: path.join(__dirname, '.env.test') });

export default defineConfig({
	testDir: './tests/e2e',
	outputDir: './tests/e2e/test-results',
	timeout: 30_000,
	expect: {
		timeout: 10_000
	},
	fullyParallel: false,
	workers: 1,
	reporter: [
		['html', {
			outputFolder: './tests/e2e/playwright-report',
			open: 'never' // Prevents auto-opening the report
		}],
		['list']
	],
	use: {
		headless: false,
		viewport: { width: 1280, height: 720 },
		baseURL: process.env.BASE_URL?.replace(/\/$/, ''),
		trace: 'on-first-retry',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure'
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices['Desktop Chrome'] },
		}
	],

	// This ensures the .last-run.json file goes in the correct location
	metadata: {
		'test-results-dir': './tests/e2e/test-results'
	},
});
