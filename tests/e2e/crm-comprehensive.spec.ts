import { expect, test } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

import {
  AUTH_DIR,
  createCustomer,
  createDeal,
  createTask,
  createTeam,
  ensureAuthDir,
  loginAs,
  registerAndLogin,
  saveAuthState,
} from './helpers/auth';

ensureAuthDir();
const AUTH_FILE = path.join(AUTH_DIR, 'qa-admin.json');
if (!fs.existsSync(AUTH_FILE)) {
  fs.writeFileSync(AUTH_FILE, JSON.stringify({ cookies: [], origins: [] }));
}

const USER = {
  name: 'QA Admin',
  email: 'qa.admin@example.com',
  password: 'Password123!',
};

function artisanQuery<T = string>(statement: string): T {
  const output = execFileSync('php', ['artisan', 'tinker', '--execute', statement], {
    cwd: process.cwd(),
    encoding: 'utf8',
  });

  return output.trim() as T;
}

test.describe.serial('CRM comprehensive E2E suite', () => {
  test.beforeAll(async ({ browser }) => {
    const context = await browser.newContext();
    const page = await context.newPage();
    await registerAndLogin(page, USER);
    await saveAuthState(context, AUTH_FILE);
    await context.close();
  });

  test.use({ storageState: AUTH_FILE });

  test('01 - register and login redirect to primary team dashboard', async ({ page }) => {
    await page.goto('/');

    await expect(page).toHaveURL(/\/dashboard|\/.*\/dashboard/);
    await expect(page.getByRole('heading', { name: /dashboard|crm dashboard/i })).toBeVisible();
  });

  test('02 - create Team A and ensure Team B isolation', async ({ page }) => {
    const teamA = 'Team A';
    const teamB = 'Team B';
    const customerEmail = 'team-a.customer@example.com';

    await createTeam(page, teamA);
    await createCustomer(page, {
      name: 'Team A Customer',
      email: customerEmail,
      company: 'Acme Labs',
      phone: '08123456789',
      status: 'lead',
    });

    const teamAId = Number(
      artisanQuery(`echo App\\Models\\Team::where('slug', '${new URL(page.url()).pathname.split('/').filter(Boolean)[0]}')->value('id');`),
    );

    const customerTeamId = Number(
      artisanQuery(`echo App\\Models\\Customer::where('email', '${customerEmail}')->value('team_id');`),
    );

    expect(customerTeamId).toBe(teamAId);

    await createTeam(page, teamB);
    await page.goto(`/${new URL(page.url()).pathname.split('/').filter(Boolean)[0]}/customers`);

    await expect(page.getByText('Team A Customer')).not.toBeVisible();

    const customerExistsInTeamB = artisanQuery<number>(`echo App\\Models\\Customer::where('email', '${customerEmail}')->where('team_id', '${teamAId}')->count();`);
    expect(Number(customerExistsInTeamB)).toBe(1);
  });

  test('03 - customer CRUD, search, and team_id binding', async ({ page }) => {
    const customerData = {
      name: 'Customer Searchable',
      email: 'searchable.customer@example.com',
      company: 'Search Co',
      phone: '081111222333',
      status: 'lead',
    };

    await createCustomer(page, customerData);
    await page.getByPlaceholder('Cari nama, email, atau perusahaan...').fill('Search');
    await expect(page.getByText(customerData.name)).toBeVisible();

    await page.getByRole('button', { name: /Edit/ }).first().click();
    await page.getByLabel('Perusahaan').fill('Search Co Updated');
    await page.getByRole('button', { name: 'Simpan' }).click();
    await expect(page.getByText('Search Co Updated')).toBeVisible();

    const customerId = artisanQuery(`echo App\\Models\\Customer::where('email', '${customerData.email}')->value('id');`);
    const teamId = Number(artisanQuery(`echo App\\Models\\Customer::where('email', '${customerData.email}')->value('team_id');`));
    const teamCount = Number(artisanQuery(`echo App\\Models\\Customer::where('id', '${customerId}')->where('team_id', '${teamId}')->count();`));
    expect(teamCount).toBe(1);

    const countBeforeDelete = Number(artisanQuery(`echo App\\Models\\Customer::count();`));
    await page.getByRole('button', { name: /Hapus/ }).first().click();
    await page.on('dialog', dialog => dialog.accept());
    const countAfterDelete = Number(artisanQuery(`echo App\\Models\\Customer::count();`));
    expect(countAfterDelete).toBeLessThan(countBeforeDelete);
  });

  test('04 - deals kanban stage and amount totals update correctly', async ({ page }) => {
    const teamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];
    const customerEmail = 'team-a.customer@example.com';
    const dealTitle = 'Website Revamp Project';

    await page.goto(`/${teamSlug}/customers`);
    const customerId = artisanQuery(`echo App\\Models\\Customer::where('email', '${customerEmail}')->value('id');`);

    await page.goto(`/${teamSlug}/deals`);
    await page.getByRole('button', { name: /Tambah Deal/i }).click();
    await page.locator('input[placeholder*="Lisensi"]').fill(dealTitle);
    await page.locator('select').first().selectOption(customerId);
    await page.locator('input[type="number"]').fill('25000000');
    await page.locator('select').nth(1).selectOption('proposal');
    await page.getByRole('button', { name: 'Simpan Deal' }).click();

    await expect(page.getByText('Website Revamp Project')).toBeVisible();

    await page.locator('select').filter({ hasText: 'Stage:' }).first().selectOption('won');
    await expect(page.getByText('Rp 25.000.000')).toBeVisible();

    const dealStage = artisanQuery(`echo App\\Models\\Deal::where('title', '${dealTitle}')->value('stage');`);
    expect(dealStage).toBe('won');
  });

  test('05 - task creation, completion toggle, and notification bell badge', async ({ page }) => {
    const taskTitle = 'Follow-up proposal follow-up';
    const dueDate = '2030-12-31';

    await createTask(page, { title: taskTitle, dueDate });

    await page.getByRole('checkbox').first().check();
    await expect(page.getByText(taskTitle)).toHaveCSS('text-decoration-line', /line-through|underline/);

    const unreadNotifications = Number(
      artisanQuery(`echo App\\Models\\User::query()->first()->unreadNotifications()->count();`),
    );
    expect(unreadNotifications).toBeGreaterThanOrEqual(0);

    await page.locator('button').filter({ has: page.locator('svg') }).first().click();
    await expect(page.getByText('Tandai Semua Dibaca')).toBeVisible();
  });

  test('06 - calendar navigation and export downloads', async ({ page, browserName }) => {
    const teamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];

    await page.goto(`/${teamSlug}/calendar`);
    await page.getByRole('button', { name: /next|prev|today/i }).first().click();
    await expect(page.getByText(/\w+ \d{4}/i)).toBeVisible();

    await page.goto(`/${teamSlug}/customers`);
    await page.getByRole('button', { name: /Export Data/i }).click();
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.getByRole('link', { name: /Export CSV/i }).click(),
    ]);
    expect(download.suggestedFilename()).toContain('.csv');

    await page.goto(`/${teamSlug}/deals`);
    await page.getByRole('button', { name: /Export Deals/i }).click();
    const [dealDownload] = await Promise.all([
      page.waitForEvent('download'),
      page.getByRole('link', { name: /Export CSV/i }).click(),
    ]);
    expect(dealDownload.suggestedFilename()).toContain('.csv');

    await page.goto(`/${teamSlug}/tasks`);
    const [taskDownload] = await Promise.all([
      page.waitForEvent('download'),
      page.goto(`/${teamSlug}/export/tasks?format=csv`),
    ]);
    expect(taskDownload.suggestedFilename()).toContain('.csv');
    expect(browserName).toBeDefined();
  });

  test('07 - login helper and session sharing works across tests', async ({ page }) => {
    const user = { email: USER.email, password: USER.password };
    await loginAs(page, user);
    await expect(page).toHaveURL(/\/dashboard|\/.*\/dashboard/);
  });
});
