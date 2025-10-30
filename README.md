# Mini Trello - Kanban Task Manager

## 📋 Descripción del Proyecto

Mini Trello es una aplicación web tipo Kanban desarrollada con **Laravel 9** y **Livewire 2**. Permite a los usuarios gestionar tareas de forma visual e interactiva, con funcionalidades de arrastrar y soltar, auditoría de acciones y una interfaz moderna basada en **TailwindCSS**. Cada usuario tiene sus propias tareas privadas y existe un sistema de roles para administración y visualización de logs.

### Funcionalidades principales
- Autenticación y registro de usuarios
- CRUD de tareas (crear, leer, actualizar, eliminar)
- Tablero Kanban con drag & drop (Livewire + Alpine.js)
- Auditoría completa de acciones (audit trail)
- Vista de logs solo para administradores
- Validaciones robustas en frontend y backend
- Notificaciones visuales (toast)
- Tests automatizados con Pest
- Docker para desarrollo local (Laravel Sail)

---

## 🚀 Instalación y Primeros Pasos

### 1. Clona el repositorio
```bash
git clone https://github.com/juanantms/mini-trello.git
cd mini-trello
```

### 2. Configura el entorno
```bash
cp .env.example .env
nano .env  # Edita APP_KEY y otros valores si es necesario
```

### 3. Instala dependencias y levanta el entorno
```bash
composer install
./vendor/bin/sail up -d
```

### 4. Genera la clave de la aplicación
```bash
./vendor/bin/sail artisan key:generate
```

---

## 🧪 Desarrollo Local (Laravel Sail)

### 1. Levanta el entorno de desarrollo
```bash
./vendor/bin/sail up -d
```

### 2. Entra al contenedor
```bash
./vendor/bin/sail shell
```

### 3. Instala dependencias de frontend
```bash
npm install
```

### 4. Ejecuta migraciones y seeders dentro del contenedor
```bash
php artisan migrate
php artisan db:seed
```

### 5. Optimiza y compila assets fuera del contenedor
```bash
php artisan optimize:clear
exit
npm run dev
```

### 6. Accede a la aplicación
- URL: [http://localhost](http://localhost)
- Usuario admin: `admin@minitrello.com`
- Contraseña: `test`

---

## 🛠️ Comandos Útiles

### Entrar al contenedor
```bash
./vendor/bin/sail shell
```

### Ejecutar comandos Artisan
```bash
./vendor/bin/sail artisan migrate
```

### Ejecutar tests
```bash
./vendor/bin/sail test
```

> Antes de ejecutar los tests, asegúrate de crear el archivo `database/testing.sqlite`:
```bash
touch database/testing.sqlite
```

**¡Listo para trabajar y desplegar en minutos!** 🚀
