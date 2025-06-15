# 🍽️ Sistema de Gestión de Restaurante/Bar

Este sistema permite gestionar pedidos, usuarios y productos en un entorno de restaurante o bar. Está desarrollado
con **Laravel**, utiliza **Vue 3 + Inertia.js** en el frontend, y permite el control de diferentes roles como
administrador, encargado, camarero, cocinero y barman.

---

## 🚀 Funcionalidades principales

### 👤 Roles

| Rol       |
| --------- |
| Admin     |
| Encargado |
| Camarero  |
| Cocina    |
| Barman    |

---

## 📦 Módulos del sistema

- **Gestión de Usuarios**: CRUD para usuarios por rol `admin`.
- **Gestión de Mesas**: Asignación de pedidos a mesas.
- **Menú**: CRUD de productos con precios y disponibilidad.
- **Pedidos (Orders)**:
    - Añadir productos al pedido (order_items).
    - Envío de cada ítem a cocina o barra según corresponda.
    - Cambio de estado del pedido.
- **Panel del Admin/Encargado**:
    - Reportes.
- **Pantallas de Producción** (Cocina/Barra): pedidos pendientes y botón para marcar como listos.

---

## 🛠️ Tecnologías utilizadas

- **Backend**: Laravel 12
- **Frontend**: Inertia.js + Vue 3
- **Base de datos**: SQLite/MySQL
- **Middleware**: roles personalizados con control de acceso

---

## 👤 Usuario de prueba

- **Contraseña**: 12345a

---

## 🌐 Enlace de producción

[https://erickmf.dev](https://erickmf.dev)
