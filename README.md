# e-wallet
 
Simple E-wallet project:

* System dependencies
    - Laravel Framework 8.*
    - PHP 7.4
    - Json Web Token (JWT)
    - MySQL
    - PHP Composer

* Endpoint
    - User Registration : POST /register
    - User Login        : POST /login
    - Get Balance Read  : GET /transaction/balance_read
    - Get Top Transaction by user : GET /transaction/top_transactions_per_user
    - Get Top User most value Transaction : GET /transaction/top_users
    - Transfer Amount to another account : POST /transaction/transfer
    - Topup Account Balance : POST /transaction/balance_topup

* Deployment instructions

    - Clone this project:
        $ git clone https://github.com/ddhieq86/e-wallet.git

    - Change directory to this project: 
        $ cd e-wallet

    - Prepare .ENV file based on .env.example:
        $ cp .env.example .env

    - Install project:
        $ composer install
    
    - Create database

    - Run migration:
        $ php artisan migrate