# Template CMS

A modern, lightweight CMS (Content Management System) template built with Vue.js and PHP.

![GitHub repo languages](https://img.shields.io/badge/Vue-71.3%25-42b883)
![GitHub repo languages](https://img.shields.io/badge/PHP-27.8%25-777BB4)
![GitHub repo size](https://img.shields.io/github/repo-size/TMaulana26/template-cms)
![License](https://img.shields.io/github/license/TMaulana26/template-cms?color=blue)

## 🚀 Features

-   **Modern UI** with responsive design for all devices
-   **Authentication system** with role-based access control
-   **Dynamic menu management** to customize navigation
-   **Activity logging** for administrative actions
-   **Data tables** with sorting, filtering, and pagination
-   **Modal components** for improved user experience
-   **Toast notifications** for user feedback

## 🔧 Technologies

-   **Frontend:** Vue.js (71.3%)

    -   Vue 3 Composition API
    -   Inertia.js for SPA functionality
    -   Tailwind CSS for styling
    -   Vue Good Table for data presentation
    -   Vue Toastification for notifications

-   **Backend:** PHP (27.8%)
    -   Laravel framework
    -   Spatie Activity Log for audit trails
    -   REST API architecture

## 📋 Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Node.js and npm
-   MySQL or compatible database

## 🔌 Installation

1. **Clone the repository**

```bash
git clone https://github.com/TMaulana26/template-cms.git
cd template-cms
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install JavaScript dependencies**

```bash
npm install
```

4. **Environment Setup**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database in .env file**

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=template_cms
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations and seed data**

```bash
php artisan migrate --seed
```

7. **Compile assets**

```bash
npm run dev
```

8. **Start the development server**

```bash
php artisan serve
```

The application should now be running at [http://localhost:8000](http://localhost:8000)

## 📖 Usage

1. **Login with the default admin account:**

    - Email: admin@example.com
    - Password: password

2. **Manage menus:**

    - Create, edit, and delete menu items
    - Configure routes and URLs

3. **View activity logs:**
    - Track user actions in the system
    - Review audit history for security purposes

## 🛠️ Development

To build for production:

```bash
npm run build
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📬 Contact

TMaulana26 - [GitHub Profile](https://github.com/TMaulana26)

Project Link: [https://github.com/TMaulana26/template-cms](https://github.com/TMaulana26/template-cms)

---

Made with ❤️ by TMaulana26
