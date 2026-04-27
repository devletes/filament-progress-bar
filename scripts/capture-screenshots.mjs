#!/usr/bin/env node

import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const projectRoot = resolve(__dirname, '..');
const outDir = resolve(projectRoot, 'docs/images');

const baseUrl = process.env.WORKBENCH_URL ?? 'http://127.0.0.1:8765';
const email = process.env.WORKBENCH_EMAIL ?? 'aria@example.com';
const password = process.env.WORKBENCH_PASSWORD ?? 'password';

const showcaseSections = [
    'sizes',
    'text-position',
    'visibility',
    'border-radius',
    'thresholds-default',
    'thresholds-descending',
    'thresholds-map',
    'recipe-battery',
    'recipe-quality',
    'recipe-squared',
    'recipe-compact',
];

const showcaseSteps = showcaseSections.flatMap((id) => {
    const filename = id.replace(/-/g, '_');
    return [
        { file: `${filename}_light.png`, selector: `[data-demo="${id}"]`, mode: 'light', padding: 25 },
        { file: `${filename}_dark.png`, selector: `[data-demo="${id}"]`, mode: 'dark', padding: 25 },
    ];
});

const targets = [
    {
        path: '/admin/users',
        steps: [
            {
                file: 'table_column_light.png',
                selector: '.fi-ta-ctn',
                mode: 'light',
                waitForSelector: '.fi-ta-table',
                padding: 25,
            },
            {
                file: 'table_column_dark.png',
                selector: '.fi-ta-ctn',
                mode: 'dark',
                waitForSelector: '.fi-ta-table',
                padding: 25,
            },
        ],
    },
    {
        path: '/admin/showcase',
        steps: showcaseSteps,
    },
    {
        path: '/admin/users/8',
        steps: [
            {
                file: 'infolist_entry_light.png',
                selector: '.fi-section',
                mode: 'light',
                waitForSelector: '.fi-section',
                padding: 25,
            },
            {
                file: 'infolist_entry_dark.png',
                selector: '.fi-section',
                mode: 'dark',
                waitForSelector: '.fi-section',
                padding: 25,
            },
        ],
    },
];

async function setColorMode(page, mode) {
    await page.evaluate((m) => {
        const root = document.documentElement;
        if (m === 'dark') {
            root.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            root.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }, mode);
}

async function main() {
    await mkdir(outDir, { recursive: true });

    const browser = await chromium.launch();
    const context = await browser.newContext({
        viewport: { width: 1280, height: 1400 },
        deviceScaleFactor: 2,
    });
    const page = await context.newPage();

    await page.goto(`${baseUrl}/admin/login`);
    await page.fill('input[type=email]', email);
    await page.fill('input[type=password]', password);
    await page.click('button[type=submit]');
    await page.waitForURL((url) => !url.toString().includes('/login'), { timeout: 15000 });
    await page.waitForLoadState('networkidle');

    for (const target of targets) {
        await page.goto(`${baseUrl}${target.path}`);
        await page.waitForLoadState('networkidle');

        for (const step of target.steps) {
            const waitSelector = step.waitForSelector ?? step.selector;
            await page.waitForSelector(waitSelector, { timeout: 15000 });
            await setColorMode(page, step.mode);
            await page.waitForTimeout(250);

            const outPath = resolve(outDir, step.file);

            if (step.padding) {
                await page.locator(step.selector).first().scrollIntoViewIfNeeded();
                await page.waitForTimeout(150);
                const box = await page.locator(step.selector).first().boundingBox();
                await page.screenshot({
                    path: outPath,
                    fullPage: true,
                    clip: {
                        x: Math.max(0, box.x - step.padding),
                        y: Math.max(0, box.y + (await page.evaluate(() => window.scrollY)) - step.padding),
                        width: box.width + step.padding * 2,
                        height: box.height + step.padding * 2,
                    },
                });
            } else {
                await page.locator(step.selector).first().screenshot({ path: outPath });
            }

            console.log(`saved ${outPath}`);
        }
    }

    await browser.close();
}

main().catch((err) => {
    console.error(err);
    process.exit(1);
});
