# Tourism Booking System

A Laravel-based web application for browsing and booking tour packages in Bolinao.

## Overview

This project supports:
- Tourist registration, login, and booking workflows
- Admin package and destination management
- Booking creation, reservation tracking, and cancellation
- Review submission and deletion by tourists
- Payment record management and verification
- Booking report export in JSON, CSV, XLSX, and PDF
- REST API endpoints for packages, bookings, and payments

## Features

- Authentication: register, login, logout, guest login, secure password hashing
- Admin package CRUD
- Admin destination CRUD
- Tourist package browsing and reservation
- Review creation and deletion
- Admin payment review and status update
- Booking report export
- REST API bookings and payments

## Documentation

Full SRS and implementation details are available in [docs/SRS.md](docs/SRS.md).

## Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy the environment file and generate an app key:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure your database connection in `.env`.

4. Run migrations and optionally seed the database:
```bash
php artisan migrate
php artisan db:seed
```

5. Build frontend assets:
```bash
npm run build
```

6. Start the local development server:
```bash
php artisan serve
```

## Default Accounts

- Admin: `admin@tourph.com` / `password123`
- Tourist: `juan@example.com` / `password123`

## Useful Routes

- Home: `/`
- Tourist packages: `/packages`
- User dashboard: `/dashboard`
- Admin dashboard: `/admin/dashboard`
- Admin packages: `/admin/packages`
- Admin destinations: `/admin/destinations`
- Admin payments: `/admin/payments`
- Reports: `/admin/reports/bookings/{format?}`

## Notes

The application now includes destination and review management, plus admin payment verification and export report support.
