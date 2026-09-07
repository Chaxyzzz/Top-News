<div align="center">

# TOPNEWS

### Modern Digital News Platform

A scalable, feature-rich, and professionally designed digital news platform built with Laravel.

<br>

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-MIT-111111?style=for-the-badge)](#license)

<br>

**Professional News Publishing System**

Content Management • Editorial Workflow • Media Management • Analytics • Notifications

</div>

---

## About TopNews

**TopNews** is a modern digital news platform designed to provide a complete ecosystem for publishing, managing, organizing, and distributing news content.

The platform combines a professional public-facing news website with a powerful administrative system. Every important content element displayed on the public website is designed to be manageable through the administration panel.

TopNews focuses on:

- Professional digital journalism
- Structured editorial workflows
- Scalable content management
- Responsive user experience
- Media and image management
- Content discovery
- Reader engagement
- Administrative control
- Performance and scalability

The project is designed to support both small editorial teams and larger content operations.

---

## Project Information

| Information | Details |
|---|---|
| Project Name | TopNews |
| Type | Digital News Platform |
| Framework | Laravel 13 |
| Programming Language | PHP 8.3 |
| Database | MySQL |
| Frontend | Blade, CSS, JavaScript |
| Build Tool | Vite |
| Location | Bireuen, Aceh, Indonesia |
| Developer | Zakky Mubaraq |

---

# Core Features

## Public Website

The public website provides a complete news reading experience with modern navigation and content discovery features.

### Available Features

- Responsive homepage
- Latest news
- Popular news
- Trending news
- Breaking news
- Editor's choice
- Category pages
- Tag pages
- Article pages
- Photo stories
- Video content
- Search functionality
- Newsletter subscription
- Contact page
- Static information pages
- Reader accounts
- Bookmarks
- Comments
- Article reactions
- SEO metadata
- Sitemap
- Robots configuration
- Responsive design

---

# Administration Panel

The administrative panel provides centralized control over the entire platform.

## Content Management

Administrators can manage:

- Articles
- Categories
- Tags
- Pages
- Galleries
- Photo stories
- Videos
- Breaking news
- Homepage sections
- Navigation menus

All content management operations support structured workflows including:

- Create
- Read
- Update
- Delete

Content deletion is designed to support permanent removal where configured.

---

## Article Management

TopNews provides a comprehensive article management system.

### Article Features

- Create articles
- Edit articles
- Publish articles
- Schedule articles
- Draft articles
- Archive articles
- Featured articles
- Breaking news
- Editor's choice
- Homepage priority
- Article categories
- Article tags
- SEO metadata
- Featured images
- Image captions
- Image alt text
- Source attribution
- Sponsored content
- Article revisions
- Editorial actions
- Reading time
- View statistics

The platform is structured to support large amounts of published content.

---

# User Management

The administration system includes structured user management.

### Available Capabilities

- Create users
- Edit users
- Manage roles
- Manage permissions
- Activate users
- Suspend users
- Manage editorial accounts
- Permanent account deletion
- Role-based access control

The system includes multiple layers of authorization through middleware and policies.

---

# Role and Permission System

TopNews uses a structured role and permission architecture.

```text
User
 │
 ├── Roles
 │     │
 │     ├── Permissions
 │     │
 │     └── Access Control
 │
 └── Policies
       │
       ├── Content Access
       ├── User Access
       ├── Administration Access
       └── System Access
