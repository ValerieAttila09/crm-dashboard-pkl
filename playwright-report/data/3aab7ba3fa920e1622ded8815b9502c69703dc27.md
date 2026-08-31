# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: crm-comprehensive.spec.ts >> CRM comprehensive E2E suite >> 01 - register and login redirect to primary team dashboard
- Location: tests/e2e/crm-comprehensive.spec.ts:50:3

# Error details

```
"beforeAll" hook timeout of 60000ms exceeded.
```

# Page snapshot

```yaml
- generic [ref=f1e2]:
  - generic [ref=f1e4]:
    - generic [ref=f1e5]: Internal Server Error
    - button "Copy as Markdown" [ref=f1e11] [cursor=pointer]
  - generic [ref=f1e18]:
    - generic [ref=f1e19]:
      - heading "Illuminate\\Database\\QueryException" [level=1] [ref=f1e20]
      - generic [ref=f1e21]: vendor/laravel/framework/src/Illuminate/Database/Connection.php:857
      - paragraph [ref=f1e23]: "SQLSTATE[42703]: Undefined column: 7 ERROR: column \"team_id\" does not exist LINE 1: ...ect \"stage\", count(*) as total from \"deals\" where \"team_id\" ... ^ (Connection: pgsql, Host: aws-0-ap-northeast-1.pooler.supabase.com, Port: 6543, Database: postgres, SQL: select \"stage\", count(*) as total from \"deals\" where \"team_id\" = 1 group by \"stage\")"
    - generic [ref=f1e24]:
      - generic [ref=f1e25]:
        - generic [ref=f1e26]:
          - generic [ref=f1e27]: LARAVEL
          - generic [ref=f1e28]: 13.29.0
        - generic [ref=f1e29]:
          - generic [ref=f1e30]: PHP
          - generic [ref=f1e31]: 8.4.10
      - generic [ref=f1e32]: UNHANDLED
      - generic [ref=f1e36]: CODE 42703
    - generic [ref=f1e38]:
      - generic [ref=f1e39]: "500"
      - generic [ref=f1e43]: GET
      - generic [ref=f1e47]: http://127.0.0.1:8000/qa-admins-team/dashboard
      - button [ref=f1e48] [cursor=pointer]
  - generic [ref=f1e53]:
    - generic [ref=f1e54]:
      - generic [ref=f1e55]:
        - heading "Exception trace" [level=3] [ref=f1e60]
        - link "1 previous exception" [ref=f1e61] [cursor=pointer]:
          - /url: "#previous-exceptions"
      - generic [ref=f1e62]:
        - generic [ref=f1e64] [cursor=pointer]:
          - generic [ref=f1e69]: 6 vendor frames
          - button [ref=f1e70]
        - generic [ref=f1e75]:
          - generic [ref=f1e76] [cursor=pointer]:
            - generic [ref=f1e79]:
              - code [ref=f1e83]:
                - generic [ref=f1e84]: Illuminate\Database\Eloquent\Builder->pluck()
              - generic [ref=f1e85]: app/Livewire/CrmDashboard.php:22
            - button [ref=f1e88]
          - code [ref=f1e97]:
            - generic [ref=f1e98]: "17"
            - generic [ref=f1e99]: 18 // 1. Data Breakdown per Stage (Doughnut Chart) HANYA untuk Tim Aktif
            - generic [ref=f1e100]: 19 $stageCounts = Deal::where('team_id', $currentTeam->id)
            - generic [ref=f1e101]: 20 ->select('stage', DB::raw('count(*) as total'))
            - generic [ref=f1e102]: 21 ->groupBy('stage')
            - generic [ref=f1e103]: 22 ->pluck('total', 'stage')
            - generic [ref=f1e104]: 23 ->toArray();
            - generic [ref=f1e105]: "24"
            - generic [ref=f1e106]: 25 $stages = ['Lead', 'Proposal', 'Negotiation', 'Won', 'Lost'];
            - generic [ref=f1e107]: 26 $stageChartData = [];
            - generic [ref=f1e108]: "27 foreach ($stages as $stage) {"
            - generic [ref=f1e109]: 28 $stageChartData[] = $stageCounts[$stage]
            - generic [ref=f1e110]: 29 ?? $stageCounts[strtolower($stage)]
            - generic [ref=f1e111]: 30 ?? $stageCounts[strtoupper($stage)]
            - generic [ref=f1e112]: 31 ?? 0;
            - generic [ref=f1e113]: "32 }"
            - generic [ref=f1e114]: "33"
            - generic [ref=f1e115]: "34"
        - generic [ref=f1e117] [cursor=pointer]:
          - generic [ref=f1e122]: 9 vendor frames
          - button [ref=f1e123]
        - generic [ref=f1e129] [cursor=pointer]:
          - generic [ref=f1e132]:
            - code [ref=f1e136]:
              - generic [ref=f1e137]: Livewire\LivewireManager->mount()
            - generic [ref=f1e138]: resources/views/dashboard.blade.php:1
          - button [ref=f1e141]
        - generic [ref=f1e147] [cursor=pointer]:
          - generic [ref=f1e152]: 20 vendor frames
          - button [ref=f1e153]
        - generic [ref=f1e159] [cursor=pointer]:
          - generic [ref=f1e162]:
            - code [ref=f1e166]:
              - generic [ref=f1e167]: "Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()"
            - generic [ref=f1e168]: app/Http/Middleware/EnsureTeamMembership.php:31
          - button [ref=f1e171]
        - generic [ref=f1e177] [cursor=pointer]:
          - generic [ref=f1e182]: 3 vendor frames
          - button [ref=f1e183]
        - generic [ref=f1e189] [cursor=pointer]:
          - generic [ref=f1e192]:
            - code [ref=f1e196]:
              - generic [ref=f1e197]: "Illuminate\\Pipeline\\Pipeline->{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}()"
            - generic [ref=f1e198]: app/Http/Middleware/SetTeamUrlDefaults.php:26
          - button [ref=f1e201]
        - generic [ref=f1e207] [cursor=pointer]:
          - generic [ref=f1e212]: 47 vendor frames
          - button [ref=f1e213]
        - generic [ref=f1e219] [cursor=pointer]:
          - generic [ref=f1e222]:
            - code [ref=f1e226]:
              - generic [ref=f1e227]: Illuminate\Foundation\Application->handleRequest()
            - generic [ref=f1e228]: public/index.php:20
          - button [ref=f1e231]
        - generic [ref=f1e237] [cursor=pointer]:
          - generic [ref=f1e242]: 1 vendor frame
          - button [ref=f1e243]
    - generic [ref=f1e248]:
      - heading "Previous exception" [level=3] [ref=f1e254]
      - generic [ref=f1e258] [cursor=pointer]:
        - generic [ref=f1e259]:
          - heading "PDOException" [level=4] [ref=f1e260]
          - paragraph [ref=f1e261]: "SQLSTATE[42703]: Undefined column: 7 ERROR: column \"team_id\" does not exist LINE 1: ...ect \"stage\", count(*) as total from \"deals\" where \"team_id\" ... ^"
        - button [ref=f1e262]
    - generic [ref=f1e267]:
      - generic [ref=f1e268]:
        - heading "Queries" [level=3] [ref=f1e273]
        - generic [ref=f1e274]: 1-5 of 5
      - generic [ref=f1e276]:
        - generic [ref=f1e277]:
          - generic [ref=f1e278]:
            - generic [ref=f1e279]: pgsql
            - code [ref=f1e286]:
              - generic [ref=f1e287]: select * from "users" where "id" = 1 limit 1
          - generic [ref=f1e288]: 894.71ms
        - generic [ref=f1e289]:
          - generic [ref=f1e290]:
            - generic [ref=f1e291]: pgsql
            - code [ref=f1e298]:
              - generic [ref=f1e299]: select * from "teams" where "teams"."id" = 1 and "teams"."deleted_at" is null limit 1
          - generic [ref=f1e300]: 110.79ms
        - generic [ref=f1e301]:
          - generic [ref=f1e302]:
            - generic [ref=f1e303]: pgsql
            - code [ref=f1e310]:
              - generic [ref=f1e311]: select * from "teams" where "slug" = 'qa-admins-team' and "teams"."deleted_at" is null limit 1
          - generic [ref=f1e312]: 123.62ms
        - generic [ref=f1e313]:
          - generic [ref=f1e314]:
            - generic [ref=f1e315]: pgsql
            - code [ref=f1e322]:
              - generic [ref=f1e323]: select exists(select * from "teams" inner join "team_members" on "teams"."id" = "team_members"."team_id" where "team_members"."user_id" = 1 and "teams"."id" = 1 and "teams"."deleted_at" is null) as "exists"
          - generic [ref=f1e324]: 111.12ms
        - generic [ref=f1e325]:
          - generic [ref=f1e326]:
            - generic [ref=f1e327]: pgsql
            - code [ref=f1e334]:
              - generic [ref=f1e335]: select * from "team_invitations" where LOWER(email) = 'qa.admin@example.com' and "accepted_at" is null and ("expires_at" is null or "expires_at" >= '2026-08-31 10:16:37') order by "created_at" desc
          - generic [ref=f1e336]: 110.93ms
  - generic [ref=f1e338]:
    - generic [ref=f1e339]:
      - heading "Headers" [level=2] [ref=f1e340]
      - generic [ref=f1e341]:
        - generic [ref=f1e342]:
          - generic [ref=f1e343]: host
          - generic [ref=f1e345]: 127.0.0.1:8000
        - generic [ref=f1e346]:
          - generic [ref=f1e347]: connection
          - generic [ref=f1e349]: keep-alive
        - generic [ref=f1e350]:
          - generic [ref=f1e351]: cache-control
          - generic [ref=f1e353]: max-age=0
        - generic [ref=f1e354]:
          - generic [ref=f1e355]: sec-ch-ua
          - generic [ref=f1e357]: "\"Not=A?Brand\";v=\"99\", \"HeadlessChrome\";v=\"151\", \"Chromium\";v=\"151\""
        - generic [ref=f1e358]:
          - generic [ref=f1e359]: sec-ch-ua-mobile
          - generic [ref=f1e361]: "?0"
        - generic [ref=f1e362]:
          - generic [ref=f1e363]: sec-ch-ua-platform
          - generic [ref=f1e365]: "\"Windows\""
        - generic [ref=f1e366]:
          - generic [ref=f1e367]: upgrade-insecure-requests
          - generic [ref=f1e369]: "1"
        - generic [ref=f1e370]:
          - generic [ref=f1e371]: user-agent
          - generic [ref=f1e373]: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36
        - generic [ref=f1e374]:
          - generic [ref=f1e375]: accept-language
          - generic [ref=f1e377]: en-US
        - generic [ref=f1e378]:
          - generic [ref=f1e379]: accept
          - generic [ref=f1e381]: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
        - generic [ref=f1e382]:
          - generic [ref=f1e383]: sec-fetch-site
          - generic [ref=f1e385]: same-origin
        - generic [ref=f1e386]:
          - generic [ref=f1e387]: sec-fetch-mode
          - generic [ref=f1e389]: navigate
        - generic [ref=f1e390]:
          - generic [ref=f1e391]: sec-fetch-dest
          - generic [ref=f1e393]: document
        - generic [ref=f1e394]:
          - generic [ref=f1e395]: referer
          - generic [ref=f1e397]: http://127.0.0.1:8000/qa-admins-team/dashboard
        - generic [ref=f1e398]:
          - generic [ref=f1e399]: accept-encoding
          - generic [ref=f1e401]: gzip, deflate, br, zstd
        - generic [ref=f1e402]:
          - generic [ref=f1e403]: cookie
          - generic [ref=f1e405]: XSRF-TOKEN=eyJpdiI6IjNVanYxYUZWbElZeXo1RW1SWDE0UHc9PSIsInZhbHVlIjoiaWNjK1dMQytHdXVLVmNSWGhDbnNBWWlQR3dWR3lwQ2Q4ekFyZzg5YzZqWW5lRFFoU2EwUWd4Vm9ZRllUaEdwUDAydE0rczBHc0toVDV1aXcxWUN3ZkVpR1JIdExNZ2F5UHdyODVvb3c5Sng4WTZscDdOSlpRY00zQ0FMR0Evbi8iLCJtYWMiOiI5MjY1ZTJjZmI2Y2E1NTMzZmRhYjlkZGQwYjQwNzQ3OWM2YjRlYjQzMGYzMzc3ZWJhODFiZmZmMDk0MjVkODVhIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6IjRzeFFsMW5LckIyeVJ0YWZ2aHYreHc9PSIsInZhbHVlIjoiQ1BMbGNUZnRZQTlpNHNHVWRhWTFHK3ZsUlA1OWJnSHJKbTJQTis4SVU5a1dHZVNwcTJyZjBmMUxaaE5ZdjMzRmZiall3SVVsa2RlNW1vVFpUQjlqSGhjbjk1eDdMazU5YWdkT0E4Ry8vdGdqZTdUQy95T0pxejJtbmsycnVFR20iLCJtYWMiOiJiNzljZmVmMTYyMmQxZjQ0MDBlZTFiNTJhZTUyYWQ2YzJhMjAyOGJmZWEwNzliOGFlMDA4MTk0NjhlOGEyYTAwIiwidGFnIjoiIn0%3D
    - generic [ref=f1e406]:
      - heading "Body" [level=2] [ref=f1e407]
      - generic [ref=f1e408]: // No request body
    - generic [ref=f1e409]:
      - heading "Routing" [level=2] [ref=f1e410]
      - generic [ref=f1e411]:
        - generic [ref=f1e412]:
          - generic [ref=f1e413]: controller
          - generic [ref=f1e415]: \Illuminate\Routing\ViewController
        - generic [ref=f1e416]:
          - generic [ref=f1e417]: route name
          - generic [ref=f1e419]: dashboard
        - generic [ref=f1e420]:
          - generic [ref=f1e421]: middleware
          - generic [ref=f1e423]: web, auth, verified, App\Http\Middleware\EnsureTeamMembership
    - generic [ref=f1e424]:
      - heading "Routing parameters" [level=2] [ref=f1e425]
      - code [ref=f1e430]:
        - generic [ref=f1e431]: "{"
        - generic [ref=f1e432]: "\"current_team\": \"qa-admins-team\","
        - generic [ref=f1e433]: "\"view\": \"dashboard\","
        - generic [ref=f1e434]: "\"data\": [],"
        - generic [ref=f1e435]: "\"status\": 200,"
        - generic [ref=f1e436]: "\"headers\": []"
        - generic [ref=f1e437]: "}"
```

# Test source

```ts
  1   | import { expect, test } from '@playwright/test';
  2   | import { execFileSync } from 'node:child_process';
  3   | import fs from 'node:fs';
  4   | import path from 'node:path';
  5   | 
  6   | import {
  7   |   AUTH_DIR,
  8   |   createCustomer,
  9   |   createDeal,
  10  |   createTask,
  11  |   createTeam,
  12  |   ensureAuthDir,
  13  |   loginAs,
  14  |   registerAndLogin,
  15  |   saveAuthState,
  16  | } from './helpers/auth';
  17  | 
  18  | ensureAuthDir();
  19  | const AUTH_FILE = path.join(AUTH_DIR, 'qa-admin.json');
  20  | if (!fs.existsSync(AUTH_FILE)) {
  21  |   fs.writeFileSync(AUTH_FILE, JSON.stringify({ cookies: [], origins: [] }));
  22  | }
  23  | 
  24  | const USER = {
  25  |   name: 'QA Admin',
  26  |   email: 'qa.admin@example.com',
  27  |   password: 'Password123!',
  28  | };
  29  | 
  30  | function artisanQuery<T = string>(statement: string): T {
  31  |   const output = execFileSync('php', ['artisan', 'tinker', '--execute', statement], {
  32  |     cwd: process.cwd(),
  33  |     encoding: 'utf8',
  34  |   });
  35  | 
  36  |   return output.trim() as T;
  37  | }
  38  | 
  39  | test.describe.serial('CRM comprehensive E2E suite', () => {
> 40  |   test.beforeAll(async ({ browser }) => {
      |        ^ "beforeAll" hook timeout of 60000ms exceeded.
  41  |     const context = await browser.newContext();
  42  |     const page = await context.newPage();
  43  |     await registerAndLogin(page, USER);
  44  |     await saveAuthState(context, AUTH_FILE);
  45  |     await context.close();
  46  |   });
  47  | 
  48  |   test.use({ storageState: AUTH_FILE });
  49  | 
  50  |   test('01 - register and login redirect to primary team dashboard', async ({ page }) => {
  51  |     await page.goto('/');
  52  | 
  53  |     await expect(page).toHaveURL(/\/dashboard|\/.*\/dashboard/);
  54  |     await expect(page.getByRole('heading', { name: /dashboard|crm dashboard/i })).toBeVisible();
  55  |   });
  56  | 
  57  |   test('02 - create Team A and ensure Team B isolation', async ({ page }) => {
  58  |     const teamA = 'Team A';
  59  |     const teamB = 'Team B';
  60  |     const customerEmail = 'team-a.customer@example.com';
  61  | 
  62  |     await createTeam(page, teamA);
  63  |     await createCustomer(page, {
  64  |       name: 'Team A Customer',
  65  |       email: customerEmail,
  66  |       company: 'Acme Labs',
  67  |       phone: '08123456789',
  68  |       status: 'lead',
  69  |     });
  70  | 
  71  |     const teamAId = Number(
  72  |       artisanQuery(`echo App\\Models\\Team::where('slug', '${new URL(page.url()).pathname.split('/').filter(Boolean)[0]}')->value('id');`),
  73  |     );
  74  | 
  75  |     const customerTeamId = Number(
  76  |       artisanQuery(`echo App\\Models\\Customer::where('email', '${customerEmail}')->value('team_id');`),
  77  |     );
  78  | 
  79  |     expect(customerTeamId).toBe(teamAId);
  80  | 
  81  |     await createTeam(page, teamB);
  82  |     await page.goto(`/${new URL(page.url()).pathname.split('/').filter(Boolean)[0]}/customers`);
  83  | 
  84  |     await expect(page.getByText('Team A Customer')).not.toBeVisible();
  85  | 
  86  |     const customerExistsInTeamB = artisanQuery<number>(`echo App\\Models\\Customer::where('email', '${customerEmail}')->where('team_id', '${teamAId}')->count();`);
  87  |     expect(Number(customerExistsInTeamB)).toBe(1);
  88  |   });
  89  | 
  90  |   test('03 - customer CRUD, search, and team_id binding', async ({ page }) => {
  91  |     const customerData = {
  92  |       name: 'Customer Searchable',
  93  |       email: 'searchable.customer@example.com',
  94  |       company: 'Search Co',
  95  |       phone: '081111222333',
  96  |       status: 'lead',
  97  |     };
  98  | 
  99  |     await createCustomer(page, customerData);
  100 |     await page.getByPlaceholder('Cari nama, email, atau perusahaan...').fill('Search');
  101 |     await expect(page.getByText(customerData.name)).toBeVisible();
  102 | 
  103 |     await page.getByRole('button', { name: /Edit/ }).first().click();
  104 |     await page.getByLabel('Perusahaan').fill('Search Co Updated');
  105 |     await page.getByRole('button', { name: 'Simpan' }).click();
  106 |     await expect(page.getByText('Search Co Updated')).toBeVisible();
  107 | 
  108 |     const customerId = artisanQuery(`echo App\\Models\\Customer::where('email', '${customerData.email}')->value('id');`);
  109 |     const teamId = Number(artisanQuery(`echo App\\Models\\Customer::where('email', '${customerData.email}')->value('team_id');`));
  110 |     const teamCount = Number(artisanQuery(`echo App\\Models\\Customer::where('id', '${customerId}')->where('team_id', '${teamId}')->count();`));
  111 |     expect(teamCount).toBe(1);
  112 | 
  113 |     const countBeforeDelete = Number(artisanQuery(`echo App\\Models\\Customer::count();`));
  114 |     await page.getByRole('button', { name: /Hapus/ }).first().click();
  115 |     await page.on('dialog', dialog => dialog.accept());
  116 |     const countAfterDelete = Number(artisanQuery(`echo App\\Models\\Customer::count();`));
  117 |     expect(countAfterDelete).toBeLessThan(countBeforeDelete);
  118 |   });
  119 | 
  120 |   test('04 - deals kanban stage and amount totals update correctly', async ({ page }) => {
  121 |     const teamSlug = new URL(page.url()).pathname.split('/').filter(Boolean)[0];
  122 |     const customerEmail = 'team-a.customer@example.com';
  123 |     const dealTitle = 'Website Revamp Project';
  124 | 
  125 |     await page.goto(`/${teamSlug}/customers`);
  126 |     const customerId = artisanQuery(`echo App\\Models\\Customer::where('email', '${customerEmail}')->value('id');`);
  127 | 
  128 |     await page.goto(`/${teamSlug}/deals`);
  129 |     await page.getByRole('button', { name: /Tambah Deal/i }).click();
  130 |     await page.locator('input[placeholder*="Lisensi"]').fill(dealTitle);
  131 |     await page.locator('select').first().selectOption(customerId);
  132 |     await page.locator('input[type="number"]').fill('25000000');
  133 |     await page.locator('select').nth(1).selectOption('proposal');
  134 |     await page.getByRole('button', { name: 'Simpan Deal' }).click();
  135 | 
  136 |     await expect(page.getByText('Website Revamp Project')).toBeVisible();
  137 | 
  138 |     await page.locator('select').filter({ hasText: 'Stage:' }).first().selectOption('won');
  139 |     await expect(page.getByText('Rp 25.000.000')).toBeVisible();
  140 | 
```