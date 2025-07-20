<?php

use App\Models\DocumentTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

return new class extends Migration
{
    public function up(): void
    {
        $installationPath = config('app.template_installation_path');

        if (Storage::exists($installationPath)) {
            Storage::deleteDirectory($installationPath);
        }

        collect(Finder::create()->directories()->in(resource_path('templates'))->depth(0))
            ->each(function (SplFileInfo $dir) {
                $name = $dir->getFilename();

                DocumentTemplate::installFromFolder($dir->getPathname(), default: $name === 'minimal');
            });
    }
};
