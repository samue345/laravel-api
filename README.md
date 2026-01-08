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

Login

POST /api/auth/login

{ "email": "user@test.com", "password": "secret123" }


Use o token retornado:

Authorization: Bearer <TOKEN>

Payments (protegido)
Criar pagamento

POST /api/payments

{ "amount": 1500, "currency": "BRL" }

Ver pagamento

GET /api/payments/{id}

Webhook

POST /api/providers/{provider}/webhook

{
  "provider_payment_id": "prov_123",
  "status": "paid",
  "event_id": "evt_123"
}

## Como testar

### 1. Registrar usuário
```bash
curl -X POST http://localhost:8080/api/auth/register \
-H "Content-Type: application/json" \
-d '{"name":"User","email":"user@test.com","password":"secret123"}'
2. Fazer login
bash
Copy code
curl -X POST http://localhost:8080/api/auth/login \
-H "Content-Type: application/json" \
-d '{"email":"user@test.com","password":"secret123"}'
Copie o token retornado.

3. Criar pagamento (autenticado)
bash
Copy code
curl -X POST http://localhost:8080/api/payments \
-H "Authorization: Bearer SEU_TOKEN_AQUI" \
-H "Content-Type: application/json" \
-d '{"amount":1500,"currency":"BRL"}'
4. Consultar pagamento
bash
Copy code
curl -X GET http://localhost:8080/api/payments/1 \
-H "Authorization: Bearer SEU_TOKEN_AQUI"
5. Simular webhook
bash
Copy code
curl -X POST http://localhost:8080/api/providers/provider_a/webhook \
-H "Content-Type: application/json" \
-d '{"provider_payment_id":"prov_123","status":"paid","event_id":"evt_123"}'



Testes
docker compose exec app php artisan test

