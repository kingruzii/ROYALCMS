# Royal Village International Foundation (RVIF) — CMS Website

A full-stack PHP/MySQL content management system for the **Royal Village International Foundation** — a nonprofit organization dedicated to empowering African youth through scholarships, vocational training, and community development.

---

## 🌍 About the Project

RVIF was founded by **Queen Georgia T. Nuahn**, a registered nurse in the United States, who left Liberia at age 8 and returned with a mission: to ensure no child's potential is limited by their circumstances.

This website serves as the public face and admin management system for RVIF, managing:
- Scholarship beneficiary profiles
- Programs and impact data
- Blog posts with image galleries
- Team and founder information
- Donation processing (Stripe)
- Partner organizations
- Contact form submissions

---

## 🗂️ Project Structure

```
ROYALCMS/
├── admin/                  # Admin dashboard (protected)
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Main dashboard
│   ├── beneficiaries.php   # Manage scholars
│   ├── blog.php            # Manage blog posts
│   ├── programs.php        # Manage programs
│   ├── team.php            # Manage team members
│   ├── founder.php         # Manage founder info
│   ├── partners.php        # Manage partner organizations
│   ├── donations.php       # View donation records
│   ├── messages.php        # View contact messages
│   ├── milestones.php      # Manage timeline milestones
│   ├── banners.php         # Manage hero banners
│   ├── impact.php          # Manage impact stats
│   ├── settings.php        # Site-wide settings
│   └── upload.php          # File upload handler
├── uploads/                # Uploaded media files (git-ignored)
│   ├── hero/               # Hero/banner images
│   ├── blog/               # Blog post images & gallery
│   ├── programs/           # Program images
│   ├── logo/               # Site logos
│   └── testimonials/       # Testimonial photos
├── about.php               # About page
├── beneficiaries.php       # Scholars listing page
├── blog.php                # Blog listing page
├── blog_view.php           # Single blog post view
├── checkout.php            # Stripe donation checkout
├── config.php              # DB config & helper functions
├── contact.php             # Contact form page
├── db.php                  # Database connection
├── donate.php              # Donation page
├── footer.php              # Shared footer
├── header.php              # Shared header/nav
├── impact.php              # Impact statistics page
├── index.php               # Homepage
├── our-work.php            # Programs overview page
├── partners.php            # Partners page
├── programs.php            # Programs detail page
├── robots.txt              # SEO robots file
├── schema.sql              # Full database schema + seed data
└── team.php                # Team members page
```

---

## ⚙️ Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 / MariaDB 10.4 or higher
- **XAMPP** (or any Apache/Nginx + PHP + MySQL stack)
- A modern web browser

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/Archie-ctr/ROAL-VILLAGE-INTERNATIONAL-FOUNDATION.git
cd ROAL-VILLAGE-INTERNATIONAL-FOUNDATION
```

Place the project folder inside your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\ROYALCMS\
```

### 2. Start XAMPP

Open the XAMPP Control Panel and start:
- **Apache**
- **MySQL**

### 3. Import the Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Create a new database named `royalcms`
3. Click **Import** and select the `schema.sql` file from the project root
4. Click **Go** — this will create all tables and populate seed data

### 4. Configure the Application

Open `config.php` and update if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'royalcms');
define('DB_USER', 'root');
define('DB_PASS', '');           // Your MySQL password

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '1234');    // Change this in production!
```

### 5. (Optional) Stripe Donations

To enable donation processing, add your Stripe API keys in `config.php`:

```php
define('STRIPE_SECRET_KEY', 'sk_live_...');
define('STRIPE_PUBLIC_KEY', 'pk_live_...');
```

### 6. Visit the Site

| Page | URL |
|------|-----|
| Homepage | `http://localhost/ROYALCMS/` |
| About | `http://localhost/ROYALCMS/about.php` |
| Our Work | `http://localhost/ROYALCMS/our-work.php` |
| Beneficiaries | `http://localhost/ROYALCMS/beneficiaries.php` |
| Blog | `http://localhost/ROYALCMS/blog.php` |
| Donate | `http://localhost/ROYALCMS/donate.php` |
| Contact | `http://localhost/ROYALCMS/contact.php` |
| **Admin Panel** | `http://localhost/ROYALCMS/admin/login.php` |

---

## 🔐 Admin Panel

The admin panel is protected by session-based authentication.

**Default credentials** (change after first login):
- Username: `admin`
- Password: `1234`

From the admin panel you can manage:
- All public-facing content (programs, team, founder, blog, etc.)
- Scholarship beneficiary profiles
- Donation records
- Contact form messages
- Site settings (logo, social links, hero text, stats)

---

## 🗄️ Database Schema

Key tables:

| Table | Purpose |
|-------|---------|
| `site_settings` | Global site config (logo, social links, stats) |
| `beneficiaries` | Scholarship recipient profiles |
| `team_members` | Staff and leadership bios |
| `founder_info` | Founder profile and story |
| `programs` | Foundation programs |
| `partners` | Partner universities and organizations |
| `blog_posts` | News and story articles |
| `blog_gallery` | Gallery images per blog post |
| `contact_messages` | Submitted contact form entries |
| `donations` | Stripe donation records |

Full schema with seed data is in [`schema.sql`](./schema.sql).

---

## 📸 Media Uploads

Uploaded files are stored in the `uploads/` directory. This folder is excluded from version control via `.gitignore`. When deploying to a new environment, create the following folders manually:

```
uploads/hero/
uploads/blog/
uploads/blog/gallery/
uploads/programs/
uploads/logo/
uploads/testimonials/
```

---

## 🌐 Pages Overview

| Page | Description |
|------|-------------|
| `index.php` | Homepage with hero, stats, featured scholars, and programs |
| `about.php` | Foundation story, founder profile, mission/vision, timeline |
| `our-work.php` | Detailed programs overview |
| `beneficiaries.php` | Full list of scholarship recipients |
| `impact.php` | Impact numbers and success stories |
| `blog.php` | News articles and updates |
| `blog_view.php` | Individual blog post with photo gallery |
| `partners.php` | Partner institutions and organizations |
| `team.php` | Leadership and staff profiles |
| `donate.php` | Donation form (Stripe) |
| `contact.php` | Contact form |
| `checkout.php` | Stripe checkout handler |

---

## 🤝 Contributing

This project is maintained by the RVIF team. For issues or suggestions, please open a GitHub issue or contact us via the website.

---

## 📄 License

This project is proprietary software owned by Royal Village International Foundation. All rights reserved.

---

*Built with ❤️ to empower Africa's next generation.*
