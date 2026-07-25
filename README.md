# 📚 Sistema de Gestión Bibliotecaria (PHP + POO + MySQL)

Un sistema web modular desarrollado en PHP aplicando **Programación Orientada a Objetos (POO)**, arquitectura por capas y consultas preparadas con **PDO** para garantizar la integridad y seguridad en el manejo de datos.

---

## 🛠️ Tecnologías Utilizadas

* **Lenguaje Backend:** PHP 8.x (POO)
* **Base de Datos:** MySQL / MariaDB
* **Conexión a BD:** PDO (PHP Data Objects)
* **Entorno de Desarrollo:** XAMPP (Apache + MySQL) / Visual Studio Code

---

## 🚀 Funcionalidades Implementadas

### 📗 1. Gestión de Libros
* **Crear / Registrar:** Formulario para agregar libros con datos como título, autor, ISBN y cantidad inicial.
* **Lectura / Inventario:** Tabla para visualizar todos los libros guardados en la base de datos con su nivel actual de stock.
* **Editar:** Permite seleccionar un libro, cargar sus datos en el formulario y actualizar su información.
* **Eliminar:** Opción para remover registros con confirmación previa.
* **Control de Duplicados:** Manejo de excepciones para evitar registros con ISBN repetido.

### 👤 2. Gestión de Usuarios
* **Crear / Registrar:** Registro de nuevos usuarios especificando nombre completo, correo electrónico y teléfono.
* **Lectura:** Visualización completa de los usuarios registrados en el sistema.
* **Editar:** Carga dinámica de datos de usuario en formulario para actualización rápida.
* **Eliminar:** Eliminación de registros de usuario previa confirmación.
* **Validación de Correo:** Previene la duplicidad de cuentas basándose en el correo electrónico único.

### 🔄 3. Gestión de Préstamos
* **Registrar Préstamo:** Asignación de un libro disponible a un usuario registrado.
* **Control Transaccional (ACID):** Uso de transacciones de base de datos (`beginTransaction` / `commit` / `rollBack`) que garantizan la integridad:
  * Registra el préstamo con fecha actual (`CURDATE()`).
  * Descuenta automáticamente **1 unidad** del stock del libro prestado.
* **Devolución de Libros:** Registra la fecha de retorno, cambia el estado a `devuelto` y reacredita (+1) el stock al inventario.
* **Filtro de Disponible:** El selector de libros solo muestra títulos que posean stock mayor a cero.

---

## 📂 Estructura del Proyecto

```text
library-management-system-main/
/-- classes/
/   --- Database.php      # Conexión PDO a la base de datos
/   --- Libro.php         # Modelo / Entidad Libro
/   --- Usuario.php       # Modelo / Entidad Usuario
/   --- Prestamo.php      # Modelo / Entidad Préstamo
/   --- Biblioteca.php    # Clase controladora con la lógica de negocio
/--- screenshots/         # Capturas de pantalla del sistema funcionando
/   --- 01-libros.png
/   --- 02-usuarios.png
/   --- 03-prestamos.png
/--- index.php            # Interfaz principal y enrutador de vistas
/--- biblioteca.sql       # Script de creación e inicialización de la BD
/--- README.md            # Documentación del proyecto