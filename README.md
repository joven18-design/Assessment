 Issue Intake & Smart Summary System

A production-quality issue tracking system with AI-powered summary generation and intelligent escalation rules. Built to demonstrate clean backend architecture, sensible data handling, and practical AI integration.

 Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                    Web Browser / API Client              │
├─────────────────────────────────────────────────────────┤
│              Laravel Application (PHP 8.2+)              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Web Routes  │  │  API Routes  │  │   Artisan    │  │
│  │  (Blade UI)  │  │  (JSON API)  │  │  Commands    │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
│         │                  │                  │          │
│  ┌──────▼──────────────────▼──────────────────▼───────┐ │
│  │              Service Layer                          │ │
│  │  ┌─────────────────┐  ┌────────────────────────┐   │ │
│  │  │ IssueSummaryService │  │  EscalationService  │   │ │
│  │  │  (OpenAI + Fallback) │  │  (Business Rules)  │   │ │
│  │  └─────────────────┘  └────────────────────────┘   │ │
│  └────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────┐ │
│  │         Eloquent ORM / Issue Model                  │ │
│  └──────────────────────┬─────────────────────────────┘ │
├─────────────────────────┼───────────────────────────────┤
│              SQLite Database                             │
└─────────────────────────────────────────────────────────┘
```

 Tech Stack

| Layer | Technology | Rationale |
|-------|-----------|-----------|
| Backend | Laravel (PHP 8.2+) | Mature, well-structured framework with excellent ORM, validation, and service container |
| Database | SQLite | Zero-config, portable, perfect for demo. Schema is fully relational and compatible with PostgreSQL/MySQL via one .env change |
| AI/Automation | OpenAI GPT API | Produces intelligent summaries and next-action suggestions with graceful rules-based fallback |
| Frontend | Blade + Tailwind CSS (CDN) | No build step, server-rendered, immediately testable |
| API | RESTful JSON | Clean, predictable endpoints for programmatic access |

 Setup Instructions

 Prerequisites
- PHP 8.2+ with extensions: curl, mbstring, openssl, pdo_sqlite, sqlite3
- Composer

 Quick Start

```bash
 1. Clone or extract the project
cd c:\test

 2. Install dependencies
composer install

 3. Configure environment
cp .env.example .env
php artisan key:generate

 4. (Optional) Add your OpenAI API key to .env
 OPENAI_API_KEY=sk-your-key-here
 If omitted, the system uses the rules-based fallback automatically.

 5. Create database and run migrations
php artisan migrate

 6. Seed sample data (14 diverse issues)
php artisan db:seed

 7. Start the development server
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

 API Documentation

 Base URL: `http://127.0.0.1:8000/api`

 Create Issue
```bash
curl -X POST http://127.0.0.1:8000/api/issues \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Payment processing timeout",
    "description": "Users are experiencing timeouts when processing payments via Stripe. The error occurs after 30 seconds of waiting.",
    "priority": "high",
    "category": "bug"
  }'
```

 List Issues (with filters)
```bash
 All issues
curl http://127.0.0.1:8000/api/issues

 Filter by status
curl http://127.0.0.1:8000/api/issues?status=open

 Filter by category and priority
curl http://127.0.0.1:8000/api/issues?category=bug&priority=critical

 Escalated issues only
curl http://127.0.0.1:8000/api/issues?escalated=1
```

 View Single Issue
```bash
curl http://127.0.0.1:8000/api/issues/1
```

 Update Issue
```bash
curl -X PUT http://127.0.0.1:8000/api/issues/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_progress",
    "priority": "critical"
  }'
```

 Error Response Example (Validation)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": ["Issue title is required."],
    "description": ["Issue description must be at least 10 characters."],
    "priority": ["Priority must be one of: low, medium, high, critical"]
  }
}
```

 Web Interface

| Page | URL | Description |
|------|-----|-------------|
| Issue List | `/issues` | Filterable list with priority/status badges and escalation indicators |
| Create Issue | `/issues/create` | Form to submit new issues |
| Issue Detail | `/issues/{id}` | Full detail view with AI summary and suggested action |
| Edit Issue | `/issues/{id}/edit` | Update issue fields and status |

 AI/Automation Layer

 How It Works
1. When an issue is created (or updated with new title/description), the system calls OpenAI's GPT API.
2. The AI generates a concise 1-2 sentence summary and a specific suggested next action.
3. If the OpenAI API key is not configured or the API call fails, the system automatically falls back to a rules-based engine.

 Rules-Based Fallback
The fallback engine uses:
- Category + priority matrix to generate templated summaries
- Keyword pattern matching on the description to suggest relevant next actions
- Categories have specific keyword-action mappings (e.g., "crash" in a bug → "Investigate crash logs")

 Configuration
Set `OPENAI_API_KEY` in `.env` to enable AI summaries. Leave blank to use the rules-based fallback.

 Business Logic: Escalation Rules

| Rule | Condition | Behavior |
|------|-----------|----------|
| 1 | Priority = Critical | Auto-escalated immediately on creation |
| 2 | Category = Security | Always escalated regardless of priority |
| 3 | Priority = High + Status = Open + Age > 24h | Flagged by scheduled command |

 Scheduled Escalation Check
```bash
php artisan issues:check-escalation
```
This command checks all high-priority open issues and escalates those older than 24 hours. In production, this would be scheduled via Laravel's task scheduler (e.g., hourly cron).

 Database Schema

| Column | Type | Description |
|--------|------|-------------|
| id | integer (PK) | Auto-increment primary key |
| title | string(255) | Issue title |
| description | text | Detailed description |
| priority | string | low, medium, high, critical |
| category | string | bug, feature_request, support, infrastructure, security |
| status | string | open, in_progress, resolved, closed |
| summary | text (nullable) | AI-generated short summary |
| suggested_action | text (nullable) | AI-generated next action |
| is_escalated | boolean | Whether the issue has been flagged for escalation |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

 Why SQLite?
- Zero configuration: No database server to install or configure
- Portable: Single file, easy to share and demo
- Fully relational: Supports all the SQL features this schema needs
- Easy to swap: Change `DB_CONNECTION` in `.env` to `pgsql` or `mysql` for production

 Database Alternatives

While this project uses SQLite by default for zero-config simplicity, Laravel's database abstraction layer makes it easy to swap to any of these alternatives by changing the `DB_CONNECTION` value in `.env`:

| Database | Best For | Configuration |
|----------|----------|---------------|
| SQLite | Development, demos, small deployments | `DB_CONNECTION=sqlite` (default) |
| MySQL / MariaDB | Production web applications with moderate to high traffic | `DB_CONNECTION=mysql` + set DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD |
| PostgreSQL | Complex queries, JSON data, full-text search, enterprise deployments | `DB_CONNECTION=pgsql` + set DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD |
| SQL Server | Enterprise environments with Microsoft stack integration | `DB_CONNECTION=sqlsrv` + set DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD |

All migrations and queries in this project are database-agnostic — no raw SQL or SQLite-specific features are used. Switching databases requires only:
1. Update `.env` with the new connection details
2. Ensure the target database server is running
3. Run `php artisan migrate` to create the schema
4. Optionally run `php artisan db:seed` to populate sample data

For production deployments, PostgreSQL or MySQL are recommended for their concurrency handling, replication support, and robust tooling ecosystem.

 Project Structure

```
app/
├── Console/Commands/
│   └── CheckEscalation.php         Artisan command for time-based escalation
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── IssueController.php   RESTful API controller
│   │   └── Web/
│   │       └── IssueController.php   Web/Blade controller
│   └── Requests/
│       ├── StoreIssueRequest.php     Creation validation
│       └── UpdateIssueRequest.php    Update validation
├── Models/
│   └── Issue.php                     Eloquent model with scopes
└── Services/
    ├── EscalationService.php         Business rule evaluation
    ├── IssueSummaryService.php       AI orchestrator (OpenAI + fallback)
    └── RulesBasedSummaryService.php  Deterministic fallback engine
database/
├── database.sqlite                   SQLite database file
├── migrations/
│   └── ..._create_issues_table.php   Issues table schema
└── seeders/
    ├── DatabaseSeeder.php
    └── IssueSeeder.php               14 sample issues
resources/views/
├── layouts/app.blade.php             Base layout with Tailwind CDN
└── issues/
    ├── index.blade.php               Issue list with filters
    ├── create.blade.php              New issue form
    ├── show.blade.php                Issue detail view
    └── edit.blade.php                Edit issue form
```

 What I Would Improve With More Time

1. Authentication & Authorization: Add user accounts, role-based access (admin vs reporter), and issue ownership.
2. Queued AI Processing: Move OpenAI calls to a background queue (Laravel Jobs) to avoid blocking the request cycle.
3. Comprehensive Test Suite: Unit tests for services, feature tests for API endpoints, and browser tests for the UI.
4. Pagination: Add cursor-based pagination for the issue list to handle large datasets efficiently.
5. Real-time Updates: WebSocket integration for live escalation notifications and status updates.
6. Caching: Cache AI summaries and implement rate limiting on the OpenAI integration.
7. Docker Setup: Containerize with Docker Compose for consistent environments across team members.
8. Audit Trail: Track all changes to issues with a polymorphic activity log.
9. Search: Full-text search across issue titles and descriptions.
10. Metrics Dashboard: Charts showing issue volume by category, resolution times, and escalation rates.
