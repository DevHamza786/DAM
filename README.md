# Digital Asset Management System

A modern, efficient, and secure Digital Asset Management (DAM) system built with Laravel. This system allows companies to manage their digital assets like films, TV shows, media files, and presentation pitches with advanced features for sharing and access control.

## Features

- **Role-based Access Control**
  - Admin role with full system access
  - Uploader role for managing their own assets
  - Secure authentication and authorization

- **Asset Management**
  - Upload and manage various file types (images, videos, documents)
  - Automatic thumbnail generation for images
  - Efficient file storage using AWS S3
  - Background processing for heavy tasks
  - File metadata and search capabilities

- **Sharing Capabilities**
  - Generate expiring shareable links
  - QR code generation for easy sharing
  - View tracking and analytics
  - Access control for shared assets

- **Performance Optimizations**
  - Background job processing
  - Efficient file storage and retrieval
  - Caching for improved performance
  - Optimized database queries

- **Security Features**
  - Secure file storage
  - Role-based permissions
  - Activity logging
  - Expiring share links
  - View tracking and analytics

## Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Redis for queue management
- AWS S3 account for file storage
- Composer
- Node.js and NPM

## Installation

1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd dam
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Configure your environment variables in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dam
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   AWS_ACCESS_KEY_ID=your_aws_key
   AWS_SECRET_ACCESS_KEY=your_aws_secret
   AWS_DEFAULT_REGION=your_aws_region
   AWS_BUCKET=your_bucket_name
   AWS_URL=your_bucket_url

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   FILESYSTEM_DISK=public
   ```

6. Generate application key:
   ```bash
   php artisan key:generate
   ```

7. Run migrations:
   ```bash
   php artisan migrate
   ```

8. Create storage link:
   ```bash
   php artisan storage:link
   ```

9. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```

## Usage

1. **Admin Setup**
   - Create an admin user through the registration process
   - Assign the admin role to the user
   - Configure system settings

2. **User Management**
   - Create uploader accounts
   - Assign appropriate roles
   - Manage user permissions

3. **Asset Management**
   - Upload files through the web interface
   - Organize assets with metadata
   - Search and filter assets
   - Manage asset permissions

4. **Sharing Assets**
   - Generate shareable links
   - Create QR codes for easy sharing
   - Monitor asset access
   - Set expiration dates and view limits

## Security Considerations

- All file uploads are validated and scanned
- Files are stored securely in AWS S3
- Access is controlled through role-based permissions
- Share links expire automatically
- All actions are logged for audit purposes

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
