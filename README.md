# Task API

A CRUD API for tasks, built with Laravel and SQLite.

## Why SQLite?

- **Single file:** the whole database is stored in one file.
- **Zero setup:** there is no separate database server to install or run.
- **Survives restarts:** tasks stay saved after the API server stops.

## Database File

The database is stored in `database/tasks.db`. The start command creates it automatically. It is git-ignored, so each clean clone starts with a fresh database.

## Getting Started

You need PHP (with the `pdo_sqlite` extension), Composer and Git. Clone the repository, open the project folder, then run:

```bash
composer run start
```

This command:

1. Installs the dependencies.
2. Creates `.env` and the application key if they are missing.
3. Creates the database file if it is missing.
4. Runs pending migrations.
5. Adds three example tasks if the `tasks` table is empty.
6. Starts the server.

The API runs at `http://127.0.0.1:8000/api` if port 8000 is free. Otherwise, use the address shown in the terminal. Press `Ctrl+C` to stop the server.

## Migrate and Seed

The start command does this for you. To run these steps yourself, first complete the setup by running `composer run start` once, then stop the server.

Run pending migrations (creates the tables):

```bash
php artisan migrate
```

Add the example tasks (only if the `tasks` table is empty):

```bash
php artisan db:seed
```

Do both in one command:

```bash
php artisan migrate --seed
```

Check which migrations have run:

```bash
php artisan migrate:status
```

Reset the database and seed it again:

```bash
php artisan migrate:fresh --seed
```

> **Warning:** `migrate:fresh` drops all tables, so all saved data is permanently deleted.

## Endpoints

Paths are relative to `http://127.0.0.1:8000/api`.

| Method | Endpoint      | Description                         | Success          | Errors       |
|--------|---------------|-------------------------------------|------------------|--------------|
| GET    | `/`           | API name, version and endpoint list | `200`            | -            |
| GET    | `/health`     | Health check                        | `200`            | -            |
| GET    | `/tasks`      | Return all tasks                    | `200`            | -            |
| GET    | `/tasks/{id}` | Return one task                     | `200`            | `404`        |
| POST   | `/tasks`      | Create a task                       | `201`            | `400`        |
| PUT    | `/tasks/{id}` | Update `title` and/or `done`        | `200`            | `400`, `404` |
| DELETE | `/tasks/{id}` | Delete a task                       | `204` (no body)  | `404`        |

### Request Bodies

**POST `/tasks`**: `title` is required and must be a non-empty string. New tasks start with `done` set to `false`.

```json
{ "title": "Buy milk" }
```

**PUT `/tasks/{id}`**: send JSON with `title`, `done`, or both. `title` must be a non-empty string. `done` should be `true` or `false`.

```json
{ "done": true }
```

## Example Request

```bash
curl -i http://127.0.0.1:8000/api/tasks
```

Response body on a fresh database:

```json
[
  { "id": 1, "title": "Learn PHP", "done": true },
  { "id": 2, "title": "Learn Laravel", "done": false },
  { "id": 3, "title": "Build a CRUD API", "done": false }
]
```

## Stage 4: Exploring SQLite

I ran this query in DB Browser for SQLite:

```sql
SELECT * FROM tasks WHERE done = 1;
```

It returned only the completed tasks (the rows where `done` is `1`).

## Database Screenshot

![The tasks database open in DB Browser for SQLite](docs/db-browser.png)

## Swagger UI

<img width="1253" height="820" alt="Swagger UI showing the Task API endpoints" src="https://github.com/user-attachments/assets/d8c83ebd-e426-4be5-94ba-01c0f5104098" />