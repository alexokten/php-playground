<?php

declare(strict_types=1);

namespace App\Jobs;

class SendEmailJob
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $body
    ) {}

    public function handle(): void
    {
        // INFO: Mock sending an email
        ray()->html("
              <strong>Sending Email</strong><br>
              <strong>To:</strong> {$this->to}<br>
              <strong>Subject:</strong> {$this->subject}<br>
              <strong>Body:</strong> {$this->body}
          ");
        sleep(1);
        ray('Email sent to {$this->to}')->green();
    }
}
