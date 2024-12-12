---

### **SQL 查詢**

```sql
SELECT
    o.bnb_id,
    bnb.name AS bnb_name,
    SUM(o.amount) AS may_amount
FROM
    orders o
        JOIN
    bnbs bnb ON o.bnb_id = bnb.id
WHERE
    o.currency = 'TWD'
  AND o.check_in_date BETWEEN '2023-05-01' AND '2023-05-31'
  AND o.created_at BETWEEN '2023-05-01 00:00:00' AND '2023-05-31 23:59:59'
GROUP BY
    o.bnb_id, bnb.name
ORDER BY
    may_amount DESC
LIMIT 10;
```

---

### **SQL 查詢優化建議**

#### 1. **分析執行計劃 (EXPLAIN)**
使用 `EXPLAIN` 查看 SQL 查詢的執行計劃，識別性能瓶頸。

#### 2. **性能瓶頸與優化方法**

##### a. **索引優化**
- 在過濾條件上（如 `currency`, `check_in_date`, `created_at`, `bnb_id`）添加索引，避免全表掃描。

##### b. **避免使用函數**
- 使用 `MONTH()` 和 `YEAR()` 等函數會使索引無效，應避免在查詢中使用。

##### c. **分批查詢**
- 大資料量時，分批查詢可減少每次查詢的負擔，避免一次性查詢過多資料(例如限縮時間區間的資料)。

#### 3. **快照表 (Scheduled Snapshot)**
- 定期將資料快照寫入另一張表，減少即時查詢時的計算，適用於資料變動不頻繁的情境。

#### 4. **MySQL 分區表 (Partitioning)**
- 根據 `created_at` 或 `bnb_id` 等字段設置分區，提升查詢性能。

#### 5. **使用 Elasticsearch**
- 將資料同步到 Elasticsearch，進行快速搜尋，特別適用於高頻率、低延遲的查詢需求。

#### 6. **冷熱庫處理**
- 將冷資料移至低成本存儲（如 AWS Glacier），減少熱庫壓力。

#### 7. **讀寫分離 (Read/Write Separation)**
- 將讀操作和寫操作分開，減少單一資料庫的負擔，提升系統可擴展性。

#### **短中期建議**
- **短期優化**：優化查詢索引、避免函數、分批查詢。
- **中期優化**：實現快照表和讀寫分離。
- **長期優化**：考慮使用 Elasticsearch、MySQL Partitioning 和冷熱資料存儲策略。

---

### **程式目錄架構**

```plaintext
app/
├── Enums/
│   └── Currency.php
├── Events/
│   └── OrderCreated.php
├── Listeners/
│   └── OrderCreateProcessed.php
├── Models/
│   └── OrderMYR.php
├── Repositories/
│   ├── OrderRepositoryFactory.php
│   ├── OrderRepositoryInterface.php
│   ├── OrderMYRRepository.php
│   ├── OrderTWDRepository.php
│   ├── OrderUsdRepository.php
│   ├── OrderJpyRepository.php
│   ├── OrderRmbRepository.php
│   └── OrderMyrRepository.php
├── Services/
│   └── OrderService.php
└── Http/
    └── Controllers/
        └── OrderController.php
tests/
├── Feature/
│   └── OrderControllerTest.php
├── Unit/
│   └── OrderServiceTest.php
database/
├── factories/
│   ├── OrderJPYFactory.php
│   ├── OrderRMBFactory.php
│   ├── OrderUSDFactory.php
│   ├── OrderTWDFactory.php
│   └── OrderMYRFactory.php
```

---

### **SOLID 原則**

#### **1. 單一職責原則 (SRP)**
每個類別或模組應只負責一件事情。

- **實現方式**：
    - `Currency`: 管理和統一處理貨幣相關的值。
    - `OrderCreated` (Event): 封裝訂單事件資料。
    - `OrderCreateProcessed` (Listener): 處理 `OrderCreated` 事件的邏輯。
    - `OrderRepositoryInterface` 和具體 Repository：處理特定貨幣的資料存取。
    - `OrderService`: 訂單處理邏輯，將資料存取委派給相應的 Repository。
    - `OrderRepositoryFactory`: 根據貨幣選擇正確的 Repository。

#### **2. 開放封閉原則 (OCP)**
類別應對擴展開放，對修改封閉。

- **實現方式**：
    - 新增貨幣時，僅需在 `Currency` Enum 和對應的 Repository 中擴展即可，無需修改現有邏輯。
    - 使用工廠模式 (`OrderRepositoryFactory`) 來動態選擇 Repository，易於擴展。

#### **3. 里氏替換原則 (LSP)**
子類別或實例應能替換其基類別，而不改變程式行為。

- **實現方式**：
    - 所有具體 Repository 實現 `OrderRepositoryInterface`，並可在工廠模式中無縫替換。
    - `OrderService` 無需關注具體的 Repository 實現，只依賴統一接口。

#### **4. 介面隔離原則 (ISP)**
為特定需求定義小型且專用的介面。

- **實現方式**：
    - `OrderRepositoryInterface` 只包含 `store` 和 `show` 方法，避免引入無關方法。
    - 每個 Repository 僅負責其定義的功能，避免不必要的操作。

#### **5. 依賴反轉原則 (DIP)**
高層模組不應依賴低層模組，兩者都應依賴抽象。

- **實現方式**：
    - `OrderService` 通過注入 `OrderRepositoryFactory`，依賴抽象工廠模式。
    - `OrderController` 依賴 `OrderService`，專注於高層業務邏輯。

---

### **線上通訊服務設計概述**

#### 目標：
提供高可用、可擴展的線上通訊服務，支援即時訊息處理、WebSocket 連接、消息隊列和動態自動擴展。

#### **技術架構**

- **PHP**：後端業務邏輯與資料庫操作。
- **Node.js**：處理 WebSocket 即時通訊，前端訊息推送。
- **Golang**：高效微服務處理，適合高併發訊息排程與處理。
- **RabbitMQ**：消息隊列，異步處理訊息，確保高效可靠的傳遞。
- **WebSocket**：即時雙向通訊，用戶之間的實時訊息。
- **Redis**：快速存取狀態數據，提升系統性能。

#### **監控與自動擴展**

- **Prometheus + Grafana**：實時監控應用與基礎設施性能。
- **AlertManager**：當指標超過預設值時發送告警。
- **Kubernetes HPA/VPA/Cluster Autoscaling**：根據負載自動調整容器數量與資源配置。

#### **資料庫與微服務溝通**

1. **資料庫設計**
    - **NoSQL (Redis/MongoDB)**：儲存即時訊息等高頻資料。
    - **MySQL**：儲存用戶資料和結構化的聊天歷史。

2. **微服務間通訊**
    - **RESTFul API**：適用於輕量級資料交互。
    - **gRPC**：適合高效、低延遲的服務間通訊。

3. **架構設計**
    - 使用 **MySQL** 存儲用戶和群組數據，**NoSQL** 處理即時訊息。
    - 服務間通訊使用 **RESTFul API** 和 **gRPC**，根據需求選擇協議。

---
# homework
