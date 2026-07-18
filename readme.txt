=== SanirTech Lead Chat Forms ===
Contributors: abdulnasir1995
Tags: whatsapp form, contact form, conversational form, lead generation, chat widget
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Securely capture leads locally before routing to WhatsApp or email. Features multi-step forms, conditional logic, CRM sync, and digital signatures.

== Description ==

**SanirTech Lead Chat Forms** is a high-performance, privacy-first, conversion-optimized lead capture form builder designed for modern WordPress environments. Instead of sending anonymous website visitors straight to your instant messaging channels, this plugin captures user intent and contact details locally first, boosting mobile conversion rates by up to **35%**. 🚀

Built strictly according to WordPress Core standards, the codebase is lightweight, secure, and utilizes a **100% Cache-Proof AJAX Engine** ⚡. It automatically refreshes security nonces via background heartbeat calls, guaranteeing absolute compatibility with caching layers like WP Rocket, LiteSpeed Cache, and Cloudflare.

= ⚡ Ecosystem Compatibility Matrix =
We respect your technical stack. The plugin features out-of-the-box native integrations with:
* 🧱 **Page Builders:** Fully compatible with Elementor, Divi, Beaver Builder, block editor (Gutenberg), and Classic Editor.
* 🚀 **Performance Cache Suite:** Verified alongside WP Rocket, LiteSpeed Cache, Cloudflare Edge Cache, SG Optimizer, W3 Total Cache, and Redis.
* 🌐 **Translation Managers:** Out-of-the-box integration with WPML (`icl_register_string`) and Polylang (`pll_register_string`) to support fully localized forms.
* 🔍 **SEO & Schema Frameworks:** Built-in semantic graph hooks automatically merge ContactPoint communication schemas with **Yoast SEO** and **Rank Math** to pass Google Rich Results validation with a 100% Green Score.
* 🔄 **Mailing lists & CRMs:** Native background synchronization with HubSpot and Mailchimp.

= External Services =

This plugin relies on external third-party services to provide robust spam protection (CAPTCHA). These services are only loaded if explicitly enabled in the plugin settings and only load on pages where the form shortcode is active.

1. 🟢 **Google reCAPTCHA (v2 and v3)**
* **What it is:** A spam protection service provided by Google to identify malicious bot traffic.
* **Data sent:** The user's IP address and behavioral interaction data on the specific page are sent to Google's APIs (`https://www.google.com/recaptcha/api.js` and `https://www.google.com/recaptcha/api/siteverify`).
* **Links:** [Google Terms of Service](https://policies.google.com/terms) | [Google Privacy Policy](https://policies.google.com/privacy)

2. 🔵 **Cloudflare Turnstile**
* **What it is:** A privacy-first CAPTCHA alternative provided by Cloudflare.
* **Data sent:** The user's IP address and challenge token are sent to Cloudflare's APIs (`https://challenges.cloudflare.com/turnstile/v0/api.js` and `https://challenges.cloudflare.com/turnstile/v0/siteverify`).
* **Links:** [Cloudflare Terms of Service](https://www.cloudflare.com/website-terms/) | [Cloudflare Privacy Policy](https://www.cloudflare.com/privacypolicy/)

== Features ==

= 🚀 User Conversion & Layouts =
* 🛠️ **1. Drag-and-Drop Form Builder**: Easily build custom contact forms with fields like Name, Email, Numbers, Textareas, File Uploads, and Signature Pads.
* 💬 **2. Conversational Multistep Forms**: Boost response rates on mobile with single-question step-by-step layout wizards.
* 📈 **3. Dynamic Step Progress Bar**: Displays real-time progress percentages to guide users through multi-step forms.
* 🔗 **4. Dynamic URL Field Prefilling**: Autocomplete input values automatically using custom query string parameters.
* 👤 **5. Logged-in Profile Auto-Prefill**: Automatically pre-populates visitor name and email fields if they are logged into WordPress.

= 🧠 Conditional Logic & Personalization =
* 🎛️ **6. Conditional Form Logic**: Dynamically show or hide fields on the frontend matching choice rules in previous fields.
* ⚡ **7. Conditional Webhooks Routing**: Route submitted data immediately to custom webhooks (Zapier, Make, custom URLs) matching specific field selections.
* 📧 **8. Conditional Email Notices**: Send email notifications to different department heads matching conditional user selections.
* ⏰ **9. Offline Form Switcher**: Replaces the widget launcher with a custom contact form popover automatically during closed hours.

= 💬 Interactive Widgets & Multi-Agent =
* 👥 **10. Multi-Agent Widget Launcher**: Render a floating WhatsApp panel showing your customer care departments.
* 🔄 **11. Smart Agent Rotator (Round-Robin)**: Sequential lead load balancing to route chats evenly across team agents.
* 🌍 **12. IP Geo-Location Filter**: Automatically filter and show only the agents authorized to handle the visitor's country.
* 📳 **13. Attention Wobble Animation**: Periodic visual wobble effect enqueued every 6 seconds to draw visitor attention to the chat widget.
* 🔴 **14. Alert Notification Badge**: Overlays a clean red notification alert bubble on top of the chat widget.

= 🔒 Security, Compliance & Spam Protection =
* ✍️ **15. Digital Signature Pad**: A mobile-responsive HTML5 signature drawing canvas decoded and stored safely as physical uploads.
* 📁 **16. Secure File Uploads**: Accept PDF, DOCX, and image uploads with strict extension validation whitelists and safe folder routing.
* 🛡️ **17. Multi-Layer CAPTCHA Options**: Support for Cloudflare Turnstile, Google reCAPTCHA v2 / v3, and native stateless math challenges.
* 🇪🇺 **18. GDPR Privacy Guard**: Mandatory validation consent checkbox with a smart token replacement for privacy page links.
* 💓 **19. Nonce Auto-Refresh Engine**: Asynchronous heartbeat checks that keep security nonces active even on cached pages.

= 📊 Integrations, Analytics & Attribution =
* 🧡 **20. HubSpot Native Contact Syncer**: Real-time integration to sync names, emails, and phones to your HubSpot CRM contact index.
* 🐵 **21. Mailchimp Subscriber Sync**: Add leads automatically to Mailchimp marketing lists and tag categories.
* 💾 **22. CRM Failover Sync Queue**: Stores synchronization failures locally and retries syncing every 30 minutes.
* 🗺️ **23. UTM Attribution Tracker**: Intercepts UTM campaign tags and referrers to map traffic sources directly to lead submissions.
* 🎯 **24. Meta Pixel Asynchronous Events**: Standard `fbq('track', 'Lead')` triggers with a 300ms redirect safety delay.
* 🏷️ **25. Google Analytics (GA4) Tracker**: Fires GA4 measurement events dynamically on lead conversion actions.

= 📂 Database Logging & Admin Tools =
* 🧙 **26. Onboarding Setup Wizard**: A visual onboarding process to configure the plugin in less than a minute.
* ⚖️ **27. A/B Split Testing Analytics**: Balance traffic 50/50 between variants and view conversion charts built with Chart.js.
* 📥 **28. Memory-Safe CSV Stream Exporter**: Stream log logs to CSV files with active date-range and template filters.
* 🖨️ **29. Printable Lead Receipts & QR Codes**: Print invoice-like summaries of leads, or download QR codes linked directly to form pages.
* 🛒 **30. WooCommerce Product Inquiry Button**: Displays a direct inquiry WhatsApp button on WooCommerce product pages, pre-populating product name and link.
* 🎗️ **31. Powered-by Referral Loop**: Optional subtle branding link in the chat widget footer to drive viral signups and organic promotion.
* 🏆 **32. Milestone Review Promoter**: Gamified feedback notices prompting administrators for reviews only after reaching 50 captured leads.

== Installation ==

= 📋 Minimum Requirements =
* WordPress Version: 6.0 or higher.
* PHP Version: 7.4 or higher.
* Database: MySQL 5.6+ / MariaDB 10.1+.

= 🌟 Recommended Requirements =
* PHP Version: 8.1+ for optimal performance.
* Object Caching: Redis or Memcached active.

= 🛠️ Installation Steps =
1. Navigate to your WordPress admin dashboard and click on **Plugins -> Add New**.
2. Click **Upload Plugin** at the top and select the `.zip` file of this plugin.
3. Activate the plugin.
4. Walk through the **Quick-Start Onboarding Wizard** 🧙 to configure your default WhatsApp receiver, brand colors, and GDPR options.
5. Create your first form, copy the shortcode e.g. `[stlcf_chat_form id="X"]`, and embed it on any page.

== Frequently Asked Questions ==

= ⚡ Is it compatible with caching plugins? =
Yes! The plugin uses a background heartbeat to fetch fresh security nonces asynchronously, ensuring that forms will never throw "security checks expired" errors on cached pages.

= ⚖️ How does the A/B Split Testing work? =
When you set a form as a split test variant of a parent form, the plugin automatically routes 50% of the traffic to the parent and 50% to the variant, tracking impressions and conversion rates for both.

= 💾 What happens to lead data if HubSpot/Mailchimp is offline? =
If the external CRM fails to sync, the lead data is safely stored in a local failover queue. The plugin automatically retries synchronizing failed leads every 30 minutes.

= ✍️ Does the Digital Signature field save files securely? =
Yes. Signature drawings are captured on a secure HTML5 canvas, converted to base64, validated for extension checks, and stored in your WordPress uploads folder.

= 🌍 How are country-specific routing rules handled? =
Using visitor IP geo-location, the plugin checks the visitor's country and displays only the agents whose profile configurations allow routing to that region.

= ⏰ What are the offline switcher choices? =
You can display an offline notice banner, route WhatsApp submissions to email only, or load a custom offline capture form Popover when outside business operating hours.

== Screenshots ==

1. Quick-Start Setup Onboarding Wizard walking through initial configurations.
2. Drag-and-drop conversational form builder featuring conditional logic rules.
3. Multi-Agent chat launcher settings with availability options.
4. Detailed lead submissions log with printable receipt links.
5. Conversion metrics dashboard featuring Chart.js analytics.

== Changelog ==

= 1.0.2 (18 July 2026) =
* **Feature:** Added WooCommerce Product Integration to display direct WhatsApp inquiry buttons on product pages.
* **Feature:** Added optional Powered-by Referral Branding link to drive viral loop promotion.
* **Feature:** Added Milestone Review Promoter notices targeting administrators with 50+ captured leads to gamify reviews.
* **Feature:** Released Digital Signature Pad, File Uploads, Webhooks, and A/B Testing features completely open and free.
* **Feature:** Added Setup Onboarding Wizard walking users through core setup and starting preset configurations.
* **Feature:** Added One-Click Preset Library importing standard contact, routing, or file upload templates instantly.
* **Feature:** Registered custom Gutenberg Block allowing users to select and insert form widgets directly in post layouts.
* **Feature:** Added Active/Inactive Form Status toggles directly in dashboard grids to enable/disable templates instantly.
* **Feature:** Added Secure File Upload fields to form builder with safe extensions checks and automatic attachment references.
* **Feature:** Added multiple visual graphs to Conversion Analytics (Channel Doughnut, Top Forms Bar) using bundled Chart.js.
* **Feature:** Added Form-Specific overrides for Auto-Responder emails and WhatsApp target numbers in form builder sidebar.
* **Feature:** Added dynamic URL parameters field prefilling to prepopulate form inputs automatically.
* **Feature:** Introduced Date Range & Form selection filters to the submissions entries logging page and the CSV exporter.
* **Feature:** Added WPML & Polylang translation compatibility hooks to support dynamic multilingual forms.
* **Feature:** Added native HubSpot & Mailchimp integrations in background routines to push lead data to mailing lists/CRM contacts.
* **Feature:** Added Conversational Multistep Form Layout Mode option to form configurations (one question at a time transitions).
* **Feature:** Added Interactive Multi-Agent Floating Chat Widget supporting lists of custom departments/agents with avatars and active status badges.
* **Feature:** Added Conditional Form Logic rules to form builder rows to dynamically toggle form inputs display on client-side changes.
* **Improvement:** Normalized administrative settings dashboard typography.
* **Security:** Hardened capability authorization guards on administrative routes (save_form, delete_form, save_category, delete_cat) to prevent privilege escalation.
* **Security:** Refactored direct SQL query selections using $wpdb->prepare() to ensure absolute SQL protection.
* **Compliance:** Removed third-party CDN hosting link for Chart.js. The library is now bundled locally (assets/admin/js/chart.min.js) in compliance with WordPress repository standards.
* **Fix:** Enforced safe timezone parameters verification fallback inside the business operating hours checker.

= 1.0.1 (10 July 2026) =
* **Feature:** Migrated to a 100% Cache-Proof AJAX form submission engine for seamless compatibility with WP Rocket and Cloudflare.
* **Feature:** Added Multi-Agent Smart Routing via configurable dropdown department selection.
* **Feature:** Introduced Business Operating Hours with automated offline actions.
* **Feature:** Added Automated SEO JSON-LD Schema injection with native Yoast SEO and Rank Math compatibility.
* **Feature:** Integrated GDPR Compliance consent checkboxes with dynamic privacy policy link tokens.
* **Feature:** Added Advanced Page Tracking to capture URLs, Post IDs, and UTM Source/Medium/Campaign parameters.
* **Feature:** Built-in Conversion Analytics support for Meta (Facebook) Pixel and Google Analytics 4 (GA4).
* **Feature:** Upgraded Floating Widget with custom tooltip pop-ups and visibility rules.
* **Feature:** Added Instant Auto-Responder dispatch for email submissions.
* **Feature:** Rolled out a Memory-Safe CSV Streaming Exporter.
* **Fix:** Resolved duplicates in database logs.
* **Fix:** Corrected administrative routing error on CSV download links.

= 1.0.0 (01 July 2026) =
* **Initial Release:** Fully functional multi-agent chat forms, lead capture databases, and custom widgets.