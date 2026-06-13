=== SanirTech Lead Chat Forms ===
Contributors: abdulnasir1995
Tags: whatsapp, contact form, lead generation, chat widget, click to chat
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Securely capture highly qualified leads locally before routing them to instant messaging channels or processing via email. 100% Cache-Proof and SEO optimized.

== Description ==

SanirTech Lead Chat Forms is a high-performance, privacy-first conversion plugin designed for modern WordPress environments. Instead of sending anonymous users straight to your instant messaging channels, this plugin embeds clean, native form fields that capture user intent and contact information first.

Built strictly according to WordPress Core standards, the codebase is lightweight, secure against cross-site scripting (XSS), and utilizes proper asset enqueuing with a **100% Cache-Proof AJAX Engine**. Say goodbye to "Security check failed" errors; our background heartbeat safely refreshes security nonces, ensuring absolute compatibility with caching layers like WP Rocket, LiteSpeed, and Cloudflare.

= ⚡ Ecosystem Compatibility Matrix =
We respect your existing technical stack. The plugin features out-of-the-box native integrations with:
*   **Performance Cache Suite:** Fully verified and battle-tested alongside WP Rocket, LiteSpeed Cache, Cloudflare Edge Cache, SG Optimizer, W3 Total Cache, and Redis object caching arrays.
*   **SEO & Structured Data Managers:** Built-in semantic graph hooks automatically merge ContactPoint communication schemas securely with **Yoast SEO** and **Rank Math** to eliminate validation duplication issues on search engine bots.

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

== Detailed Core Feature Breakdown ==

= 1. Intuitive Drag-and-Drop Form Builder =
Easily construct conversion-optimized forms containing text inputs, emails, numbers, and descriptive text areas. Generate a clean shortcode (e.g., `[stlcf_chat_form id="X"]`) and embed it on any page, post, or widget area. 

= 2. 100% Cache-Proof AJAX Engine =
Traditional forms break when static HTML snapshots bypass PHP lifecycle states. Our engine utilizes a background async handler that silently checks, fetches, and replaces expired cryptographic nonces the millisecond a page loads. Forms remain fully active 24/7 without requiring manual cache purges.

= 3. Advanced Multi-Layer Antispam Engine =
Keep your inboxes clean by choosing your preferred line of defense. The plugin natively integrates with Cloudflare Turnstile, Google reCAPTCHA v2 (Checkboxes), Google reCAPTCHA v3 (Invisible Score Analysis), or a lightweight, stateless native math challenge.

= 4. Multi-Agent & Department Smart Routing =
Enforce automated sales pipeline segmentations. Use our flexible field layout systems to define specific contact targets mapped to custom department tags or regional phone numbers (e.g., `Sales Department|919999999999`). 

= 5. Dual Routing Fallback Workflows =
Empower users to choose between instantly opening a direct interactive WhatsApp messaging window or dispatching leads immediately via a native `wp_mail()` server-side routing fallback if they prefer email.

= 6. Advanced Floating Widget Customization =
Deploy a sitewide sticky floating chat bubble complete with custom tooltips ("Chat with us!"), prefilled URL WhatsApp messages, custom widget positioning (left/right), and strict page-specific visibility rules (Sitewide, Homepage only, or Singular posts only).

= 7. Business Operating Hours & Offline Mode =
Map your active working days and timezone. Choose your automated offline enforcement action: Display a customized warning banner, completely hide the sitewide widget, or automatically disable the WhatsApp gateway to force secondary email submissions when you are out of office.

= 8. Automated SEO Schema Injector =
Contextually injects valid JSON-LD `ContactPoint` schema graphs. Instead of throwing loose blocks, we intercept official graph generation arrays—using filters like `wpseo_schema_graph_pieces` for Yoast and `rank_math/json_ld` for Rank Math—merging data maps flawlessly to pass the Google Rich Results Validation with a 100% Green Score.

= 9. GDPR Privacy Compliance Guard =
Maintain absolute international data compliance parameters. One-click toggle adds a mandatory validation checkbox layout directly inside your active form canvas. Includes a smart content parsing token `[privacy_link]` to dynamically render standard legal hyperlink tags.

= 10. Dynamic Lead Tracking & Analytics Pixels =
Capture UTM parameters (Source/Medium/Campaign) alongside the specific Page Title and URL context. Optimize your paid ads by firing asynchronous tracking hooks (Meta `fbq` and GA4 `generate_lead`) with a custom 300ms micro-delay to ensure pixel delivery before WhatsApp redirection occurs.

= 11. Instant Email Auto-Responder =
Build immediate consumer trust. When leads are generated via email, automatically dispatch a personalized confirmation blueprint. Customize your messaging template with high-fidelity dynamic placeholders like `[Your Name]` and `[Form Title]`.

= 12. Local Database & Memory-Safe CSV Export =
Capture and manage all leads locally inside custom, lightweight database tables (`wp_stlcf_entries`). Download hundreds of thousands of leads via an optimized stream buffer (`php://output`) without hitting server memory exhaustion limits. Customize the look with a granular Triple-Color Matrix for buttons and widgets.

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
4. Go to the **SanirTech Forms -> Settings** menu to configure your global target numbers, operating hours, analytics IDs, and spam protection keys.
5. Navigate to **Add New** to assemble a custom form, copy the output shortcode e.g., `[stlcf_chat_form id="X"]`, and embed it on any page.

== Frequently Asked Questions ==

= Is it compatible with caching plugins and server-side object caching? =
Yes! This is a flagship feature. The plugin uses a dynamic heartbeat to fetch fresh security nonces asynchronously, meaning forms will never break or throw "security errors" on cached pages.

= Does this plugin offload assets or inject external scripts on every page? =
No. Scripts for Cloudflare Turnstile or Google reCAPTCHA are enqueued dynamically and conditionally. They will only load on pages where the shortcode is actively parsed.

= How do I route leads to different WhatsApp numbers? =
In the form builder, add the "Agent Dropdown Routing" field. In the configuration box, simply type your departments and numbers separated by a pipe character, like this: `Sales Department|919876543210`.

= Will the automated SEO schema duplicate my existing plugin's schema? =
No. We actively check for Yoast SEO and Rank Math. If they are running, we inject our ContactPoint data directly into their single JSON-LD graph array to prevent any structural duplicates.

= What strings tokens placeholders can I write inside the email auto-responder message textareas? =
You can write the dynamic shortcodes tokens `[Your Name]` (which parses the corresponding user text input field) and `[Form Title]` (which reflects the exact name attribute assigned to the template layout configurations).

== Screenshots ==

1. Comprehensive settings panel featuring dedicated modules for Business Hours, Pixels tracking, and anti-spam gates.
2. Lightweight shortcode-driven drag-and-drop form builder canvas featuring multi-agent routing.
3. Clean local Leads Submission Entries Log showcasing dynamic URL context tracking parameters.
4. Memory-safe stream buffered CSV Export operations block in action.

== Changelog ==

= 1.0.1 =
* **Feature:** Migrated to a 100% Cache-Proof AJAX form submission engine for seamless compatibility with WP Rocket and Cloudflare.
* **Feature:** Added Multi-Agent Smart Routing via configurable dropdown department selection.
* **Feature:** Introduced Business Operating Hours with automated offline actions (hide widget, force email routing, or show alert banner).
* **Feature:** Added Automated SEO JSON-LD Schema injection with native Yoast SEO and Rank Math compatibility.
* **Feature:** Integrated GDPR Compliance consent checkboxes with dynamic privacy policy link tokens.
* **Feature:** Added Advanced Page Tracking to capture URLs, Post IDs, and UTM Source/Medium/Campaign parameters.
* **Feature:** Built-in Conversion Analytics support for Meta (Facebook) Pixel and Google Analytics 4 (GA4) with async event tracking.
* **Feature:** Upgraded Floating Widget with custom tooltip pop-ups, prefilled text messages, and dynamic page visibility rules.
* **Feature:** Added Instant Auto-Responder dispatch for email submissions featuring dynamic `[Your Name]` tokens.
* **Feature:** Rolled out a Memory-Safe CSV Streaming Exporter for handling massive lead logs without server timeouts.
* **Fix:** Resolved a parallel execution loop causing duplicate entries in the local database upon submission.
* **Fix:** Corrected an administrative 404 routing error on the CSV export download action link.
* **Fix:** Patched isolated PHP fatal crashes related to `isset()` expression structures and `$wpdb` dynamic syntax strings.

= 1.0.0 =
* Initial public release.
* Introduced a lightweight drag-and-drop form builder for capturing leads.
* Added secure local database storage for logging all form submissions.
* Implemented dual routing: Direct app redirect and Email fallback method.
* Added a customizable sitewide floating chat widget.
* Integrated multi-layer spam protection including Math Captcha, Cloudflare Turnstile, and Google reCAPTCHA v2/v3.
* Built full shortcode support for seamless embedding across posts, pages, and widgets.