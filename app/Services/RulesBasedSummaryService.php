<?php

namespace App\Services;

class RulesBasedSummaryService
{
    /**
     * Generate a summary and suggested action using rules-based logic.
     */
    public function generate(string $title, string $description, string $priority, string $category): array
    {
        $summary = $this->generateSummary($title, $description, $priority, $category);
        $suggestedAction = $this->generateSuggestedAction($description, $priority, $category);

        return [
            'summary' => $summary,
            'suggested_action' => $suggestedAction,
        ];
    }

    private function generateSummary(string $title, string $description, string $priority, string $category): string
    {
        $priorityLabel = ucfirst($priority);
        $categoryLabel = str_replace('_', ' ', $category);
        
        // Extract first meaningful sentence from description (up to 100 chars)
        $shortDesc = $this->extractKeyPhrase($description);

        $templates = [
            'bug' => "{$priorityLabel}-priority bug report: {$shortDesc}",
            'feature_request' => "{$priorityLabel}-priority feature request: {$shortDesc}",
            'support' => "{$priorityLabel}-priority support request: {$shortDesc}",
            'infrastructure' => "{$priorityLabel}-priority infrastructure issue: {$shortDesc}",
            'security' => "{$priorityLabel}-priority security concern: {$shortDesc}",
        ];

        return $templates[$category] ?? "{$priorityLabel}-priority {$categoryLabel} issue: {$shortDesc}";
    }

    private function generateSuggestedAction(string $description, string $priority, string $category): string
    {
        $descLower = strtolower($description);

        // Priority-based urgency actions with detailed steps
        $urgencyActions = [
            'critical' => "1. Immediately page the on-call senior engineer and notify the team lead via Slack/PagerDuty.\n2. Create a war-room channel and gather relevant stakeholders within 15 minutes.\n3. Check application health dashboards (Grafana/Datadog) to assess blast radius and user impact.\n4. Begin initial triage: review logs, recent deployments, and infrastructure changes from the last 2 hours.\n5. Prepare a status update for stakeholders within 30 minutes with findings and ETA for resolution.",
            'high' => "1. Assign to an available engineer within the next 2 hours and set up a tracking thread in team chat.\n2. Review the issue details and reproduce the problem in a staging or development environment.\n3. Check application logs (storage/logs/laravel.log) and monitoring dashboards for related errors.\n4. Identify root cause through log correlation, code review of recent changes, and database query analysis.\n5. Implement and test a fix, then schedule deployment with appropriate review.",
            'medium' => "1. Add to the current sprint backlog and prioritize against existing work items.\n2. Assign to a team member with relevant domain expertise during next standup.\n3. Gather additional context: review related tickets, check if this is a recurring issue, and talk to the reporter.\n4. Create a technical investigation plan with clear acceptance criteria.\n5. Schedule implementation and testing within the current sprint cycle.",
            'low' => "1. Add to the product backlog with appropriate labels and categorization.\n2. Review during next sprint planning to determine if priority should be elevated.\n3. Document any workarounds available to affected users in the meantime.\n4. Link to any related issues or feature requests for context.\n5. Re-evaluate priority in 2 weeks if not yet addressed.",
        ];

        // Category-specific actions with detailed multi-step instructions
        $categoryActions = [
            'bug' => [
                'crash' => "1. Check the application error logs (storage/logs/laravel.log) for stack traces and exception details related to the crash.\n2. Reproduce the crash in a development environment with APP_DEBUG=true to capture full error context.\n3. Identify the failing component by analyzing the stack trace, and review recent git commits to that area (git log --oneline -10 -- path/to/file).\n4. Write a failing test case that reproduces the crash condition to prevent regression.\n5. Implement a fix, verify it passes the test case, and deploy to staging for validation before production release.",
                'error' => "1. Review error logs (storage/logs/laravel.log) and filter for the specific error message or exception class.\n2. Check the error frequency and pattern: is it intermittent or consistent? Use 'grep -c' to count occurrences over time.\n3. Trace the error to its source by examining the full stack trace and identifying the triggering request/input.\n4. Reproduce the error locally with the same input data and environment conditions.\n5. Fix the root cause, add appropriate error handling or validation, and write a test to cover the scenario.",
                'slow' => "1. Run performance profiling using Laravel Telescope or Debugbar to identify slow queries and bottlenecks.\n2. Check database query performance: enable query logging (DB::enableQueryLog()) and look for N+1 queries or missing indexes.\n3. Review application-level caching: verify Redis/Memcached is operational and cache hit rates are normal.\n4. Use 'php artisan route:cache' and 'php artisan config:cache' to ensure production optimizations are active.\n5. If database-related, run EXPLAIN on slow queries and add appropriate indexes; if code-related, optimize the algorithm or add caching.",
                'timeout' => "1. Identify which request or job is timing out by checking web server logs (nginx/Apache) and Laravel logs.\n2. Check server resource utilization: CPU, memory, disk I/O using 'top', 'htop', or monitoring dashboards.\n3. Review database connection pool status and check for long-running queries or deadlocks (SHOW PROCESSLIST).\n4. Verify external service dependencies are responding within expected latency (API endpoints, third-party services).\n5. Increase timeout thresholds temporarily if needed, then optimize the slow operation or implement async processing via queues.",
                'performance' => "1. Profile the application using Laravel Telescope, Blackfire, or Xdebug to identify CPU and memory hotspots.\n2. Analyze database queries: check for N+1 problems, missing indexes, and unoptimized joins using EXPLAIN.\n3. Review cache utilization: verify cache keys are being set and hit correctly (Redis MONITOR or cache stats).\n4. Check for memory leaks or excessive object instantiation in loops.\n5. Implement optimizations: add eager loading, query caching, pagination, or move heavy processing to background queues.",
                'broken' => "1. Verify the issue is occurring in production by testing the affected feature directly and checking error monitoring (Sentry/Bugsnag).\n2. Review recent deployments: check git log for the last 24-48 hours and identify any changes to related code.\n3. Compare current behavior against expected behavior documented in tests; run the relevant test suite locally.\n4. If caused by a recent deployment, consider a rollback while investigating (git revert or deploy previous tag).\n5. Fix the underlying issue, add test coverage for the broken scenario, and deploy with careful monitoring.",
                'not working' => "1. Clarify the exact expected vs. actual behavior by reproducing the issue and documenting the steps.\n2. Check application logs and browser developer console for errors or failed network requests.\n3. Verify the environment: check config values, .env settings, and service connections (database, cache, queues).\n4. Run the test suite for the affected module to identify if existing tests catch the issue: 'php artisan test --filter=ModuleName'.\n5. Trace the code path from the entry point (route/controller) through to the failing behavior and apply a fix.",
                'default' => "1. Attempt to reproduce the bug using the exact steps described in the issue report.\n2. Check application logs (storage/logs/laravel.log) for any related errors or warnings.\n3. Review recent code changes in the affected area using 'git log --oneline -20' and 'git diff'.\n4. Write a test case that demonstrates the bug (the test should fail before the fix).\n5. Implement a fix, verify the test passes, and check for any side effects in related functionality.",
            ],
            'feature_request' => [
                'default' => "1. Review the feature request with the product team to clarify requirements, acceptance criteria, and user impact.\n2. Research existing solutions: check if similar functionality exists in the codebase or available packages (search Packagist/npm).\n3. Create a technical design document outlining the implementation approach, database changes, and API contracts.\n4. Break the feature into smaller deliverable tasks with clear estimates (story points or hours).\n5. Schedule a design review with the team, then add tasks to the sprint backlog with proper sequencing and dependencies.",
            ],
            'support' => [
                'access' => "1. Verify the user's current role and permissions in the database: check the users, roles, and permissions tables.\n2. Review the access control configuration in config/auth.php and any Gate/Policy definitions.\n3. Check if the user's account is active and not locked (verify email_verified_at, banned_at fields).\n4. Test the same action with an admin account to confirm the feature works correctly with proper permissions.\n5. If permissions are incorrect, update the user's role/permissions and notify them; if it's a bug in the authorization logic, escalate to development.",
                'login' => "1. Check the authentication logs for failed login attempts: review storage/logs/laravel.log for 'auth' related entries.\n2. Verify the user's account status in the database: check if account is active, not locked, and email is verified.\n3. Test the login flow in an incognito browser to rule out cached/stale session issues.\n4. If using OAuth/SSO, verify the provider configuration in config/services.php and check provider status.\n5. Reset the user's password if needed, clear any rate-limiting blocks (check cache for throttle keys), and confirm successful login.",
                'password' => "1. Initiate a password reset through the standard reset flow and verify the reset email is delivered (check mail logs).\n2. Verify the user's email address is correct and the account exists in the database.\n3. Check for rate limiting on password reset attempts (Laravel throttles to 1 per minute by default).\n4. If the reset link doesn't work, check the APP_URL and password reset token expiration in config/auth.php.\n5. Confirm the user can log in with the new password; if issues persist, manually set a temporary password via tinker and have them reset.",
                'integration' => "1. Identify which integration or API is failing and check its current status (status page, health endpoint).\n2. Verify API credentials and configuration in .env and config/services.php are correct and not expired.\n3. Test the API connection directly using curl or Postman to isolate whether the issue is in our code or the external service.\n4. Review recent changes to the integration code and check if the API version has been updated or deprecated.\n5. If the external service is down, implement graceful degradation; if it's our code, fix the integration and add error handling.",
                'api' => "1. Identify the specific API endpoint failing and review the error response (status code, body).\n2. Check API authentication: verify tokens, API keys, or OAuth credentials are valid and not expired.\n3. Test the API call in isolation using curl or Postman with the same parameters and headers.\n4. Review API rate limits and check if we're being throttled (look for 429 responses in logs).\n5. Fix the issue (update credentials, adjust request format, handle rate limiting) and add monitoring for the API health.",
                'connect' => "1. Identify which service connection is failing (database, Redis, external API, etc.) and check its status.\n2. Verify network connectivity: test with 'ping', 'telnet', or 'curl' to the target host and port.\n3. Review connection configuration in .env: check host, port, credentials, and SSL/TLS settings.\n4. Check for firewall rules, security groups, or network policies that may be blocking the connection.\n5. Restore connectivity by fixing configuration, whitelisting IPs, or restarting the target service, then verify the application reconnects.",
                'default' => "1. Acknowledge receipt of the support request and set expectations for response time based on priority.\n2. Gather additional details from the reporter: exact steps to reproduce, screenshots, browser/device info, and timestamps.\n3. Check if this is a known issue by searching existing tickets and documentation.\n4. Attempt to reproduce the issue in a test environment with the provided information.\n5. Provide a resolution or workaround, and escalate to engineering if it requires a code fix.",
            ],
            'infrastructure' => [
                'deploy' => "1. Review the deployment pipeline logs (CI/CD dashboard) to identify where the deployment failed or what changed.\n2. Check the deployment diff: compare the current release with the previous one using 'git diff' between tags or commits.\n3. Verify configuration changes: review .env files, config maps, and environment variables for discrepancies.\n4. If the deployment caused issues, roll back to the previous known-good version immediately.\n5. Fix the deployment issue (update configs, fix broken migrations, resolve dependency conflicts), test in staging, then redeploy.",
                'server' => "1. Check server health metrics: CPU, memory, disk space, and network I/O via monitoring dashboards or 'top'/'df -h'/'free -m'.\n2. Review system logs: check /var/log/syslog, nginx/apache error logs, and PHP-FPM logs for errors.\n3. Verify all critical services are running: web server, PHP-FPM, database, Redis, queue workers (systemctl status).\n4. Check for recent system changes: package updates, config changes, cron job modifications.\n5. Resolve the issue (restart services, free disk space, scale resources) and set up alerts to prevent recurrence.",
                'database' => "1. Check database server health: connection count, CPU/memory usage, disk space, and replication lag.\n2. Review slow query log and identify expensive queries (SHOW PROCESSLIST, pg_stat_activity for PostgreSQL).\n3. Verify connection pool status: check max_connections setting vs. active connections; review pool configuration.\n4. Check for table locks, deadlocks, or long-running transactions that may be blocking other queries.\n5. Optimize or kill problematic queries, increase connection limits if needed, and add indexes for frequent slow queries.",
                'scaling' => "1. Review current resource utilization across all instances: CPU, memory, network, and request queues.\n2. Check auto-scaling configuration and verify scaling policies are triggering correctly at defined thresholds.\n3. Identify the bottleneck: is it application servers, database, cache, or external dependencies?\n4. Implement immediate relief: manually scale up instances, increase connection pool sizes, or enable additional caching.\n5. Plan long-term capacity: review traffic projections, optimize resource-heavy operations, and update scaling thresholds.",
                'load' => "1. Check current traffic levels and compare against normal baselines using monitoring tools (Grafana/CloudWatch).\n2. Identify which component is under strain: web servers, database, cache layer, or queue workers.\n3. Review load balancer health checks and ensure traffic is distributed evenly across healthy instances.\n4. Scale up affected resources immediately: add instances, increase database IOPS, or boost cache memory.\n5. Investigate root cause (traffic spike, bot traffic, inefficient code) and implement appropriate mitigation.",
                'capacity' => "1. Audit current resource usage vs. provisioned capacity across all infrastructure components.\n2. Identify the constraint: disk space, database connections, memory, CPU, or network bandwidth.\n3. Check growth trends over the past 30 days to predict when capacity will be fully exhausted.\n4. Implement immediate relief: clean up old data, archive logs, increase resource allocations.\n5. Plan capacity expansion: upgrade instance types, add storage, or implement data lifecycle management policies.",
                'default' => "1. Check system monitoring dashboards (Grafana/Datadog/CloudWatch) for anomalies in key metrics.\n2. Review recent infrastructure changes: deployments, config changes, scaling events, and maintenance windows.\n3. Verify all critical services are healthy: run health check endpoints and verify service dependencies.\n4. Check alerting rules to understand why this wasn't caught earlier and adjust thresholds if needed.\n5. Document findings, implement a fix or workaround, and create follow-up tasks for permanent resolution.",
            ],
            'security' => [
                'vulnerability' => "1. Assess the vulnerability severity using CVSS scoring and determine which systems/data are affected.\n2. Check if the vulnerability is actively being exploited: review access logs, WAF logs, and intrusion detection alerts.\n3. Identify available patches or mitigations: check vendor advisories, CVE databases, and package changelogs.\n4. Apply the patch in staging first, run security regression tests, then deploy to production with monitoring.\n5. Document the vulnerability, notify affected stakeholders per compliance requirements, and update security scanning rules.",
                'breach' => "1. IMMEDIATELY initiate the incident response protocol: isolate affected systems and preserve forensic evidence (logs, memory dumps).\n2. Identify the breach vector: review access logs, authentication events, and network traffic for unauthorized activity.\n3. Revoke compromised credentials: rotate all API keys, passwords, and tokens that may have been exposed.\n4. Assess the scope of data exposure: determine what data was accessed and which users/customers are affected.\n5. Notify security leadership and legal/compliance team; prepare breach notification per regulatory requirements (GDPR, etc.).",
                'unauthorized' => "1. Review access logs immediately to identify the unauthorized activity: who, when, what, and from where (IP, user agent).\n2. Revoke suspicious sessions and tokens: invalidate all active sessions for the affected accounts.\n3. Check for privilege escalation: review role assignments, permission changes, and admin actions in the audit log.\n4. Block the source IP addresses and strengthen authentication (enable 2FA, add IP allowlisting if appropriate).\n5. Conduct a full access audit: review all actions taken by the unauthorized actor and remediate any changes they made.",
                'data' => "1. Identify what data may have been exposed: review access logs and data flow to determine scope.\n2. Immediately restrict access to the affected data source and revoke any exposed API keys or credentials.\n3. Assess the sensitivity of exposed data: PII, financial, health records, or credentials require specific response procedures.\n4. Enable additional monitoring on the affected systems: set up alerts for unusual data access patterns.\n5. Notify the Data Protection Officer and prepare incident documentation; implement encryption or access controls to prevent recurrence.",
                'leak' => "1. Confirm the data leak: identify what data was exposed, where it was found, and how it was leaked.\n2. Remove or request removal of leaked data from public sources immediately (GitHub, paste sites, etc.).\n3. Rotate all credentials, API keys, or secrets that may have been included in the leak.\n4. Trace the leak source: review commit history, access logs, and sharing permissions to find how data was exposed.\n5. Implement preventive measures: add secret scanning to CI/CD, review .gitignore rules, and conduct security awareness training.",
                'exposure' => "1. Assess what has been exposed and its sensitivity level (public, internal, confidential, restricted).\n2. Immediately restrict access: remove public endpoints, add authentication, or take affected services offline.\n3. Review access logs to determine if the exposed data/service was accessed by unauthorized parties.\n4. Fix the exposure: update access controls, add authentication, correct misconfigured firewall rules.\n5. Scan for similar exposures across the infrastructure and implement automated checks to prevent recurrence.",
                'default' => "1. Escalate to the security team immediately with all available details about the concern.\n2. Gather evidence: collect relevant logs, screenshots, and timestamps without modifying the affected systems.\n3. Assess the potential impact: what systems, data, or users could be affected if this is a real threat?\n4. Implement precautionary measures: increase monitoring, restrict access if needed, and enable additional logging.\n5. Coordinate with security team for a full investigation and await their guidance before making system changes.",
            ],
        ];

        // Try to match keyword in description
        if (isset($categoryActions[$category])) {
            foreach ($categoryActions[$category] as $keyword => $action) {
                if ($keyword !== 'default' && str_contains($descLower, $keyword)) {
                    return $action;
                }
            }
            // Use category default
            if (isset($categoryActions[$category]['default'])) {
                return $categoryActions[$category]['default'];
            }
        }

        // Fall back to priority-based action
        return $urgencyActions[$priority] ?? "1. Review the issue details and assess the impact on users and systems.\n2. Assign to the most appropriate team member based on the issue domain.\n3. Gather additional context from the reporter and related documentation.\n4. Investigate the root cause and develop a resolution plan.\n5. Implement the fix, verify it resolves the issue, and communicate the resolution to stakeholders.";
    }

    private function extractKeyPhrase(string $description): string
    {
        // Get first sentence or first 100 characters
        $firstSentence = strtok($description, '.!?');
        if (strlen($firstSentence) > 100) {
            return substr($firstSentence, 0, 97) . '...';
        }
        return trim($firstSentence);
    }
}
