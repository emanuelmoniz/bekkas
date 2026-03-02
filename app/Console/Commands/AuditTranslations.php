<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Pillar 3 — translations:audit
 *
 * Scans all blade/PHP source files for t('key') calls and diffs them
 * against the keys declared in StaticTranslationsSeeder.php.
 *
 *   php artisan translations:audit            # read-only report
 *   php artisan translations:audit --fix      # also remove unused rows from seeder
 *   php artisan translations:audit --details  # list every file + line for each key
 *   php artisan translations:audit --missing  # show only undeclared (missing) keys
 *   php artisan translations:audit --unused   # show only unused seeder keys
 */
class AuditTranslations extends Command
{
    protected $signature = 'translations:audit
                            {--fix     : Rewrite the seeder removing unused keys}
                            {--details : Show every source file + line for each key}
                            {--missing : Limit output to keys used in code but absent from seeder}
                            {--unused  : Limit output to seeder keys not found in any source file}';

    protected $description = 'Audit t() calls in source files against StaticTranslationsSeeder keys.';

    // ── Paths ─────────────────────────────────────────────────────────────────

    private string $seederPath;

    private array $scanRoots;

    private array $excludeDirs;

    private array $excludeFiles;

    public function handle(): int
    {
        $this->seederPath = database_path('seeders/StaticTranslationsSeeder.php');
        $this->scanRoots = [
            app_path(),
            resource_path(),
            base_path('routes'),
        ];
        $this->excludeDirs = array_filter([
            realpath(base_path('vendor')),
            realpath(base_path('node_modules')),
            realpath(database_path('seeders')),  // don't scan the seeder itself
        ]);
        // Exclude this command file itself (its docblock examples would create false positives)
        $this->excludeFiles = [
            realpath(__FILE__),
        ];

        // ── 1. Collect keys used in source code ───────────────────────────────

        $this->line('');
        $this->line('<fg=cyan;options=bold>Scanning source files for t() calls…</>');

        /** @var array<string, array<int, string>> $usagesRaw  key → [file:line, …] */
        $usagesRaw = [];

        foreach ($this->scanRoots as $root) {
            if (is_dir($root)) {
                $this->collectUsages($root, $usagesRaw);
            }
        }

        $usages = collect($usagesRaw);
        $codeKeys = $usages->keys()->sort()->values();

        // ── 2. Collect keys declared in seeder ────────────────────────────────

        $seederKeys = $this->extractSeederKeys()->sort()->values();

        // ── 3. Diff ───────────────────────────────────────────────────────────

        $missing = $codeKeys->diff($seederKeys)->values();    // in code, not in seeder
        $unused = $seederKeys->diff($codeKeys)->values();    // in seeder, not in code
        $healthy = $codeKeys->intersect($seederKeys)->count();

        // ── 4. Report ─────────────────────────────────────────────────────────

        $showMissing = ! $this->option('unused');
        $showUnused = ! $this->option('missing');

        $this->line('');
        $this->line(sprintf(
            '<fg=green>  ✔  %d keys matched (present in both code and seeder)</>',
            $healthy
        ));

        // --- Missing (undeclared) keys ----------------------------------------
        if ($showMissing) {
            $this->line('');
            if ($missing->isEmpty()) {
                $this->line('<fg=green>  ✔  No undeclared t() keys found.</> All code keys are in the seeder.');
            } else {
                $this->line(sprintf(
                    '<fg=red;options=bold>  ✘  %d key(s) used in code but NOT in seeder (undeclared):</>',
                    $missing->count()
                ));
                foreach ($missing as $key) {
                    $this->line("       <fg=red>$key</>");
                    if ($this->option('details')) {
                        foreach (($usages[$key] ?? []) as $loc) {
                            $this->line("           <fg=gray>$loc</>");
                        }
                    }
                }
            }
        }

        // --- Unused seeder keys -----------------------------------------------
        if ($showUnused) {
            $this->line('');
            if ($unused->isEmpty()) {
                $this->line('<fg=green>  ✔  No unused seeder keys found.</> Every seeder key is called somewhere.');
            } else {
                $this->line(sprintf(
                    '<fg=yellow;options=bold>  ⚠  %d key(s) in seeder but NOT found in any source file (unused):</>',
                    $unused->count()
                ));
                foreach ($unused as $key) {
                    $this->line("       <fg=yellow>$key</>");
                }
            }
        }

        // ── 5. Stats ──────────────────────────────────────────────────────────

        $this->line('');
        $this->line(sprintf(
            '<fg=cyan>  Scanned %d call-sites | %d unique code keys | %d seeder keys</>',
            $usages->sum(fn ($locs) => count($locs)),
            $codeKeys->count(),
            $seederKeys->count()
        ));

        // ── 6. --fix: rewrite seeder ──────────────────────────────────────────

        if ($this->option('fix')) {
            $this->line('');

            if ($unused->isEmpty()) {
                $this->line('<fg=green>  --fix: nothing to remove — seeder is already clean.</>');

                return self::SUCCESS;
            }

            if (! $this->confirm(sprintf(
                '--fix will remove %d unused key(s) from StaticTranslationsSeeder.php. Continue?',
                $unused->count()
            ))) {
                $this->line('Aborted.');

                return self::SUCCESS;
            }

            $removed = $this->fixSeeder($unused);

            $this->line(sprintf(
                '<fg=green>  --fix: removed %d key(s) (%d rows) from seeder. Review git diff before committing.</>',
                $unused->count(),
                $removed
            ));
        } elseif ($unused->isNotEmpty()) {
            $this->line('  Tip: run with <fg=yellow>--fix</> to remove the unused keys from the seeder.');
        }

        $this->line('');

        return $missing->isEmpty() ? self::SUCCESS : self::FAILURE;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Recursively walk $dir and collect every t('key') / t("key") occurrence.
     *
     * @param  array<string, list<string>>  &$usages
     */
    private function collectUsages(string $dir, array &$usages): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $realPath = realpath($file->getPathname());

            foreach ($this->excludeDirs as $excl) {
                if (str_starts_with($realPath, $excl)) {
                    continue 2;
                }
            }

            // Skip explicitly excluded files (e.g. this command itself)
            if (in_array($realPath, $this->excludeFiles, true)) {
                continue;
            }

            $lines = file($realPath, FILE_IGNORE_NEW_LINES);

            foreach ($lines as $lineNo => $line) {
                // Match t('some.key') or t("some.key") — plain string literal only.
                // Does NOT match dynamic keys like t($var) or t('prefix.'.$var).
                preg_match_all(
                    '/\bt\(\s*([\'"])([a-zA-Z0-9_.\-]+)\1/',
                    $line,
                    $matches
                );

                foreach ($matches[2] as $key) {
                    $relative = str_replace(base_path().'/', '', $realPath);
                    $usages[$key][] = $relative.':'.($lineNo + 1);
                }
            }
        }
    }

    /**
     * Parse the seeder file and return the unique set of keys in its $rows array.
     *
     * @return Collection<int, string>
     */
    private function extractSeederKeys(): Collection
    {
        if (! file_exists($this->seederPath)) {
            $this->error('Seeder not found: '.$this->seederPath);

            return collect();
        }

        $src = file_get_contents($this->seederPath);
        $keys = [];

        // Match: 'key' => 'some.dotted.key'  or  "key" => "some.dotted.key"
        preg_match_all(
            '/[\'"]key[\'"]\s*=>\s*[\'"]([a-zA-Z0-9_.\-]+)[\'"]/',
            $src,
            $matches
        );

        foreach ($matches[1] as $key) {
            $keys[$key] = true;
        }

        return collect(array_keys($keys));
    }

    /**
     * Rewrite the seeder removing every row that belongs to an unused key.
     * Returns the number of individual row-lines removed (each key has N locale rows).
     */
    private function fixSeeder(Collection $unusedKeys): int
    {
        $src = file_get_contents($this->seederPath);
        $lines = explode("\n", $src);
        $out = [];
        $removed = 0;
        $unusedSet = $unusedKeys->flip()->toArray();

        foreach ($lines as $line) {
            if (preg_match('/[\'"]key[\'"]\s*=>\s*[\'"]([a-zA-Z0-9_.\-]+)[\'"]/', $line, $m)
                && isset($unusedSet[$m[1]])) {
                $removed++;

                continue; // drop the line
            }
            $out[] = $line;
        }

        file_put_contents($this->seederPath, implode("\n", $out));

        return $removed;
    }
}
