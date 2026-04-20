# Create the README.md content for HireHub project
readme_content = """# HireHub - Freelancing Platform API (MVP)

HireHub is a specialized Arabic freelancing platform built with **Laravel 12** and **PHP 8.3**. It aims to connect professional Freelancers with Clients through a secure, scalable, and performance-optimized API.

---

## 🚀 Tech Stack
- **Framework:** Laravel 12
- **Language:** PHP 8.3+
- **Authentication:** Laravel Sanctum
- **Database:** MySQL
- **Design Pattern:** Service Layer Pattern, API Resources, Repository-like Scopes.

---

## 🛠 Features
- **User Roles:** Distinct logic for `Clients` and `Freelancers`.
- **Verification System:** Freelancers must be verified by the admin to participate.
- **Dynamic Bidding:** Rules-based bidding system preventing duplicates and invalid entries.
- **Polymorphic Reviews:** A unified system to review both Projects and Freelancers after completion.
- **Polymorphic Attachments:** Handling file uploads for projects and bids seamlessly.
- **Founder Dashboard:** Real-time statistics and financial metrics protected by secure middleware.

---

## 🏗 Architecture Decisions
This project adheres to **SOLID** principles to ensure maintainability:
- **Service Layer:** All business logic is encapsulated in Services (e.g., `BidService`, `ReviewService`) to keep Controllers skinny.
- **API Resources:** Data transformation layer that ensures consistent JSON responses optimized for Mobile Apps.
- **Form Requests:** Complex validation and business rules are handled outside controllers.
- **Middleware:** Performance monitoring (Logger) and Role-based access control (Founder, Verified).
- **Eloquent Optimization:** Solved the **N+1 Query Problem** using Eager Loading (`with`, `withCount`).

---

## 📡 API Endpoints

### 1. Authentication
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| POST | `/api/register` | Register a new user (Client/Freelancer) | Guest |
| POST | `/api/login` | Login and receive Sanctum Token | Guest |

### 2. General / Metadata
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| GET | `/api/skills` | List all available skills | Public |
| GET | `/api/tags` | List all project tags | Public |
| GET | `/api/cities` | List cities and countries | Public |

### 3. Projects
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| GET | `/api/projects` | Browse projects (with filters: budget, date) | Public |
| POST | `/api/projects` | Create a new project | Client |
| POST | `/api/projects/{id}/close` | Mark project as finished | Client (Owner) |

### 4. Freelancers
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| GET | `/api/freelancers` | List freelancers (sorted by rating/availability) | Public |
| GET | `/api/freelancers/{id}` | Get detailed freelancer profile | Auth |

### 5. Bids (Offers)
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| POST | `/api/bids` | Submit an offer on a project | Verified Freelancer |
| GET | `/api/projects/{id}/bids` | List bids for a specific project | Client (Owner) |

### 6. Reviews & Stats
| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| POST | `/api/reviews` | Review a project or a freelancer | Client |
| GET | `/api/stats` | Founder Dashboard statistics | Founder Only |

---

## 🔧 Installation

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/zaher-sankari/hirehub.git](https://github.com/zaher-sankari/hirehub.git)

2. **Perform Composer install:**
   ```bash
   composer install
   ```
3. **Copy .env.example file:**
   ```bash
   cp .evn.example .env
   ```
4. **Generate Key:**
   ```bash
   php artisan key:generate
   ```
5. **Migrate database tables:**
   ```bash
   php artisan migrate
   ```
6. **Seed database:**
   ```bash
   php artisan db:seed
   ```
7- **Run the project:**
   ```bash
   php artisan ser
   ```