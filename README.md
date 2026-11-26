# Task API

Tasks are kept in Laravel's `file` cache store, so changes remain after a server restart. Clearing that cache restores the default tasks.

## Getting Started

You need PHP and Composer. Run these commands from the project folder (macOS, Linux or Git Bash).

Install dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```


Start the server:

```bash
php artisan serve
```

The API runs at `http://127.0.0.1:8000/api`.

After the first setup, you only need `php artisan serve` to start the server.

## Endpoints

| Method | Endpoint      | Description                         | Success          | Errors     |
|--------|---------------|-------------------------------------|------------------|------------|
| GET    | `/`           | API name, version and endpoint list | `200`            | -          |
| GET    | `/health`     | Health check                        | `200`            | -          |
| GET    | `/tasks`      | Return all tasks                    | `200`            | -          |
| GET    | `/tasks/{id}` | Return one task                     | `200`            | `404`      |
| POST   | `/tasks`      | Create a task                       | `201`            | `400`      |
| PUT    | `/tasks/{id}` | Update `title` and/or `done`        | `200`            | `400`, `404` |
| DELETE | `/tasks/{id}` | Delete a task                       | `204` (no body)  | `404`      |

### Request Bodies

**POST `/tasks`**: `title` is required and must be a non-empty string. New tasks start with `done` set to `false`.

```json
{ "title": "Buy milk" }
```

**PUT `/tasks/{id}`**: send JSON with `title`, `done`, or both. `title` must be a non-empty string, and `done` must be `true` or `false`.

```json
{ "done": true }
```

## Example Request

```bash
curl -i http://127.0.0.1:8000/tasks
```

Output:
```json
{
    [
  {
    "id": 1,
    "title": "Learn PHP",
    "done": true
  },
  {
    "id": 2,
    "title": "Learn Laravel",
    "done": false
  },
  {
    "id": 3,
    "title": "Build a CRUD API",
    "done": true
  },
  {
    "id": 4,
    "title": "hallo",
    "done": false
  }
]
}
```

## Stage 4: Exploring SQLite

I ran this query in DB Browser for SQLite:

```sql
SELECT * FROM tasks WHERE done = 1;
```

It returned only the completed tasks (the rows where `done` is `1`).

## Swagger UI

![Swagger UI showing the Task API endpoints](<img width="1253" height="820" alt="Screenshot 2026-11-25 at 6 21 50 PM" src="https://github.com/user-attachments/assets/d8c83ebd-e426-4be5-94ba-01c0f5104098" />)
