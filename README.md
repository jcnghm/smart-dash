# Smart Dash ⚡

A powerful and intuitive dashboard builder built with Laravel

## Features

### Dashboard Management
- **Visual Dashboard Builder** - Drag-and-drop interface for creating custom layouts
- **Multiple Dashboard Support** - Create and manage unlimited dashboards
- **Public/Private Dashboards** - Control visibility and sharing permissions
- **Real-time Preview** - See your changes instantly as you build

### Widget Library
- **Charts & Graphs** - Interactive charts powered by modern visualization libraries
- **Data Tables** - Sortable, filterable data displays
- **Metrics & KPIs** - Display key performance indicators with custom styling
- **Text Widgets** - Rich text content and notes
- **Custom Widgets** - Extensible widget system for custom components

### User Experience
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile
- **Dark/Light Mode** - Automatic theme switching
- **Real-time Updates** - Live data updates using Laravel Broadcasting
- **Fast Performance** - Optimized for speed with caching and lazy loading

### Security & Access
- **User Authentication** - Secure login and registration system
- **Role-based Permissions** - Control who can view and edit dashboards

## Quick Start

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

5. **Install dependencies inside the container**
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

Visit `http://localhost` to access the application.
