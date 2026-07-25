<?php
require_once 'classes/Biblioteca.php';

$biblioteca = new Biblioteca();

$mensaje = '';
$tipoMensaje = 'exito';

// Variables para modo edición
$libroEditar = null;
$usuarioEditar = null;

// Capturar libro o usuario a editar por GET
if (isset($_GET['editar_libro'])) {
    $libroEditar = $biblioteca->buscarLibro((int)$_GET['editar_libro']);
}

if (isset($_GET['editar_usuario'])) {
    $usuarioEditar = $biblioteca->buscarUsuario((int)$_GET['editar_usuario']);
}

// Processing POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    switch ($formAction) {
        // --- LIBROS ---
        case 'guardar_libro':
            $libro = new Libro($_POST['titulo'], $_POST['autor'], $_POST['isbn'], (int)$_POST['cantidad']);
            if ($biblioteca->agregarLibro($libro)) {
                $mensaje = 'Libro agregado correctamente.';
            } else {
                $mensaje = 'Error al agregar el libro (Verifica que el ISBN no esté duplicado).';
                $tipoMensaje = 'error';
            }
            break;

        case 'actualizar_libro':
            $id = (int)$_POST['id'];
            $datos = [
                'titulo' => $_POST['titulo'],
                'autor' => $_POST['autor'],
                'isbn' => $_POST['isbn'],
                'cantidad' => (int)$_POST['cantidad']
            ];
            if ($biblioteca->editarLibro($id, $datos)) {
                $mensaje = 'Libro actualizado correctamente.';
            } else {
                $mensaje = 'Error al actualizar el libro.';
                $tipoMensaje = 'error';
            }
            break;

        case 'eliminar_libro':
            $id = (int)$_POST['id'];
            if ($biblioteca->eliminarLibro($id)) {
                $mensaje = 'Libro eliminado correctamente.';
            } else {
                $mensaje = 'Error al eliminar el libro.';
                $tipoMensaje = 'error';
            }
            break;

        // --- USUARIOS ---
        case 'guardar_usuario':
            $usuario = new Usuario($_POST['nombre'], $_POST['email'], $_POST['telefono']);
            if ($biblioteca->agregarUsuario($usuario)) {
                $mensaje = 'Usuario registrado correctamente.';
            } else {
                $mensaje = 'Error al registrar usuario (Verifica que el correo no esté duplicado).';
                $tipoMensaje = 'error';
            }
            break;

        case 'actualizar_usuario':
            $id = (int)$_POST['id'];
            $datos = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono']
            ];
            if ($biblioteca->editarUsuario($id, $datos)) {
                $mensaje = 'Usuario actualizado correctamente.';
            } else {
                $mensaje = 'Error al actualizar usuario.';
                $tipoMensaje = 'error';
            }
            break;

        case 'eliminar_usuario':
            $id = (int)$_POST['id'];
            if ($biblioteca->eliminarUsuario($id)) {
                $mensaje = 'Usuario eliminado correctamente.';
            } else {
                $mensaje = 'Error al eliminar el usuario.';
                $tipoMensaje = 'error';
            }
            break;

        // --- PRÉSTAMOS ---
        case 'prestar_libro':
            $libroId = (int)$_POST['libro_id'];
            $usuarioId = (int)$_POST['usuario_id'];
            if ($biblioteca->prestarLibro($libroId, $usuarioId)) {
                $mensaje = 'Préstamo realizado exitosamente.';
            } else {
                $mensaje = 'No se pudo realizar el préstamo (comprueba el stock).';
                $tipoMensaje = 'error';
            }
            break;

        case 'devolver_libro':
            $prestamoId = (int)$_POST['prestamo_id'];
            if ($biblioteca->devolverLibro($prestamoId)) {
                $mensaje = 'Libro devuelto exitosamente.';
            } else {
                $mensaje = 'Error al procesar la devolución.';
                $tipoMensaje = 'error';
            }
            break;
    }
}

$seccion = $_GET['action'] ?? 'libros';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Biblioteca</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background: #f4f6f9; color: #333; }
        .container { max-width: 950px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; color: #2c3e50; }
        nav { background: #34495e; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: #ecf0f1; font-weight: bold; }
        nav a:hover { color: #1abc9c; }
        .alerta { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .alerta.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alerta.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
        form { background: #f9f9f9; padding: 15px; border: 1px solid #e0e0e0; border-radius: 6px; margin-bottom: 20px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #27ae60; color: white; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        button:hover { background: #219150; }
        .btn-edit { background: #2980b9; }
        .btn-edit:hover { background: #1f618d; }
        .btn-del { background: #c0392b; }
        .btn-del:hover { background: #962d22; }
        .btn-dev { background: #e67e22; }
        .btn-dev:hover { background: #d35400; }
        .actions-cell { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Sistema de Biblioteca</h1>
        
        <nav>
            <a href="index.php?action=libros">Libros</a>
            <a href="index.php?action=usuarios">Usuarios</a>
            <a href="index.php?action=prestamos">Préstamos</a>
        </nav>

        <?php if (!empty($mensaje)): ?>
            <div class="alerta <?= $tipoMensaje ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div id="content">
            <?php if ($seccion === 'libros'): ?>
                <!-- SECCIÓN DE LIBROS -->
                <h2>Gestión de Libros</h2>
                
                <form action="index.php?action=libros" method="POST">
                    <input type="hidden" name="form_action" value="<?= $libroEditar ? 'actualizar_libro' : 'guardar_libro' ?>">
                    <?php if ($libroEditar): ?>
                        <input type="hidden" name="id" value="<?= $libroEditar['id'] ?>">
                    <?php endif; ?>

                    <h3><?= $libroEditar ? 'Editar Libro' : 'Registrar Nuevo Libro' ?></h3>
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($libroEditar['titulo'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" name="autor" value="<?= htmlspecialchars($libroEditar['autor'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ISBN:</label>
                        <input type="text" name="isbn" value="<?= htmlspecialchars($libroEditar['isbn'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cantidad Disponible:</label>
                        <input type="number" name="cantidad" min="1" value="<?= htmlspecialchars($libroEditar['cantidad'] ?? '1') ?>" required>
                    </div>
                    <button type="submit"><?= $libroEditar ? 'Actualizar Libro' : 'Agregar Libro' ?></button>
                    <?php if ($libroEditar): ?>
                        <a href="index.php?action=libros" style="margin-left: 10px;">Cancelar</a>
                    <?php endif; ?>
                </form>

                <h3>Inventario de Libros</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($biblioteca->obtenerLibros() as $libro): ?>
                            <tr>
                                <td><?= htmlspecialchars($libro['id']) ?></td>
                                <td><?= htmlspecialchars($libro['titulo']) ?></td>
                                <td><?= htmlspecialchars($libro['autor']) ?></td>
                                <td><?= htmlspecialchars($libro['isbn']) ?></td>
                                <td><?= htmlspecialchars($libro['cantidad']) ?></td>
                                <td class="actions-cell">
                                    <a href="index.php?action=libros&editar_libro=<?= $libro['id'] ?>"><button type="button" class="btn-edit">Editar</button></a>
                                    <form action="index.php?action=libros" method="POST" style="margin:0; padding:0; background:none; border:none;" onsubmit="return confirm('¿Eliminar este libro?');">
                                        <input type="hidden" name="form_action" value="eliminar_libro">
                                        <input type="hidden" name="id" value="<?= $libro['id'] ?>">
                                        <button type="submit" class="btn-del">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($seccion === 'usuarios'): ?>
                <!-- SECCIÓN DE USUARIOS -->
                <h2>Gestión de Usuarios</h2>

                <form action="index.php?action=usuarios" method="POST">
                    <input type="hidden" name="form_action" value="<?= $usuarioEditar ? 'actualizar_usuario' : 'guardar_usuario' ?>">
                    <?php if ($usuarioEditar): ?>
                        <input type="hidden" name="id" value="<?= $usuarioEditar['id'] ?>">
                    <?php endif; ?>

                    <h3><?= $usuarioEditar ? 'Editar Usuario' : 'Registrar Nuevo Usuario' ?></h3>
                    <div class="form-group">
                        <label>Nombre Completo:</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuarioEditar['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuarioEditar['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($usuarioEditar['telefono'] ?? '') ?>" required>
                    </div>
                    <button type="submit"><?= $usuarioEditar ? 'Actualizar Usuario' : 'Registrar Usuario' ?></button>
                    <?php if ($usuarioEditar): ?>
                        <a href="index.php?action=usuarios" style="margin-left: 10px;">Cancelar</a>
                    <?php endif; ?>
                </form>

                <h3>Usuarios Registrados</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($biblioteca->obtenerUsuarios() as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['id']) ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                                <td class="actions-cell">
                                    <a href="index.php?action=usuarios&editar_usuario=<?= $usuario['id'] ?>"><button type="button" class="btn-edit">Editar</button></a>
                                    <form action="index.php?action=usuarios" method="POST" style="margin:0; padding:0; background:none; border:none;" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        <input type="hidden" name="form_action" value="eliminar_usuario">
                                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                        <button type="submit" class="btn-del">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($seccion === 'prestamos'): ?>
                <!-- SECCIÓN DE PRÉSTAMOS -->
                <h2>Gestión de Préstamos</h2>

                <form action="index.php?action=prestamos" method="POST">
                    <input type="hidden" name="form_action" value="prestar_libro">
                    <h3>Registrar Nuevo Préstamo</h3>
                    <div class="form-group">
                        <label>Seleccionar Libro:</label>
                        <select name="libro_id" required>
                            <option value="">-- Selecciona un libro --</option>
                            <?php foreach ($biblioteca->obtenerLibros() as $libro): ?>
                                <?php if ($libro['cantidad'] > 0): ?>
                                    <option value="<?= $libro['id'] ?>">
                                        <?= htmlspecialchars($libro['titulo']) ?> (Stock: <?= $libro['cantidad'] ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Usuario:</label>
                        <select name="usuario_id" required>
                            <option value="">-- Selecciona un usuario --</option>
                            <?php foreach ($biblioteca->obtenerUsuarios() as $usr): ?>
                                <option value="<?= $usr['id'] ?>"><?= htmlspecialchars($usr['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Realizar Préstamo</button>
                </form>

                <h3>Préstamos Activos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libro</th>
                            <th>Usuario</th>
                            <th>Fecha Préstamo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($biblioteca->obtenerPrestamosActivos() as $prestamo): ?>
                            <tr>
                                <td><?= htmlspecialchars($prestamo['id']) ?></td>
                                <td><?= htmlspecialchars($prestamo['libro']) ?></td>
                                <td><?= htmlspecialchars($prestamo['usuario']) ?></td>
                                <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                                <td><?= htmlspecialchars($prestamo['estado']) ?></td>
                                <td>
                                    <form action="index.php?action=prestamos" method="POST" style="margin:0; padding:0; background:none; border:none;">
                                        <input type="hidden" name="form_action" value="devolver_libro">
                                        <input type="hidden" name="prestamo_id" value="<?= $prestamo['id'] ?>">
                                        <button type="submit" class="btn-dev">Devolver</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>