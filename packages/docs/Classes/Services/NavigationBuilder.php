<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Symfony\Component\Yaml\Yaml;

class NavigationBuilder
{
    public function buildNavigation(string $baseDir, string $navFile): array
    {
        $allDocs = $this->scanDocs($baseDir);

        if (!file_exists($navFile)) {
            throw new \Exception('navFile not found', 1757843323);
        }

        $navConfig = Yaml::parseFile($navFile);
        $navigation = [];

        foreach ($navConfig as $section) {
            $group = [
                'title' => $section['group'] ?? null,
                'items' => [],
            ];

            foreach ($section['items'] as $slug) {
                if (isset($allDocs[$slug])) {
                    $group['items'][] = $allDocs[$slug];
                    unset($allDocs[$slug]);
                }
            }

            $navigation[] = $group;
        }

        return $navigation;
    }

    private function scanDocs(string $baseDir): array
    {
        $docs = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $relPath = str_replace($baseDir, '', $file->getPathname());
            $slug = str_replace('.md', '', $relPath);
            $title = $this->extractTitle($file->getPathname());

            $docs[$slug] = [
                'slug' => '/' . $slug,
                'title' => $title,
            ];
        }

        return $docs;
    }

    private function extractTitle(string $filePath): string
    {
        $lines = file($filePath);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '# ')) {
                return trim(ltrim($line, '# '));
            }
        }
        return basename($filePath, '.md');
    }

    private function parseFrontmatter(string $content): array
    {
        if (preg_match('/^---(.*?)---/s', $content, $matches)) {
            $meta = Yaml::parse($matches[1]);
            $body = preg_replace('/^---(.*?)---/s', '', $content, 1);
            return [$meta, trim($body)];
        }

        return [[], $content];
    }
}
