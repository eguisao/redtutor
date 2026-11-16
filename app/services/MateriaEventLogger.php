<?php
namespace App\Services;

use RuntimeException;

class MateriaEventLogger
{
    public static function registrar(array $materia): bool
    {
        $apiUrl = env('KAFKA_PRODUCER_API_URL');
        $token  = env('KAFKA_PRODUCER_API_TOKEN');

        if (empty($apiUrl) || empty($token)) {
            throw new RuntimeException(
                'No está configurada la conexión con Kafka.'
            );
        }

        if (! function_exists('curl_init')) {
            throw new RuntimeException(
                'La extensión cURL de PHP no está habilitada.'
            );
        }

        $eventId = 'RT-MAT-' . bin2hex(random_bytes(16));
        error_log(
            'DATOS MATERIA KAFKA: '
            . json_encode($materia, JSON_UNESCAPED_UNICODE)
        );

        $payload = [
            'event_id'     => $eventId,
            'usuario_id'   => 1,
            'modulo'       => 'materias',
            'accion'       => 'crear_materia',
            'nivel'        => 'INFO',

            'mensaje'      => sprintf(
                'Se creó la materia "%s" con código "%s" en el área "%s".',
                $materia['nombre'] ?? '',
                $materia['codigo'] ?? '',
                $materia['area'] ?? ''
            ),

            'datos'        => [
                // Datos de la materia
                'codigo'      => $materia['codigo'] ?? '',
                'nombre'      => $materia['nombre'] ?? '',
                'descripcion' => $materia['descripcion'] ?? '',
                'area'        => $materia['area'] ?? '',
                'activa'      => 1,

                // Datos de la operación
                'tipo'        => 'CREACION',
                'ruta'        => '/redtutor/materias',
                'metodo_http' => 'POST',
                'resultado'   => 'EXITOSO',
            ],

            'fecha_evento' => date('Y-m-d H:i:s'),
        ];

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        $ch = curl_init($apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,

            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 5,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException(
                'Error de conexión con Kafka Producer API: '
                . $curlError
            );
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException(
                'Kafka Producer API respondió HTTP '
                . $httpCode
                . '. Respuesta: '
                . $response
            );
        }

        return true;
    }
}
