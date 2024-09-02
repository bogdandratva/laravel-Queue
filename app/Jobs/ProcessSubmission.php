<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Submission;
use App\Events\SubmissionSaved;
use Illuminate\Support\Facades\Log;

class ProcessSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        Log::info('Processing submission job:', $this->data);

        $submission = new Submission();
        $submission->name = $this->data['name'];
        $submission->email = $this->data['email'];
        $submission->message = $this->data['message'];
        $submission->save();

        Log::info('Submission saved with ID: ' . $submission->id);

        event(new SubmissionSaved($submission));
    }

    public function getData()
    {
        return $this->data;
    }
}
