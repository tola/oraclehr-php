# 🧑‍💼 HR Portal – Oracle HR Schema Demo

  
A secure, role-based HR portal built in PHP that integrates with Oracle’s HR sample schema. This project blends clean backend architecture, best practices in security, and a polished Bootstrap UI—ideal for developers looking to sharpen their full-stack chops with real-world data models and session-aware workflows.

## 🚀 Features
  
- 🔐 Secure login with **admin** and **employee** roles

- 🧾 Live dashboard for admins with HR data summaries

- 📥 AJAX-powered employee search with **CSRF** protection

- 🛡️ Best practices in session management, XSS mitigation, and password security

- 🌌 Modern login page with animated particle background

- 💠 Built using PHP, Bootstrap 5, Oracle’s HR schema, and OCI8

  

## 🎯 Technologies

  
- PHP 8.x

- Oracle DB (HR sample schema)

- OCI8 driver

- Bootstrap 5 (via CDN)

- Particles.js (via CDN)

- Vanilla JavaScript + AJAX

- Modular PHP routing (inspired by WordPress front-controller)

  

## 🔐 Security Highlights

  
- Passwords hashed using `password_hash()` + optional HMAC pepper

- Session hardening (`session.cookie_httponly`, `regenerate_id()`, IP pinning)

- Custom **CSRF token** with optional nonce per action

- Prepared statements with `oci_bind_by_name()` to prevent SQL injection

- Escaped output with `htmlspecialchars()` throughout the app


## ⚙️ Setup Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/hr-php-app.git

2.  Set up Oracle connection in `config/db.php`. 
3.  Ensure PHP has OCI8 extension enabled.
  

## 🔭 Roadmap

[ ] Move users to DB with hashed credentials
[ ] Add Chart.js visualizations
[ ] Expand role-based access controls
[ ] RESTful API for frontend or mobile clients
[ ] Unit testing for auth and route modules

> 💡 Built as a practice app to explore secure PHP workflows, Oracle integration, and modular dashboard design.