<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ToggleRepository extends Command
{
    protected $signature = 'media-library-extensions:toggle-repository {--force : Skip confirmation prompts}';

    protected $description = 'Toggle between local and Packagist repositories for development packages. Manages symlinks and runs composer update.';

    protected array $packages = [
        'mlbrgn/laravel-medialibrary-extensions' => [
            'path' => './packages/mlbrgn/laravel-medialibrary-extensions',
            'symlink' => 'media-library-extensions',
        ],
        // Add more packages here if needed
    ];

    public function handle(): int
    {
        $composerPath = base_path('composer.json');

        if (!file_exists($composerPath)) {
            $this->error('composer.json not found!');
            return self::FAILURE;
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        $repositories = $composer['repositories'] ?? [];

        $toggled = [];

        foreach ($this->packages as $name => $data) {
            $pathRepo = $data['path'];
            $symlinkName = $data['symlink'];

            $isLinked = collect($repositories)->contains(fn($repo
            ) => ($repo['type'] ?? '') === 'path' && ($repo['url'] ?? '') === $pathRepo
            );

            $linkPath = public_path('vendor/'.$symlinkName);
            $targetPath = realpath(base_path(trim($pathRepo, './').'/dist'));

            if ($isLinked) {
                if (!$this->option('force') && !$this->confirm("Remove local path for [$name]?")) {
                    continue;
                }

                $repositories = array_values(array_filter($repositories,
                    fn($repo) => !($repo['type'] === 'path' && $repo['url'] === $pathRepo)
                ));

                if (file_exists($linkPath) || is_link($linkPath)) {
                    File::delete($linkPath);
                    $this->info("Removed symlink: $linkPath");
                }

                $this->info("🔗 Removed local path for $name");
                $toggled[] = $name;
            } else {
                if (!$this->option('force') && !$this->confirm("Use local path for [$name]?")) {
                    continue;
                }

                $repositories[] = [
                    'type' => 'path',
                    'url' => $pathRepo,
                    'options' => ['symlink' => true],
                ];

                if (!is_dir($targetPath)) {
                    $this->warn("⚠️ dist folder not found for $name, skipping symlink.");
                } else {
                    if (file_exists($linkPath) || is_link($linkPath)) {
                        File::delete($linkPath);
                    }

                    symlink($targetPath, $linkPath);
                    $this->info("Created symlink: $linkPath → $targetPath");
                }

                $this->info("🔗 Added local path for $name");
                $toggled[] = $name;
            }
        }

        $composer['repositories'] = $repositories;
        file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (count($toggled)) {
            $this->info('Running composer update for: '.implode(', ', $toggled));
            $process = Process::fromShellCommandline('composer update '.implode(' ', $toggled));
            $process->setTty(Process::isTtySupported());
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        } else {
            $this->info('Nothing to update.');
        }

        return self::SUCCESS;
    }
}

//{


//    public function handle(): int
//    {
//        $composerPath = base_path('composer.json');
//
//        if (!file_exists($composerPath)) {
//            $this->error('composer.json not found!');
//            return self::FAILURE;
//        }
//
//        $composer = json_decode(file_get_contents($composerPath), true);
//
//        $repositories = $composer['repositories'] ?? [];
//
//        $toggled = [];
//
//        foreach ($this->packages as $name => $data) {
//            $pathRepo = $data['path'];
//            $symlinkName = $data['symlink'];
//
//            $isLinked = collect($repositories)->contains(fn($repo
//            ) => ($repo['type'] ?? '') === 'path' && ($repo['url'] ?? '') === $pathRepo
//            );
//
//            $linkPath = public_path('vendor/'.$symlinkName);
//            $targetPath = realpath(base_path(trim($pathRepo, './').'/dist'));
//
//            if ($isLinked) {
//                if (!$this->option('force') && !$this->confirm("Remove local path for [$name]?")) {
//                    continue;
//                }
//
//                $repositories = array_values(array_filter($repositories,
//                    fn($repo) => !($repo['type'] === 'path' && $repo['url'] === $pathRepo)
//                ));
//
//                if (file_exists($linkPath) || is_link($linkPath)) {
//                    File::delete($linkPath);
//                    $this->info("Removed symlink: $linkPath");
//                }
//
//                $this->info("🔗 Removed local path for $name");
//                $toggled[] = $name;
//            } else {
//                if (!$this->option('force') && !$this->confirm("Use local path for [$name]?")) {
//                    continue;
//                }
//
//                $repositories[] = [
//                    'type' => 'path',
//                    'url' => $pathRepo,
//                    'options' => ['symlink' => true],
//                ];
//
//                if (!is_dir($targetPath)) {
//                    $this->warn("⚠️ dist folder not found for $name, skipping symlink.");
//                } else {
//                    if (file_exists($linkPath) || is_link($linkPath)) {
//                        File::delete($linkPath);
//                    }
//
//                    symlink($targetPath, $linkPath);
//                    $this->info("Created symlink: $linkPath → $targetPath");
//                }
//
//                $this->info("🔗 Added local path for $name");
//                $toggled[] = $name;
//            }
//        }
//
//        $composer['repositories'] = $repositories;
//        file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//
//        if (count($toggled)) {
//            $this->info('Running composer update for: '.implode(', ', $toggled));
//            $process = Process::fromShellCommandline('composer update '.implode(' ', $toggled));
//            $process->setTty(Process::isTtySupported());
//            $process->run(function ($type, $buffer) {
//                echo $buffer;
//            });
//        } else {
//            $this->info('Nothing to update.');
//        }
//
//        return self::SUCCESS;
//    }
//}
//
//public function handle(): int
//    {
//        $composerPath = base_path('composer.json');
//
//        if (!file_exists($composerPath)) {
//            $this->error('composer.json not found!');
//            return self::FAILURE;
//        }
//
//        $composer = json_decode(file_get_contents($composerPath), true);
//        $repositories = $composer['repositories'] ?? [];
//
//        $isLinked = collect($repositories)->contains(function ($repo) {
//            return ($repo['type'] ?? '') === 'path' && ($repo['url'] ?? '') === $this->pathRepo;
//        });
//
//        if ($isLinked) {
//            // Remove path repo
//            $composer['repositories'] = array_values(array_filter($repositories, function ($repo) {
//                return !(
//                    ($repo['type'] ?? '') === 'path' &&
//                    ($repo['url'] ?? '') === $this->pathRepo
//                );
//            }));
//
//            file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//            $this->info('🔗 Local path repository removed (back to Packagist).');
//            $this->removeSymlink();
//        } else {
//            // Add path repo
//            $composer['repositories'][] = [
//                'type' => 'path',
//                'url' => $this->pathRepo,
//                'options' => ['symlink' => true],
//            ];
//
//            file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//            $this->info('🔗 Local path repository added.');
//            $this->createSymlink();
//        }
//
//        $this->warn('⚠️ Run: composer update mlbrgn/laravel-medialibrary-extensions');
//        return self::SUCCESS;
//    }
//
//    protected function createSymlink(): void
//    {
//        $target = realpath(base_path('packages/mlbrgn/laravel-medialibrary-extensions/dist'));
//        $link = public_path('vendor/media-library-extensions');
//
//        if (! $target || ! is_dir($target)) {
//            $this->error("dist folder not found: {$target}");
//            return;
//        }
//
//        if (file_exists($link) || is_link($link)) {
//            $this->info("Removing existing link: {$link}");
//            File::delete($link);
//        }
//
//        $this->info("Creating symlink: {$link} → {$target}");
//        symlink($target, $link);
//        $this->info('Symlink created.');
//    }
//
//    protected function removeSymlink(): void
//    {
//        $link = public_path('vendor/media-library-extensions');
//
//        if (is_link($link) || file_exists($link)) {
//            $this->info("Removing symlink or folder: {$link}");
//            File::delete($link);
//            $this->info('Symlink removed.');
//        } else {
//            $this->info('No symlink to remove.');
//        }
//    }
//}




