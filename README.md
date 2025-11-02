# 🏍️ DMW Inventory Dashboard Website  
### *Tropang Maselan Motorshop*  
**Group 4 — BSDS Project**

## 👥 Members
- **Jeo-Criz Izzack E. Perdio**  
- **Rodrigo D. Salandanan**  
- **Rhommel M. Palermo**  
- **Kyla F. Mercado**

---
---

## 📖 Introduction
The **DMW Inventory Dashboard Website** is a web-based inventory and record management system built for **Tropang Maselan Motorshop**. It provides a secure and user-friendly platform to handle daily shop operations such as tracking inventory, recording sales, and monitoring staff activity.  

This project aims to improve efficiency by automating manual tasks and providing a real-time view of shop performance through an intuitive dashboard interface. The system includes role-based access for both **Admin** and **Cashier**, ensuring that each user can only access the features relevant to their position.

---

## ⚙️ Features

### 👥 User Roles
- **Admin**
  - Full system access and controls.
  - Manage cashier accounts.
  - View and edit all records, dashboards, and activity logs.
  - Access the account list and perform administrative actions.
  
- **Cashier**
  - Limited access to essential functions such as recording transactions, viewing the dashboard, and activity logs.

---

### 📋 Core Functionalities
- **🔐 Login System with Password Hashing**  
  Secure login using PHP’s `password_hash()` and `password_verify()` functions for encrypted authentication.

- **🏠 Home Dashboard**  
  Displays key information and metrics relevant to the user’s role.

- **🧭 Navigation by Role**  
  Dynamic navigation that adapts according to whether the user is an Admin or Cashier.

- **📑 Record Management**  
  Add, edit, delete, and view records — with automatic calculation of totals.

- **📊 Dashboard Overview**  
  Provides real-time insights into shop performance, stock levels, and sales summaries.

- **📜 Activity Log**  
  Tracks user actions for accountability and monitoring (accessible to both Admin and Cashier).

- **👥 Account List (Admin Only)**  
  Enables the Admin to manage all registered accounts within the system.

- **📘 Tutorial Page**  
  Offers user guidance and instructions on how to use the system effectively.

- **🚪 Logout Function**  
  Secure session handling and logout process to protect user data.

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3, JavaScript  
- **Backend:** PHP (with PDO for secure database access)  
- **Database:** MySQL  
- **Server Environment:** XAMPP / Apache  
- **Security:** Session management, password hashing, and cache control headers

---

## 📁 Project Setup
1. **Clone the repository:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/DMW_Inventory_Dashboard_Website.git
