# Facebook Comments Sentiment Analysis SaaS Platform

A production-ready MVP SaaS platform that connects to Facebook Pages and automatically analyzes sentiment of comments using OpenAI's GPT models.

## 🚀 Features

- **Multi-Tenant Authentication**: Secure user registration and login with Laravel Sanctum
- **Facebook Integration**: OAuth flow to connect Facebook Pages
- **Automated Comment Sync**: Artisan command and scheduled jobs to import comments
- **AI Sentiment Analysis**: OpenAI GPT-4o-mini powered sentiment classification (Positive/Negative/Neutral)
- **Modern Dashboard**: React + TypeScript + Inertia.js with Shadcn/UI components
- **Real-time Analytics**: Visual sentiment distribution and statistics
- **Data Isolation**: Each user sees only their own pages and comments

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **Laravel Sanctum** - API authentication
- **MySQL** - Database
- **Redis** - Queue and cache driver
- **Facebook Graph API** - Page and comment data
- **OpenAI API** - Sentiment analysis

### Frontend
- **React 18** - UI library
- **TypeScript** - Type safety
- **Inertia.js** - SPA without API
- **TailwindCSS** - Utility-first CSS
- **Shadcn/UI** - Beautiful component library
- **Vite** - Build tool

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- MySQL 8.0+
- Redis
- Facebook App (for OAuth)
- OpenAI API Key

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd fecbook-comments-saas-platform/backend
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 5. Configure Environment Variables

Edit `.env` file with your configuration:

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fb_sentiment
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Redis Configuration (for queues)
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
QUEUE_CONNECTION=redis
```

#### Facebook App Configuration
1. Create a Facebook App at https://developers.facebook.com/
2. Add Facebook Login product
3. Configure OAuth redirect URI: `http://localhost:8000/auth/facebook/callback`
4. Get your App ID and Secret

```env
FACEBOOK_APP_ID=your_facebook_app_id
FACEBOOK_APP_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

#### OpenAI Configuration
Get your API key from https://platform.openai.com/api-keys

```env
OPENAI_API_KEY=your_openai_api_key
```

### 6. Database Setup

Create database:

```bash
mysql -u root -p
CREATE DATABASE fb_sentiment;
exit;
```

Run migrations:

```bash
php artisan migrate
```

Seed demo data (optional):

```bash
php artisan db:seed
```

This creates a demo account:
- **Email**: demo@example.com
- **Password**: password

### 7. Build Frontend Assets

Development:
```bash
npm run dev
```

Production:
```bash
npm run build
```

### 8. Start the Application

#### Terminal 1: Laravel Server
```bash
php artisan serve
```

#### Terminal 2: Queue Worker
```bash
php artisan queue:work
```

#### Terminal 3: Vite Dev Server (for development)
```bash
npm run dev
```

The application will be available at: http://localhost:8000

## 📖 Usage Guide

### 1. User Registration

1. Navigate to http://localhost:8000/register
2. Create an account
3. You'll be redirected to the dashboard

### 2. Connect Facebook Page

1. Click "Connect New Page" button
2. Authorize the Facebook app
3. Select a page from your managed pages
4. The page will be connected

### 3. Sync Comments

Run the sync command manually:

```bash
php artisan facebook:sync-comments
```

Or sync a specific page:

```bash
php artisan facebook:sync-comments --page-id=YOUR_PAGE_ID
```

### 4. View Analytics

- **Dashboard** (`/dashboard`): View connected pages
- **Comments** (`/comments`): Browse all comments with sentiment
- **Analytics** (`/analytics`): View sentiment distribution and statistics

## 🔧 Artisan Commands

### Sync Facebook Comments

```bash
# Sync all active pages
php artisan facebook:sync-comments

# Sync specific page
php artisan facebook:sync-comments --page-id=123456789
```

## 🏗️ Project Structure

```
backend/
├── app/
│   ├── Console/Commands/      # Artisan commands
│   ├── Http/Controllers/      # API & web controllers
│   ├── Jobs/                  # Queue jobs
│   ├── Models/                # Eloquent models
│   ├── Policies/              # Authorization policies
│   └── Services/              # Business logic services
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── css/                   # Stylesheets
│   ├── js/                    # React/TypeScript frontend
│   │   ├── components/        # React components
│   │   ├── pages/             # Inertia pages
│   │   ├── types/             # TypeScript types
│   │   └── lib/               # Utilities
│   └── views/                 # Blade templates
└── routes/
    ├── web.php                # Web routes
    └── api.php                # API routes
```

## 🚀 Deployment

### Production Environment Setup

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Build frontend assets: `npm run build`
3. Optimize Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Set up supervisor for queue workers
5. Configure web server (Nginx/Apache) to serve `public/` directory

## 📊 Database Schema

### Users
- id, name, email, password

### Facebook Pages
- id, user_id, page_id, page_name, access_token, is_active, last_synced_at

### Facebook Comments
- id, facebook_page_id, facebook_comment_id, post_id, message, author_name, author_id, comment_created_time, sentiment_status

### Sentiment Results
- id, facebook_comment_id, sentiment, confidence, reason

## 🔒 Security

- All user data is isolated (multi-tenant)
- Facebook access tokens are encrypted
- CSRF protection enabled
- XSS protection via Laravel
- SQL injection prevention via Eloquent ORM
- Sanctum for API authentication

## 📝 API Documentation

### Authentication

**POST** `/api/register`
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**POST** `/api/login`
```json
{
  "email": "john@example.com",
  "password": "password"
}
```

**POST** `/api/logout` (requires auth)

### Facebook Pages

**GET** `/api/pages` - Get connected pages (requires auth)

**GET** `/api/comments` - Get comments with sentiment (requires auth)

**GET** `/api/analytics` - Get analytics data (requires auth)

## 📄 License

This project is open-source software.

