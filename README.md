# Ski Selector - Personalized Equipment Matching
![PrestaShop](https://img.shields.io/badge/PrestaShop-8-blue)

## Overview

**Ski Selector** is a specialized PrestaShop module developed for the winter sports industry (specifically for **nartyok.pl**). It acts as a digital consultant, guiding users through a step-by-step wizard to find the perfect skis based on their physical profile, skill level, and preferred skiing style.

## The Problem Solved

Choosing skis is complex. Factors like height, weight, and "Skiing Level" (Beginner to Expert) determine the required ski length and stiffness. This module automates that expert advice, increasing conversion rates by reducing "buyer's doubt."

---

## Key Features

* **Step-by-Step Wizard:** A user-friendly UI that collects:
* Gender/Age Group (Men, Women, Kids).
* Height & Weight.
* Skill Level (1-10 scale).
* Preferred Terrain (On-piste, All-mountain, Powder).


* **Dynamic Filtering:** Instead of a static list, it queries the PrestaShop database to find products that match the calculated "Ideal Length" range.
* **Nartyok.pl Integration:** Specifically tuned to the category IDs and features used in your Polish ski shop implementation.

---

## Installation

1. Upload the `skiselector` folder to `/modules/`.
2. Install via the **Module Manager**.


## Technical Architecture & Logic

### 1. The Matching Engine

The module uses a custom algorithm to calculate the recommended ski length:

* **Base:** $User Height - X\text{ cm}$.
* **Adjustment:** The value of $X$ shifts based on the **Skill Level**.
* *Beginners:* Shorter skis for easier turning ($-15\text{ cm}$ to $-10\text{ cm}$).
* *Experts:* Longer skis for stability at speed ($0\text{ cm}$ to $+5\text{ cm}$).



### 2. Database Integration

The module performs an optimized `INNER JOIN` on `ps_product`, `ps_product_attribute`, and `ps_feature_value` to find items that are:

1. In stock.
2. Within $\pm 2\text{ cm}$ of the calculated ideal length.

### 3. Front-End (AJAX)

To ensure a smooth "Single Page Application" feel, the wizard uses **AJAX (jQuery)** to move between steps without reloading the page, eventually rendering the results template (`results.tpl`).

---

## Portability Note (For Reuse)

While built for **nartyok.pl**, this module can be adapted for any ski shop by:

1. Updating the `category_id` constants in the main class.
2. Modifying the `calcLength()` method if a different sizing philosophy is required.
3. Translating the Polish strings (currently supports PL/EN).

<img width="1182" height="724" alt="image" src="https://github.com/user-attachments/assets/e6b37e91-ef0b-4730-b560-2c2a2432dbe5" />

<img width="796" height="777" alt="image" src="https://github.com/user-attachments/assets/5f4791fb-eeb4-4cbf-b545-8970ae3e6c61" />

---
