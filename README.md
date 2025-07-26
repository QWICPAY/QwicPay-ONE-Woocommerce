# QwicPay Checkout for WooCommerce

![QwicPay Logo](https://qwicpay.com/assets/QwicPayLogo.png)

This integration enables your WooCommerce store to offer **QwicPay ONE**, providing a faster, seamless payment experience.

> ⚠️ **IMPORTANT:** You will need a QwicPay merchant account. You can sign up at [https://www.qwicpay.com](https://www.qwicpay.com).

---

## 🚀 Features

- Adds the **QwicPay ONE** payment option.
- Redirects customers to QwicPay’s secure payment page.
- Fully configurable and easy to install.
- Direct Merchant portal access from WordPress
- Store user cards in a PCI-DSS level 1 vault.
- Accept Apple Pay, Samsung Pay, Card and QwicPay's stored cards.
- Use QwicPay's TPPP or use your own MID

---

## Installation

1. Download the ZIP and upload via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin in **Plugins → Installed Plugins**.
3. Go to **Payments → QwicPay → Manage** to enter your Merchant ID, Key and other options. **Save**
4. Activate the payment method and ensure status becomes `Active`

---

## **Permalinks:**

WordPress permalinks must be set to a human-readable format.  
 Go to `Settings > Permalinks` and choose any option **other than "Plain"**.  
 The **"Day and name"** structure is a great default and works well with QwicPay.


## Plugin Options

| Option        | Description                                        | Default                                     |
| ------------- | -------------------------------------------------- | ------------------------------------------- |
| Merchant ID   | Your QwicPay merchant identifier                   | _(empty — must be set)_                     |
| Merchant KEY  | Your QwicPay merchant API KEY                      | _(empty — must be set)_                     |
| Stage         | Test or Production environment                     | `Test`                                      |


## 📄 License & Legal

This code is the property of **QwicPay Pty Ltd**.  
Copying, modifying, or using this code without express permission is strictly prohibited.

---

## 💬 Support

If you have any questions or require assistance with integration:

📧 Email: `support@qwicpay.com`  
🌐 Website: `https://qwicpay.com`

---

## Changelog

### 1.2.40

- Block styling updates and payment logos

### 1.2.0

- Introduced block support

### 1.1.8

- Uptime monitoring & HMAC verification

### 1.0.0

- Initial release: settings tab, legacy method.


---

Made with Care by [QwicPay](https://qwicpay.com)
