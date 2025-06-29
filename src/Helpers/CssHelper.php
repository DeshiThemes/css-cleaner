<?php

namespace DeshiThemes\CssCleaner\Helpers;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\{File, Log};
use Illuminate\Support\Str;

class CssHelper
{
    public static function purge(string $inputPath, string $outputPath, array $safelist, bool $dryRun = false): array
    {
        if (!File::exists($inputPath)) {
            throw new \RuntimeException("Input path does not exist: {$inputPath}");
        }

        if (empty($safelist)) {
            throw new \RuntimeException("Safelist cannot be empty");
        }

        File::ensureDirectoryExists($outputPath);

        $results = [];
        $cssFiles = collect(File::allFiles($inputPath))
            ->filter(fn($file) => strtolower($file->getExtension()) === 'css');

        $bar = self::createProgressBar($cssFiles->count(), 'Purging CSS');

        $cssFiles->each(function ($file) use ($outputPath, $safelist, $dryRun, &$results, $bar) {
            try {
                $relativePath = str_replace(public_path(), '', $file->getPath());
                $outputDir = $outputPath . $relativePath;

                if ($dryRun) {
                    $results[] = [
                        'file' => $file->getRelativePathname(),
                        'output' => $outputDir . '/' . $file->getFilename()
                    ];
                    $bar->advance();
                    return;
                }

                File::ensureDirectoryExists($outputDir);

                $command = [
                    'npx',
                    'purgecss',
                    '--css',
                    $file->getPathname(),
                    '--content',
                    implode(' ', config('csscleaner.content_paths')),
                    '--output',
                    $outputDir,
                    '--safelist',
                    implode(',', $safelist)
                ];

                $process = new Process($command);
                $process->setTimeout(600);
                $process->run();

                if (!$process->isSuccessful()) {
                    Log::error("PurgeCSS Error: " . $process->getErrorOutput());
                    $bar->clear();
                    throw new \RuntimeException("Failed to purge: " . $file->getFilename());
                }

                $results[] = [
                    'file' => $file->getRelativePathname(),
                    'output' => $outputDir . '/' . $file->getFilename()
                ];

                $bar->advance();
            } catch (\Exception $e) {
                $bar->clear();
                throw $e;
            }
        });

        $bar->finish();
        return $results;
    }

    public static function minify(string $path, bool $dryRun = false): array
    {
        self::validatePaths($path);

        if (!is_writable($path) && !$dryRun) {
            throw new \RuntimeException("Directory is not writable: {$path}");
        }

        $cssFiles = collect(File::allFiles($path))
            ->filter(fn($file) => strtolower($file->getExtension()) === 'css');

        $results = [];
        $bar = self::createProgressBar($cssFiles->count(), 'Minifying CSS');

        $cssFiles->each(function ($file) use (&$results, $bar, $dryRun) {
            try {
                $originalSize = filesize($file->getPathname());
                $css = file_get_contents($file->getPathname());

                // Minify CSS content
                $minified = preg_replace([
                    '/\/\*[\s\S]*?\*\/|[\r\n\t]+/',
                    '/\s*([\{\}:;,])\s*/',
                    '/\s+/',
                    '/;}/',
                    '/#([a-f0-9])\1([a-f0-9])\2([a-f0-9])\3/i'
                ], ['', '$1', ' ', '}', '#$1$2$3'], $css);

                // Only write if not in dry-run mode
                if (!$dryRun) {
                    // Check if file is writable
                    if (!is_writable($file->getPathname())) {
                        throw new \RuntimeException("File is not writable: " . $file->getPathname());
                    }

                    // Write minified content and verify success
                    $bytesWritten = file_put_contents($file->getPathname(), $minified);
                    if ($bytesWritten === false) {
                        throw new \RuntimeException("Failed to write minified CSS to: " . $file->getPathname());
                    }
                }

                // Record results
                $results[] = [
                    'file' => $file->getRelativePathname(),
                    'original' => $originalSize,
                    'optimized' => $dryRun ? $originalSize : filesize($file->getPathname()),
                    'saved' => $dryRun ? 0 : ($originalSize - filesize($file->getPathname()))
                ];

                $bar->advance();
            } catch (\Exception $e) {
                $bar->clear();
                throw $e;
            }
        });

        $bar->finish();
        return $results;
    }

    private static function validatePaths(...$paths): void
    {
        foreach ($paths as $path) {
            if (!File::exists($path)) {
                throw new \RuntimeException("Path does not exist: {$path}");
            }
        }
    }

    private static function createProgressBar(int $max, string $message): \Symfony\Component\Console\Helper\ProgressBar
    {
        $bar = app('console')->createProgressBar($max);
        $bar->setFormat("  <fg=cyan>➜</> {$message} [<fg=green>%bar%</>] %percent%% <fg=yellow>(%remaining% remaining)</>");
        $bar->setBarCharacter('▓');
        $bar->setEmptyBarCharacter('░');
        $bar->setProgressCharacter('✨');
        $bar->setRedrawFrequency(10);
        $bar->start();
        return $bar;
    }
}
