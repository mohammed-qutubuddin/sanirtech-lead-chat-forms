=== SanirTech Lead Chat Forms ===
Contributors: abdulnasir1995
Tags: lead capture, form builder, spam protection, floating widget
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Securely capture highly qualified leads locally before routing them to instant messaging channels or processing via email.

== Description ==

SanirTech Lead Chat Forms is a high-performance, privacy-first conversion plugin designed for modern WordPress environments. Instead of sending anonymous users straight to your instant messaging channels, this plugin embeds clean, native form fields that capture user intent and contact information first.

Built strictly according to WordPress Core standards, the codebase is lightweight, secure against cross-site scripting (XSS), and utilizes proper asset enqueuing.

== External Services ==

This plugin relies on external third-party services to provide robust spam protection (CAPTCHA) mechanisms. These services are only requested if explicitly enabled in the plugin settings and only load on pages where the form shortcode is actively used.

1. **Google reCAPTCHA (v2 and v3)**
* **What it is:** A spam protection service provided by Google to identify malicious bot traffic.
* **Data sent:** The user's IP address and behavioral interaction data on the specific page are sent to Google's APIs (`https://www.google.com/recaptcha/api.js` and `https://www.google.com/recaptcha/api/siteverify`) to calculate a human-probability score.
* **Links:** [Google Terms of Service](https://policies.google.com/terms) | [Google Privacy Policy](https://policies.google.com/privacy)

2. **Cloudflare Turnstile**
* **What it is:** A privacy-first CAPTCHA alternative provided by Cloudflare.
* **Data sent:** The user's IP address and challenge token are sent to Cloudflare's APIs (`https://challenges.cloudflare.com/turnstile/v0/api.js` and `https://challenges.cloudflare.com/turnstile/v0/siteverify`) to verify human interaction without cross-site tracking.
* **Links:** [Cloudflare Terms of Service](https://www.cloudflare.com/website-terms/) | [Cloudflare Privacy Policy](https://www.cloudflare.com/privacypolicy/)

== Features ==

* **Secure Local Database Storage:** Capture and manage all leads locally inside custom, lightweight database tables before they are safely routed externally.
* **Granular Triple-Color Matrix:** Independently configure separate custom brand colors for the primary Direct Chat button, the Email dispatch fallback button, and the sitewide sticky floating widget.
* **Advanced Multi-Layer Antispam Engine:** Keep your inboxes clean by choosing between a stateless math challenge, Cloudflare Turnstile, Google reCAPTCHA v2 (Checkboxes), or Google reCAPTCHA v3 (Invisible Score Analysis).
* **Drag-and-Drop Form Repeater Builder:** Easily construct conversion-optimized forms containing text inputs, emails, numbers, and descriptive text areas.
* **Dynamic Lead Categorization:** Separate sales inquiries, customer support request items, and general leads with zero performance overhead using targeted category taxonomy.
* **Dual Routing Workflows:** Empower users to choose between instantly opening a direct interactive messaging app window or dispatching leads immediately via native wp_mail() routing.
* **Modern Native Dashboard UI:** Seamlessly manage forms and entries with a beautiful, strict WordPress-standard interface featuring an instant interactive shortcode copier and premium visual badges.
* **100% Privacy Compliant:** Zero tracking scripts, zero external content offloading blocks on static pages, and absolute localization of generated user logs.

== Installation ==

= A) Minimum Requirements =
* WordPress Version: 6.0 or higher.
* PHP Version: 7.4 or higher.
* Database: MySQL 5.6+ / MariaDB 10.1+.

= B) Recommended Requirements =
* PHP Version: 8.1+ or 8.2+ for optimal memory footprint.
* Object Caching: Redis or Memcached active.

= C) Installation Steps =
1. Navigate to your WordPress admin dashboard, click on **Plugins -> Add New**.
2. Click **Upload Plugin** at the top and select the `.zip` file of this plugin.
3. Activate the plugin.
4. Go to the **SanirTech Forms -> Settings** menu to configure your global target numbers, colors, and spam protection keys.
5. Navigate to **Add New** to assemble a custom form, copy the output shortcode e.g., `[whatsapp_form id='XX']`, and embed it on any page.

== Frequently Asked Questions ==

= Does this plugin offload assets or inject external scripts on every page? =
No. Scripts for Cloudflare Turnstile or Google reCAPTCHA are enqueued dynamically and conditionally. They will only load on pages where the shortcode is actively parsed by WordPress.

= Where are the captured lead logs saved? =
All submissions are safely stored inside your local WordPress database tables (`wp_stlcf_forms` and `wp_stlcf_entries`) with secure unslashing and sanitization.

= Can I use different brand colors for each action button? =
Yes! The visual customizer allows you to configure completely independent color themes for your direct chat form triggers, secondary email actions, and the sitewide sticky floating widget.

= Is it compatible with caching plugins and server-side object caching? =
Absolutely. The verification mechanisms use stateless cryptographic validation arrays which bypass caching conflicts smoothly.

== Screenshots ==

1. The global settings panel displaying database configurations, custom button colors, and dynamic captcha toggles.
2. The drag-and-drop form repeater builder for creating customized input fields.
3. Clean, responsive front-end form layout featuring side-by-side direct messaging and email fallback submission actions.
4. High-performance backend entries log displaying captured leads details with quick filter pipelines.

== Changelog ==

= 1.0.0 =
* Initial public release.
* Introduced a lightweight drag-and-drop form builder for capturing leads.
* Added secure local database storage for logging all form submissions.
* Implemented dual routing: Direct app redirect and Email fallback method.
* Added a customizable sitewide floating chat widget.
* Integrated multi-layer spam protection including Math Captcha, Cloudflare Turnstile, and Google reCAPTCHA v2.
* Built full shortcode support for seamless embedding across posts, pages, and widgets.