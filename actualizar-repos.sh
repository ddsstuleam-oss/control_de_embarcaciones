#!/bin/bash
set -e

cd ~/Desktop/trabajo-temporal/control_de_embarcaciones

echo "Actualizando backend..."
git clone https://github.com/mosquera23-uleam/embarcaciones_fcvt_backend.git temp-backend
rm -rf temp-backend/.git
cp -a temp-backend/. backend-laravel/
rm -rf temp-backend

echo "Actualizando frontend..."
git clone https://github.com/mosquera23-uleam/uleam_embarcaciones.git temp-frontend
rm -rf temp-frontend/.git
cp -a temp-frontend/. frotend-flutter/
rm -rf temp-frontend

git add .
git commit -m "Actualiza backend-laravel y frotend-flutter con últimos cambios"
git push origin main

echo "¡Listo!"
