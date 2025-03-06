# 🍎🥕 Symfony Fruits & Vegetables API

## Overview
This is a Symfony-based REST API that allows users to manage a list of fruits and vegetables. The API supports the following operations:
- List all fruits and vegetables
- Add a new fruit or vegetable
- Delete an existing fruit or vegetable

## Requirements
- PHP 8.1 or higher
- Composer
- Symfony CLI
- MariaDB or MySQL database

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/did-64/fruits-and-vegetables.git
   cd fruits-and-vegetables
   ```

2. Install dependencies:
   ```sh
   composer install
   ```

3. Configure environment variables:
   Copy the `.env` file and update the database configuration:
   ```sh
   cp .env .env.local
   ```
   Update the `DATABASE_URL` in `.env.local`:
   ```
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
   ```

4. Set up the database:
   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```

5. Run the Symfony server:
   ```sh
   symfony server:start
   ```

## API Endpoints

### Populate database
_process the request.json file at root and populate database_
```http
GET /api/process-json
```
#### Response
Status Code: 201 Created
```json
{
  "success": true
}
```

### Get all fruits and vegetables
```http
GET /api/list/{type}
(e.g., /api/list/fruit)
The quantity field is expressed in grams.
```
#### Response
Status Code: 200 OK
```json
[
  { "id": 1, "name": "Apple", "quantity": 2000 },
  { "id": 2, "name": "Peach", "quantity": 3000 }
]
```

### Add a new fruit or vegetable
```http
POST /api/create
The request body can be either a single object or an array of objects.
The unit field must be either "g" (grams) or "kg" (kilograms).
The quantity field should match the unit (e.g., 500 g or 2 kg)
```
#### Request Body (Single Item)
```json
  {
    "name": "Grape",
    "type": "fruit",
    "quantity": 20,
    "unit": "kg"
  }
```
#### Request Body (Multiple Items)
```json
[
  {
    "name": "Banana",
    "type": "fruit",
    "quantity": 1000,
    "unit": "g"
  },
  {
    "name": "Bean",
    "type": "vegetable",
    "quantity": 24,
    "unit": "kg"
  }
]
```
#### Response
Status Code: 201 Created
```json
{
  "success": true
}
```

### Delete a fruit or vegetable
```http
DELETE /api/remove/{type}/{id}
(e.g., /api/remove/vegetable/2)
```
#### Response
Status Code: 204 No Content
```text
(No response body)
```

## Testing
Run the test suite:
```sh
php bin/phpunit
```


