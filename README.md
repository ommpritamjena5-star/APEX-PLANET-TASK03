# ApexPlanet Task 3 - CRUD Application with Search & Post Detail View

## Project Overview

A PHP and MySQL-based web application with user authentication, full CRUD functionality, real-time and server-side search filtering, single post detail view, and a modern, responsive user interface featuring rich text formatting (Bold, Italic, Underline).

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3 (Custom Design System & Modern Typography)
- JavaScript (Client-side real-time search & autocomplete)
- XAMPP
- Git & GitHub

## Features

- **User Authentication:**
  - Registration with duplicate username prevention
  - Secure password hashing (`password_hash` with `PASSWORD_DEFAULT`)
  - Login & session-based access control
  - Logout functionality
- **Search System:**
  - Real-time client-side live filtering (instantly hides non-matching posts as you type)
  - Server-side prepared statement search across `title` and `content`
  - Instant autocomplete search dropdown &mdash; clicking any match opens that post immediately
  - Keyword highlighting on matched post titles
  - One-click clear search button (`✕`)
- **Single Post View (`posts/view.php`):**
  - Click on any post title, post card, or "👁️ View Post" button to view that specific post
  - Displays full post title, formatted content, published timestamp, and post ID
  - Action buttons: Back to all posts, Edit post, Delete post, and Share / Copy post link
- **Full CRUD Operations on Posts:**
  - **Create:** Add new posts with rich formatting toolbar
  - **Read (All):** Browse all posts with timestamps and search options
  - **Read (Single):** Inspect individual posts in full view mode (`posts/view.php`)
  - **Update:** Edit existing post titles and content
  - **Delete:** Remove posts with confirmation dialog
- **Rich Text Formatting Toolbar:**
  - Format text with **Bold** (`Ctrl+B`), *Italic* (`Ctrl+I`), and <u>Underline</u> (`Ctrl+U`)
- **Modern UI & UX:**
  - Plus Jakarta Sans typography, card-based layout, and micro-animations

## Database Setup

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin/`) or MySQL CLI.
2. Import the provided [`database.sql`](database.sql) file.
3. The database name is `blog`, with two tables: `users` and `posts`.

### Database Schema

#### `users` Table
| Column | Type | Attributes |
|---|---|---|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| username | VARCHAR(100) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL |

#### `posts` Table
| Column | Type | Attributes |
|---|---|---|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| content | TEXT | NOT NULL |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

## Project Structure

```text
ApexPlanet-Task-03/
├── config/
│   └── database.php
├── auth/
│   ├── register.php
│   ├── login.php
│   └── logout.php
├── posts/
│   ├── create.php
│   ├── index.php    (Search bar + Post list)
│   ├── view.php     (Single Post Detail View)
│   ├── edit.php
│   └── delete.php
├── database.sql
├── index.php
├── README.md
└── style.css
```
