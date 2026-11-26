# thrumble.smallloan

A compact, modern, lightweight **Gold Loan / Girvi Management Software** built with **Laravel 11**, **MySQL**, **Livewire**, and **Tailwind CSS** for Indian jewellery and gold loan businesses.

## Features

- **Dashboard:** Active loans, outstanding principal, total interest collected, customer count, loans due today, overdue alerts, vault collateral summary, and recent transactions.
- **Customer Management:** Add, edit, archive, and search customers with PAN/Aadhaar/ID proof KYC details, customer-wise loan history, and outstanding balance tracking.
- **Gold Pledge Management:** Track jewellery description, category, gross/stone/net gold weight, purity (`18K`, `20K`, `22K`, `24K`), market gold rate, valuation, packet number, locker reference, and printable vault tags.
- **Loan Management:** Automatic loan numbering (`LN-YYYY-XXXX`), multi-item collateral pledging, LTV calculation, and printable loan agreements/pledge receipts.
- **Interest Calculation:** Supports **Monthly**, **Daily**, **Annual**, **Flat**, and **Reducing Balance** interest methods with live accrued interest breakdown.
- **Payment Management:** Collect interest, principal, partial payments, and full settlements via **Cash**, **UPI**, **Bank Transfer**, or **Cheque** with immutable printable receipts.
- **Loan Renewals & Gold Release:** Extend maturity dates, record renewal interest, release settled collateral with handover certificates, and enforce admin override protection for unsettled loans.
- **Reports & Exports:** Daily/monthly collection reports, active/overdue/closed loan reports, vault portfolio summary, customer ledger, CSV export, and print-ready views.
- **Users & Settings:** Admin/staff user management, profile & password security, business/receipt settings, and configurable daily gold rates.

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 11, Livewire 4
- **Frontend:** Tailwind CSS, Vite
- **Database:** MySQL

## Quick Setup

```bash
git clone <repository-url>
cd smallloan

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Configure your MySQL database in `.env`, then run migrations and seeders:

```bash
php artisan migrate --seed
php artisan db:seed --class=ExtraDemoDataSeeder
php artisan serve
```

## Default Demo Credentials

- **Admin:** `admin@thrumble.smallloan` / `admin123`
- **Staff:** `staff@thrumble.smallloan` / `staff123`

## Credits

- **Developed by:** [Attrix Technologies](https://www.attrixtech.com)
- **Marketed by:** [Infusionn Pvt. Ltd.](https://www.infusionn.in)
