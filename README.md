# AbuNajib
![image](https://github.com/user-attachments/assets/61a499af-6f4c-466a-bbc4-45b8877523d6)

This project was developed over a weekend for personal use, and functionality may evolve based on changing requirements.

## Overview

AbuNajib is a multi-tenant budget planning and transaction tracking application inspired by a character from a famous Syrian series known for being extremely frugal. The project leverages Laravel and Filament to provide a robust and user-friendly experience for managing finances. 

This application allows users to manage multiple bank accounts, set up different account types, transfer money between accounts, and track various transactions. Additionally, users can create categories and groups for transactions to better organize and plan their budgets.

## Features

### Accounts Management
- **Multiple Account Types**: Users can create various account types such as cash, checking accounts, credit cards, and debts.
- **Transfers**: Transfer funds between different accounts seamlessly.

### Transactions
- **Tracking**: Create transactions to record different expenditures and income.
- **Categorization**: Assign categories to transactions for better organization. Categories can be user-defined.
- **Grouping**: Categories belong to groups that users can create to manage their transactions better.

### Budget Planning
- **Plan Transactions**: Plan future transactions to keep track of expected expenditures.
- **Transfers**: Plan transfers between accounts as part of budget management.
- **Budgeting by Category**: Create budgets for different transaction categories to manage spending effectively.

### Reporting
- **Track Budget Adherence**: Monitor how well you are following your budget through detailed reporting.

## Technology Stack

AbuNajib was built using **Laravel** and **Filament**. 



## Installation and Setup

1. Clone the repository:
    ```bash
    git clone https://github.com/mikeashi/AbuNajib.git
    ```

2. Navigate to the project directory:
    ```bash
    cd AbuNajib
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Set up environment variables:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. Configure your `.env` file with the necessary database.

6. Run the migrations:
    ```bash
    php artisan migrate --seed
    ```

7. Serve the application:
    ```bash
    php artisan serve
    ```

## Usage

Once the application is set up, you can start by creating accounts and setting up different account types. Use the transfer functionality to move money between accounts. Track your spending by creating transactions and categorizing them. Plan your budget by setting up budgets for different transaction categories and monitor your progress through the reporting features.

## Contributing

Since this project is primarily for personal use, contributions are currently not accepted. However, if you have suggestions or feedback, feel free to reach out.

## License

This project is licensed under the MIT License.
