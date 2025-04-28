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
13. Implemented soft delete 
14. Implemented feature testing for authentication

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

                        SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000

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

##  Auth API Docs
##### Create a User
        Method: POST
        URL: /api/v1/register
        Body (JSON):
        {
            "name": "Super Customer",
            "email": "customer3@gmail.com",
            "password": "Pass@123",
            "confirm_password": "Pass@123",
            "role": "customer"
        }

        Response (201):
        {
            "data": {
                "user": {
                    "id": 4,
                    "name": "Super Customer",
                    "email": "customer3@gmail.com",
                    "role": "customer",
                    "created_at": "2025-04-28T14:01:44.000000Z"
                },
                "token": "20|3Foo4Fgf2sPNsbjMEth4CY2NhDQR38zUuEgHSKGf62d4898e"
            },
            "status": "success",
            "message": "Operation successful"
        }

##### Login
        Method: POST
        URL: /api/v1/login
        Body (JSON):
        {  
            "email": "customer3@gmail.com",
            "password": "Pass@123"
        }

        Response (201):
        {
            "data": {
                "user": {
                    "id": 4,
                    "name": "Super Customer",
                    "email": "customer3@gmail.com",
                    "role": "customer",
                    "created_at": "2025-04-28T14:01:44.000000Z"
                },
                "token": "21|VTHTVoPCOLiDvoaltdoYQIswptPXyTqm24wBHWGu8bdc31c4"
            },
            "status": "success",
            "message": "Operation successful"
        }

##### Logout
        *Needed Bearer Token 
        Method: POST
        URL: /api/v1/logout
        
        Response (200):
        {
            "data": [],
            "status": "success",
            "message": "Logged out successfully"
        }

##  Category API Docs

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

##### Update a Category
        Method: PUT
        URL: /api/v1/admin/categories/1
        Body (JSON):
        {
            "name": "smartphone updated"
        }
        Response (201):
        {
            "data": {
                "id": 1,
                "name": "smartphone updated",
                "slug": "smartphone-updated",
                "created_at": "2025-04-28T09:32:15+00:00",
                "updated_at": "2025-04-28T09:35:50+00:00"
            },
            "status": "success",
            "message": "Category updated successfully"
        }

##### Category List
        Method: GET
        URL: /api/v1/admin/categories
        
        Response (200):
        {
            "data": [
                {
                    "id": 3,
                    "name": "smartphone",
                    "slug": "smartphone",
                    "created_at": "2025-04-28T11:49:55+00:00",
                    "updated_at": "2025-04-28T11:49:55+00:00"
                },
                {
                    "id": 4,
                    "name": "iphone",
                    "slug": "iphone",
                    "created_at": "2025-04-28T11:54:05+00:00",
                    "updated_at": "2025-04-28T11:54:05+00:00"
                }
            ],
            "status": "success",
            "message": "Categories retrieved successfully"
        }

##### Delete Category 
        Method: DELETE
        URL: /api/v1/admin/categories/3
        
        Response (200):
        {
            "data": null,
            "status": "success",
            "message": "Category deleted successfully"
        }

##  Product API Docs
##### Create a Product
        For uploading image you can use form data in postman

        Method: POST
        URL: /api/v1/admin/products
        Body (JSON):
        {
            "name": "Test Product",
            "description": "A sample product description",
            "price": 99.99,
            "stock_quantity": 100,
            "category_ids": [1],
            "images": []
        }
        Response (201):
        {
            "data": {
                "id": 25,
                "name": "Test Product",
                "slug": "test-product",
                "description": "A sample product description",
                "price": 99.99,
                "stock_quantity": 100,
                "images": [],
                "categories": [
                    "smartphone"
                ],
                "created_at": "2025-04-28T14:08:25+00:00"
            },
            "status": "success",
            "message": "Product created successfully"
        }

##### Update a Product
        For uploading image you can use form data
        Method: POST
        URL: /api/v1/admin/products
        Body (JSON):
        {
            "name": "Samsung6",
            "description": "lorem ipsum",
            "price": 4500,
            "stock_quantity": 1000,
            "category_ids": [3],
            "images:[],
        }

        Response (201):
        {
            "data": {
                "id": 16,
                "name": "Samsung6 updae",
                "slug": "samsung6-updae",
                "description": "lorem ipsum",
                "price": "4500",
                "stock_quantity": "1000",
                "images": [
                    "http://127.0.0.1:8000/storage/products/tip2c9GAd86OaBMj3dpL4E0DWs14MucJllIo8LBE.jpg",
                    "http://127.0.0.1:8000/storage/products/2ByYW4L3zZl4OxyPEOZ0jUWitog3rEb2LKXzQ4eV.jpg",
                 
                ],
                "categories": [
                    "smartphone"
                ],
                "created_at": "2025-04-28T11:48:35+00:00"
            },
            "status": "success",
            "message": "Product updated successfully"
        }

##### Product List
        Method: GET
        URL: /api/v1/products/5
        Response (200):
       {
            "data": {
                "id": 5,
                "name": "Samsung6",
                "slug": "samsung6",
                "description": "SamsungSamsungSamsungSamsungSamsungSamsung",
                "price": "4500.00",
                "stock_quantity": 1000,
                "images": [
                    "http://127.0.0.1:8000/storage/products/qm5rEwi3ga7vSuo1KRHbeoZMcFLQxOcGoG6zEQmx.jpg",
                    "http://127.0.0.1:8000/storage/products/7kWAxGaWzGpFcBbD5bNwXJP4C7iF2aYDjXBpDHCL.jpg",
                    "http://127.0.0.1:8000/storage/products/qx9AKz5wzG7y9ilkZfBtAcGwJXDGe0bDO4lIpHht.jpg"
                ],
                "created_at": "2025-04-27T20:59:16.000000Z",
                "updated_at": "2025-04-27T20:59:16.000000Z",
                "deleted_at": null
            },
            "status": "success",
            "message": "Operation successful"
        }

##### Delete A Product
        Method: GET
        URL: /api/v1/admin/products/5
        Response (200):
        {
            "status": "success",
            "message": "Product deleted successfully"
        }



##### Adjust Stock
        Method: PATCH
        URL: /api/v1/admin/inventory/1  
        Body (JSON):
        {
            "adjustment":100
        }
       
        Response (200):
       {
            "data": {
                "id": 1,
                "name": "iphone 16e",
                "slug": "iphone-16e",
                "description": "iphone",
                "price": "4500.00",
                "stock_quantity": 1300,
                "images": [],
                "categories": [],
                "created_at": "2025-04-28T11:50:38+00:00"
            },
            "status": "success",
            "message": "Stock adjusted successfully"
        }

##### Get Inventory
        Method: GET
        URL: /api/v1/admin/inventory?per_page=2&low_stock_threshold=10
      
       
        Response (200):
       {
            "data": [
                {
                    "id": 1,
                    "name": "Samsung 1",
                    "slug": "samsung",
                    "stock_quantity": 0,
                    "price": "450000.00"
                },
                {
                    "id": 2,
                    "name": "Samsung2",
                    "slug": "samsung2",
                    "stock_quantity": 0,
                    "price": "450000.00"
                }
            ],
            "pagination": {
                "total": 9,
                "per_page": 2,
                "current_page": 1,
                "last_page": 5
            },
            "status": "success",
            "message": "Inventory retrieved successfully"
        }

## Order API Docs
##### Get Order History
        Method: GET
        URL: /api/v1/orders
       
        {
    "data": [
        {
                    "id": 1,
                    "user_id": 2,
                    "total_amount": "2250000.00",
                    "status": "pending",
                    "items": [
                        {
                            "id": 1,
                            "product_id": 2,
                            "product_name": "Samsung2",
                            "quantity": 5,
                            "price": "450000.00",
                            "subtotal": 2250000
                        }
                    ],
                    "created_at": "2025-04-28T10:19:20+00:00",
                    "updated_at": "2025-04-28T10:19:20+00:00"
                },
                {
                    "id": 2,
                    "user_id": 2,
                    "total_amount": "2250000.00",
                    "status": "pending",
                    "items": [
                        {
                            "id": 2,
                            "product_id": 2,
                            "product_name": "Samsung2",
                            "quantity": 5,
                            "price": "450000.00",
                            "subtotal": 2250000
                        }
                    ],
                    "created_at": "2025-04-28T10:20:23+00:00",
                    "updated_at": "2025-04-28T10:20:23+00:00"
                },
                {
                    "id": 3,
                    "user_id": 2,
                    "total_amount": "2250000.00",
                    "status": "pending",
                    "items": [
                        {
                            "id": 3,
                            "product_id": 1,
                            "product_name": "Samsung 1",
                            "quantity": 5,
                            "price": "450000.00",
                            "subtotal": 2250000
                        }
                    ],
                    "created_at": "2025-04-28T12:21:29+00:00",
                    "updated_at": "2025-04-28T12:21:29+00:00"
                },
                {
                    "id": 4,
                    "user_id": 2,
                    "total_amount": "2250000.00",
                    "status": "pending",
                    "items": [
                        {
                            "id": 4,
                            "product_id": 1,
                            "product_name": "Samsung 1",
                            "quantity": 5,
                            "price": "450000.00",
                            "subtotal": 2250000
                        }
                    ],
                    "created_at": "2025-04-28T12:22:42+00:00",
                    "updated_at": "2025-04-28T12:22:42+00:00"
                },
                {
                    "id": 5,
                    "user_id": 2,
                    "total_amount": "22500.00",
                    "status": "pending",
                    "items": [
                        {
                            "id": 5,
                            "product_id": 17,
                            "product_name": "iphone 16e",
                            "quantity": 5,
                            "price": "4500.00",
                            "subtotal": 22500
                        }
                    ],
                    "created_at": "2025-04-28T17:04:23+00:00",
                    "updated_at": "2025-04-28T17:04:23+00:00"
                }
            ],
            "status": "success",
            "message": "Order history retrieved successfully"
        }
##### Create Order
    Method: POST
    URL: /api/v1/orders
    Body (JSON):
        {
            "items": [
                {
                    "product_id": 17,
                    "quantity": 5
                }
            ]
        }
    Response (201):
    {
        "data": {
            "id": 5,
            "user_id": 2,
            "total_amount": 22500,
            "status": "pending",
            "items": [
                {
                    "id": 5,
                    "product_id": 17,
                    "product_name": "iphone 16e",
                    "quantity": 5,
                    "price": "4500.00",
                    "subtotal": 22500
                }
            ],
            "created_at": "2025-04-28T17:04:23+00:00",
            "updated_at": "2025-04-28T17:04:23+00:00"
        },
        "status": "success",
        "message": "Order created successfully"
    }
       
##### Get Single Order
    Method: POST
    URL: /api/v1/orders/2
   
    Response (200):
    {
    "data": {
        "id": 2,
        "user_id": 2,
        "total_amount": "2250000.00",
        "status": "pending",
        "items": [
            {
                "id": 2,
                "product_id": 2,
                "product_name": "Samsung2",
                "quantity": 5,
                "price": "450000.00",
                "subtotal": 2250000
            }
        ],
        "created_at": "2025-04-28T10:20:23+00:00",
        "updated_at": "2025-04-28T10:20:23+00:00"
    },
    "status": "success",
    "message": "Order details retrieved successfully"
}
       


