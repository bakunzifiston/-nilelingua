<?php

namespace Database\Seeders;

use App\Support\Cms\ContentManager;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Imports the website content into the CMS (existing content is left untouched).
     * Create the first administrator with: php artisan cms:admin you@example.com
     */
    public function run(): void
    {
        ContentManager::import();
    }
}
