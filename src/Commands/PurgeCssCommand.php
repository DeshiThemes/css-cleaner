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

        $this->line("\n  <fg=blue>┌───────────────────────────────────────────┐</>");
        $this->line("  <fg=blue>│</> <fg=cyan;options=bold>🧹  PURGING UNUSED CSS</> <fg=blue>                      │</>");
        $this->line("  <fg=blue>└───────────────────────────────────────────┘</>");

        // Check Node.js and PurgeCSS
        if (!self::isNodeAvailable()) {
            $this->line('  <fg=red>┌───────────────────────────────────────┐</>');
            $this->line('  <fg=red>│</> <fg=white;options=bold>⚠️  NODE.JS REQUIRED</> <fg=red>                      │</>');
            $this->line('  <fg=red>├───────────────────────────────────────┤</>');
            $this->line('  <fg=red>│</> Please install Node.js >=16.0.0     <fg=red>│</>');
            $this->line('  <fg=red>│</> Download: https://nodejs.org        <fg=red>│</>');
            $this->line('  <fg=red>└───────────────────────────────────────┘</>');
            return self::FAILURE;
        }

        if (!self::isPurgeCssAvailable()) {
            $this->line('  <fg=red>┌───────────────────────────────────────┐</>');
            $this->line('  <fg=red>│</> <fg=white;options=bold>⚠️  PURGECSS REQUIRED</> <fg=red>                     │</>');
            $this->line('  <fg=red>├───────────────────────────────────────┤</>');
            $this->line('  <fg=red>│</> Run: <fg=cyan>npm install @fullhuman/postcss-purgecss</> <fg=red>│</>');
            $this->line('  <fg=red>└───────────────────────────────────────┘</>');
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


    private static function isNodeAvailable(): bool
    {
        $process = new Process(['node', '--version']);
        try {
            $process->mustRun();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function isPurgeCssAvailable(): bool
    {
        $process = new Process(['npx', 'purgecss', '--version']);
        try {
            $process->mustRun();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
