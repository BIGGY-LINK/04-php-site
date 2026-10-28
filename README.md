# EFLASH GRAPHIXS

A responsive, multi-page website for a fictional graphic design studio. The site uses reusable PHP includes, data-driven service and team pages, and a contact form that validates and stores submissions locally as JSON Lines.

## Features

- Shared site configuration, header, footer, and navigation
- Responsive Home, About, Services, and Contact pages
- Data-driven service cards and individual service detail pages
- Team profiles rendered from a PHP array
- Contact form with server-side validation, sticky values, CSRF protection, and Post/Redirect/Get
- Contact submissions saved to `storage/messages.jsonl`
- Password-protected local page to view submissions newest-first

## Tech stack

- PHP 8 (strict types)
- HTML5
- CSS3
- Apache via XAMPP
- JSON Lines for local contact submission storage

## Run with XAMPP

1. Install and start **Apache** from the XAMPP Control Panel.
2. Place the project folder at:

   `C:\xampp\htdocs\swd\04-php-site`

3. Open the site at:

   [http://localhost/swd/04-php-site/](http://localhost/swd/04-php-site/)

4. To view contact form submissions, open:

   [http://localhost/swd/04-php-site/messages.php](http://localhost/swd/04-php-site/messages.php)

   The local learning password is configured as `$messagesPassword` in `includes/config.php`. Change `change-me-local-only` to a password of your choice before using the message viewer.

Apache needs permission to write to the `storage` folder. The folder's `.htaccess` denies direct web access to the saved submissions; this protection depends on Apache allowing `.htaccess` overrides.

## Project structure

```text
04-php-site/
├── about.php
├── contact.php
├── index.php
├── info.php
├── messages.php
├── service.php
├── services.php
├── assets/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── data.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
└── storage/
    ├── .htaccess
    └── messages.jsonl  (created when the first contact form submission is saved)
```

## Local-only password notice

The message viewer uses a hard-coded password to make sessions and basic access checks easier to learn. This is **not production-grade authentication**: the password is stored in source code, has no account management or password hashing workflow, and the page has no rate limiting or audit logging. Do not use this setup to protect real personal data or expose it on a public server. A deployed site should use proper user authentication, securely managed password hashes and secrets, HTTPS, and additional access controls.

## What I learned

- I learned how to use PHP includes to share page layout: **[write your notes here]**
- I learned how to render pages from PHP arrays: **[write your notes here]**
- I learned how server-side form validation and CSRF tokens work: **[write your notes here]**
- I learned how Post/Redirect/Get prevents duplicate form submissions: **[write your notes here]**
- I learned why output must be escaped and how submissions can be stored as JSON Lines: **[write your notes here]**
