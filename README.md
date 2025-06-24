# Sistema de Gestión de Citas - Taller de Vehículos

Sistema completo para la gestión de citas de un taller de vehículos desarrollado con **Laravel 10**, **Laravel Breeze**, **Inertia.js**, **Vue 3** y **PrimeVue**.

## 🚀 Características

### Backend (Laravel + Breeze)
- ✅ Autenticación completa con Laravel Breeze
- ✅ CRUD completo de citas con validaciones
- ✅ Sistema de roles (cliente/admin)
- ✅ Policies para control de acceso
- ✅ Eventos y listeners para notificaciones por email
- ✅ API para horarios disponibles

### Frontend (Vue 3 + Inertia + PrimeVue)
- ✅ SPA con Vue 3 y Composition API
- ✅ Integración perfecta con Inertia.js
- ✅ Interfaz moderna con PrimeVue
- ✅ Dashboard diferenciado por roles
- ✅ Formularios reactivos con validación
- ✅ Notificaciones toast y confirmaciones

## 📋 Requisitos

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL/PostgreSQL
- Servidor de correo (para notificaciones)

## 🛠️ Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/kmilo1717/AppointmentManager.git
cd AppointmentManager
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Configurar el archivo .env
```bash
cp .env.example .env
php artisan key:generate
```

Configurar la base de datos y correo en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taller_vehiculos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

MAIL_MAILER=smtp
MAIL_HOST=tu_host_smtp
MAIL_PORT=587
MAIL_USERNAME=tu_email
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@taller.com
MAIL_FROM_NAME="Taller de Vehículos"
```

### 4. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

### 5. Instalar dependencias de Node.js
```bash
npm install
```

### 6. Compilar assets
```bash
npm run dev
# o para producción
npm run build
```

### 7. Iniciar el servidor
```bash
php artisan serve
```

## 👥 Usuarios de Prueba

El seeder crea los siguientes usuarios:

**Administrador:**
- Email: admin@taller.com
- Password: password

**Clientes:**
- Email: juan@email.com / Password: password
- Email: maria@email.com / Password: password
- Email: carlos@email.com / Password: password

## 🎨 Funcionalidades por Rol

### Cliente
- ✅ Registro y login con Breeze
- ✅ Ver sus propias citas
- ✅ Crear nuevas citas
- ✅ Editar citas pendientes
- ✅ Cancelar citas
- ✅ Filtrar citas por estado y fecha

### Administrador
- ✅ Acceso completo al sistema
- ✅ Ver todas las citas
- ✅ Crear citas para cualquier usuario
- ✅ Editar cualquier cita
- ✅ Eliminar citas
- ✅ Cambiar estado de citas
- ✅ Ver estadísticas del sistema
- ✅ Filtros avanzados

## 🔒 Seguridad

- Autenticación con Laravel Breeze
- Middleware de roles personalizado
- Policies para autorización
- Validación de datos en frontend y backend
- Protección CSRF automática
- Sanitización de inputs

## 📧 Notificaciones

El sistema envía emails automáticamente cuando:
- Se crea una nueva cita
- Se confirma una cita
- Se cancela una cita

## 🚀 Despliegue

### Producción
1. Configurar servidor web (Apache/Nginx)
2. Configurar base de datos
3. Ejecutar `composer install --optimize-autoloader --no-dev`
4. Ejecutar `npm run build`
5. Configurar variables de entorno
6. Ejecutar migraciones: `php artisan migrate --force`
7. Configurar cron jobs para Laravel Scheduler

### Cron Job
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

## 📝 Estructura del Proyecto

```
├── app/
│   ├── Events/           # Eventos del sistema
│   ├── Http/
│   │   ├── Controllers/  # Controladores
│   │   └── Middleware/   # Middleware personalizado
│   ├── Listeners/        # Listeners de eventos
│   ├── Mail/            # Clases de correo
│   ├── Models/          # Modelos Eloquent
│   └── Policies/        # Policies de autorización
├── database/
│   ├── migrations/      # Migraciones de BD
│   └── seeders/         # Seeders
├── resources/
│   ├── js/
│   │   ├── Components/  # Componentes Vue
│   │   ├── Layouts/     # Layouts de la app
│   │   └── Pages/       # Páginas Inertia
│   └── views/           # Templates Blade
└── routes/
    ├── auth.php         # Rutas de autenticación (Breeze)
    └── web.php          # Rutas web principales
```

## 🔧 Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerar autoload
composer dump-autoload

# Compilar assets en desarrollo
npm run dev

# Compilar assets para producción
npm run build

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o preguntas:
- Email: soporte@taller.com
- Issues: [GitHub Issues](https://github.com/tu-usuario/taller-vehiculos/issues)

---

Desarrollado con ❤️ usando Laravel Breeze + Inertia.js + Vue 3 + PrimeVue
