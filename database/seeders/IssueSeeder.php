<?php

namespace Database\Seeders;

use App\Models\Issue;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    public function run(): void
    {
        $issues = [
            [
                'title' => 'Login page returns 500 error after password reset',
                'description' => 'After completing the password reset flow, users are redirected to the login page which returns a 500 Internal Server Error. This affects all users who have recently reset their passwords. The error started appearing after last Friday\'s deployment.',
                'priority' => 'critical',
                'category' => 'bug',
                'status' => 'open',
                'summary' => 'Critical bug: Login page crashes with 500 error post-password reset, affecting all reset users since last deployment.',
                'suggested_action' => 'Immediately assign to senior engineer and notify team lead.',
                'is_escalated' => true,
            ],
            [
                'title' => 'Add dark mode toggle to user settings',
                'description' => 'Users have requested a dark mode option in the application. This should be a toggle in the user settings page that persists their preference. Consider using CSS variables for theme switching and storing preference in the user profile.',
                'priority' => 'low',
                'category' => 'feature_request',
                'status' => 'open',
                'summary' => 'Low-priority feature request: Add persistent dark mode toggle to user settings using CSS variables.',
                'suggested_action' => 'Add to backlog for future sprint consideration.',
                'is_escalated' => false,
            ],
            [
                'title' => 'Database connection pool exhaustion during peak hours',
                'description' => 'The database connection pool is being exhausted between 9-11 AM daily, causing timeout errors for approximately 15% of requests. Current pool size is 50 connections. Server monitoring shows connections are not being released properly by the ORM.',
                'priority' => 'high',
                'category' => 'infrastructure',
                'status' => 'in_progress',
                'summary' => 'High-priority infrastructure issue: DB connection pool exhaustion during peak hours causing 15% request timeouts.',
                'suggested_action' => 'Review database performance metrics and connection pool status.',
                'is_escalated' => false,
            ],
            [
                'title' => 'Unauthorized access attempt detected on admin panel',
                'description' => 'Security monitoring detected multiple unauthorized access attempts on the admin panel from IP range 45.33.x.x. Over 200 failed login attempts in the past hour. No successful breaches confirmed yet, but the rate-limiting appears insufficient.',
                'priority' => 'critical',
                'category' => 'security',
                'status' => 'open',
                'summary' => 'Critical security alert: Brute-force attack detected on admin panel from suspicious IP range with 200+ attempts.',
                'suggested_action' => 'Initiate incident response protocol immediately.',
                'is_escalated' => true,
            ],
            [
                'title' => 'Customer cannot export reports to CSV',
                'description' => 'A customer reported that the CSV export button on the reports page does nothing when clicked. The browser console shows a JavaScript error related to the file download handler. This is affecting their monthly reporting workflow.',
                'priority' => 'medium',
                'category' => 'support',
                'status' => 'open',
                'summary' => 'Medium-priority support issue: CSV export button non-functional due to JavaScript error, blocking customer reporting.',
                'suggested_action' => 'Acknowledge receipt and gather additional details from the reporter.',
                'is_escalated' => false,
            ],
            [
                'title' => 'Implement two-factor authentication',
                'description' => 'As part of our security roadmap, we need to implement two-factor authentication (2FA) using TOTP. This should integrate with Google Authenticator and similar apps. Requirements include setup wizard, backup codes, and admin enforcement options.',
                'priority' => 'high',
                'category' => 'feature_request',
                'status' => 'in_progress',
                'summary' => 'High-priority feature: Implement TOTP-based 2FA with Google Authenticator support and admin controls.',
                'suggested_action' => 'Assign to available engineer within the next 2 hours.',
                'is_escalated' => false,
            ],
            [
                'title' => 'SSL certificate expiring in 7 days',
                'description' => 'The SSL certificate for api.example.com is expiring on the 19th. Auto-renewal through Let\'s Encrypt failed due to a DNS validation issue. Manual intervention is required to renew the certificate before it expires and causes service disruption.',
                'priority' => 'high',
                'category' => 'infrastructure',
                'status' => 'open',
                'summary' => 'High-priority: SSL certificate for api.example.com expiring in 7 days, auto-renewal failed.',
                'suggested_action' => 'Check system monitoring dashboards and recent changes.',
                'is_escalated' => false,
                'created_at' => now()->subHours(30),
            ],
            [
                'title' => 'Mobile app crashes on iOS 17 when uploading images',
                'description' => 'Users on iOS 17 devices report that the app crashes when trying to upload images larger than 5MB. The crash occurs in the image compression library. Android devices are not affected. Crash reports available in Crashlytics.',
                'priority' => 'high',
                'category' => 'bug',
                'status' => 'open',
                'summary' => 'High-priority bug: iOS 17 app crash on image upload >5MB, caused by compression library issue.',
                'suggested_action' => 'Investigate crash logs and attempt to reproduce in staging environment.',
                'is_escalated' => false,
                'created_at' => now()->subHours(26),
            ],
            [
                'title' => 'Update API rate limiting documentation',
                'description' => 'The API documentation does not reflect the new rate limits that were implemented last sprint. We need to update the docs to show the current limits: 100 requests/minute for free tier, 1000 requests/minute for premium. Also add examples of rate limit headers.',
                'priority' => 'low',
                'category' => 'support',
                'status' => 'resolved',
                'summary' => 'Low-priority documentation update: API rate limit docs outdated after last sprint changes.',
                'suggested_action' => 'Add to backlog for future sprint consideration.',
                'is_escalated' => false,
            ],
            [
                'title' => 'Memory leak in background job processor',
                'description' => 'The background job processor is gradually consuming more memory over time, reaching the container limit after approximately 8 hours of operation. The worker then gets OOM-killed and restarts, causing a brief gap in job processing. Heap dumps suggest the issue is in the email templating engine.',
                'priority' => 'medium',
                'category' => 'bug',
                'status' => 'in_progress',
                'summary' => 'Medium-priority bug: Memory leak in job processor via email templating engine, causes OOM after 8 hours.',
                'suggested_action' => 'Attempt to reproduce the bug and document steps clearly.',
                'is_escalated' => false,
            ],
            [
                'title' => 'Deploy CI/CD pipeline for staging environment',
                'description' => 'Set up a continuous deployment pipeline for the staging environment using GitHub Actions. Should include automated testing, Docker image build, and deployment to the staging Kubernetes cluster. Include Slack notifications for deployment status.',
                'priority' => 'medium',
                'category' => 'infrastructure',
                'status' => 'resolved',
                'summary' => 'Medium-priority infrastructure: Set up GitHub Actions CI/CD pipeline for staging with Docker and K8s.',
                'suggested_action' => 'Review deployment pipeline and check for configuration changes.',
                'is_escalated' => false,
            ],
            [
                'title' => 'XSS vulnerability in user profile bio field',
                'description' => 'A cross-site scripting vulnerability was discovered in the user profile bio field. Unescaped HTML is rendered when viewing other users\' profiles. This could allow attackers to steal session cookies or redirect users to malicious sites.',
                'priority' => 'critical',
                'category' => 'security',
                'status' => 'in_progress',
                'summary' => 'Critical security vulnerability: XSS in profile bio field allows session theft and malicious redirects.',
                'suggested_action' => 'Conduct immediate security assessment and patch evaluation.',
                'is_escalated' => true,
            ],
            [
                'title' => 'Add bulk import feature for customer data',
                'description' => 'Sales team needs the ability to bulk import customer records via CSV upload. Should support mapping columns, validation of required fields, duplicate detection, and a preview step before committing. Target is up to 10,000 records per import.',
                'priority' => 'medium',
                'category' => 'feature_request',
                'status' => 'open',
                'summary' => 'Medium-priority feature: CSV bulk import for customer data with validation, dedup, and preview.',
                'suggested_action' => 'Review requirements with product team and estimate effort.',
                'is_escalated' => false,
            ],
            [
                'title' => 'User unable to access shared workspace',
                'description' => 'A user reports they cannot access a shared workspace that they were previously a member of. Their permissions appear correct in the admin panel. Clearing browser cache and cookies did not resolve the issue. Other workspace members can access it fine.',
                'priority' => 'low',
                'category' => 'support',
                'status' => 'closed',
                'summary' => 'Low-priority support: User locked out of shared workspace despite correct permissions.',
                'suggested_action' => 'Verify user permissions and check access control settings.',
                'is_escalated' => false,
            ],
        ];

        foreach ($issues as $issueData) {
            Issue::create($issueData);
        }
    }
}
