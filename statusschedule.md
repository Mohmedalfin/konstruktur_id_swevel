# PRD: Dashboard Project Status Analytics (SPI & CPI)

## 1. Overview

This document defines the logic for calculating Project Health indicators using Earned Value Management (EVM) principles:
- **SPI (Schedule Performance Index):** Efficiency of time/schedule.
- **CPI (Cost Performance Index):** Efficiency of cost/resources.

## 2. Calculation Formulas

### A. Schedule Performance Index (SPI)
SPI = **EV / PV**
- **EV (Earned Value):** Cumulative actual progress (Actual % * Total Budget).
- **PV (Planned Value):** Cumulative planned progress (Planned % * Total Budget) as of today.

### B. Cost Performance Index (CPI)
CPI = **EV / AC**
- **EV (Earned Value):** Cumulative actual progress (Actual % * Total Budget).
- **AC (Actual Cost):** Total real expenditure (Sum of Realisasi SDM, Bahan, Alat).

## 3. Status Classification

### SPI (Schedule)
| SPI Value   | Status         | Color   | Meaning              |
| ----------- | -------------- | ------- | -------------------- |
| > 1.05      | Early          | Emerald | Ahead of schedule    |
| 0.95 - 1.05 | On Time        | Slate   | On schedule          |
| 0.85 - 0.94 | Slightly Delay | Amber   | Minor delay detected |
| < 0.85      | Delayed        | Rose    | Significant delay    |

### CPI (Cost)
| CPI Value   | Status         | Color   | Meaning              |
| ----------- | -------------- | ------- | -------------------- |
| > 1.05      | Under Budget   | Emerald | More efficient       |
| 0.95 - 1.05 | On Budget      | Slate   | According to plan    |
| 0.85 - 0.94 | Slightly Over  | Amber   | Slightly overbudget  |
| < 0.85      | Overrun        | Rose    | Critical overbudget  |

## 4. Category-Level Analytics (Modal Detail)

When viewing a specific category, the formulas remain the same but are **filtered by Category ID**:
- **Cat EV:** Sum of (Item Weight * Actual %) for all items in category.
- **Cat PV:** Sum of (Item Weight * Planned %) for all items in category.
- **Cat AC:** Sum of real expenditures linked to items in category.
- **Cat SPI/CPI:** Calculated using the filtered values above.

## 5. Data Source Mapping

| Component | Database Tables |
| :--- | :--- |
| **Weights & Info** | `proyek_item`, `proyek_category` |
| **Actual Progress** | `realisasi_pekerjaan_item` (sum quantity / volume) |
| **Planned Progress** | `schedule_item` (interpolation based on start/end dates) |
| **Actual Cost (AC)** | `realisasi_sdm_item`, `realisasi_bahan_item`, `realisasi_alat_item` |
| **Total Budget (BAC)** | `ahs_item` / RAP calculation |

## 8. Implementation Phases (Phase 1 Focus)

### Backend Logic Implementation
- **Service Layer**: Implement logic in `DashboardService.php`.
    - `getProjectMetrics()`: Calculate SPI, CPI, EV, PV, and AC for the whole project.
    - `getCategoryMetrics($categoryId)`: Calculate same metrics filtered by category.
- **Controller Layer**: Update `DashboardController.php`.
    - Ensure `getData()` response includes the new project metrics.
    - Create new endpoint `getCategoryDetail($categoryId)` for the modal.
- **Technical Goal**: API must return accurate SPI & CPI values in the `overview` and `category_detail` objects.
