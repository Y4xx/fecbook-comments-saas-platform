# Project Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                       Frontend (React)                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Auth Pages  │  │  Dashboard   │  │  Analytics   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│           ▲                ▲                 ▲               │
│           │         Inertia.js              │               │
│           └────────────────┼────────────────┘               │
└─────────────────────────────┼──────────────────────────────┘
                              │
┌─────────────────────────────┼──────────────────────────────┐
│                    Laravel Backend                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Controllers  │  │  Services    │  │    Jobs      │     │
│  │              │  │              │  │              │     │
│  │ • Auth       │  │ • Facebook   │  │ • Sentiment  │     │
│  │ • Pages      │  │ • OpenAI     │  │   Analysis   │     │
│  │ • Comments   │  │              │  │              │     │
│  │ • Analytics  │  │              │  │              │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│           │                │                 │              │
│           ▼                ▼                 ▼              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Eloquent ORM / Models                   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────┼──────────────────────────────┘
                              │
┌─────────────────────────────┼──────────────────────────────┐
│                        MySQL Database                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  users   │  │  pages   │  │ comments │  │sentiment │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    External Services                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Facebook    │  │   OpenAI     │  │    Redis     │      │
│  │  Graph API   │  │     API      │  │   (Queues)   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. User Authentication Flow
```
User → Register/Login → Sanctum Token → Authenticated Session
```

### 2. Facebook Page Connection Flow
```
User → "Connect Page" Button
  ↓
Facebook OAuth Dialog (permissions request)
  ↓
Callback with short-lived token
  ↓
Exchange for long-lived token
  ↓
Fetch user's pages
  ↓
User selects page
  ↓
Store in database (encrypted token)
```

### 3. Comment Sync Flow
```
Artisan Command / Webhook
  ↓
FacebookService.getPagePosts()
  ↓
For each post: FacebookService.getPostComments()
  ↓
Store in facebook_comments table
  ↓
Dispatch AnalyzeCommentSentimentJob
  ↓
Queue Worker picks up job
  ↓
OpenAISentimentService.analyzeSentiment()
  ↓
Store result in sentiment_results table
  ↓
Update comment status to "analyzed"
```

### 4. Real-time Webhook Flow
```
Facebook → New Comment Event
  ↓
POST /api/webhooks/facebook
  ↓
Verify signature
  ↓
Parse event data
  ↓
Create FacebookComment record
  ↓
Dispatch AnalyzeCommentSentimentJob
  ↓
Process in background
```

## Database Schema

### users
```sql
- id (PK)
- name
- email (unique)
- password (hashed)
- created_at
- updated_at
```

### facebook_pages
```sql
- id (PK)
- user_id (FK → users.id)
- page_id (unique)
- page_name
- access_token (encrypted)
- is_active (boolean)
- last_synced_at (timestamp)
- created_at
- updated_at
```

### facebook_comments
```sql
- id (PK)
- facebook_page_id (FK → facebook_pages.id)
- facebook_comment_id (unique)
- post_id
- message (text)
- author_name
- author_id
- comment_created_time
- sentiment_status (enum: pending|analyzing|analyzed|failed)
- created_at
- updated_at
```

### sentiment_results
```sql
- id (PK)
- facebook_comment_id (FK → facebook_comments.id)
- sentiment (enum: positive|negative|neutral)
- confidence (decimal 0-1)
- reason (text)
- created_at
- updated_at
```

## Key Components

### Backend Services

#### FacebookService
- `getUserPages()` - Fetch pages user manages
- `getPagePosts()` - Get posts from a page
- `getPostComments()` - Get comments for a post
- `getLongLivedToken()` - Exchange short token for long-lived

#### OpenAISentimentService
- `analyzeSentiment()` - Analyze single comment
- `analyzeBatch()` - Batch analyze multiple comments
- Returns: sentiment, confidence, reason

### Jobs

#### AnalyzeCommentSentimentJob
- Queued job for async sentiment analysis
- Updates comment status
- Stores sentiment result
- Handles failures gracefully

### Commands

#### facebook:sync-comments
- Manual sync trigger
- Can target specific page with `--page-id`
- Fetches latest posts and comments
- Dispatches analysis jobs

### Frontend Pages

#### Authentication
- `/login` - User login
- `/register` - User registration

#### Dashboard
- `/dashboard` - Connected pages overview
- `/comments` - Comments list with sentiment
- `/analytics` - Analytics and statistics

#### Facebook Integration
- `/facebook/select-page` - Page selection after OAuth

## Security Features

1. **Multi-tenancy**: User data isolation via Eloquent scopes
2. **API Authentication**: Laravel Sanctum token-based auth
3. **Authorization**: Policies for resource access control
4. **Token Encryption**: Facebook tokens stored encrypted
5. **CSRF Protection**: Built-in Laravel CSRF
6. **XSS Protection**: React escapes by default
7. **SQL Injection**: Eloquent ORM prevents injection

## Scalability Considerations

1. **Queue System**: Redis-backed queues for async processing
2. **Database Indexing**: Indexes on foreign keys and frequently queried fields
3. **Caching**: Redis cache for frequently accessed data
4. **API Rate Limiting**: Can be added via Laravel throttle middleware
5. **Horizontal Scaling**: Stateless design allows multiple app instances

## Technology Stack Summary

### Backend
- PHP 8.2+
- Laravel 12
- MySQL 8.0+
- Redis

### Frontend
- React 18
- TypeScript 5
- Inertia.js
- TailwindCSS 3
- Vite 5

### External APIs
- Facebook Graph API v21.0
- OpenAI GPT-4o-mini

### Development Tools
- Composer
- NPM
- Laravel Artisan
- Vite Dev Server
