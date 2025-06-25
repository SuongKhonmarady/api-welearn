<?php

namespace App\Console\Commands;

use App\Models\Scholarship;
use Illuminate\Console\Command;

class GenerateScholarshipSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scholarships:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing scholarships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating slugs for existing scholarships...');
        
        $scholarships = Scholarship::whereNull('slug')->get();
        
        if ($scholarships->isEmpty()) {
            $this->info('No scholarships found without slugs.');
            return;
        }
        
        $progressBar = $this->output->createProgressBar($scholarships->count());
        $progressBar->start();
        
        foreach ($scholarships as $scholarship) {
            $scholarship->slug = Scholarship::generateUniqueSlug($scholarship->title);
            $scholarship->save();
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully generated slugs for {$scholarships->count()} scholarships.");
    }
}
