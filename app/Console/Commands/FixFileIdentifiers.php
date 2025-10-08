<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;

class FixFileIdentifiers extends Command
{
    protected $signature = 'files:fix-identifiers';
    protected $description = 'Add identifiers to files that don\'t have them';

    public function handle()
    {
        $this->info('Checking files without identifiers...');
        
        $filesWithoutIdentifiers = File::whereNull('identifier')->get();
        
        $this->info("Found {$filesWithoutIdentifiers->count()} files without identifiers.");
        
        if ($filesWithoutIdentifiers->count() > 0) {
            $this->info('Adding identifiers to existing files...');
            
            $bar = $this->output->createProgressBar($filesWithoutIdentifiers->count());
            $bar->start();
            
            foreach ($filesWithoutIdentifiers as $file) {
                $file->identifier = File::generateIdentifier();
                $file->save();
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info('Done! All files now have identifiers.');
        } else {
            $this->info('All files already have identifiers.');
        }
        
        // Show some statistics
        $totalFiles = File::count();
        $filesWithIdentifiers = File::whereNotNull('identifier')->count();
        
        $this->info("Total files: {$totalFiles}");
        $this->info("Files with identifiers: {$filesWithIdentifiers}");
        
        return 0;
    }
}
