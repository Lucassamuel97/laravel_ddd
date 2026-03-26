# Laravel DDD — RESTful API Template

Template de API RESTful construído com **Laravel 11**, **PHP 8.3+**, **MySQL** e **Docker**, seguindo rigorosamente os princípios de **Domain-Driven Design (DDD)**, **Clean Architecture** e **Test-Driven Development (TDD)**.

---

## 🗂 Estrutura do Projeto

```
app/
├── Domain/
│   └── User/
│       ├── Entities/          # Entidade User (puro PHP, sem Laravel)
│       ├── ValueObjects/      # Email, Password
│       ├── Repositories/      # Interface UserRepositoryInterface
│       └── Exceptions/        # DuplicateEmailException, InvalidEmailException...
│
├── Application/
│   └── User/
│       ├── UseCases/          # RegisterUserUseCase
│       └── DTOs/              # RegisterUserInputDTO, RegisterUserOutputDTO
│
├── Infrastructure/
│   └── Persistence/
│       ├── Eloquent/          # UserModel (Eloquent)
│       └── Repositories/      # EloquentUserRepository
│
├── Interfaces/
│   └── Http/
│       ├── Controllers/       # UserController
│       ├── Requests/          # RegisterUserRequest
│       └── Resources/         # UserResource
│
├── Providers/
│   └── AppServiceProvider     # Bind interface → implementação
│
└── Shared/
```

---

## 🚀 Como rodar com Docker

```bash
# 1. Copiar o .env
cp .env.example .env

# 2. Subir os containers
docker-compose up -d

# 3. Instalar dependências
docker-compose exec app composer install

# 4. Gerar a key da aplicação
docker-compose exec app php artisan key:generate

# 5. Executar as migrations
docker-compose exec app php artisan migrate

# 6. Popular o banco com o seeder (admin@admin.com / password)
docker-compose exec app php artisan db:seed
```

A API estará disponível em: **http://localhost:8080**

---

## 🔌 Endpoint

### Cadastrar Usuário

```
POST /api/users
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "role": "user"
}
```

**Resposta (201 Created):**
```json
{
    "data": {
        "id": "1",
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    }
}
```

**Erros:**
- `422` — Email duplicado ou dados inválidos

---

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Ou via PHPUnit diretamente
./vendor/bin/phpunit
```

### Cobertura dos testes

| Tipo        | Classe                        | O que testa                                         |
|-------------|-------------------------------|-----------------------------------------------------|
| Unit        | `UserTest`                    | Criação da entidade, role padrão                    |
| Unit        | `EmailTest`                   | Validação, normalização, comparação                 |
| Unit        | `RegisterUserUseCaseTest`     | Fluxo de sucesso, email duplicado, role admin       |
| Feature     | `UserControllerTest`          | POST /api/users — sucesso, duplicado, validações    |

---

## 🧱 Regras de Arquitetura

| Camada         | Depende de                  | Não depende de            |
|----------------|-----------------------------|---------------------------|
| Domain         | Nada (PHP puro)             | Laravel, Eloquent         |
| Application    | Domain                      | Infrastructure, Laravel   |
| Infrastructure | Domain, Laravel/Eloquent    | Application               |
| Interfaces     | Application, Laravel HTTP   | Domain direto, Eloquent   |

---

## 🐳 Serviços Docker

| Serviço | Imagem          | Porta      |
|---------|-----------------|------------|
| app     | PHP 8.3-fpm     | —          |
| nginx   | nginx:alpine    | 8080:80    |
| mysql   | mysql:8.0       | 3306:3306  |

---

## 🌱 Usuário padrão (Seeder)

| Campo  | Valor             |
|--------|-------------------|
| email  | admin@admin.com   |
| senha  | password          |
| role   | admin             |