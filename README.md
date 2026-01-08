# Laravel Payments API

API Laravel com autenticação (Sanctum) e pagamentos.

---

## Rodar localmente

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

Autenticação
Registrar

POST /api/auth/register

{ "name": "User", "email": "user@test.com", "password": "secret123" }
