# Hyperlocal

This is a personal WordPress project I built to practice and show modern development workflows.  
It uses [Bedrock](https://roots.io/bedrock/) as the base and runs on PHP 8.4 inside Lando/Docker.

The main goal: build a fast, maintainable WordPress setup with a custom block theme that can score close to 100 on Lighthouse for performance and Core Web Vitals.

---

## Tech stack
- WordPress 6.8.2 (Bedrock)
- PHP 8.4 via Lando
- MariaDB 10.6
- Redis for object caching
- Composer for dependency management
- Docker + Lando for local development

---

## What’s included
- A custom block theme in `web/app/themes/hyperlocal`
  - Uses the system font stack (no webfonts to keep things light)
  - Minimal CSS to reduce layout shifts
  - A custom server-rendered **LCP Hero block** that handles the featured image properly with preload + eager loading
- `.env` for environment setup
- Redis caching support

---

## Getting started locally
1. Clone this repo  
2. Run `lando start`  
3. Install WordPress:

   ```bash
   lando wp core install \
     --url="https://hyperlocal.lndo.site" \
     --title="Hyperlocal" \
     --admin_user="admin" \
     --admin_password="admin" \
     --admin_email="you@example.com"
