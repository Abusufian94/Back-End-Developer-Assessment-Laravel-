<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



# Back-End Developer Assessment (Laravel)
## Objective:
Build a small e-commerce product management system with API endpoints using
Laravel.

## Laravel Inventory API:

This is a Laravel-based RESTful API for managing an inventory system, built as part of a Back-End Developer Assessment. The API allows administrators to manage products, categories, and inventory, with features like file-based caching to optimize performance for listing endpoints.

## Features

1. Product Management: Create, update, delete, and retrieve products with attributes like name, price, stock quantity, and images.
2. Category Management: Assign products to categories and filter by category.
3. Inventory Listing: Retrieve paginated product listings with filters for category and low stock thresholds.
4. File-Based Caching: Optimizes the GET /api/v1/inventory endpoint by caching results in the filesystem.
5. Authentication: Secured with Laravel Sanctum.
6. Spatie : Used Spatie for future role based access control requiring admin role for inventory operations.
7. Repository Pattern & services : Added Repoitory pattern with repository and services to separate the business logic and code logic which enhance the scalibility.
8. Form Request Validation : Created Request form validation for clean coding to sanitize the request without queries.
9. Resource Response : Added Laravel Resource to sanitize the response.
10. Custom Admin Middleware : To check the user is admin and and send the reponse accordingly.
11. Exception : Added Custom Exception for error handling.
12. Service Provider : Added custom providers to bind the Repositories and services.

## Prerequisites
1. Docker and Docker Compose (for Laravel Sail).
2. PHP >= 8.2
3. Composer


## Setup Instructions
#### 1. Clone the Repository : git clone https://github.com/Abusufian94/Back-End-Developer-Assessment-Laravel-.git
#### 2. cd Back-End-Developer-Assessment-Laravel-
#### 3. Install Dependencies : composer install
#### 4. Configure Environment : 

                        APP_URL=http://localhost:8000

                        CACHE_DRIVER=file

                        SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,::1

                        DB_CONNECTION=mysql

                        DB_HOST=mysql

                        DB_PORT=3306

                        DB_DATABASE=laravel

                        DB_USERNAME=sail

                        DB_PASSWORD=password

#### 5. Start Laravel Sail : ./vendor/bin/sail up -d
#### 6. Run Migrations : ./vendor/bin/sail artisan migrate
#### 7. Seed in to Database : ./vendor/bin/sail artisan db:seed 
#### 8. Link the storage : ./vendor/bin/sail artisan link:storage


## API Usage 
### Base url 
#### http://localhost:8000
### Authentication
#### All endpoints require an Authorization header with a Bearer token
Authorization: Bearer <token>
Accept: application/json
#### Key Endpoints
##### Create a Category
        Method: POST
        URL: /api/v1/admin/categories
        Body (JSON):
         {
                "name": "Electronics",
                "slug": "electronics"
         }
        Response (201):
        {
            "data":
            {
                "id": 1,
                "name": "Electronics",
                "slug": "electronics"
            },
            "status": "success"
        }



