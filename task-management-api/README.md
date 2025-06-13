# Task Management API - Backend

## Overview

This is a comprehensive task management API built with Laravel 10. The API provides functionality for user authentication, role-based access control, and complete CRUD operations for projects and tasks. It uses UUID primary keys for enhanced security and Laravel Sanctum for token-based authentication.

## Features

- **User Authentication**
  - Registration with role assignment
  - Login with token generation
  - Logout with token revocation
  - User profile retrieval with roles

- **Role-Based Access Control**
  - Multiple user roles (admin, user, manager)
  - Role-specific permissions and access control

- **Project Management**
  - Create, read, update, and delete projects
  - Project ownership and access control
  - Project listing with filtering options

- **Task Management**
  - Create, read, update, and delete tasks
  - Task assignment to users
  - Task status tracking (pending, in_progress, completed)
  - Task prioritization (low, medium, high)
  - Due date management
  - Filtering tasks by status, priority, and project

- **Real-time Updates**
  - Event broadcasting for task status changes

## Technical Stack

- **Backend**: Laravel 10.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL
- **API**: RESTful JSON API
- **Primary Keys**: UUID
- **PHP Version**: 8.1+

## Installation

1. Clone the repository
   ```bash
   git clone <repository-url>
   cd task-management-api
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Configure environment variables
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in the `.env` file
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations and seeders
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Start the development server
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

- **POST /api/register**
  - Register a new user
  - Required fields: name, email, password, password_confirmation

- **POST /api/login**
  - Login and get authentication token
  - Required fields: email, password

- **POST /api/logout** (requires authentication)
  - Logout and invalidate token

- **GET /api/user** (requires authentication)
  - Get authenticated user profile with roles

### Projects

- **GET /api/projects** (requires authentication)
  - List all projects (admin) or user's projects

- **POST /api/projects** (requires authentication)
  - Create a new project
  - Required fields: title
  - Optional fields: description

- **GET /api/projects/{id}** (requires authentication)
  - Get project details with tasks

- **PUT/PATCH /api/projects/{id}** (requires authentication)
  - Update project details
  - Fields: title, description

- **DELETE /api/projects/{id}** (requires authentication)
  - Delete a project and its tasks

### Tasks

- **GET /api/tasks** (requires authentication)
  - List all tasks (admin) or user's tasks
  - Optional query parameters: project_id, status, priority

- **POST /api/tasks** (requires authentication)
  - Create a new task
  - Required fields: title, project_id
  - Optional fields: description, status, due_date, priority

- **GET /api/tasks/{id}** (requires authentication)
  - Get task details

- **PUT/PATCH /api/tasks/{id}** (requires authentication)
  - Update task details
  - Fields: title, description, status, due_date, priority, project_id

- **DELETE /api/tasks/{id}** (requires authentication)
  - Delete a task

## Default Credentials

After running the seeders, you can use the following credentials to login as an admin:

- **Email**: admin@example.com
- **Password**: password

## Integration with Frontend

This API is designed to work with the Next.js frontend application located in the `task-management-nextjs` directory. The frontend communicates with this API through HTTP requests and handles authentication using the tokens provided by Laravel Sanctum.

## UUID Implementation

This API uses UUID primary keys instead of auto-incrementing integers for enhanced security and to prevent enumeration attacks. The UUIDs are generated using Laravel's UUID support.

## License

The Task Management API is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
