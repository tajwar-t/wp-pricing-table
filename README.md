=== BitBirds Pricing Table ===
Contributors: bitbirds
Tags: pricing table, pricing, shortcode, whatsapp, reseller hosting
Requires at least: 5.5
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create beautiful, responsive pricing tables and display them anywhere with shortcodes.

== Description ==

**BitBirds Pricing Table** lets you build professional pricing tables from the WordPress admin and embed them anywhere using simple shortcodes. Perfect for hosting providers, SaaS products, agencies, and any business with tiered pricing.

= Key Features =

- **Multiple tables per shortcode** — group tables into a grid (1–6 columns)
- **Drag-and-drop** feature list reordering
- **Include / Exclude icons** — green ✓ and red ✗ per feature item
- **10 built-in color schemes** — Amber, Blue, Green, Purple, Red, Bronze, Silver, Gold, Dark, Teal
- **Per-table color scheme** — or override all tables in a group with one click
- **WhatsApp button** — link directly to WhatsApp with a pre-filled message
- **Regular URL button** or no button option
- **Featured / Recommended badge** — diagonal ribbon on highlighted plans
- **Optional section title** per group
- **Fully responsive** grid layout
- **Font Awesome icons** (loaded via CDN only if shortcode is present)

= Shortcodes =

**Group shortcode** (multiple tables in a grid):
`[bbpt_group id="123"]`

**Single table shortcode**:
`[bbpt_table id="456"]`

**Single table with color override**:
`[bbpt_table id="456" scheme="gold"]`

= Available Color Scheme Keys =
`amber` · `blue` · `green` · `purple` · `red` · `bronze` · `silver` · `gold` · `dark` · `teal`

== Installation ==

1. Upload the `bitbirds-pricing-table` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins** in WordPress admin
3. Go to **Pricing Tables** in the left menu
4. Create individual tables under **All Tables → Add New Table**
5. Group them under **Table Groups → Add New Group**
6. Copy the shortcode and paste it into any page or post

== Frequently Asked Questions ==

= How do I show multiple tables side by side? =
Create a **Table Group**, add your tables, set the **Grid Columns**, then use `[bbpt_group id="X"]`.

= Can I use different colors for each table in a group? =
Yes! Set a color scheme on each individual table. If you want to override them all at once, set a **Default Color Scheme** on the group.

= How does the WhatsApp button work? =
Select **WhatsApp** as the button type, enter the phone number with country code (no + symbol, e.g. `8801XXXXXXXXX`), and optionally enter a pre-filled message.

= Can I use the shortcode in widgets or page builders? =
Yes — the shortcode works anywhere WordPress processes shortcodes, including Gutenberg (Shortcode block), Elementor (Shortcode widget), and classic widgets.

== Changelog ==

= 1.0.0 =

- Initial release

== Screenshots ==

1. Admin dashboard overview
2. Individual pricing table editor with drag-and-drop features
3. Table group editor with scheme override
4. Frontend output – 4-column grid
5. Frontend output – WhatsApp button variant
