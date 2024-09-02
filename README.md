# Simple API Endpoint

## Setup Instructions

1. Clone the repository.
2. Run `composer install` to install dependencies.
3. Configure your `.env` file with your database settings. Copy `.env.example` and rename to `.env`
4. Generate key `php artisan key:generate`
5. Run `php artisan migrate` to create the necessary tables.
6. Run server `php artisan serve`
7. Start the queue worker with `php artisan queue:work`.

## Testing the Endpoint

1. Use a tool like Postman or CURL to make a POST request to the `/api/submit` endpoint with the following JSON payload:
    ```json
    {
        "name": "John Doe",
        "email": "john.doe@example.com",
        "message": "This is a test message."
    }
    ```

2. You should receive a response indicating that the submission is being processed.

## Running Tests

1. Run the tests with `php artisan test`.