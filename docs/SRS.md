# Tourism Booking System — Software Requirements Specification (SRS)

## 1. Introduction

**Project Title:** Tourism Booking System

**Purpose:**
A Laravel web application for tourists to browse tour packages, book tours, manage reservations, pay for bookings, and submit tour reviews. Administrators can manage tour packages, destinations, bookings, payments, and run reports.

**Scope:**
- User registration and login
- Browse available tour packages
- Book tours and manage reservations
- Submit payment information
- View booking history
- Submit reviews
- Generate reports in JSON, CSV, PDF, and XLSX
- RESTful API endpoints for packages, bookings, and payments
- Admin management of destinations, packages, payments, and reports

## 2. Functional Requirements

### FR1 Authentication
- Register new tourist accounts
- Login as tourist or admin
- Logout and session handling
- Password hashing and validation
- Guest login support

### FR2 Destination Management
- Add destinations
- Edit destinations
- Delete destinations
- View destinations in admin panel
- Link tour packages to destinations

### FR3 Tour Package Management
- Create tour packages
- Update tour packages
- Delete tour packages
- View package details
- Upload package images
- Set availability status and ratings

### FR4 Booking Management
- Book tour packages
- Cancel bookings
- Track booking status
- Manage booking details from the dashboard

### FR5 Payment Management
- Store payment records
- Track payment status
- Manage payments in admin dashboard
- Support proof reference data for payments

### FR6 Review Management
- Submit reviews for tour packages
- Delete own reviews
- Display package reviews on package detail pages

### FR7 Reports
- Export booking reports in JSON, CSV, PDF, and XLSX
- View booking summaries in admin report endpoints

## 3. Non-Functional Requirements

- Secure authentication and middleware protection
- Mobile-responsive UI and admin pages
- Fast server-side response times through Eloquent queries
- Clear separation between admin and tourist routes

## 4. User Roles

### Admin
- Manage destinations and packages
- Manage payments
- Generate and export reports
- Access admin dashboard

### Tourist
- Browse tour packages
- Book tours
- Submit reviews
- View booking history

## 5. Entity Relationship Diagram (ERD)

- USERS 1 --- * BOOKINGS
- DESTINATIONS 1 --- * TOUR_PACKAGES
- TOUR_PACKAGES 1 --- * BOOKINGS
- BOOKINGS 1 --- 1 PAYMENTS
- USERS 1 --- * REVIEWS
- TOUR_PACKAGES 1 --- * REVIEWS

## 6. Database Schema

### users
- id
- name
- email
- password
- role
- remember_token
- timestamps

### destinations
- id
- name
- location
- description
- timestamps

### tour_packages
- id
- destination_id
- name
- description
- location
- price
- duration_days
- max_guests
- image
- status
- rating
- timestamps

### bookings
- id
- booking_number
- user_id
- tour_package_id
- tour_date
- num_guests
- total_price
- special_requests
- status
- timestamps

### payments
- id
- booking_id
- amount
- method
- reference_number
- proof
- status
- paid_at
- timestamps

### reviews
- id
- user_id
- tour_package_id
- rating
- comment
- timestamps

## 7. Web Routes

### Authentication
- GET /login → login modal route on landing page
- POST /login → `AuthController@loginTourist`
- POST /register → `AuthController@register`
- POST /logout → `AuthController@logout`
- GET /admin/login → `AuthController@showAdminLoginForm`
- POST /admin/login → `AuthController@loginAdmin`
- POST /guest-login → `AuthController@guestLogin`

### Tourist Routes
- GET /dashboard → tourist dashboard
- GET /packages → browse packages
- GET /packages/{tourPackage} → package detail
- POST /bookings → create booking
- GET /bookings/{tourPackage}/create → booking form
- GET /reservations → user reservations
- GET /reservations/{booking} → reservation detail
- DELETE /reservations/{booking} → cancel reservation
- POST /packages/{tourPackage}/reviews → submit review
- DELETE /reviews/{review} → delete own review

### Admin Routes
- GET /admin/dashboard → admin dashboard
- GET /admin/packages → manage packages
- GET /admin/packages/create → create package
- POST /admin/packages → store package
- GET /admin/packages/{package}/edit → edit package
- PUT /admin/packages/{package} → update package
- DELETE /admin/packages/{package} → delete package
- GET /admin/destinations → manage destinations
- GET /admin/destinations/create → create destination
- POST /admin/destinations → store destination
- GET /admin/destinations/{destination}/edit → edit destination
- PUT /admin/destinations/{destination} → update destination
- DELETE /admin/destinations/{destination} → delete destination
- GET /admin/payments → admin payments list
- GET /admin/payments/{payment}/edit → review payment
- PUT /admin/payments/{payment} → update payment
- GET /admin/reports/bookings/{format?} → export bookings report

## 8. API Routes

### Public package endpoints
- GET /api/packages
- GET /api/packages/{package}

### Authenticated endpoints
- GET /api/bookings
- POST /api/bookings
- GET /api/bookings/{booking}
- PUT /api/bookings/{booking}
- DELETE /api/bookings/{booking}
- GET /api/payments
- POST /api/payments
- GET /api/payments/{payment}

## 9. Controllers

### AuthController
- `loginTourist()`
- `loginAdmin()`
- `register()`
- `guestLogin()`
- `logout()`

### DestinationController
- `index()`
- `create()`
- `store()`
- `edit()`
- `update()`
- `destroy()`

### Admin\PackageController
- `index()`
- `create()`
- `store()`
- `show()`
- `edit()`
- `update()`
- `destroy()`

### BookingController (Tourist)
- `create()`
- `storeBooking()`
- `index()` / `reservations()`
- `show()`
- `cancel()`

### PaymentController (Admin)
- `index()`
- `edit()`
- `update()`

### ReviewController
- `store()`
- `destroy()`

### ReportController
- `bookings()`

## 10. Migrations

- `create_users_table`
- `add_role_to_users_table`
- `create_admins_table`
- `create_tour_packages_table`
- `create_bookings_table`
- `create_payments_table`
- `create_destinations_table`
- `add_destination_id_to_tour_packages_table`
- `create_reviews_table`
- `add_proof_to_payments_table`

## 11. Eloquent Relationships

### User
- `hasMany(Bookings)`
- `hasMany(Reviews)`

### Destination
- `hasMany(TourPackages)`

### TourPackage
- `belongsTo(Destination)`
- `hasMany(Bookings)`
- `hasMany(Reviews)`

### Booking
- `belongsTo(User)`
- `belongsTo(TourPackage)`
- `hasOne(Payment)`

### Payment
- `belongsTo(Booking)`

### Review
- `belongsTo(User)`
- `belongsTo(TourPackage)`

## 12. Middleware

- `auth` protects tourist and admin routes
- `EnsureAdmin` protects admin dashboard, package, destination, payment, and report routes
- `guest` redirects authenticated users away from auth pages

## 13. Notes

- The system now includes admin destination CRUD support and package-to-destination linkage.
- Tourists can submit and delete their own reviews on package detail pages.
- Admins can manage payment statuses and store proof references.
- Booking reports can be exported as JSON, CSV, XLSX, and PDF.
