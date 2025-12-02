# ROOF21 WordPress + Bitrix24 Integration
## Production-Ready Architecture & Implementation

---

## 1. ARCHITECTURE OVERVIEW

### System Architecture

The ROOF21 platform is built on a **hybrid architecture** where:

- **Bitrix24** serves as the **single source of truth** for:
  - Property data (deals with custom fields)
  - Lead management
  - Contact management
  - Form submissions

- **WordPress** serves as the **presentation layer** that:
  - Syncs property data from Bitrix24 (scheduled via WP-Cron)
  - Caches property information locally for performance
  - Generates multi-format XML feeds for Thai real estate portals
  - Handles multi-currency/multi-language frontend
  - Manages watermarked and original property images

### Data Flow

```
Bitrix24 CRM (Source of Truth)
    ↓
    ↓ (Scheduled Sync via REST API)
    ↓
WordPress (Presentation Layer)
    ├── Property CPT (cached data)
    ├── XML Feed Endpoints
    │   ├── /feed/roof21-proppit.xml
    │   ├── /feed/roof21-featured.xml
    │   └── /feed/roof21-condos.xml
    └── Frontend Display
        ├── Property Search
        ├── Property Details
        └── Contact Forms → Bitrix24
```

### Data Model

**WordPress Entities:**

1. **Custom Post Types (CPT)**
   - `roof21_property` - Individual properties
   - `roof21_development` - New development projects
   - `roof21_area_guide` - Location guides
   - `roof21_team` - Team members

2. **Taxonomies**
   - `roof21_location` - Bangkok, Pattaya, Phuket, Samui, etc.
   - `roof21_property_type` - Condo, Villa, House, Apartment, Land
   - `roof21_ownership_type` - Foreign Quota, Thai Quota, Company
   - `roof21_feature` - Pool, Sea View, Gym, Security, etc.
   - `roof21_listing_type` - For Sale, For Rent
   - `roof21_country` - For international properties

3. **Property Meta Fields**
   - `_roof21_bitrix_id` - Bitrix24 deal ID
   - `_roof21_reference_code` - Property reference
   - `_roof21_price_thb` - Price in Thai Baht
   - `_roof21_price_usd` - Price in USD
   - `_roof21_price_eur` - Price in EUR
   - `_roof21_beds` - Number of bedrooms
   - `_roof21_baths` - Number of bathrooms
   - `_roof21_living_area` - Living area (sqm)
   - `_roof21_land_area` - Land area (sqm)
   - `_roof21_project_name` - Development/project name
   - `_roof21_availability_start` - Available from date (rentals)
   - `_roof21_availability_end` - Available until date (rentals)
   - `_roof21_gallery` - Serialized array of image IDs
   - `_roof21_gallery_watermarked` - Watermarked versions
   - `_roof21_featured` - Featured listing flag
   - `_roof21_proppit_project` - Proppit project mapping
   - `_roof21_lat` - Approximate latitude
   - `_roof21_lng` - Approximate longitude

**Bitrix24 Entities (mapped to WordPress):**

- **Deals** → Properties
- **Contacts** → Leads/Inquiries
- **Custom Fields** → Property meta fields

### Multi-Currency Strategy

- Primary currency: **Thai Baht (THB)**
- Secondary currencies: **USD, EUR**
- Exchange rates stored in WordPress options (updated daily via external API or manual entry)
- All property meta stores prices in all supported currencies
- Frontend displays based on user selection (stored in session/cookie)

### Multi-Language Strategy

- Using **Polylang** or **WPML** (or custom implementation)
- Supported languages: English (EN - primary), Slovak (SK)
- Language switcher in header
- Property content translated via WordPress multilingual plugin
- Bitrix24 data mapped to default language, translations managed in WordPress

### Sync Strategy

**Method:** Scheduled Pull from Bitrix24

1. **WP-Cron Job** runs every hour (configurable)
2. Pulls all active deals from Bitrix24 via REST API
3. Maps Bitrix24 fields to WordPress CPT meta
4. Creates/updates property posts
5. Downloads and processes images (watermarking)
6. Logs sync results in database

**Alternative:** Webhook Push from Bitrix24

- Bitrix24 sends webhook on deal create/update
- WordPress endpoint receives and processes
- Real-time updates (optional, more complex to secure)

### Watermarking System

**Process:**

1. When property images are uploaded (via sync or manual upload):
   - Original image saved to uploads directory
   - Watermarked copy generated using GD/Imagick
   - Transparent PNG logo overlaid at bottom-right corner
   - Both paths stored in post meta

2. **Two Image Sets:**
   - `_roof21_gallery` - Original images
   - `_roof21_gallery_watermarked` - Watermarked versions

3. **XML Feeds** can choose which version to export based on portal requirements

---

## 2. ASSUMPTIONS & DECISIONS

### Assumptions Made:

1. **Bitrix24 Setup:**
   - Properties stored as "Deals" in Bitrix24
   - Custom fields already configured for beds, baths, areas, etc.
   - Webhook/REST API access available
   - Deal stages represent listing status (Active, Rented, Sold, etc.)

2. **WordPress Environment:**
   - PHP 8.1+ with GD or Imagick extension
   - WordPress 6.4+
   - Clean permalink structure enabled
   - Sufficient memory for image processing (256MB+)

3. **Theme Base:**
   - Using WP Residence as visual reference only
   - Building custom theme from scratch (no theme framework dependency)
   - Tailwind CSS for styling (via CDN or compiled)
   - Alpine.js for interactive components

4. **Property Images:**
   - Maximum 20 images per property
   - Watermark logo provided as PNG (300x100px transparent)
   - Watermark position: bottom-right, 20px padding, 70% opacity

5. **XML Feed Requirements:**
   - Standard real estate XML format (similar to Proppit/Thai portals)
   - UTF-8 encoding
   - Configurable filters via admin UI
   - Generated on-demand (no caching initially)

6. **Forms Integration:**
   - Contact forms use Bitrix24 web forms (embedded via iframe/script)
   - OR custom forms that POST to WordPress, then sync to Bitrix24 via API
   - All inquiries create a new Lead + Contact in Bitrix24

7. **Performance:**
   - Property data cached in WordPress database
   - Sync runs hourly (not real-time) to reduce API calls
   - Transients used for expensive queries (search results, feeds)
   - CDN recommended for production (Cloudflare, etc.)

8. **International Properties:**
   - Stored as same CPT `roof21_property`
   - Differentiated by `roof21_country` taxonomy
   - Separate archive template for international section

9. **New Developments:**
   - Separate CPT `roof21_development`
   - Individual properties linked via meta field `_roof21_development_id`
   - Development detail page shows all units in that project

10. **Currency Exchange Rates:**
    - Updated daily via free API (e.g., exchangerate-api.io)
    - Fallback to manual entry in admin settings
    - Cached for 24 hours

### Technical Decisions:

1. **Plugin Architecture:** Object-oriented, namespaced PHP
2. **Admin UI:** WordPress Settings API + custom meta boxes
3. **Frontend:** Custom templates with hooks/filters for extensibility
4. **JavaScript:** Minimal dependencies (Alpine.js + vanilla JS)
5. **CSS Framework:** Tailwind CSS (compiled, not CDN in production)
6. **Image Processing:** GD library (with Imagick fallback)
7. **API Communication:** WordPress HTTP API (`wp_remote_get/post`)
8. **Security:** Nonces, capability checks, input sanitization, output escaping
9. **Translation:** WordPress i18n functions (`__()`, `_e()`, text domain: `roof21-core`)
10. **Logging:** Custom database table for sync logs + WP_DEBUG_LOG

---

## 3. PLUGIN & THEME STRUCTURE

### Plugin: `roof21-core/`

```
roof21-core/
├── roof21-core.php                 # Main plugin file
├── README.md                       # Plugin documentation
├── uninstall.php                   # Cleanup on uninstall
│
├── includes/
│   ├── class-core.php              # Main plugin class
│   ├── class-activator.php         # Activation hooks
│   ├── class-deactivator.php       # Deactivation hooks
│   ├── class-loader.php            # Hooks loader
│   │
│   ├── post-types/
│   │   ├── class-property-cpt.php
│   │   ├── class-development-cpt.php
│   │   ├── class-area-guide-cpt.php
│   │   └── class-team-cpt.php
│   │
│   ├── taxonomies/
│   │   ├── class-location-taxonomy.php
│   │   ├── class-property-type-taxonomy.php
│   │   ├── class-ownership-type-taxonomy.php
│   │   ├── class-feature-taxonomy.php
│   │   ├── class-listing-type-taxonomy.php
│   │   └── class-country-taxonomy.php
│   │
│   ├── bitrix24/
│   │   ├── class-bitrix24-api.php          # API client
│   │   ├── class-bitrix24-sync.php         # Sync engine
│   │   ├── class-bitrix24-webhook.php      # Webhook handler
│   │   └── class-bitrix24-forms.php        # Forms integration
│   │
│   ├── feeds/
│   │   ├── class-feed-base.php             # Base feed class
│   │   ├── class-proppit-feed.php          # Proppit XML
│   │   ├── class-featured-feed.php         # Featured properties
│   │   └── class-condos-feed.php           # Condos only
│   │
│   ├── watermark/
│   │   └── class-watermark-processor.php   # Image watermarking
│   │
│   ├── helpers/
│   │   ├── class-currency.php              # Currency conversion
│   │   ├── class-language.php              # Language helper
│   │   └── functions.php                   # Global helper functions
│   │
├── admin/
│   ├── class-admin.php                     # Admin functionality
│   ├── class-settings.php                  # Settings pages
│   ├── class-meta-boxes.php                # Custom meta boxes
│   ├── css/
│   │   └── admin.css                       # Admin styles
│   ├── js/
│   │   └── admin.js                        # Admin scripts
│   └── views/
│       ├── settings-main.php               # Main settings page
│       ├── settings-bitrix24.php           # Bitrix24 settings
│       ├── settings-feeds.php              # Feed configuration
│       ├── settings-watermark.php          # Watermark settings
│       └── sync-status.php                 # Sync logs/status
│
├── public/
│   ├── class-public.php                    # Frontend functionality
│   ├── class-shortcodes.php                # Shortcodes
│   ├── class-ajax.php                      # AJAX handlers
│   ├── css/
│   │   └── public.css                      # Public styles
│   └── js/
│       ├── search.js                       # Property search
│       ├── currency-switcher.js            # Currency switching
│       └── language-switcher.js            # Language switching
│
└── assets/
    ├── images/
    │   └── watermark-logo.png              # Default watermark
    └── fonts/
```

### Theme: `roof21-theme/`

```
roof21-theme/
├── style.css                       # Theme stylesheet
├── functions.php                   # Theme functions
├── screenshot.png                  # Theme screenshot
├── README.md                       # Theme documentation
│
├── templates/
│   ├── front-page.php              # Homepage
│   ├── archive-roof21_property.php # Property archive
│   ├── single-roof21_property.php  # Property detail
│   ├── archive-roof21_development.php  # Developments archive
│   ├── single-roof21_development.php   # Development detail
│   ├── page-about.php              # About us
│   ├── page-contact.php            # Contact
│   ├── page-international.php      # International properties
│   └── page-guides.php             # Guides hub
│
├── template-parts/
│   ├── header/
│   │   ├── top-bar.php             # Contact/language/currency bar
│   │   └── navigation.php          # Main navigation
│   ├── footer/
│   │   ├── footer-main.php         # Main footer
│   │   └── footer-bottom.php       # Copyright/links
│   ├── property/
│   │   ├── card.php                # Property card
│   │   ├── grid.php                # Property grid
│   │   ├── search-form.php         # Search form
│   │   ├── filters.php             # Filter sidebar
│   │   ├── gallery.php             # Property gallery
│   │   ├── details.php             # Property details
│   │   ├── features.php            # Property features
│   │   ├── agent.php               # Agent box
│   │   └── similar.php             # Similar properties
│   ├── development/
│   │   ├── card.php                # Development card
│   │   └── units-list.php          # Units in development
│   ├── sections/
│   │   ├── hero.php                # Hero section
│   │   ├── featured-properties.php
│   │   ├── new-developments.php
│   │   ├── area-guide.php
│   │   ├── about-us.php
│   │   ├── testimonials.php
│   │   ├── latest-videos.php
│   │   ├── latest-blog.php
│   │   ├── faq.php
│   │   ├── leadingre.php
│   │   └── contact-map.php
│   └── global/
│       ├── breadcrumbs.php
│       └── pagination.php
│
├── inc/
│   ├── theme-setup.php             # Theme setup functions
│   ├── enqueue.php                 # Scripts/styles enqueue
│   ├── template-tags.php           # Custom template tags
│   ├── customizer.php              # Theme customizer
│   └── widgets.php                 # Widget areas
│
├── assets/
│   ├── css/
│   │   ├── tailwind.css            # Tailwind source
│   │   └── custom.css              # Custom styles
│   ├── js/
│   │   ├── main.js                 # Main scripts
│   │   ├── header.js               # Header interactions
│   │   ├── property-search.js      # Search functionality
│   │   └── map.js                  # Google Maps integration
│   └── images/
│       ├── logo-white.svg
│       ├── logo-green.svg
│       └── placeholder.jpg
│
└── languages/
    └── roof21-theme.pot            # Translation template
```

---

## 4. KEY FEATURES IMPLEMENTATION

### Property Search

- AJAX-powered search form
- Filters: Buy/Rent, Property Type, Beds, Price Range, Location, Ownership Type, Reference Code
- Results update without page reload
- URL parameters for shareable searches
- Pagination with infinite scroll option

### Bitrix24 Sync

- Scheduled via WP-Cron (hourly default)
- Manual sync trigger in admin
- Sync status dashboard showing:
  - Last sync time
  - Properties added/updated
  - Errors/warnings
  - Sync logs table

### XML Feeds

- Dynamic feed generation
- Configurable filters (admin UI)
- Multiple feed formats:
  - Proppit standard
  - Featured properties only
  - Condos only
  - Custom filtered feeds
- Choose watermarked or original images per feed

### Watermarking

- Automatic on image upload/sync
- Configurable position (9 positions grid)
- Adjustable opacity (0-100%)
- Custom watermark logo upload
- Batch re-watermark existing properties

### Multi-Currency

- Supported: THB, USD, EUR (extensible)
- Cookie-based currency selection
- Exchange rates updated daily (auto or manual)
- Display all prices on property details
- Currency switcher in header

### Multi-Language

- English (primary), Slovak
- Compatible with Polylang/WPML
- Language switcher in header
- Translated property content
- SEO-friendly URLs per language

### Rental Availability

- Start/end date meta fields
- Display "Available from [date]" on cards
- Filter rentals by availability date
- Booking calendar integration (future)

### International Properties

- Country taxonomy
- Country cards on international page
- Interactive world map with pins (Leaflet.js)
- Filter by country/city

### New Developments

- Separate CPT for developments
- Village vs Condo categorization
- Link individual units to development
- Development detail page with:
  - Project info
  - Location map
  - Available units
  - Gallery
  - Brochure download

---

## 5. SECURITY CONSIDERATIONS

- Nonce verification on all forms
- Capability checks for admin functions
- Input sanitization (`sanitize_text_field`, `sanitize_email`, etc.)
- Output escaping (`esc_html`, `esc_url`, `esc_attr`)
- Prepared SQL statements for database queries
- Bitrix24 API credentials encrypted in database
- Webhook endpoints with signature verification
- Rate limiting on public API endpoints
- File upload validation (type, size, mime)
- HTTPS required for Bitrix24 communication

---

## 6. PERFORMANCE OPTIMIZATION

- Property data cached in WordPress (hourly sync)
- Transients for expensive queries (15-30 min TTL)
- Object caching support (Redis/Memcached)
- Lazy loading for images
- Responsive images (srcset)
- Minified/concatenated CSS/JS
- CDN integration for static assets
- Database indexing on meta fields (reference_code, bitrix_id, featured)
- Pagination limits (max 100 properties per page)
- Feed caching (15-minute transients)

---

## 7. EXTENSIBILITY

The plugin is built with extensibility in mind:

**Hooks (Actions):**
- `roof21_before_property_sync`
- `roof21_after_property_sync`
- `roof21_property_created`
- `roof21_property_updated`
- `roof21_before_watermark`
- `roof21_after_watermark`
- `roof21_feed_generated`

**Filters:**
- `roof21_property_meta_mapping` - Customize Bitrix24 field mapping
- `roof21_watermark_position` - Adjust watermark placement
- `roof21_feed_query_args` - Modify feed query
- `roof21_currency_rates` - Custom exchange rates
- `roof21_search_query_args` - Customize search query

**Template Override System:**
- All template parts can be overridden in child theme
- Similar to WooCommerce template system

---

## 8. TESTING STRATEGY

- Unit tests for core classes (PHPUnit)
- Integration tests for Bitrix24 API
- Manual QA checklist:
  - Property sync from Bitrix24
  - Image watermarking
  - XML feed generation
  - Contact form to Bitrix24
  - Currency switching
  - Language switching
  - Property search
  - Responsive design (mobile/tablet/desktop)
  - Cross-browser testing (Chrome, Firefox, Safari, Edge)

---

## 9. DEPLOYMENT CHECKLIST

- [ ] Install WordPress 6.4+
- [ ] Upload and activate `roof21-core` plugin
- [ ] Upload and activate `roof21-theme` theme
- [ ] Configure Bitrix24 API credentials in plugin settings
- [ ] Set up exchange rate API key (or manual rates)
- [ ] Upload watermark logo PNG
- [ ] Configure XML feed filters
- [ ] Map Proppit projects
- [ ] Run initial property sync from Bitrix24
- [ ] Test all forms (contact, property inquiry)
- [ ] Configure WP-Cron or server cron for hourly sync
- [ ] Set up SSL certificate
- [ ] Configure CDN (Cloudflare recommended)
- [ ] Install caching plugin (WP Rocket or similar)
- [ ] Install security plugin (Wordfence or similar)
- [ ] Configure backup solution
- [ ] Set up Google Maps API key
- [ ] Configure SMTP for email delivery
- [ ] Test all XML feeds
- [ ] Import initial content (guides, about us, team, etc.)
- [ ] Configure redirects from old site (if applicable)
- [ ] Submit sitemaps to Google Search Console

---

## 10. MAINTENANCE & SUPPORT

**Regular Tasks:**
- Monitor Bitrix24 sync logs (daily)
- Review and respond to form submissions in Bitrix24 (daily)
- Update exchange rates if manual (weekly)
- Review XML feed quality (weekly)
- Update WordPress core, plugins, theme (monthly)
- Security audit (quarterly)
- Performance review (quarterly)
- Backup verification (monthly)

**Monitoring:**
- Set up uptime monitoring (UptimeRobot, Pingdom)
- Error logging and notifications
- Sync failure alerts (email/Slack)
- Form submission tracking

---

This architecture provides a robust, scalable, and maintainable foundation for the ROOF21 real estate platform.
