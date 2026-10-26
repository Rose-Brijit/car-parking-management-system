# 🚗 Car Parking Management System - Docker Setup

This project is a containerized web application for managing car parking slots, user reservations, payments, and admin monitoring.

---

## 🛠️ Architecture & Tech Stack

- **Web Server:** PHP 8.2 + Apache (`php:8.2-apache`) with `mysqli`, `pdo`, and `pdo_mysql` extensions.
- **Database:** MariaDB 10.4 (`mariadb:10.4`) with automatic SQL initialization and data persistence via named volumes.
- **Orchestration:** Docker Compose (`docker-compose.yml`).

---

## 🚀 Getting Started

### 1-Click Startup Script (Recommended)
After turning on your PC or restarting:
```powershell
.\start-all.ps1
```
To safely stop everything without losing any data:
```powershell
.\stop-all.ps1
```

---

### Manual Startup Steps
#### 1. Prerequisites
- Docker Desktop installed and running.

### 2. Start the Application
Run the following command from the project root:
```bash
docker compose up -d
```
Docker will download the images, initialize the database automatically, and start both containers.

### 3. Open in Browser
- **Website URL:** [http://localhost:8080](http://localhost:8080)
- **User Login:** [http://localhost:8080/login.php](http://localhost:8080/login.php)
- **User Registration:** [http://localhost:8080/register.php](http://localhost:8080/register.php)
- **Admin Portal:** [http://localhost:8080/admin_login.php](http://localhost:8080/admin_login.php)

---

## 🔑 Default Credentials

### User Accounts
| Username | Password | Role |
| :--- | :--- | :--- |
| `aami` | `1234` | Customer |
| `anna` | `1234` | Customer |
| `aileen` | `1234` | Customer |
| `aimy_elzha` | `1234` | Customer |

*(Or register a new account from the registration page!)*

### Admin Account
| Username | Password | Role |
| :--- | :--- | :--- |
| `admin` | `admin123` | Administrator |

---

## 📋 Common Docker Commands

- **Check container status:**
  ```bash
  docker compose ps
  ```

- **View live application & server logs:**
  ```bash
  docker compose logs -f
  ```

- **View web logs only:**
  ```bash
  docker compose logs -f web
  ```

- **View database logs only:**
  ```bash
  docker compose logs -f db
  ```

- **Stop containers:**
  ```bash
  docker compose down
  ```

- **Restart and rebuild containers:**
  ```bash
  docker compose up --build -d
  ```

- **Reset database (wipes volume and re-imports `init.sql`):**
  ```bash
  docker compose down -v
  docker compose up -d
  ```

---

## 📂 Project Structure

```text
├── Dockerfile              # Dockerfile for Apache + PHP 8.2 & MySQL extensions
├── docker-compose.yml      # Orchestrates Web & MariaDB containers
├── .dockerignore           # Excludes local files from image build
├── README.md               # Documentation & usage guide
├── db/
│   └── init.sql            # Initial database schema and sample data
├── index.php               # Landing portal
├── login.php               # User authentication
├── register.php            # User registration
├── dashboard.php           # User dashboard
├── booking.php             # Parking slot booking page
├── view_bookings.php       # Real-time slot status view
├── admin_login.php         # Admin authentication
├── admin_dashboard.php     # Admin control panel (Users, Slots, Bookings)
├── db.php                  # Database connection with env vars & retry logic
├── style.css               # Styling
└── b1.jpg                  # Background image
```
