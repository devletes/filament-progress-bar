<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::addLocation(__DIR__.'/../../resources/views');

        $publicSource = __DIR__.'/../../public';

        if (is_dir($publicSource)) {
            foreach (scandir($publicSource) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $src = $publicSource.'/'.$file;
                $dest = public_path($file);

                if (is_file($src) && (! file_exists($dest) || filemtime($src) > filemtime($dest))) {
                    @copy($src, $dest);
                }
            }
        }
    }
}
