# Smart Dash - API Testing Scaffolding ⚡


A powerful and intuitive dashboard builder built with Laravel

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
