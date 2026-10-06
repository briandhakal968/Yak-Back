<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $email,
        private readonly string $subject,
        private readonly string $view,
        private readonly array $data = [],
    ) {}

    public function handle(): void
    {
        try {
            $html = View::make($this->view, $this->data)->render();
            Mail::html($html, function ($message) {
                $message->to($this->email)->subject($this->subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Failed to send queued email', [
                'email' => $this->email,
                'subject' => $this->subject,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
