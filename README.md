# Dealership Management System

## Overview

The Dealership Management System is a comprehensive web application built with Laravel and Vue.js designed to streamline operations for automotive dealerships. The system provides tools for inventory management, customer relationship management, sales tracking, and automated calling features through VAPI integration.

## Features

- **Inventory Management**: Track vehicles, parts, and accessories
- **Customer Management**: Store and manage customer information
- **Sales Pipeline**: Track leads and sales opportunities
- **Call Logs**: Record and manage customer interactions
- **Automated Calling**: Integration with VAPI for automated customer outreach
- **User Authentication**: Role-based access control
- **Reporting**: Generate sales and inventory reports

## Tech Stack

- **Backend**: Laravel 10.x
- **Frontend**: Vue.js 3 with Inertia.js
- **Styling**: Tailwind CSS
- **Database**: MySQL
- **API Integration**: VAPI for automated calling

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/robbie-brender/dealership-management-laravel.git
   cd dealership-management-laravel
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Create environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file

7. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

8. Build frontend assets:
   ```bash
   npm run dev
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

## Webhook Integration

### Local Development with ngrok

For local development and testing of webhooks:

1. Start your Laravel application:
   ```bash
   php artisan serve
   ```

2. Start ngrok to expose your local server:
   ```bash
   ngrok http 8000
   ```

3. Use the generated ngrok URL for webhook endpoints:
   ```
   https://[your-ngrok-subdomain].ngrok-free.app/api/raw-webhook
   ```

### Production Deployment

When deploying to production, update your webhook URLs to point to your production domain.

## Deployment Options

### Heroku Deployment

1. Create a Heroku account and install the Heroku CLI
2. Create a new Heroku app:
   ```bash
   heroku create dealership-management
   ```

3. Add the necessary buildpacks:
   ```bash
   heroku buildpacks:add heroku/php
   heroku buildpacks:add heroku/nodejs
   ```

4. Configure environment variables in Heroku dashboard

5. Deploy your application:
   ```bash
   git push heroku master
   ```

6. Run migrations on Heroku:
   ```bash
   heroku run php artisan migrate --force
   ```

## License

This project is licensed under the MIT License.
