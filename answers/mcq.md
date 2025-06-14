# Part A: Multiple Choice Questions (30 points) - with justification 

## Question 1. Database Performance

👉  **Answer:** B - Implement full-text search indexing.

💡  **Justification:** Use a full-text index, which breaks down the text into individual words and creates an inverted index (much like a search engine). It's designed for this type of word search.

---

## Question 2. Security Vulnerability (3 points)...
👉  **Answer:** C - SQL injection, weak hashing, and session fixation.

💡  **Justification:** ->SQL Injection: The query uses raw user input without proper sanitization or prepared statements, making it vulnerable to SQL injection attacks.

->Weak Hashing: Passwords are compared directly in the query, which is insecure. Passwords should be hashed using password_hash() and verified with password_verify() for proper security.

-> Session Fixation: The session ID is not regenerated after login. Without session_regenerate_id(true), an attacker can hijack the session using a known or fixed session ID.

---

## Question 3. API Rate Limiting
👉  **Answer:** C -  Combination of IP (100/min) and API key (5000/hour) with burst allowance.

💡  **Justification:** Combining IP and API key limits means:

    -> IP + API key limit = prevents both single-user abuse and large bot attacks.

    -> Burst allowance = real users can send faster requests without being blocked.

    -> The best mix of security and smooth experience.

---

## Question 4. Caching Strategy
👉  **Answer:** C -  Cache category-product mappings and product data separately with different TTLs.

💡  **Justification:** Category-product mapping (e.g. which products belong to a category) doesn't change often - cache it for a long time. Product data (e.g. price, stock) changes often - cache it for a short time. This approach keeps pages fast and data fresh.

---

## Question 5. Payment Processing
👉  **Answer:** D - Both B and C 

💡  **Justification:** -> B: Idempotent logic = handles the same webhook multiple times in a safe way.
    -> C: Async Email = ensures the email continues to be sent even if something fails.

---

## Question 6. Scalability Architecture
👉  **Answer:** C - Add Redis cache and implement database read replicas

💡  **Justification:** -> Redis cache = reduces database load by storing frequent data in memory.
    -> Read replicas = let you handle many read requests at once.
---

## Question 7. Inventory Management
👉  **Answer:** B -  Use database transactions with row-level locking.

💡  **Justification:** Row-level locking ensures that only one checkout at a time can update product stock. This is the safest way to prevent overselling during high traffic.
---

## Question 8. Search Implementation
👉  **Answer:** C - Use Elasticsearch with analyzers and tokenization.

💡  **Justification:** Elasticsearch breaks search text and product names into pieces (tokens) and understands different word forms. So it can match “iPhone 15 pro max 256gb” with “Apple iPhone 15 Pro Max - 256GB”, even if the order or wording is different.

---

## Question 9. Session Management
👉  **Answer:** D - All of the above should be implemented

💡  **Justification:** -> Session timeouts: If sessions expire too quickly, users are logged out. Fix this by increasing session lifetime/expire time.
    -> File locking: PHP's default file-based sessions can block under high load. Redis handles sessions quickly and without file lock issues.
    -> Load balancer: Without sticky sessions, user requests can go to different servers with different sessions, causing logouts.

---

## Question 10. Data Integrity
👉  **Answer:** D - Validation at all layers - frontend, backend, and database constraints.

💡  **Justification:** -> Frontend: Catches mistakes early (e.g. user tries to enter -1).
    -> Backend: Stops bad or fake data from APIs or scripts.
    -> Database: Last safety net to block invalid data like negative quantities.

---
