<?php

namespace App\Console\Commands;

use App\Traits\Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtractEnumDocs extends Command
{
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enum:docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this commands extracts selected enums to scribe folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = "#Enums\n\n";
        foreach ($this->getAvailableClasses('App\\Enums\\') as $enum) {
            $content .= '##' . Str::headline(class_basename($enum)) . "\n\n";
            foreach ($enum::cases() as $enumItem) {
                $content .= "{$enumItem->name}: {$enumItem->value} \n\n";
            }
        }
        $full_path = '.scribe/append.md';
        File::ensureDirectoryExists(dirname($full_path));
        file_put_contents($full_path, $content);

        return self::SUCCESS;
    }
}
