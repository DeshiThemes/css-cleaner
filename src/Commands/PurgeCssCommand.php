<?php

namespace DeshiThemes\CssCleaner\Commands;

use Illuminate\Console\Command;
use DeshiThemes\CssCleaner\Helpers\CssHelper;

class PurgeCssCommand extends Command
{
    protected $signature = 'css:purge 
    {--show-output : Display output paths}
    {--path= : Custom CSS source path}
    {--output= : Custom output path}
    {--dry-run : Simulate without saving}';

    protected $description = '🧹 Remove unused CSS from public files';

    public function handle()
    {

        if (!file_exists(base_path('node_modules/.bin/purgecss'))) {
            $this->error("PurgeCSS not found. Please install with: npm install @fullhuman/postcss-purgecss");
            return self::FAILURE;
        }
        $inputPath = $this->option('path') ?: config('csscleaner.css_path');
        $outputPath = $this->option('output') ?: config('csscleaner.output_path');
        $this->line("\n  <fg=blue>🧹</> <fg=white;options=bold>STARTING CSS PURGE</>");

        try {
            $results = CssHelper::purge(
                $inputPath, // Use the variable instead of config
                $outputPath, // Use the variable instead of config
                config('csscleaner.safelist'),
                $this->option('dry-run') // Add dry-run support
            );

            $this->newLine();
            $this->line("  <fg=green>✅ PURGE COMPLETE! Processed " . count($results) . " files</>");

            if ($this->option('show-output')) {
                $this->table(
                    ['File', 'Output Path'],
                    array_map(fn($item) => [
                        $item['file'],
                        $item['output']
                    ], $results)
                );
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("  💥 Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
