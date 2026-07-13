# Laravel URL Shortener ENG

A Laravel 12 web application that allows authenticated users to create shortened URLs, track every visitor, record click analytics, and manage links through both a custom dashboard and a Filament admin panel.

## 1. Project Overview —What is this application?

Laravel URL Shortener is a web application that allows authenticated users to create and manage shortened URLs through a custom dashboard. Each generated short link redirects visitors to its original destination while automatically recording click analytics such as the visitor's IP address and the time of the visit.

The application separates user functionality from administration. Registered users can create, view, monitor, and delete only their own links, while administrators can manage application data through a Filament v3 admin panel built on the existing Laravel Eloquent models.

The project was developed to demonstrate practical backend development using Laravel, including authentication, authorization, Eloquent relationships, database design, request validation, analytics tracking, and CRUD operations.

## 2. Project Features -What can it do?

### Authentication

- User registration and login using Laravel Breeze.
- Route protection with Laravel authentication middleware.
- Users can access only their own dashboard and links.

### URL Shortening

- Create shortened URLs from original URLs.
- Generate unique 6-character short codes.
- Prevent duplicate short codes before saving.

### Link Management

- Display all links belonging to the authenticated user.
- Delete existing links.
- Automatically remove related click history using cascade delete.

### Click Analytics

- Redirect visitors to the original URL.
- Record every click automatically.
- Store the visitor's IP address.
- Store the click timestamp.
- Display the total number of clicks for each link.
- View complete click history ordered by the most recent click.

### Security

- Server-side URL validation.
- Custom validation error messages.
- Authorization checks to prevent users from viewing or deleting links they do not own.

### Filament Admin Panel

- Manage links through Filament v3.
- Manage click records through Filament v3.
- Search records.
- Sort records.
- Display Eloquent relationships within the admin panel.

## 3. Technologies Used

### Backend

- PHP 8.4
- Laravel 12

### Database

- MySQL

### ORM

- Laravel Eloquent ORM

### Authentication

- Laravel Breeze

### Admin Panel

- Filament v3

### Frontend

- Blade
- Tailwind CSS

### Development Tools

- Composer
- Git
- GitHub

### Local Development

- Laravel Herd

## 4. Database Structure

The application uses three main database tables:

### users

Stores registered users who can log in and manage their own shortened links.

| Column     | Description                                     |
| ---------- | ----------------------------------------------- |
| id         | Primary key that uniquely identifies each user. |
| name       | User's name.                                    |
| email      | User's email address used for authentication.   |
| password   | Hashed user password.                           |
| created_at | Date and time the account was created.          |
| updated_at | Date and time the account was last updated.     |

---

### links

Stores every shortened URL created by authenticated users.

| Column       | Description                                                                  |
| ------------ | ---------------------------------------------------------------------------- |
| id           | Primary key that uniquely identifies each shortened link.                    |
| user_id      | Foreign key that references `users.id` and identifies the owner of the link. |
| original_url | The destination URL entered by the user.                                     |
| short_code   | The unique six-character code used to generate the shortened URL.            |
| created_at   | Date and time the link was created.                                          |
| updated_at   | Date and time the link was last updated.                                     |

---

### clicks

Stores every visit made through a shortened URL.

A new row is inserted into this table every time a visitor opens a shortened link.

| Column     | Description                                                                   |
| ---------- | ----------------------------------------------------------------------------- |
| id         | Primary key that uniquely identifies each recorded click.                     |
| link_id    | Foreign key that references `links.id` and identifies which link was clicked. |
| ip_address | IP address of the visitor.                                                    |
| created_at | Date and time the click occurred.                                             |
| updated_at | Date and time the record was last updated.                                    |

---

### Table Responsibilities

**users**

Stores registered users who can authenticate and manage their own shortened links.

**links**

Stores every shortened URL created by authenticated users. Each link belongs to one user and contains both the original destination URL and its generated short code.

**clicks**

Stores every visit made through a shortened URL. A new row is created each time a visitor opens a short link, allowing the application to record click history, visitor IP addresses, timestamps, and total click counts.

## 5. Application Architecture

The application follows Laravel's MVC (Model–View–Controller) architecture. Each layer has a single responsibility, making the application easier to understand, maintain, and extend.

### Architecture Overview

```text
Browser
    │
    ▼
Routes (web.php)
    │
    ▼
Controllers
    │
    ▼
Models (Eloquent ORM)
    │
    ▼
MySQL Database
    │
    ▼
Blade Views
    │
    ▼
Browser Response
```

### Component Responsibilities

#### Routes

Routes receive HTTP requests from the browser and determine which controller method should handle the request.

Examples:

- `GET /dashboard`
- `POST /links`
- `GET /{shortCode}`
- `GET /links/{link}/stats`
- `DELETE /links/{link}`

---

#### Controllers

Controllers contain the application's business logic.

Responsibilities include:

- Validating user input.
- Generating unique short codes.
- Querying the database.
- Recording click analytics.
- Redirecting visitors.
- Passing data to Blade views.

---

#### Models

Eloquent models represent database tables and define the relationships between them.

```
User
    │
    └── hasMany(Link)

Link
    ├── belongsTo(User)
    └── hasMany(Click)

Click
    └── belongsTo(Link)
```

These relationships allow Laravel to navigate between related database records without writing manual SQL joins.

---

#### Blade Views

Blade templates display data received from controllers.

Examples include:

- Dashboard
- Statistics page
- Authentication pages

Blade is responsible only for presenting data and does not contain business logic.

---

#### Database

The MySQL database stores the application's persistent data.

- `users` stores registered users.
- `links` stores shortened URLs.
- `clicks` stores click analytics.

The database is accessed through Laravel's Eloquent ORM.

## 6. Application Flow

The application processes requests by following Laravel's MVC (Model–View–Controller) architecture. Every user request passes through a predictable flow before a response is returned to the browser.

### Request Lifecycle

```text
Browser
    │
    ▼
Route
    │
    ▼
Controller
    │
    ▼
Model (Eloquent ORM)
    │
    ▼
MySQL Database
    │
    ▼
Controller
    │
    ▼
Blade View / Redirect
    │
    ▼
Browser Response
```

### Flow Explanation

1. The browser sends an HTTP request.

2. Laravel matches the request to a route defined in `routes/web.php`.

3. The matched route calls the appropriate controller method.

4. The controller performs the required application logic, such as validating data, querying the database, generating short codes, recording analytics, or redirecting visitors.

5. The controller communicates with Eloquent models to retrieve or update database records.

6. The database returns the requested data, and Eloquent converts it into model object(s) before passing it back to the controller.

7. The controller either:
    - passes data to a Blade view for display, or
    - redirects the user to another page.

8. Laravel returns the final HTTP response to the browser.

## 7. Authentication Flow

### Login Flow

```text
Browser

↓

POST /login

↓

Route
POST /login

↓

Laravel Authentication Controller

↓

Validate email and password

↓

User model

↓

users table

↓

Credentials match?

├── No
│     ↓
│  Return validation errors
│
└── Yes
      ↓
Create authenticated session

↓

Redirect to:

GET /dashboard

↓

Route

↓

LinkController::index()

↓

Link model

↓

links table

↓

dashboard.blade.php

↓

Browser
```

### Flow Explanation

1. The user submits the login form.

2. Laravel matches the request to:

```
POST /login
```

3. Laravel's authentication controller validates the submitted credentials.

4. The `User` model searches the `users` table for the submitted email address.

5. If the credentials are valid, Laravel creates an authenticated session.

6. Laravel redirects the user to:

```
GET /dashboard
```

7. The `/dashboard` route calls:

```php
LinkController::index()
```

8. `LinkController::index()` retrieves all links belonging to the authenticated user.

9. The retrieved links are passed to:

```text
dashboard.blade.php
```

10. Laravel returns the rendered dashboard to the browser.

## 8. URL Shortening Flow

This flow begins when an authenticated user submits a URL through the dashboard to generate a shortened link.

### URL Shortening Flow

```text
Authenticated User

↓

Dashboard

↓

POST /links

↓

Route

↓

LinkController::store(Request $request)

↓

Validate submitted URL

↓

Generate unique short code

↓

Link model

↓

links table

↓

Create new row

↓

Redirect to:

GET /dashboard

↓

Route

↓

LinkController::index()

↓

Link model

↓

Retrieve all links belonging to the authenticated user

↓

dashboard.blade.php

↓

Browser
```

### Flow Explanation

1. The authenticated user enters a URL into the dashboard form and clicks **Create Short Link**.

2. The browser submits:

```
POST /links
```

3. Laravel matches the request to:

```php
Route::post('/links', [LinkController::class, 'store']);
```

4. Laravel calls:

```php
LinkController::store(Request $request)
```

5. The controller validates the submitted URL.

6. A unique six-character short code is generated.

7. The `Link` model creates a new row in the `links` table containing:

- authenticated user's ID
- original URL
- generated short code

8. After the link is successfully created, Laravel redirects the user to:

```
GET /dashboard
```

9. Laravel matches:

```php
Route::get('/dashboard', [LinkController::class, 'index']);
```

10. Laravel calls:

```php
LinkController::index()
```

11. The `Link` model retrieves every link belonging to the authenticated user.

12. The retrieved Link model objects are passed to:

```text
dashboard.blade.php
```

13. Blade renders the updated dashboard showing the newly created shortened link.

## 9. Redirect and Click Tracking Flow

This flow begins when a visitor opens a shortened URL.

### Redirect and Click Tracking Flow

```text
Visitor opens:

GET /eR7aM8

↓

Route:

GET /{shortCode}

↓

Laravel assigns:

{shortCode} = eR7aM8

↓

LinkController::redirect($shortCode)

↓

Link model

↓

Search the `links` table where:

links.short_code = eR7aM8

↓

Matching Link model object stored in:

$link

↓

Click model

↓

Create a new row in the `clicks` table:

clicks.link_id = $link->id
clicks.ip_address = visitor IP
clicks.created_at = current time

↓

Redirect visitor to:

$link->original_url
```

### Flow Explanation

1. The visitor opens a shortened URL, for example:

```text
http://127.0.0.1:8000/eR7aM8
```

2. Laravel matches the request to:

```php
Route::get('/{shortCode}', [LinkController::class, 'redirect']);
```

3. Laravel extracts the value after the forward slash and assigns it to the route parameter:

```php
$shortCode = "eR7aM8";
```

4. Laravel calls:

```php
LinkController::redirect($shortCode)
```

5. The `Link` model searches the `links` table where:

```text
links.short_code = eR7aM8
```

6. If a matching row is found, Eloquent converts the returned database row into a `Link` model object and stores it in:

```php
$link
```

7. The `Click` model creates a new row in the `clicks` table containing:

```text
link_id     = $link->id
ip_address  = request()->ip()
created_at  = current timestamp
```

8. Laravel redirects the visitor to:

```php
$link->original_url
```

9. The browser automatically loads the original destination URL.

## 10. Statistics Flow

This flow begins when an authenticated user opens the statistics page for one of their links.

### Statistics Flow

```text
Authenticated User

↓

Clicks:

View Statistics

↓

GET /links/8/stats

↓

Route:

GET /links/{link}/stats

↓

Laravel assigns:

{link} = 8

↓

Laravel uses route model binding

↓

Link model searches:

links.id = 8

↓

Matching row is stored in:

$link

↓

LinkController::stats(Link $link)

↓

Authorization check:

$link->user_id = auth()->id()

↓

Use the `clicks()` relationship

↓

Search the `clicks` table where:

clicks.link_id = $link->id

↓

Order matching rows from newest to oldest

↓

Store Click model objects in:

$clicks

↓

Pass `$link` and `$clicks` to:

links/stats.blade.php

↓

Blade displays:

Original URL
Visitor IP addresses
Click timestamps

↓

Browser
```

### Flow Explanation

1. The authenticated user clicks **View Statistics** for a specific link.

2. Blade generates a URL using:

```php
route('links.stats', $link->id)
```

Example:

```text
links.id = 8
```

becomes:

```text
GET /links/8/stats
```

3. Laravel matches the request to:

```php
Route::get('/links/{link}/stats', [LinkController::class, 'stats'])
    ->name('links.stats');
```

4. Laravel assigns:

```text
{link} = 8
```

5. Because the controller method uses:

```php
LinkController::stats(Link $link)
```

Laravel uses the `Link` model to search:

```text
links.id = 8
```

6. Eloquent converts the matching database row into a `Link` model object and stores it in:

```php
$link
```

7. The controller checks that the current link belongs to the authenticated user:

```php
abort_if($link->user_id !== auth()->id(), 403);
```

8. The controller calls:

```php
$link->clicks()->latest()->get();
```

9. The `clicks()` relationship searches the `clicks` table where:

```text
clicks.link_id = $link->id
```

10. `latest()` orders the matching click rows by the newest `created_at` value first.

11. `get()` retrieves the matching rows as a collection of `Click` model objects and stores them in:

```php
$clicks
```

12. The controller passes both variables to:

```php
return view('links.stats', [
    'link' => $link,
    'clicks' => $clicks,
]);
```

13. `links/stats.blade.php` displays:

```text
$link->original_url
$click->ip_address
$click->created_at
```

14. Laravel returns the rendered statistics page to the browser.

## 11. Filament Admin Panel

The project includes a Filament v3 admin panel that provides a ready-made administration interface for managing the application's data.

Instead of manually creating controllers, Blade views, tables, forms, search, sorting, and pagination, Filament generates an administration interface directly from the existing Eloquent models.

The Filament admin panel uses the same database, models, and relationships as the main application.

### Admin Panel Architecture

```text
Browser

↓

/admin

↓

Filament

↓

Resource

↓

Eloquent Model

↓

MySQL Database

↓

Resource

↓

Filament Interface

↓

Browser
```

### Link Resource

The `LinkResource` is associated with the `Link` model.

```php
protected static ?string $model = Link::class;
```

This tells Filament to use the `Link` model, which represents the `links` table.

The Link resource displays:

- Link ID
- Owner
- Original URL
- Short Code
- Total Click Count
- Created Date

The resource also provides:

- Search
- Sorting
- Editing
- Deleting

---

### Click Resource

The `ClickResource` is associated with the `Click` model.

```php
protected static ?string $model = Click::class;
```

This tells Filament to use the `Click` model, which represents the `clicks` table.

The Click resource displays:

- Click ID
- Related Short Code
- Link ID
- Visitor IP Address
- Click Timestamp

The resource demonstrates Eloquent relationships by displaying:

```php
Tables\Columns\TextColumn::make('link.short_code')
```

Flow:

```text
ClickResource

↓

Click Model

↓

clicks.link_id

↓

links.id

↓

links.short_code

↓

Display Short Code
```

### Purpose

The Filament admin panel was added as a bonus feature to demonstrate how an existing Laravel application can be extended with a professional administration interface while reusing the same Eloquent models, database relationships, and application data.
