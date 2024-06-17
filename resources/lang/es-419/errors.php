<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'load_failed' => 'Error al cargar los datos.',
    'missing_route' => 'URL no válida o método de solicitud incorrecto.',
    'no_restricted_access' => 'No podrás realizar esta acción mientras tu cuenta esté en estado restringido.',
    'supporter_only' => 'Debes ser un osu!supporter para utilizar esta función.',
    'unknown' => 'Se produjo un error desconocido.',

    'codes' => [
        'http-401' => 'Inicia sesión para continuar.',
        'http-403' => 'Acceso denegado.',
        'http-404' => 'No encontrado.',
        'http-429' => 'Demasiados intentos. Inténtalo de nuevo más tarde.',
    ],
    'account' => [
        'profile-order' => [
            'generic' => 'Se produjo un error. Intenta actualizar la página.',
        ],
    ],
    'beatmaps' => [
        'invalid_mode' => 'Modo especificado no válido.',
        'standard_converts_only' => 'No hay puntuaciones disponibles para el modo solicitado en esta dificultad del mapa.',
    ],
    'checkout' => [
        'generic' => 'Se produjo un error mientras se preparaba el pago.',
    ],
    'search' => [
        'default' => 'No se ha podido obtener ningún resultado, inténtalo de nuevo más tarde.',
        'invalid_cursor_exception' => 'Se ha especificado un parámetro para el cursor no válido.',
        'operation_timeout_exception' => 'La búsqueda está más saturada de lo habitual, inténtalo de nuevo más tarde.',
    ],
];
