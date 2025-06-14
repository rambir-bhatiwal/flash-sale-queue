# Chapter Apps – Backend Assessment (Scenario 1: Flash Sale Crisis)

This project solves the **Flash Sale Crisis** scenario using a Redis-backed queue system to prevent overselling, support high concurrency, and provide fair access to limited stock products.

---

## 🚀 Technologies Used

- PHP 8.2 (Apache)
- MySQL 5.7
- Redis (alpine)
- Docker & Docker Compose
- PECL Redis extension
- Composer & Dotenv

---

## 📁 Folder Structure

    backend-assessment/
    ├── Dockerfile
    ├── docker-compose.yml
    ├── .env
    ├── setup.php
    ├── seed.php
    ├── reset_flash_sale.php
    ├── README.md
    ├── vendor/
    ├── src/
    │  └── Scenario1/
    │   ├── CartController.php
    │   ├── QueueProcessor.php
    │   └── redis.php
    └── tests/
        ├── CartControllerTest.php
        └── Scenario1FullTest.php




---

## ⚙️ Setup Instructions

### 1. Clone the Project

```bash
git clone <your-repo-url>
cd backend-assessment

```

### 2. Create .env File 

    DB_HOST=chapter_db
    DB_PORT=3306
    DB_NAME=test
    DB_USER=root
    DB_PASS=root

    REDIS_HOST=chapter_redis
    REDIS_PORT=6379



### 3. Build and Start Docker
    docker-compose up --build -d
### 4. Install Composer Dependencies
    docker exec -it chapter_app composer install

## 🗄️ Database Setup
    -- Run setup to create tables:
        docker exec -it chapter_app php /var/www/html/setup.php
    -- Insert test product: 
        docker exec -it chapter_app php /var/www/html/seed.php

##  🛒 Flash Sale Workflow

    -> Users are added to Redis queue flash_sale_queue:{productId}
    -> Stock is cached in flash_sale_stock:{productId}
    -> Cart is temporarily saved as cart_item:{userId}:{productId} with 5-minute TTL
    -> A queue processor picks valid users and finalizes orders in MySQL

## ✅ Testing
   # -> Full System Test command: 
    --> docker exec -it chapter_app php /var/www/html/tests/Scenario1FullTest.php
    ✅ Example Output

            ✅ Redis connected.
            ✅ Test product exists.
            ✅ Cart added to queue: User ID 6189, Queue Position: 1
            ✅ Redis cart TTL set: cart_item:6189:1
            ✅ Cart item successfully saved to database.


   # -> Reset Environment command:
    --> docker exec -it chapter_app php /var/www/html/reset_flash_sale.php

## 🔍 Redis Keys Used
    | Key Pattern               | Purpose                       |
    | ------------------------- | ----------------------------- |
    | `flash_sale_stock:{id}`   | Cached stock value            |
    | `flash_sale_queue:{id}`   | FIFO queue of user IDs        |
    | `cart_item:{userId}:{id}` | Temporary cart with 5-min TTL |

## 🐳 Useful Commands
    -> Access Redis CLI 
        docker exec -it chapter_redis redis-cli   
    -> Access MySQL CLI
        docker exec -it chapter_db mysql -u root -proot test


## 👨‍💻 Author
    -> Backend system implemented for the Chapter Apps Fullstack Engineer Assessment

### 🧑‍💻 Developer: Rambir


