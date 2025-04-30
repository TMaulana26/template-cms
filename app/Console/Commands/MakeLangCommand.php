<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeLangCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lang {name : The language file name (e.g. en.test or id.pages.test)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new language file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $parts = explode('.', $name);

        if (count($parts) < 2) {
            $this->error('The name format should be {locale}.{filename} or {locale}.{directory}.{filename}');
            return Command::FAILURE;
        }

        $locale = array_shift($parts);
        $filename = array_pop($parts);
        $directory = $parts;

        // Build the path
        $path = lang_path($locale);

        if (!empty($directory)) {
            $path .= '/' . implode('/', $directory);
        }

        // Create directory if it doesn't exist
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = $path . '/' . $filename . '.php';

        // Check if file already exists
        if (File::exists($filePath)) {
            if (!$this->confirm("The file {$filePath} already exists. Do you want to overwrite it?")) {
                $this->info('Command canceled.');
                return Command::SUCCESS;
            }
        }

        // Create the file content
        $content = <<<EOT
                <?php

                return [
                    //
                ];

                EOT;

        File::put($filePath, $content);

        $this->info("Language file created successfully: {$filePath}");

        return Command::SUCCESS;
    }
}
