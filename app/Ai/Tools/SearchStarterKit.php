<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchStarterKit implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search Laravel documentation for information about Laravel starter kits, installation, configuration, authentication, Vue, React, Livewire, and related Laravel features.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request['value'];

        // Example: fetch Laravel documentation
        $response = Http::timeout(10)
            ->get('https://laravel.com/docs/starter-kits');

        if (! $response->successful()) {
            return 'Unable to fetch Laravel documentation.';
        }

        $html = $response->body();

        // Remove HTML tags
        $text = strip_tags($html);

        // Clean excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Find relevant content around the search query
        $position = stripos($text, $query);

        if ($position === false) {
            return "No documentation content found for: {$query}";
        }

        $start = max(0, $position - 1500);

        return substr($text, $start, 3000);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema
                ->string()
                ->description('The Laravel documentation topic or question to search for.')
                ->required(),
        ];
    }
}