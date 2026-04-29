<?php

if (! function_exists('renombrarPermiso')) {
    /**
     * Convierte el nombre técnico de un permiso en una etiqueta legible para mostrar.
     * Ej: "denuncias.crear" → "Crear denuncias"
     *
     * @param  string  $nombre  Nombre del permiso (ej. denuncias.crear, usuarios.editar)
     * @return string
     */
    function renombrarPermiso(string $nombre): string
    {
        $partes = explode('.', $nombre, 2);
        $modulo = isset($partes[0]) ? str_replace('_', ' ', $partes[0]) : '';
        $accion = isset($partes[1]) ? str_replace('_', ' ', $partes[1]) : '';
        $accion = ucfirst($accion);

        if ($modulo === '' && $accion === '') {
            return $nombre;
        }

        if ($accion === '') {
            return ucfirst($modulo);
        }

        return $accion . ' ' . $modulo;
    }
}
