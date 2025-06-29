<?php

namespace DeshiThemes\CssCleaner\Commands;

use Illuminate\Console\Command;
use DeshiThemes\CssCleaner\Helpers\CssHelper;

class OptimizeCssCommand extends Command
{
    protected $signature = 'css:optimize 
                           {--show-stats : Show optimization statistics}
                           {--dry-run : Simulate without saving}';

    protected $description = '🚀 <fg=magenta>Optimize CSS</> (Purge + Minify)';

    public function handle()
    {
        $this->displayWelcome();

        try {
            // Purge
            $this->newLine();
            $this->line('  <fg=magenta>🪄</> <fg=white;options=bold>PURGING UNUSED CSS</>');
            $purgeResults = CssHelper::purge(
                config('csscleaner.css_path'),
                config('csscleaner.output_path'),
                config('csscleaner.safelist'),
                $this->option('dry-run')
            );

            // Minify
            $this->newLine();
            $this->line('  <fg=magenta>✨</> <fg=white;options=bold>MINIFYING CSS</>');
            $minifyResults = CssHelper::minify(
                config('csscleaner.output_path'),
                $this->option('dry-run')
            );

            $this->displayResults($purgeResults, $minifyResults);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->displayError($e);
            return self::FAILURE;
        }
    }

    private function displayWelcome(): void
    {
        $this->line('');
        $this->line('  <fg=magenta>╭──────────────────────────────────────────────╮</>');
        $this->line('  <fg=magenta>│</>  <fg=cyan;options=bold>🚀  LARAVEL CSS OPTIMIZER</> <fg=magenta>                     │</>');
        $this->line('  <fg=magenta>│</>  <fg=yellow>By DeshiThemes</> <fg=magenta>                                   │</>');
        $this->line('  <fg=magenta>╰──────────────────────────────────────────────╯</>');
        $this->line('');
    }

    private function displayResults(array $purgeResults, array $minifyResults): void
    {
        $totalSaved = array_sum(array_column($minifyResults, 'saved'));
        $totalReduction = $this->calculateReduction($minifyResults);

        $this->newLine();
        $this->line('  <fg=green>╭──────────────────────────────────────────────╮</>');
        $this->line('  <fg=green>│</> <fg=white;options=bold>✅  OPTIMIZATION COMPLETE!</> <fg=green>                   │</>');
        $this->line('  <fg=green>├──────────────────────────────────────────────┤</>');
        $this->line("  <fg=green>│</> <fg=white>Files processed:</> " . str_pad(count($purgeResults), 13, ' ', STR_PAD_LEFT) . "  <fg=green>│</>");
        $this->line("  <fg=green>│</> <fg=white>Space saved:</> " . str_pad($this->formatBytes($totalSaved), 16, ' ', STR_PAD_LEFT) . "  <fg=green>│</>");
        $this->line("  <fg=green>│</> <fg=white>Size reduction:</> " . str_pad($totalReduction . '%', 12, ' ', STR_PAD_LEFT) . "  <fg=green>│</>");
        $this->line('  <fg=green>╰──────────────────────────────────────────────╯</>');

        if ($this->option('show-stats')) {
            $this->newLine();
            $this->table(
                ['File', 'Original', 'Optimized', 'Saved', 'Reduction'],
                array_map(function ($item) {
                    $reduction = round(($item['saved'] / $item['original']) * 100) . '%';
                    return [
                        $item['file'],
                        $this->formatBytes($item['original']),
                        $this->formatBytes($item['optimized']),
                        $this->formatBytes($item['saved']),
                        $reduction
                    ];
                }, $minifyResults)
            );
        }
    }

    private function displayError(\Exception $e): void
    {
        $this->newLine();
        $this->line('  <fg=red>┌───────────────────────────────────────┐</>');
        $this->line('  <fg=red>│</> <fg=white;options=bold>⚠️  ERROR DURING OPTIMIZATION</> <fg=red>            │</>');
        $this->line('  <fg=red>├───────────────────────────────────────┤</>');
        $this->line('  <fg=red>│</> ' . wordwrap($e->getMessage(), 35) . ' <fg=red>│</>');
        $this->line('  <fg=red>└───────────────────────────────────────┘</>');
        $this->newLine();
    }

    private function calculateReduction(array $results): float
    {
        $totalOriginal = array_sum(array_column($results, 'original'));
        $totalOptimized = array_sum(array_column($results, 'optimized'));

        if ($totalOriginal === 0) return 0;

        return round((($totalOriginal - $totalOptimized) / $totalOriginal) * 100, 2);
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
