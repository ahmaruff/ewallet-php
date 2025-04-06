# E-Wallet App
E-Wallet App is a web application designed to manage user wallets, transactions, and payment gateway integrations. It allows users to deposit, withdraw, and manage their wallet balances, while also interacting with third-party payment gateway services. The app is built with Laravel for the backend and Vue.js for the frontend, providing a seamless experience for both users and administrators.

This application includes features such as:
- User authentication
- Secure transaction processing via payment gateway
- Deposit and withdrawal validation
- Transaction history
- User authentication
- Integration with a third-party payment gateway
- Deposit and withdrawal features with validation
- Transaction history tracking
- Unit and feature tests using Pest and PHPUnit

## Technologies Used
- Laravel 12 (Backend)
- SQLite (Database)
- Vue.js (Frontend)
- Inertia.js (Frontend integration with backend)
- Laravel Sanctum (API Authentication)
- Pest & PHPUnit (Testing)
- Payment Gateway API Services (Third-party payment integration)

## Pre-Requerements
- PHP >= 8.2.x
- Node >= 22.13.x

## Instalation
#### Clone the repository to your local machine.  

```
git clone https://github.com/ahmaruff/ewallet-php.git
```

#### Install dependencies
```
cd ewallet-php
composer install
npm install
```

#### Setup Environtment File
Create a .env file by copying the example .env.example file.

```
cp .env.example .env

```
Edit the .env file and configure your database, payment gateway, and other environment variables.
```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost, yourdomain.com
SESSION_DOMAIN=yourdomain.com

PAYMENT_GATEWAY_BASE_URL=https://yourdomain.com
PAYMENT_GATEWAY_API_KEY=your-api-key
```

#### Run Migration
Run the database migrations to set up the necessary tables.
```
php artisan migrate
```

#### Run Seeder
```
php artisan db:seed
```

#### Start The Application
```
composer run dev
```

## API Documentation
### POST /api/transactions/deposit
This endpoint allows users to deposit funds into their wallet.

##### Request

```
POST /api/transactions/deposit
Content-Type: application/json
Accept: application/json

{
  "amount": 100000.00
}

```

###### Response
```
{
  "status": "success",
  "code": 201,
  "message": "funds deposited",
  "data": {
    "transaction": {
      "wallet_id' : "4141hwkwbgfwrefdsgt2",
      "type' : "deposit",
      "amount' : 100000.00,
      "description' : "",
      "reference_id' : "",
      "meta' : []
    },
    "user_id" : "asffsfjhsofhfdsjhs",
    "balance" : 100000.00
  }
}
```

### POST /api/transactions/withdraw
This endpoint allows users to withdraw funds from their wallet.

##### Request

```
POST /api/transactions/withdraw
Content-Type: application/json
Accept: application/json

{
  "amount": 100000.00
}

```
##### Response

```
{
  "status": "success",
  "code": 201,
  "message": "funds withdrawed",
  "data": {
    "transaction": {
      "wallet_id' : "4141hwkwbgfwrefdsgt2",
      "type' : "withdrawal",
      "amount' : 100000.00,
      "description' : "",
      "reference_id' : "",
      "meta' : []
    },
    "user_id" : "asffsfjhsofhfdsjhs",
    "balance" : 0.00
  }
}

```

## Testing
You can run the application's tests using Pest. Tests are located in the tests directory.
```
./vendor/bin/pest
```
