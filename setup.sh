#!/bin/bash

# Quick Start Script for Facebook Sentiment SaaS Platform
# This script helps set up the development environment

echo "🚀 Setting up Facebook Sentiment SaaS Platform..."
echo ""

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    cd backend 2>/dev/null || {
        echo "❌ Error: Please run this script from the project root or backend directory"
        exit 1
    }
fi

# Check prerequisites
echo "📋 Checking prerequisites..."

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer."
    exit 1
fi

# Check Node
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+."
    exit 1
fi

# Check MySQL
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL client not found. Make sure MySQL server is running."
fi

echo "✅ Prerequisites check passed"
echo ""

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

echo ""
echo "📦 Installing Node dependencies..."
npm install

# Setup environment
if [ ! -f ".env" ]; then
    echo ""
    echo "🔧 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ .env file created. Please configure your database and API keys."
else
    echo "✅ .env file already exists"
fi

# Database setup
echo ""
read -p "🗄️  Do you want to run database migrations now? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate
    
    read -p "📊 Do you want to seed demo data? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed
        echo ""
        echo "✅ Demo data seeded!"
        echo "📧 Demo Email: demo@example.com"
        echo "🔑 Demo Password: password"
    fi
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "To start the development servers, run these commands in separate terminals:"
echo ""
echo "Terminal 1 (Laravel):"
echo "  cd backend && php artisan serve"
echo ""
echo "Terminal 2 (Queue Worker):"
echo "  cd backend && php artisan queue:work"
echo ""
echo "Terminal 3 (Vite Dev Server):"
echo "  cd backend && npm run dev"
echo ""
echo "Then visit: http://localhost:8000"
echo ""
