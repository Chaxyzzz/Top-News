# TopNews — Enterprise Digital News & Media Publishing Platform

> *"Informasi Cepat. Perspektif Jelas. Berita Terpercaya."*  
> *"Fast information. Clear perspective. Trusted news."*

TopNews is an enterprise-grade, modern, fast, secure, scalable, and SEO-optimized digital news publishing platform built on Laravel 12 and PHP 8.3. It implements complete newsroom editorial workflows, multi-rubric categorization, multimedia stories, engagement features, homepage layout management, advertising tracking, subscriber newsletters, technical SEO automation, and advanced security safeguards.

---

## 📌 Platform Overview

- **Framework**: Laravel 12.x / PHP 8.3+
- **Frontend Stack**: Blade Templates, Tailwind CSS / Custom Modular Design System, Vanilla JavaScript (Zero bloated frontend frameworks)
- **Asset Bundler**: Vite 6+
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Cache / Session**: Redis / Database / File
- **Test Suite**: PHPUnit (196 tests, 701 assertions, 100% passing)
- **Code Style**: Laravel Pint (PSR-12 strict)
- **Timezone**: `Asia/Jakarta` (WIB)
- **Default Locale**: `id` (Bahasa Indonesia)

---

## 🏛️ System Architecture & Features (Phases 01 – 12)

### 1. Foundation, Design System & Branding (Phase 01)
- **60-30-10 Editorial Palette**: Clean pure white background (`#FFFFFF`), authoritative charcoal headlines (`#111111`), secondary slate metadata (`#5F6368`), and high-impact TopNews Red (`#E50914`).
- **Typography**: Instrument Sans for punchy news headlines, Merriweather serif for long-form readability, and JetBrains Mono for data points.
- **Responsive Layout**: Sticky editorial header with breaking ticker, mega-menu channel navigation, mobile slide-out drawer, quick search dialog, and legal footer.

### 2. Authentication, Users & RBAC (Phase 02)
- **Multi-Guard Access**: Strict segregation between reader accounts and newsroom staff.
- **7 Granular Roles**:
  - `super_admin`: Full system authority, site settings, security logs. Protected against deletion or suspension.
  - `admin`: Staff account operations, permission management, audit trail inspection.
  - `editor_in_chief`: Editorial standards, breaking news overrides, lead story curation.
  - `editor`: Peer review, revision requests, proofreading, article approval.
  - `journalist`: News reporting, draft authoring, editorial submission.
  - `contributor`: External columnists and opinion writers.
  - `reader`: Front-facing public audience (bookmarks, discussions, reactions).
- **Audit Logging**: Immutable security audit trail recording IP addresses, user agents, actions, and previous/new state diffs.
- **CLI Super Admin Provisioning**: Zero hardcoded credentials in migrations. CLI tool `php artisan topnews:make-super-admin` with hidden password inputs.

### 3. Newsroom Admin Dashboard & CMS Foundation (Phase 03)
- **Editorial Dashboard**: Real-time KPI counters (articles published, pending review, scheduled, staff counts, audit events).
- **Navigation & Drawer**: Desktop collapsible sidebar and mobile drawer with permission-filtered navigation badges.
- **Account Profiles & Security**: Two-factor authentication readiness, session termination, and credential updates.

### 4. News Content & Multi-Stage Editorial Workflow (Phase 04)
- **Lifecycle States**: `draft` ➔ `submitted` ➔ `in_review` ➔ `revision_requested` ➔ `approved` ➔ `scheduled` ➔ `published` ➔ `archived`.
- **Content Types**: Standard News, In-Depth Investigative Reports, Editorial Opinions, Video News, Photo Stories, and Sponsored Content.
- **Multi-Category & Tags**: Canonical category routing with accent badges, primary tags, and slug collision prevention.
- **Revision History & Diffs**: Complete revision tracking with title and body diff inspections before approval.
- **Automated Publishing**: Background scheduler (`topnews:publish-scheduled`) checks every minute for scheduled articles reaching publication time.

### 5. Homepage & News Discovery (Phase 05)
- **Headline Architecture**: Dominant lead hero story, supporting focal stories, and secondary trending grids.
- **Channel Highlights**: Dedicated feeds for Nasional, Politik, Ekonomi & Bisnis, Teknologi, Internasional, Olahraga, Hiburan, and Gaya Hidup.
- **Velocity-Based Trending Algorithm**: Real-time decaying viral scoring based on views in the last 48 hours:
  $$\text{Score} = \frac{\text{Recent Views}}{(\text{Hours Since Publish} + 2)^{1.2}}$$
- **Empty State Resilience**: Graceful empty states when no articles match criteria, preventing 500 errors.

### 6. Article Reading Experience (Phase 06)
- **Distraction-Free Typography**: Optimal line heights, readable serif body text, and reading progress indicators.
- **Multimedia Embedding**: Responsive figure captions, photo credits, audio/video embeds.
- **Social Sharing**: Direct deep links to WhatsApp, Facebook, X (Twitter), Telegram, and native clipboard copying.
- **Sequential Navigation**: Next and previous article recommendation cards within the same category.
- **Related Coverage**: Intelligent related article discovery sharing common tags and category taxonomy.

### 7. Media Gallery & Video System (Phase 07)
- **Centralized Media Library**: WebP/JPEG/PNG uploads, automated dimension extraction, mime-type validation, and ALT text enforcement.
- **Video Articles**: YouTube and external video embed integration with custom playable poster overlays.
- **Photo Stories**: Immersive photo galleries with slide navigation, full-resolution zoom, and photographer attributions.

### 8. Search, Bookmarks & Engagement (Phase 08)
- **Full-Text News Search**: Filter by keyword, category, date range, and sort by relevance or recency.
- **Reader Bookmarks**: Personal saved article collection for authenticated readers.
- **Public Discussion & Moderation**: Nested comments with anti-spam rate limiting, vulgarity filtering, and admin moderation (approve, mark spam, trash).
- **Editorial Reactions**: Interactive emotion reactions (Suka, Terkejut, Inspiratif, Sedih, dll) with IP and user deduplication.

### 9. Homepage CMS, Breaking News & Advertising (Phase 09)
- **Dynamic Section Manager**: Reorder, enable, or disable homepage sections directly from admin CMS without code edits.
- **Breaking News Bar**: Sticky ticker with emergency broadcast flag, active timeframes, and direct headline linking.
- **Ad Server & Tracking**:
  - Ad placements: `home_leaderboard`, `home_inline_1`, `article_top`, `article_inline_1`, `article_bottom`, `sidebar`.
  - Impression tracking via beacon and click counting via encrypted UUID endpoints.
  - Open-redirect protection: Strictly redirects to validated campaign destination URLs.

### 10. Newsletter, Static Pages & Site Settings (Phase 10)
- **Double Opt-In Ready Newsletter**: Subscription widget with email validation, verification tokens, and one-click unsubscribe links.
- **Subscriber Management**: Filter by status (`active`, `unconfirmed`, `unsubscribed`), export lists, and audit subscriptions.
- **Contact Us & Editorial Inbox**: Feedback form with honeypot spam protection, status badges (`new`, `read`, `replied`, `archived`), and staff internal notes.
- **Institutional CMS Pages**: Manage About Us (`/about`), Editorial Board (`/editorial`), Editorial Guidelines (`/editorial-guidelines`), Privacy Policy (`/privacy`), Terms (`/terms`), and Disclaimer (`/disclaimer`).
- **Centralized Site Settings**: General, Branding, Social Links, Editorial, and SEO configurations stored in database and cached in memory.

### 11. SEO, Analytics & Advanced Hardening (Phase 11)
- **Metadata Automation**: Dynamic `<title>`, `<meta name="description">`, and absolute canonical URLs.
- **Social Graph Protocol**: Complete Open Graph (`og:*`) and Twitter Card (`summary_large_image`) tagging.
- **Schema.org Structured Data**:
  - `NewsArticle` schema with publisher, author, datePublished, and image schemas.
  - `NewsMediaOrganization` schema with official press name, logo, and verified social handles.
  - `WebSite` schema with Sitelinks Searchbox integration.
  - `BreadcrumbList` hierarchy navigation.
- **Robots & Sitemaps**: Dynamic `/robots.txt` and XML sitemaps index (`/sitemap.xml`, `/sitemaps/articles.xml`, `/sitemaps/categories.xml`, `/sitemaps/pages.xml`).
- **Editorial Analytics**: Daily rollup engine tracking article pageviews, unique visitors, devices (mobile vs desktop), and referrers.
- **Security Headers**: HSTS, X-Content-Type-Options (`nosniff`), X-Frame-Options (`SAMEORIGIN`), X-XSS-Protection, and Referrer-Policy (`strict-origin-when-cross-origin`).

### 12. Final System Audit & Verification (Phase 12)
- Complete end-to-end regression audit across all 12 phases.
- Zero leftover prototypes or hardcoded mockups.
- Blade views decoupled from direct Eloquent model queries using View Composers and Services.
- Full idempotency across all database migrations and seeders.
- 100% green test suite (196 tests, 701 assertions).

---

## 🚀 Installation & Local Setup

### Requirements
- PHP 8.3 or higher with extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd` or `imagick`, `xml`
- Composer 2.x
- Node.js 20.x or higher & npm
- MySQL 8.0 or MariaDB 10.4+

### Quick Start

1. **Clone repository & enter directory**:
   ```bash
   git clone <repo-url> top-news
   cd top-news
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` to configure your database credentials:*
   ```env
   APP_NAME=TopNews
   APP_URL=http://topnews.test
   APP_TIMEZONE=Asia/Jakarta
   APP_LOCALE=id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=topnews
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations & Seed Foundational Data**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   *The seeder sets up roles, permissions, default institutional pages, navigation menus, ad slots, default settings, categories, and initial seed articles.*

5. **Provision the Initial Super Administrator**:
   ```bash
   php artisan topnews:make-super-admin
   ```
   *Follow the prompts to enter name, username, email, and password safely.*

6. **Build Frontend Assets**:
   ```bash
   npm run build
   # Or for active development:
   npm run dev
   ```

7. **Create Storage Symbolic Link**:
   ```bash
   php artisan storage:link
   ```

8. **Serve the Application**:
   ```bash
   php artisan serve
   ```
   *Access the platform at `http://localhost:8000` (or `http://topnews.test` in Laragon).*

---

## ⏰ Cron & Background Scheduler

TopNews utilizes Laravel's task scheduler for autonomous publishing and daily statistics compilation. In production, configure a crontab entry running every minute:

```bash
* * * * * cd /path-to-topnews && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Tasks:
| Command | Frequency | Description |
|---|---|---|
| `topnews:publish-scheduled` | Every minute | Automatically changes `scheduled` articles to `published` once their `scheduled_at` timestamp is reached. |
| `topnews:analytics-rollup` | Daily at 00:05 WIB | Computes daily pageviews, unique visitors, and device breakdown rollups. |

---

## 🧪 Testing & Quality Assurance

Run the comprehensive test suite using PHPUnit:

```bash
# Run all tests across all 12 phases
php artisan test --compact

# Run specific domain test suites
php artisan test --filter=FinalSystemAuditTest
php artisan test --filter=EditorialWorkflowTest
php artisan test --filter=SeoAndAnalyticsTest
php artisan test --filter=AdSystemTest
```

### Code Formatting
Ensure strict PSR-12 and Laravel code style:
```bash
vendor/bin/pint --format agent
```

---

## 🛡️ Production Deployment Checklist

1. [ ] Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
2. [ ] Ensure `APP_KEY` is generated and secure.
3. [ ] Run `php artisan config:cache` to compile configuration.
4. [ ] Run `php artisan route:cache` to compile route mappings.
5. [ ] Run `php artisan view:cache` to precompile Blade templates.
6. [ ] Ensure `npm run build` completed and `public/build/manifest.json` exists.
7. [ ] Ensure crontab is active for `php artisan schedule:run`.
8. [ ] Verify SSL certificate and HTTPS redirection.
9. [ ] Provision the production Super Admin account via `php artisan topnews:make-super-admin`.

---

## 📄 License & Publishing Ethics

TopNews is proprietary digital newsroom software. All editorial workflows, publication ethics, and rights are maintained in compliance with the Press Council Journalist Code of Ethics (Kode Etik Jurnalistik Dewan Pers).
