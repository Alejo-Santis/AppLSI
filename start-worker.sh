#!/bin/bash
echo "🚀 Iniciando worker de notificaciones..."
echo "Presiona Ctrl+C para detener"

while true; do
    echo "$(date): Iniciando worker..."
    php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=120
    echo "$(date): Worker detenido, reiniciando en 3 segundos..."
    sleep 3
done