<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchDocumentation implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search the Bishal Starter Kit documentation files and return relevant documentation for a feature, configuration, command, component, or problem.';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = trim($request['query']);

        if ($query === '') {
            return json_encode([
                'success' => false,
                'message' => 'Documentation search query cannot be empty.',
                'results' => [],
            ]);
        }

        $docsPath = base_path('docs');

        if (! is_dir($docsPath)) {
            return json_encode([
                'success' => false,
                'message' => 'Documentation directory does not exist.',
                'results' => [],
            ]);
        }

        $files = glob($docsPath . '/*.md');

        $queryWords = $this->getSearchWords($query);

        $results = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if ($content === false) {
                continue;
            }

            $score = $this->calculateScore(
                $content,
                basename($file),
                $queryWords
            );

            if ($score <= 0) {
                continue;
            }

            $results[] = [
                'title' => $this->getTitle($content, basename($file)),
                'file' => basename($file),
                'score' => $score,
                'content' => $this->getRelevantContent(
                    $content,
                    $queryWords
                ),
            ];
        }

        usort(
            $results,
            fn ($a, $b) => $b['score'] <=> $a['score']
        );

        $results = array_slice($results, 0, 5);

        return json_encode([
            'success' => true,
            'query' => $query,
            'results' => $results,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema
                ->string()
                ->description(
                    'The feature, topic, error, command, or question to search for in the starter kit documentation.'
                )
                ->required(),
        ];
    }

    private function getSearchWords(string $query): array
    {
        return collect(
            preg_split('/\s+/', strtolower($query))
        )
            ->filter(fn ($word) => strlen($word) >= 2)
            ->unique()
            ->values()
            ->toArray();
    }

    private function calculateScore(
        string $content,
        string $filename,
        array $queryWords
    ): int {
        $contentLower = strtolower($content);
        $filenameLower = strtolower($filename);

        $score = 0;

        foreach ($queryWords as $word) {

            // Match in filename.
            if (str_contains($filenameLower, $word)) {
                $score += 10;
            }

            // Match in content.
            $score += substr_count($contentLower, $word);
        }

        return $score;
    }

    private function getTitle(
        string $content,
        string $filename
    ): string {
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        return ucwords(
            str_replace(
                ['-', '_', '.md'],
                [' ', ' ', ''],
                $filename
            )
        );
    }

    private function getRelevantContent(
        string $content,
        array $queryWords
    ): string {
        $contentLower = strtolower($content);

        foreach ($queryWords as $word) {

            $position = strpos($contentLower, $word);

            if ($position !== false) {

                $start = max(0, $position - 500);

                return trim(
                    substr($content, $start, 2500)
                );
            }
        }

        return substr($content, 0, 2500);
    }
}