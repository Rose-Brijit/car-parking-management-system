# 🚀 Hosting Car Parking Management System on Render (render.com)

This guide walks you through deploying your containerized Car Parking Management System on **Render** using Render's **Free Tier**.

---

## 🌟 How It Works on Render
Render's Free Tier provides free **Web Services** built from Docker containers.
Our Docker setup is self-contained:
- **Web Server:** Apache + PHP 8.2 with `mysqli` and `pdo_mysql`.
- **Database:** Built-in MariaDB 10.4 initialized automatically from `db/init.sql` on first boot.
- **Port Management:** Automatically adapts to Render's dynamic `$PORT` environment variable.
- **Cost:** **100% Free** ($0 / month).

---

## 📋 Step 1: Push Project to GitHub

Render deploys directly from your GitHub repository.

1. Open your browser and go to [GitHub: Create a New Repository](https://github.com/new).
2. Set:
   - **Repository Name:** `car-parking-management-system` (or any name you prefer)
   - **Visibility:** Public (or Private)
   - **Do NOT check** "Add a README" (your project already has files).
   - Click **Create repository**.
3. In PowerShell at `C:\S5 Project`, run the following commands (replace `<YOUR_GITHUB_USERNAME>` with your GitHub username, e.g. `Rose-Brijit`):

```powershell
git remote add origin https://github.com/Rose-Brijit/car-parking-management-system.git
git branch -M main
git push -u origin main
```

---

## 📋 Step 2: Deploy on Render

1. Go to [Render Dashboard](https://dashboard.render.com/) and log in (or sign up using your GitHub account).
2. In the top-right corner, click **New +** and select **Web Service**.
3. Select **Build and deploy from a Git repository** and click **Next**.
4. Connect your GitHub account (if not already connected) and select your repository: `car-parking-management-system`.
5. Configure the deployment settings:
   - **Name:** `car-parking-system` *(This determines your URL: `car-parking-system.onrender.com`)*
   - **Region:** Choose the region closest to you (e.g. *Singapore* or *Frankfurt*).
   - **Branch:** `main`
   - **Root Directory:** *(leave blank)*
   - **Runtime:** **Docker** *(Render detects the Dockerfile automatically)*
   - **Instance Type:** **Free** ($0/month)
6. Scroll down and click **Deploy Web Service** (or **Create Web Service**).

---

## 📋 Step 3: Deployment Progress & Access

1. Render will show the real-time build logs:
   - It pulls PHP 8.2 and MariaDB.
   - It compiles Apache and extensions.
   - It starts MariaDB and imports `db/init.sql`.
   - It reports: `==> Starting Apache Web Server...`
2. Once the status shows **Live** (green checkmark), click your generated URL at the top of the page:
   ```text
   https://car-parking-system.onrender.com
   ```

---

## 🔑 Default Credentials on Render

- **Admin Account:**
  - Username: `admin`
  - Password: `admin123`
- **Sample User Account:**
  - Username: `aami`
  - Password: `1234`
- **New Users:** You can register new accounts directly from the web interface.

---

## 💡 Important Note on Render Free Tier
Render free web services spin down (sleep) after 15 minutes of inactivity to conserve resources. When you open the website after it has been sleeping, the first request may take 30–50 seconds to wake up the container. Subsequent requests will be instant.
