<?php

class Prestamo {
    private $id;
    private $libro_id;
    private $usuario_id;
    private $fecha_prestamo;
    private $fecha_devolucion;
    private $estado;

    public function __construct(
        $libro_id = null, 
        $usuario_id = null, 
        $fecha_prestamo = null, 
        $fecha_devolucion = null, 
        $estado = 'activo', 
        $id = null
    ) {
        $this->id = $id;
        $this->libro_id = $libro_id;
        $this->usuario_id = $usuario_id;
        // Si no se especifica una fecha, se asigna automáticamente la fecha actual (YYYY-MM-DD)
        $this->fecha_prestamo = $fecha_prestamo ?? date('Y-m-d');
        $this->fecha_devolucion = $fecha_devolucion;
        $this->estado = $estado;
    }

    // Getters y Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLibroId() {
        return $this->libro_id;
    }

    public function setLibroId($libro_id) {
        $this->libro_id = $libro_id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    public function getFechaPrestamo() {
        return $this->fecha_prestamo;
    }

    public function setFechaPrestamo($fecha_prestamo) {
        $this->fecha_prestamo = $fecha_prestamo;
    }

    public function getFechaDevolucion() {
        return $this->fecha_devolucion;
    }

    public function setFechaDevolucion($fecha) {
        $this->fecha_devolucion = $fecha;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}