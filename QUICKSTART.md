# Quick Start Guide

Get the Facebook Sentiment Analysis SaaS running in 5 minutes!

## Prerequisites Checklist

Before you begin, ensure you have:

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js 18+ and NPM installed
- [ ] MySQL 8.0+ installed and running
- [ ] Redis installed and running (for queues)

## Quick Setup (Automated)

Run the setup script:

```bash
./setup.sh
```

Then follow the prompts. The script will:
1. Install PHP dependencies
2. Install Node dependencies
3. Create .env file
4. Optionally run migrations and seed demo data

## Manual Setup

If you prefer manual setup:

```bash
cd backend

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure .env with your database credentials
# DB_DATABASE=fb_sentiment
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Create database
mysql -u root -p -e "CREATE DATABASE fb_sentiment;"

# Run migrations
php artisan migrate

# (Optional) Seed demo data
php artisan db:seed
```

## Configure External Services

### 1. Facebook App Setup

1. Go to https://developers.facebook.com/
2. Create a new app
3. Add "Facebook Login" product
4. In Settings → Basic, copy:
   - App ID
   - App Secret
5. In Facebook Login → Settings, add redirect URI:
   - `http://localhost:8000/auth/facebook/callback`
6. Update `.env`:
   ```
   FACEBOOK_APP_ID=your_app_id
   FACEBOOK_APP_SECRET=your_app_secret
   ```

### 2. OpenAI API Key

1. Go to https://platform.openai.com/api-keys
2. Create a new API key
3. Update `.env`:
   ```
   OPENAI_API_KEY=sk-...
   ```

## Start Development Servers

You need 3 terminal windows:

### Terminal 1: Laravel Server
```bash
cd backend
php artisan serve
```

### Terminal 2: Queue Worker
```bash
cd backend
php artisan queue:work
```

### Terminal 3: Vite Dev Server
```bash
cd backend
npm run dev
```

## Access the Application

1. Open your browser: http://localhost:8000

2. If you seeded demo data, login with:
   - **Email**: demo@example.com
   - **Password**: password

3. Or create a new account at: http://localhost:8000/register

## Test the Features

### 1. Connect a Facebook Page
1. Click "Connect New Page"
2. Authorize with Facebook
3. Select a page you manage
4. Page will appear in your dashboard

### 2. Sync Comments
```bash
php artisan facebook:sync-comments
```

This will:
- Fetch recent posts from your connected pages
- Import comments
- Queue them for sentiment analysis

### 3. View Analytics
- Navigate to Analytics to see sentiment distribution
- Check Comments page to see individual sentiments

## Troubleshooting

### "Connection refused" when accessing Laravel
- Make sure `php artisan serve` is running
- Check that port 8000 is not in use

### Queue jobs not processing
- Ensure Redis is running: `redis-cli ping` should return "PONG"
- Make sure queue worker is running: `php artisan queue:work`

### Frontend not loading
- Run `npm run dev` to start Vite
- Clear browser cache and reload

### Database connection errors
- Verify MySQL is running
- Check DB credentials in `.env`
- Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`

## Next Steps

- Read the full [README.md](README.md) for detailed documentation
- Configure Facebook webhooks for real-time updates
- Set up scheduled tasks for automatic comment syncing
- Deploy to production (see README for deployment guide)

## Need Help?

Check the main README.md or open an issue on GitHub.
