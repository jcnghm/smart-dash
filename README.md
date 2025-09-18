<div align="center">
  <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel" width="100" height="100">

# Laravel Dashboard & API Testing Platform
[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-10%2B%20%7C%2011%2B-red.svg)](https://laravel.com)

A Laravel-based dashboarding solution that combines visualization capabilities with API testing tools. Build custom dashboards, test external or internal APIs, and keep your data synchronized with automated job uploads.

</div>

## Features

### Dashboard Builder
- **Custom Dashboards**: Create unlimited dashboards tailored to your needs
- **Dynamic Widgets**: Build and customize widgets with various data visualization options
- **Real-time Updates**: Live data updates across all dashboard components

### API Integration & Testing
- **API Testing Suite**: Test and validate external API endpoints
- **Job Processing**: Manual job system for reliable data syncing when needed, push data to an external API
- **Connection Management**: Manage multiple API connections and configurations
- **Object Synchronization**: Seamlessly sync data objects to external sources

## Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js & npm
- Docker (for Laravel Sail Development)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/jcnghm/smart-dash.git
   cd smart-dash
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   ```

3. **Configure your environment for Sail**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=smart_dash
   DB_USERNAME=sail
   DB_PASSWORD=password

   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

4. **Start Laravel Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Install dependencies**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ```

6. **Generate application key**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

7. **Run migrations and seeders**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

8. **Build frontend assets**
   ```bash
   ./vendor/bin/sail npm run build
   ```

9. **Access the application**
   
   Visit `http://localhost` in your browser to start using Smart Dash.

## Usage

### Creating Your First Dashboard

1. Navigate to the Dashboard section
2. Click "Create New Dashboard"
3. Give your dashboard a name and description
4. Start adding widgets to visualize your data

### Setting Up API Connections

1. Go to API Testing section
2. Add your external API endpoints
3. Configure authentication and headers
4. Test the connection to ensure it's working
5. Set up sync jobs to automatically pull or push data

### Managing Widgets

1. From any dashboard, click "Add Widget"
2. Choose your widget type (chart, table, metric, etc.)
3. Configure data sources and display options
4. Save and position your widget on the dashboard

### Running Jobs

```bash
# Run jobs manually
./vendor/bin/sail artisan queue:work

# Or schedule them to run automatically
./vendor/bin/sail artisan schedule:work
```

## Configuration

### Queue Configuration

Smart Dash uses Laravel's queue system for job processing. Configure your preferred queue driver in `.env`:

```env
QUEUE_CONNECTION=redis
```

### API Rate Limiting

Configure API rate limits and retry logic in `config/services.php`:

```php
'api_sync' => [
    'rate_limit' => 60,
    'retry_attempts' => 3,
    'retry_delay' => 5,
],
```

## Customization

### Custom Widget Types

Create custom widget types by extending the base widget class:

```php
php artisan make:widget CustomMetricWidget
```

### Dashboard Themes

Customize the look and feel by modifying the CSS variables in `resources/css/dashboard.css`.

## Development

### Running Tests

```bash
./vendor/bin/sail artisan test
```

### Code Style

```bash
./vendor/bin/sail composer pint
```

### Frontend Development

```bash
# Watch for changes
./vendor/bin/sail npm run dev

# Build for production
./vendor/bin/sail npm run build
```

## Usage

This project is a proof of concept.
