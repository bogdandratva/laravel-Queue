<?php
namespace Tests\Unit;

use App\Http\Controllers\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Events\SubmissionSaved;
use App\Jobs\ProcessSubmission;
use App\Models\Submission;

class SubmissionTest extends TestCase
{
    public function test_submission_endpoint()
    {
        // Mock the Queue facade so jobs aren't actually dispatched
        Queue::fake();

        // Mock the Event facade so events aren't actually dispatched
        Event::fake();

        // Mock the Log facade
        Log::shouldReceive('info')->never();

        // Create a new request instance
        $request = Request::create('/api/submit', 'POST', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.'
        ]);

        // Create an instance of the controller
        $controller = new SubmissionController();

        // Call the store method
        $response = $controller->store($request);

        // Assert the response
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['message' => 'Submission is being processed'], $response->getData(true));

        // Ensure a job was dispatched
        Queue::assertPushed(ProcessSubmission::class, function ($job) {
            return $job->getData()['email'] === 'john.doe@example.com';
        });

        // Manually handle the job
        $job = new ProcessSubmission($request->all());
        $job->handle();

        // Ensure an event was fired
        Event::assertDispatched(SubmissionSaved::class, function ($event) use ($request) {
            return $event->submission->email === $request->email;
        });

        // Mock logging for the event listener and ensure it is called once
        Log::shouldReceive('info')
            ->withArgs(function ($message) {
                return str_contains($message, 'Submission saved:') &&
                       str_contains($message, 'John Doe') && 
                       str_contains($message, 'john.doe@example.com');
            })
            ->once();

        // Handle the event manually to trigger the listener
        $listener = new \App\Listeners\LogSubmissionSaved();
        $listener->handle(new SubmissionSaved(
            Submission::where('email', 'john.doe@example.com')->first()
        ));
    }
}