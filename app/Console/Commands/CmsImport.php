<?php

namespace App\Console\Commands;

use App\Support\Cms\ContentManager;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cms:import {--overwrite : Replace content already in the database with the original content}')]
#[Description('Import the website content from config/site*.php into the CMS database')]
class CmsImport extends Command
{
    public function handle(): int
    {
        if ($this->option('overwrite') && ! $this->confirm('This replaces the published content of every page with the original content (drafts are discarded). Continue?')) {
            return self::FAILURE;
        }

        $result = ContentManager::import((bool) $this->option('overwrite'));

        $this->info("Imported {$result['created']} new entries".($result['updated'] ? ", replaced {$result['updated']}" : '').'.');

        return self::SUCCESS;
    }
}
