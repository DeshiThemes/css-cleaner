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

    protected $description = '✨ <fg=cyan>Minify CSS files</> in output path';

    public function handle()
    {
        $path = $this->option('path') ?: config('csscleaner.output_path');
        $this->line("\n  <fg=blue>┌───────────────────────────────────────────┐</>");
        $this->line("  <fg=blue>│</> <fg=cyan;options=bold>✨  MINIFYING CSS FILES</> <fg=blue>                      │</>");
        $this->line("  <fg=blue>└───────────────────────────────────────────┘</>");

        try {
            $results = CssHelper::minify($path, $this->option('dry-run'));

            $this->newLine();
            $this->line("  <fg=green>┌───────────────────────────────────────────┐</>");
            $this->line("  <fg=green>│</> <fg=white>✅  COMPLETED: </><fg=green>Processed " . count($results) . " files</>  <fg=green>│</>");
            $this->line("  <fg=green>└───────────────────────────────────────────┘</>");

            if ($this->option('show-stats')) {
                $totalSaved = array_sum(array_column($results, 'saved'));
                $reduction = round(($totalSaved / array_sum(array_column($results, 'original')) * 100), 2);

                $this->newLine();
                $this->line("  <fg=yellow>⇢</> <fg=white>Total saved:</> " . $this->formatBytes($totalSaved) . " <fg=gray>($reduction% reduction)</>");

                $this->table(
                    ['File', 'Original', 'Minified', 'Saved', 'Reduction'],
                    array_map(fn($item) => [
                        $this->truncateFilename($item['file']),
                        $this->formatBytes($item['original']),
                        $this->formatBytes($item['optimized']),
                        $this->formatBytes($item['saved']),
                        round(($item['saved'] / $item['original']) * 100) . '%'
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

    private function truncateFilename(string $filename, int $length = 30): string
    {
        if (strlen($filename) <= $length) {
            return $filename;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $availableLength = $length - strlen($extension) - 3; // Account for extension and '...'

        return substr($basename, 0, $availableLength) . '...' . $extension;
    }
}
