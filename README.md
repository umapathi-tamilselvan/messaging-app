# India-Focused Messaging App

A production-ready, scalable messaging platform built with Laravel backend, MySQL database, Redis for real-time operations, WebSocket for instant delivery, and cloud storage for media. Designed for the Indian market with data residency compliance, low-bandwidth optimization, and cost-effective scaling.

## Features

- **OTP-based Authentication**: Phone number verification using OTP
- **Real-time Messaging**: Instant message delivery via WebSocket (Pusher)
- **Private & Group Conversations**: Support for 1-on-1 and group chats
- **Media Attachments**: Direct-to-S3 upload with presigned URLs
- **Read Receipts**: Message delivery and seen status tracking
- **Message Status Tracking**: Sent, delivered, and seen statuses
- **Caching**: Redis-based caching for conversations and presence
- **API Authentication**: Laravel Sanctum token-based authentication

## Architecture

### Core Components

- **Laravel API Servers**: Stateless REST API cluster
- **MySQL Database**: Primary database with read replicas support
- **Redis**: Caching and pub/sub for real-time operations
- **WebSocket**: Pusher for instant message delivery
- **S3 Storage**: Cloud storage for media attachments

### Database Schema

- **users**: User profiles with phone-based authentication
- **conversations**: Private and group conversations
- **messages**: Message content with soft deletes
- **attachments**: Media file metadata
- **message_statuses**: Delivery and read tracking
- **otps**: One-time password management

## Installation

### Prerequisites

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- Redis 7+
- Node.js 18+ (for frontend assets)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repo-url>
cd messaging-app
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Update `.env` file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=messaging_app
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=your_pusher_host
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=your_bucket_name
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Start the development server**
```bash
php artisan serve
```

7. **Start queue worker** (in a separate terminal)
```bash
php artisan queue:work
```

## API Endpoints

### Authentication

#### Send OTP
```http
POST /api/auth/otp
Content-Type: application/json

{
  "phone": "+919876543210"
}
```

#### Verify OTP
```http
POST /api/auth/verify
Content-Type: application/json

{
  "phone": "+919876543210",
  "otp": "123456"
}
```

Response:
```json
{
  "token": "eyJ...",
  "user": {
    "id": 1,
    "phone": "+919876543210",
    "name": null,
    "avatar_url": null,
    "status": "offline",
    "last_seen": null
  }
}
```

### Conversations

#### List Conversations
```http
GET /api/conversations?page=1&limit=20
Authorization: Bearer {token}
```

#### Create Private Conversation
```http
POST /api/conversations
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "private",
  "user_id": 2
}
```

#### Create Group Conversation
```http
POST /api/conversations
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "group",
  "name": "Team Chat",
  "user_ids": [2, 3, 4]
}
```

#### Get Conversation Messages
```http
GET /api/conversations/{id}/messages?before_id=12345&limit=50
Authorization: Bearer {token}
```

### Messages

#### Send Message
```http
POST /api/conversations/{id}/messages
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "Hello!",
  "type": "text",
  "reply_to_id": null
}
```

#### Mark Message as Seen
```http
PUT /api/messages/{id}/seen
Authorization: Bearer {token}
```

#### Delete Message
```http
DELETE /api/messages/{id}
Authorization: Bearer {token}
```

### Upload

#### Get Presigned Upload URL
```http
POST /api/upload/sign
Authorization: Bearer {token}
Content-Type: application/json

{
  "filename": "photo.jpg",
  "mime_type": "image/jpeg",
  "size": 1024000
}
```

Response:
```json
{
  "upload_url": "https://s3.amazonaws.com/...",
  "attachment_id": 123,
  "expires_in": 300
}
```

## WebSocket Events

### Client → Server

Subscribe to conversation:
```javascript
pusher.subscribe('private-conversation.123');
```

### Server → Client

#### New Message
```javascript
pusher.bind('message:new', (data) => {
  // data: { id, conversation_id, user, message, type, created_at }
});
```

#### Message Seen
```javascript
pusher.bind('message:seen', (data) => {
  // data: { message_id, user_id, seen_at }
});
```

#### Message Deleted
```javascript
pusher.bind('message:deleted', (data) => {
  // data: { message_id, deleted_at }
});
```

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Queue Processing
For development, use:
```bash
php artisan queue:work
```

For production, use a process manager like Supervisor.

## Production Deployment

### Environment Setup

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up Redis cluster
5. Configure S3 bucket with proper permissions
6. Set up Pusher account or self-hosted Socket.IO

### Queue Configuration

Use Supervisor to manage queue workers:
```ini
[program:messaging-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/logs/queue.log
```

### Database Scaling

- Set up MySQL read replicas for read-heavy operations
- Use connection pooling for high concurrency
- Monitor replica lag (<1 second target)

### Caching Strategy

- Cache conversation lists with 1-hour TTL
- Cache user presence with 5-minute TTL
- Invalidate cache on new messages

## Security

- All API endpoints require authentication via Laravel Sanctum
- OTP rate limiting: 3 requests per hour per phone
- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- XSS protection for message content
- S3 presigned URLs with 5-minute expiration

## Performance Optimization

- Use Redis for caching conversation lists
- Implement database indexes as per migrations
- Use connection pooling for database
- Enable query result caching for frequently accessed data
- Compress attachments before storage
- Use CDN for static assets

## Cost Optimization (India)

- Use AWS Mumbai region (ap-south-1) for data residency
- S3 Intelligent Tiering for old media
- Reserved instances for RDS and EC2
- Self-host WebSocket server at scale (>50K users)
- Aggressive caching to reduce database load

## Monitoring

Key metrics to monitor:
- API response time (target: <200ms p95)
- Message delivery latency (target: <2s for online users)
- Database replica lag (<1 second)
- Queue processing time
- Cache hit rate (>90%)
- Error rate (<0.1%)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please open an issue on GitHub.
