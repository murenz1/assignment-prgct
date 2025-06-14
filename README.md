# Task Management Full-Stack Application

## Overview

This is a modern full-stack task management application built with Next.js and Laravel. The application provides a comprehensive solution for managing projects and tasks with a beautiful, responsive user interface and a robust API backend.

## Project Structure

This repository contains two main components:

- **Frontend**: Located in the `task-management-nextjs` directory
- **Backend**: Located in the `task-management-api` directory

## Key Features

- **Modern Authentication System**
  - User registration and login
  - Token-based authentication with Laravel Sanctum
  - Protected routes and API endpoints

- **Project Management**
  - Create, view, update, and delete projects
  - Project status tracking
  - Project ownership and access control

- **Task Management**
  - Create, view, update, and delete tasks
  - Task assignment to projects
  - Status tracking (pending, in progress, completed)
  - Priority levels (low, medium, high)
  - Due date management

- **Modern UI/UX**
  - Responsive design for all device sizes
  - Custom color scheme (#0E1530, #EF5615, #FFFFFF)
  - Interactive modals and components
  - Loading states and error handling

## Technical Stack

### Frontend
- Next.js 14 with App Router
- TypeScript
- Tailwind CSS
- React Context API
- Axios for API communication

### Backend
- Laravel 10
- Laravel Sanctum for authentication
- UUID primary keys
- RESTful API architecture
- MySQL/PostgreSQL database

## Getting Started

### Prerequisites
- Node.js 18+ for the frontend
- PHP 8.1+ for the backend
- Composer
- MySQL or PostgreSQL database

### Installation

1. Clone the repository
   ```bash
   git clone <repository-url>
   cd lk-group-v2
   ```

2. Set up the backend (API)
   ```bash
   cd task-management-api
   composer install
   cp .env.example .env
   # Configure your database in .env
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```

3. Set up the frontend
   ```bash
   cd ../task-management-nextjs
   npm install
   cp .env.example .env.local
   # Set NEXT_PUBLIC_API_URL=http://localhost:8000/api in .env.local
   npm run dev
   ```

4. Open your browser and navigate to:
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000

## Default Login Credentials

After setting up both frontend and backend, you can use these credentials to login:

- **Email**: admin@example.com
- **Password**: password

## Documentation

For more detailed information about each component:

- [Frontend Documentation](./task-management-nextjs/README.md)
- [Backend API Documentation](./task-management-api/README.md)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
#   a s s i g n m e n t - p r g c t  
 