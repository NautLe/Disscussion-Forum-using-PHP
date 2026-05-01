# Programming Discussion Forum with AI Assistant 

## Overview
A full-stack web discussion platform inspired by Stack Overflow where users can ask questions, create topics, share programming knowledge, and interact with an AI assistant for support.

## Features

### User Authentication
- User Registration
- User Login / Logout
- Admin Login
- Session Management

### Discussion Forum
- Create discussion topics
- Add posts and questions
- Edit and delete posts
- Manage modules/categories
- View and participate in discussions

### AI Chat Assistance
- Integrated AI chatbot support
- Interactive help for users
- Supports question guidance and assistance

### Admin Functions
- Manage users
- Manage posts
- Manage modules
- Contact admin functionality

## Technologies Used

### Frontend
- HTML5
- CSS3
- JavaScript

### Backend
- PHP

### Database
- MySQL

### Tools
- XAMPP
- PHPMailer

## Project Structure
```bash
project/
│
├── admin_login.php
├── admin_dashboard.php
├── add_post.php
├── add_user.php
├── create_post.php
├── contact_admin.php
├── edit_post.php
├── delete_post.php
├── dbConnect.php
└── uploads/
```

## Key Functionalities
✔ User authentication system  
✔ Topic and post management (CRUD)  
✔ Discussion forum system  
✔ AI chatbot assistance  
✔ Admin dashboard  
✔ Contact admin feature

## Installation

Clone repository:

```bash
git clone https://github.com/NautLe/Disscussion-Forum-using-PHP.git
```

Move project into XAMPP htdocs:

```bash
xampp/htdocs/project-folder
```

Import database into MySQL.

Configure database connection in:

```php
dbConnect.php
```

Start Apache and MySQL in XAMPP.

Run project:

```bash
http://localhost/project-folder
```

## Future Improvements
- Upvote and downvote system
- Comment replies
- Real-time chat
- Search and filtering
- User profiles and reputation system
- Enhanced AI assistant features

## Author
Developed by Tuan Le
