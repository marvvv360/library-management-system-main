<?php
require_once 'Database.php';
require_once 'Libro.php';
require_once 'Usuario.php';
require_once 'Prestamo.php';

class Biblioteca {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // ==========================================
    // 1. GESTIÓN DE LIBROS
    // ==========================================

    public function agregarLibro(Libro $libro) {
        try {
            $sql = "INSERT INTO libros (titulo, autor, isbn, cantidad) VALUES (:titulo, :autor, :isbn, :cantidad)";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':titulo'   => $libro->getTitulo(),
                ':autor'    => $libro->getAutor(),
                ':isbn'     => $libro->getIsbn(),
                ':cantidad' => $libro->getCantidad()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editarLibro($id, $nuevosDatos) {
        try {
            $sql = "UPDATE libros SET titulo = :titulo, autor = :autor, isbn = :isbn, cantidad = :cantidad WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id'       => $id,
                ':titulo'   => $nuevosDatos['titulo'],
                ':autor'    => $nuevosDatos['autor'],
                ':isbn'     => $nuevosDatos['isbn'],
                ':cantidad' => $nuevosDatos['cantidad']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminarLibro($id) {
        try {
            $sql = "DELETE FROM libros WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerLibros() {
        try {
            $sql = "SELECT * FROM libros";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarLibro($id) {
        try {
            $sql = "SELECT * FROM libros WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================
    // 2. GESTIÓN DE USUARIOS
    // ==========================================

    public function agregarUsuario(Usuario $usuario) {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nombre'   => $usuario->getNombre(),
                ':email'    => $usuario->getEmail(),
                ':telefono' => $usuario->getTelefono()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editarUsuario($id, $nuevosDatos) {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id'       => $id,
                ':nombre'   => $nuevosDatos['nombre'],
                ':email'    => $nuevosDatos['email'],
                ':telefono' => $nuevosDatos['telefono']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminarUsuario($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerUsuarios() {
        try {
            $sql = "SELECT * FROM usuarios";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarUsuario($id) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================
    // 3. GESTIÓN DE PRÉSTAMOS
    // ==========================================

    public function prestarLibro($libro_id, $usuario_id) {
        $libro = $this->buscarLibro($libro_id);

        if (!$libro || $libro['cantidad'] <= 0) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado) 
                    VALUES (:libro_id, :usuario_id, CURDATE(), 'activo')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':libro_id'  => $libro_id,
                ':usuario_id' => $usuario_id
            ]);

            $sqlStock = "UPDATE libros SET cantidad = cantidad - 1 WHERE id = :id";
            $stmtStock = $this->conn->prepare($sqlStock);
            $stmtStock->execute([':id' => $libro_id]);

            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function devolverLibro($prestamo_id) {
        $sql = "SELECT libro_id, estado FROM prestamos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $prestamo_id]);
        $prestamo = $stmt->fetch();

        if (!$prestamo || $prestamo['estado'] === 'devuelto') {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            $sqlDevolver = "UPDATE prestamos 
                            SET estado = 'devuelto', fecha_devolucion = CURDATE() 
                            WHERE id = :id";
            $stmtDevolver = $this->conn->prepare($sqlDevolver);
            $stmtDevolver->execute([':id' => $prestamo_id]);

            $sqlStock = "UPDATE libros SET cantidad = cantidad + 1 WHERE id = :id";
            $stmtStock = $this->conn->prepare($sqlStock);
            $stmtStock->execute([':id' => $prestamo['libro_id']]);

            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function obtenerPrestamosActivos() {
        try {
            $sql = "SELECT p.id, l.titulo AS libro, u.nombre AS usuario, p.fecha_prestamo, p.estado 
                    FROM prestamos p
                    JOIN libros l ON p.libro_id = l.id
                    JOIN usuarios u ON p.usuario_id = u.id
                    WHERE p.estado = 'activo'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}