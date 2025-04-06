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
- Transaction chart

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
This APIs uses `Laravel Sanctum` for authentication. I haven’t implemented token-based authentication in this app yet, so the APIs are not fully stateless.

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


### GET /api/transactions/get-chart
This endpoint allows users to retrive transaction chart.

##### Request

```
GET /api/transactions/get-chart
Content-Type: application/json
Accept: application/json

// optional
{
  "user": "sfwgsdsgdgsdgsdg",
  "start_date" : "2025-01-01",
  "end_date" : "2025-03-01",
}

```
##### Response

```
{
    "status": "success",
    "code": 200,
    "message": "success",
    "data": {
        "labels": [
            "2025-03-07",
            "2025-03-08",
            "2025-03-09",
            "2025-03-10",
            "2025-03-11",
            "2025-03-12",
            "2025-03-13",
            "2025-03-14",
            "2025-03-15",
            "2025-03-16",
            "2025-03-17",
            "2025-03-18",
            "2025-03-19",
            "2025-03-20",
            "2025-03-21",
            "2025-03-22",
            "2025-03-23",
            "2025-03-24",
            "2025-03-25",
            "2025-03-26",
            "2025-03-27",
            "2025-03-28",
            "2025-03-29",
            "2025-03-30",
            "2025-03-31",
            "2025-04-01",
            "2025-04-02",
            "2025-04-03",
            "2025-04-04",
            "2025-04-05",
            "2025-04-06"
        ],
        "data": [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            2486,
            100
        ]
    }
}

```

## Testing
You can run the application's tests using Pest. Tests are located in the tests directory.
```
./vendor/bin/pest
```

## Admin Account

email : `admin@admin.com`  
password: `12345678`

admin dashboard url: `/admin/dashboard`