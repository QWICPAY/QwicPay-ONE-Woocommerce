=== QwicPay ONE ===
Contributors: qwicpay, jbeira
Tags: payment gateway, QwicPay, Apple Pay, Samsung Pay, Card
Requires at least: 5.0
Requires PHP: 7.4
Requires Plugins: woocommerce
Tested up to: 6.8
Stable tag: 1.2.46
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html



Enable QwicPay ONE on your WooCommerce checkout with secure, compliant payments. Accept Apple Pay, Samsung Pay, cards, and stored cards.

== Description ==

This integration enables your WooCommerce store to offer **QwicPay ONE**, a modern checkout option with seamless redirection to a secure payment page.  
Accept Apple Pay, Samsung Pay, stored cards, and standard credit/debit cards using QwicPay’s PCI-DSS Level 1 vault.  
Merchants can use QwicPay's own TPPP or their own MID.

> ⚠️ **IMPORTANT:** A QwicPay merchant account is required. Sign up at [https://www.qwicpay.com](https://www.qwicpay.com).

**Key Features:**

- Adds the **QwicPay ONE** payment method to your WooCommerce checkout.
- Redirect-based secure payments using QwicPay's hosted payment page.
- Accepts Apple Pay, Samsung Pay, card payments, and stored QwicPay cards.
- Store cards in QwicPay's PCI-DSS Level 1 vault.
- Provides direct access to your merchant portal from WordPress.
- Fully configurable and easy to install.
- Supports block-based checkout (v1.2+).

== Installation ==

1. Download the plugin ZIP and upload it via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin from **Plugins → Installed Plugins**.
3. Navigate to **WooCommerce → Settings → Payments → QwicPay**.
4. Enter your QwicPay Merchant ID and Merchant Key, then **Save changes**.
5. Enable the payment method and confirm the status becomes `Active`.

**Note:**  
Make sure WordPress permalinks are set to a human-readable format.  
Go to **Settings → Permalinks** and select any option **other than "Plain"**.  
We recommend using **"Day and name"**.

== Frequently Asked Questions ==

= Do I need a QwicPay account? =  
Yes. You must register for a merchant account at [https://www.qwicpay.com](https://www.qwicpay.com).

= Is QwicPay PCI-compliant? =  
Yes, QwicPay is PCI-DSS Level 1 compliant. All card data is stored and processed securely.

= Can I accept Apple Pay and Samsung Pay? =  
Yes, provided your merchant account is configured to support those methods.

= Can I use my own MID instead of QwicPay’s processing? =  
Yes. QwicPay supports both third-party processing and your own acquiring account.



== Plugin Options ==

| Option        | Description                                  | Default    |
| ------------- | --------------------------------------------- | ---------- |
| Merchant ID   | Your QwicPay merchant identifier              | _(empty)_  |
| Merchant KEY  | Your QwicPay merchant API key                 | _(empty)_  |
| Stage         | Test or Production environment                | `Test`     |

== Changelog ==

= 1.2.40 =
* Block styling updates and payment logos.

= 1.2.0 =
* Introduced block checkout support.

= 1.1.8 =
* Added uptime monitoring and HMAC signature verification.

= 1.0.0 =
* Initial release: legacy method and basic settings.

== Upgrade Notice ==

= 1.2.40 =
Block support and visual updates added. Update recommended for newer WooCommerce versions.

== License ==

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Support ==

If you need help integrating or using QwicPay ONE:

* Email: [support@qwicpay.com](mailto:support@qwicpay.com)
* Website: [https://qwicpay.com](https://qwicpay.com)

---

Made with ❤️ by [QwicPay](https://qwicpay.com)
