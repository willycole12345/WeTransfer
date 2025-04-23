<?php

namespace App\Console\Commands;

use App\Models\UploadSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
       $expiredSessions = UploadSession::with('files')->where('expires_at', '<', now())->get();

        $this->info("Found {$expiredSessions->count()} expired session(s)");

        foreach ($expiredSessions as $session) {
            foreach ($session->files as $file) {
                $filePath = $file->stored_path ?? $file->stored_name;
                if (Storage::disk('uploads')->exists($filePath)) {
                    Storage::disk('uploads')->delete($filePath);
                    $this->line("Deleted file: $filePath");
                }
            }

            $session->delete();
            $this->line("Deleted session: {$session->token}");
        }

        $this->info("Cleanup complete.");

    }
}
