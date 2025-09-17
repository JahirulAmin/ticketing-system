# Customer Support Ticketing System

Welcome to the Customer Support Ticketing System. This application provides a platform to manage customer support tickets with real-time chat, user authentication, and a responsive UI.

Built with Laravel (backend), Vue.js (frontend) and Laravel Reverb for real-time features, this repo follows MVC best practices and ships with seed data for fast local testing.

---

## 🚀 Quick overview

- Purpose: Web-based ticketing system for managing customer support interactions (tickets, messages, real-time updates).
- Stack:
  - Backend: Laravel 11.x, PHP 8.1+
  - Frontend: Vue 3, Vite, Bootstrap
  - Real-time: Laravel Reverb, Laravel Echo (Pusher-compatible client)
  - Auth: Laravel Sanctum

---

## ✨ Features

- User authentication (login / logout)
- Ticket creation, viewing and management
- Real-time ticket chat (with a polling fallback)
- Responsive UI with Bootstrap

---

## 📦 Prerequisites

- PHP 8.1 or newer
- Composer
- Node.js & npm
- MySQL (or another supported DB)
- Git

---

## ⚙️ Installation (Windows / PowerShell)

1. Clone the repository

   git clone https://github.com/yourusername/ticketing-system.git
   cd ticketing-system

2. Install PHP dependencies

   composer install

3. Install JavaScript dependencies

   npm install

4. Copy environment file and update values

   copy .env.example .env

   Update the following values in `.env` to match your environment:

   DB_DATABASE=ticketing_system
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   BROADCAST_DRIVER=reverb
   REVERB_APP_ID=123456
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   VITE_REVERB_HOST=127.0.0.1
   VITE_REVERB_PORT=8080

   (In production consider setting REVERB_SCHEME=https and using secure credentials.)

5. Generate application key

   php artisan key:generate

6. Run migrations and seeders

   php artisan migrate --seed

   The seeder creates sample accounts for testing:

   - Admin: admin@example.com / password
   - Customer: customer@example.com / password

7. Start development servers

   Start Laravel backend (default port 8000):

   php artisan serve

   Start Vite frontend (separate terminal):

   npm run dev

   Start Reverb (if you use the included artisan command):

   php artisan reverb:start --debug --host=127.0.0.1 --port=8080

8. Open the app

   Visit: http://localhost:8000

---

## 👥 Usage & roles

- Admin: full access to view and manage all tickets and chats (example: admin@webns.com).
- Customer: create tickets and view their own requests (example: customer@webns.com).

Key flows:
- Create ticket → add chats / attachments → receive real-time updates for participants.

---

## 💬 Real-time chat behaviour

- Messages are ordered from oldest → newest (created_at ascending).
- Real-time transport: Reverb + Echo broadcast events (TicketChatSent).


## 🔒 Security

- Authentication: Laravel Sanctum for SPA and API token auth.
- Authorization: role-based checks for channels and ticket access.
- Use HTTPS (REVERB_SCHEME=https) and secure credentials in production.

---

## 🐞 Troubleshooting

- Reverb not connecting: ensure REVERB_HOST and REVERB_PORT match the running Reverb instance and the port is free.
- Real-time issues: check the browser console, Reverb debug output, and `storage/logs/laravel.log`.
- API errors: confirm DB credentials, run `php artisan migrate --seed` and check `config/database.php`.

---

## 📂 Project structure (high level)

- `app/` - Models, Controllers, Events
- `resources/js/` - Vue components and app bootstrap
- `routes/` - `web.php` and `api.php` routes
- `database/migrations/` - Schema and migrations
- `config/` - Broadcasting, Reverb and other app configs

---

## 🛠️ Developer notes & tips

- To test real-time locally: open two browser windows, login with different accounts, open the same ticket, then send messages.

---

## 🙏 Acknowledgments

Built with Laravel, Vue.js, Vite, Bootstrap and Laravel Reverb. Thanks to the open-source community.

---
