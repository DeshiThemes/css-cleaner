<?php

namespace DeshiThemes\CssCleaner\Commands;

use Illuminate\Console\Command;
use DeshiThemes\CssCleaner\Helpers\CssHelper;

class MinifyCssCommand extends Command
{
    protected $signature = 'css:minify 
    {--show-stats : Display optimization statistics}
    {--path= : Custom path to minify (overrides config)}
    {--dry-run : Simulate without saving}';

    protected $description = '✨ Minify CSS files in output path';

    public function handle()
    {
        $path = $this->option('path') ?: config('csscleaner.output_path');
        $this->line("\n  <fg=blue>✨</> <fg=white;options=bold>STARTING CSS MINIFICATION</>");

        try {
            $results = CssHelper::minify($path, $this->option('dry-run')); // Updated this line

            $this->newLine();
            $this->line("  <fg=green>✅ MINIFICATION COMPLETE! Processed " . count($results) . " files</>");

            if ($this->option('show-stats')) {
                $totalSaved = array_sum(array_column($results, 'saved'));
                $this->line("  <fg=yellow>⇢</> <fg=white>Total space saved:</> " . $this->formatBytes($totalSaved));

                $this->table(
                    ['File', 'Original', 'Optimized', 'Saved'],
                    array_map(fn($item) => [
                        $item['file'],
                        $this->formatBytes($item['original']),
                        $this->formatBytes($item['optimized']),
                        $this->formatBytes($item['saved'])
                    ], $results)
                );
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("  💥 Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
