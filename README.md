# RideGo System Documentation

## 1. System Architecture Diagram (Conceptual Block Diagram)

This conceptual diagram outlines the three-tier architecture and communication flow for the RideGo application using **Laravel** (Backend/API) and **Flutter** (Mobile Clients).

| TIER | COMPONENT | TECHNOLOGY / ROLE               | COMMUNICATION PROTOCOL |
| :--- | :--- |:--------------------------------| :--- |
| **I. Client Tier** | **Admin Web Panel** | Laravel Blade                   | HTTPS (Session-Based Auth) |
| | **Rider Mobile App** | Flutter (iOS & Android)         | HTTPS (REST API/JWT) |
| | **Driver Mobile App** | Flutter (iOS & Android)         | HTTPS (REST API/JWT) |
| **II. Application Tier (Backend)** | **Web Server / Load Balancer** | Nginx                           | |
| | **Laravel Application** | PHP (API Logic, Business Rules) | |
| | **Push Notification Service** | Firebase Cloud Messaging (FCM)  | API Calls (from Laravel) |
| | **Mapping/Location Service** | Google Maps API / Mapbox        | API Calls (from Flutter & Laravel) |
| | **Payment Gateway** | Tap Payment                     | API Calls (from Laravel) |
| **III. Data Tier** | **Primary Database** | MySQL              | Database Queries (via Eloquent) |
| | **Cache Layer** | Redis / Memcached               | |
| | **File Storage** | Local Disk             | |

---

## 2. Web Frontend Details (Laravel Admin Panel)

This documentation details the structure and mechanisms of the administrative panel used for managing users, services, and operations.

| Detail | Description for RideGo Admin Panel |
| :--- | :--- |
| **Technology Stack** | **Laravel** (PHP) with **Blade** templating. Often leverages **Livewire** or **Inertia.js** for interactive data management (e.g., dynamic tables, form submissions). |
| **Routing Setup** | Routes are defined in Laravel's `web.php`. All core administrative routes are prefixed (e.g., `/admin/`) and protected by the **`auth:admin`** middleware. |
| **Authentication** | Uses Laravel's built-in **Session-based authentication**. The admin user logs in, and a server-side session is maintained and tracked via a secure cookie. |
| **Component Hierarchy** | Based on **Blade Layouts**. Includes a master layout file defining the global structure: **Static Sidebar** (main navigation), **Header**, and the main **Content Area** where specific views are injected. |
| **Core Functions** | **CRUD** (Create, Read, Update, Delete) management screens for: **Riders**, **Drivers**, **Coupons**, **Rewards**, **Regions**, and **Services**. Also includes detailed **Ride History** and comprehensive **Settings** management. |

---

## 3. Mobile Frontend Details (Flutter Apps: Rider & Driver)

This covers the shared and distinct components of the mobile applications built with Flutter.

| Detail | Description for Rider & Driver Apps (Flutter) |
| :--- | :--- |
| **SDKs & Libraries** | **State Management:** (e.g., **Provider**, Riverpod, BLoC). **Networking:** **`dio`** (for REST API calls). **Geolocation/Maps:** **`Maps_flutter`**, **`geolocator`**. **Notifications:** **`firebase_messaging`**. **Storage:** **`flutter_secure_storage`** (for sensitive data). |
| **Folder Structure** | **`lib/`** is typically organized into: **`screens/`** (full-page UIs), **`widgets/`** (reusable components), **`models/`** (data structures for API parsing), **`services/`** (API client, location handler), and **`state/`** (state management logic). |
| **Authentication Flow** | **Token-based Authentication (JWT).** After successful login, the **Laravel API** issues a **JWT** access token. The token is stored securely on the device and sent in the **Authorization Header** of every subsequent API request. |
| **Localization (i18n)** | Uses Flutter's **`intl`** package or a library like **`easy_localization`**. Translation files (e.g., JSON or ARB format) are stored within the **`assets/i18n`** directory. |
| **Key Component Breakdown** | **Rider App Focus:** `BookingScreen` (Map View, search), `PaymentSelection`, `RideHistory`. **Driver App Focus:** `AwaitingRequestScreen` (listening for new rides), `OnTripScreen` (navigation), `EarningReport`. Both reuse generic widgets like `CustomButton`, `LoadingIndicator`. |
