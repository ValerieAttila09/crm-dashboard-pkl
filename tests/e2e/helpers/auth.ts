import type { BrowserContext, Page } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

export const AUTH_DIR = path.resolve(process.cwd(), 'playwright/.auth');

export function ensureAuthDir(): void {
  fs.mkdirSync(AUTH_DIR, { recursive: true });
}

export async function saveAuthState(context: BrowserContext, filePath: string): Promise<void> {
  ensureAuthDir();
  await context.storageState({ path: filePath });
}

export async function registerAndLogin(page: Page, user: { name: string; email: string; password: string }): Promise<void> {
  await page.goto('/register');

  await page.getByLabel('Name').fill(user.name);
  await page.getByLabel('Email address').fill(user.email);
  await page.locator('input[name="password"]').first().fill(user.password);
  await page.locator('input[name="password_confirmation"]').first().fill(user.password);
  await page.getByRole('button', { name: 'Create account' }).click();

  await page.waitForURL(/\/dashboard|\/.*\/dashboard/);
}

export async function loginAs(page: Page, user: { email: string; password: string }): Promise<void> {
  await page.goto('/login');

  await page.getByLabel('Email address').fill(user.email);
  await page.locator('input[name="password"]').first().fill(user.password);
  await page.getByRole('button', { name: 'Log in' }).click();

  await page.waitForURL(/\/dashboard|\/.*\/dashboard/);
}

export async function createTeam(page: Page, teamName: string): Promise<string> {
  await page.getByTestId('team-switcher-trigger').click();
  await page.getByTestId('team-switcher-new-team').click();
  await page.getByTestId('switcher-create-team-name').fill(teamName);
  await page.getByTestId('switcher-create-team-submit').click();

  await page.waitForURL(/\/.*\/dashboard/);

  const currentTeamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];
  return currentTeamSlug;
}

export async function createCustomer(
  page: Page,
  payload: { name: string; email: string; company?: string; phone?: string; status?: string },
): Promise<void> {
  await page.goto('/' + new URL(page.url()).pathname.split('/').filter(Boolean)[0] + '/customers');
  await page.getByRole('button', { name: /Tambah Pelanggan/i }).click();

  await page.getByLabel('Nama').fill(payload.name);
  await page.getByLabel('Email').fill(payload.email);
  if (payload.phone) await page.getByLabel('No. Telepon').fill(payload.phone);
  if (payload.company) await page.getByLabel('Perusahaan').fill(payload.company);
  if (payload.status) {
    await page.locator('select').nth(0).selectOption(payload.status);
  }

  await page.getByRole('button', { name: 'Simpan' }).click();
  await expect(page.getByText(payload.name)).toBeVisible();
}

export async function createDeal(
  page: Page,
  payload: { title: string; customerName: string; amount: string; stage: string; closeDate?: string },
): Promise<void> {
  const teamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];
  await page.goto(`/${teamSlug}/deals`);
  await page.getByRole('button', { name: /Tambah Deal/i }).click();

  await page.locator('input[placeholder*="Lisensi"]').fill(payload.title);
  await page.locator('select').first().selectOption({ label: new RegExp(payload.customerName, 'i') });
  await page.locator('input[type="number"]').fill(payload.amount);
  await page.locator('select').nth(1).selectOption(payload.stage);
  if (payload.closeDate) {
    await page.locator('input[type="date"]').fill(payload.closeDate);
  }

  await page.getByRole('button', { name: 'Simpan Deal' }).click();
  await expect(page.getByText(payload.title)).toBeVisible();
}

export async function createTask(page: Page, payload: { title: string; dueDate: string; dealTitle?: string }): Promise<void> {
  const teamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];
  await page.goto(`/${teamSlug}/tasks`);

  await page.getByLabel('Judul / Agenda Follow-Up').fill(payload.title);
  await page.getByLabel('Tenggat Waktu (Due Date)').fill(payload.dueDate);
  if (payload.dealTitle) {
    await page.locator('select').last().selectOption({ label: new RegExp(payload.dealTitle, 'i') });
  }
  await page.getByRole('button', { name: 'Simpan Tugas' }).click();
  await expect(page.getByText(payload.title)).toBeVisible();
}
