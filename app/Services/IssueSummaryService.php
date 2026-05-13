<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IssueSummaryService
{
    private RulesBasedSummaryService $fallbackService;

    public function __construct(RulesBasedSummaryService $fallbackService)
    {
        $this->fallbackService = $fallbackService;
    }

    /**
     * Generate summary and suggested action for an issue.
     * Uses OpenAI if available, otherwise falls back to rules-based engine.
     */
    public function generate(string $title, string $description, string $priority, string $category): array
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            Log::info('OpenAI API key not configured, using rules-based fallback.');
            return $this->fallbackService->generate($title, $description, $priority, $category);
        }

        try {
            return $this->generateWithOpenAI($title, $description, $priority, $category, $apiKey);
        } catch (\Exception $e) {
            Log::warning('OpenAI API call failed, falling back to rules-based engine.', [
                'error' => $e->getMessage(),
            ]);
            return $this->fallbackService->generate($title, $description, $priority, $category);
        }
    }

    private function generateWithOpenAI(string $title, string $description, string $priority, string $category, string $apiKey): array
    {
        $prompt = $this->buildPrompt($title, $description, $priority, $category);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a senior support ticket analyst and technical troubleshooter. Your job is to analyze support/operations issues and provide: (1) a clear summary of the problem, and (2) detailed, step-by-step instructions on how to investigate and fix the issue. Your suggested actions should include specific tools, commands, or techniques to use, ordered by what to check first, second, third, etc. Provide 3-5 numbered steps that are practical and actionable. Always respond in valid JSON format with exactly two keys: "summary" and "suggested_action". The suggested_action value should contain numbered steps separated by newlines.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI API returned status: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content');
        $parsed = json_decode($content, true);

        if (!$parsed || !isset($parsed['summary']) || !isset($parsed['suggested_action'])) {
            throw new \RuntimeException('Failed to parse OpenAI response as expected JSON.');
        }

        return [
            'summary' => $parsed['summary'],
            'suggested_action' => $parsed['suggested_action'],
        ];
    }

    private function buildPrompt(string $title, string $description, string $priority, string $category): string
    {
        return <<<PROMPT
Analyze this support issue and provide:
1. A concise 1-2 sentence summary of the core problem
2. Detailed step-by-step instructions (3-5 numbered steps) on how to investigate and fix this issue. Include:
   - Specific commands, tools, or techniques to use
   - What to check first, second, third
   - What the likely root cause might be
   - How to verify the fix works

Issue Details:
- Title: {$title}
- Description: {$description}
- Priority: {$priority}
- Category: {$category}

Respond in JSON format:
{"summary": "...", "suggested_action": "1. First step...\n2. Second step...\n3. Third step..."}
PROMPT;
    }
}
