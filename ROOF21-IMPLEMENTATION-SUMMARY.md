# ROOF21 WordPress + Bitrix24 Integration
## Implementation Summary & Remaining Files

---

## COMPLETED CORE FILES ✓

### Plugin Foundation
- ✓ `roof21-core/roof21-core.php` - Main plugin file with autoloader
- ✓ `roof21-core/includes/class-activator.php` - Activation logic
- ✓ `roof21-core/includes/class-deactivator.php` - Deactivation logic
- ✓ `roof21-core/includes/class-core.php` - Main core class

### Helper Functions
- ✓ `roof21-core/includes/helpers/functions.php` - Global helper functions
- ✓ `roof21-core/includes/helpers/class-currency.php` - Currency conversion
- ✓ `roof21-core/includes/helpers/class-language.php` - Language management

### Custom Post Types
- ✓ `roof21-core/includes/post-types/class-property-cpt.php`
- ✓ `roof21-core/includes/post-types/class-development-cpt.php`
- ✓ `roof21-core/includes/post-types/class-area-guide-cpt.php`
- ✓ `roof21-core/includes/post-types/class-team-cpt.php`

### Taxonomies
- ✓ `roof21-core/includes/taxonomies/class-location-taxonomy.php`
- ✓ `roof21-core/includes/taxonomies/class-property-type-taxonomy.php`
- ✓ `roof21-core/includes/taxonomies/class-ownership-type-taxonomy.php`
- ✓ `roof21-core/includes/taxonomies/class-feature-taxonomy.php`
- ✓ `roof21-core/includes/taxonomies/class-listing-type-taxonomy.php`
- ✓ `roof21-core/includes/taxonomies/class-country-taxonomy.php`

### Bitrix24 Integration
- ✓ `roof21-core/includes/bitrix24/class-bitrix24-api.php` - API client
- ✓ `roof21-core/includes/bitrix24/class-bitrix24-sync.php` - Sync engine
- ✓ `roof21-core/includes/bitrix24/class-bitrix24-webhook.php` - Webhook handler
- ✓ `roof21-core/includes/bitrix24/class-bitrix24-forms.php` - Forms integration

### XML Feeds
- ✓ `roof21-core/includes/feeds/class-feed-base.php` - Base feed class
- ✓ `roof21-core/includes/feeds/class-proppit-feed.php` - Proppit XML feed
- ✓ `roof21-core/includes/feeds/class-featured-feed.php` - Featured properties feed
- ✓ `roof21-core/includes/feeds/class-condos-feed.php` - Condos only feed

### Watermarking
- ✓ `roof21-core/includes/watermark/class-watermark-processor.php` - Image watermarking

---

## REMAINING PLUGIN FILES TO CREATE

### Admin Classes

#### 1. Admin Main Class
**File:** `roof21-core/admin/class-admin.php`

**Purpose:** Register admin menus, enqueue admin assets, handle admin functionality

**Key Methods:**
- `add_plugin_admin_menu()` - Add admin menu pages
- `add_settings_link()` - Add settings link to plugins page
- `display_sync_status()` - Show sync status dashboard
- `handle_manual_sync()` - Manual sync trigger
- `display_sync_logs()` - Show sync logs table

#### 2. Settings Class
**File:** `roof21-core/admin/class-settings.php`

**Purpose:** Handle all plugin settings pages and options

**Settings Tabs:**
1. **General Settings**
   - Default currency
   - Properties per page
   - Feed cache duration

2. **Bitrix24 Settings**
   - Webhook URL
   - Client ID & Secret
   - Sync interval (hourly, twice-daily, daily)
   - Enable/disable sync
   - Test connection button

3. **Feed Settings**
   - Proppit feed filters (locations, property types)
   - Featured feed options
   - Condos feed options
   - Custom feed creation

4. **Watermark Settings**
   - Enable/disable watermarking
   - Upload watermark logo
   - Position selector (9-grid)
   - Opacity slider
   - Padding value
   - Batch re-watermark button

5. **Currency Settings**
   - Exchange rate API key
   - Manual exchange rates
   - Supported currencies
   - Update frequency

#### 3. Meta Boxes Class
**File:** `roof21-core/admin/class-meta-boxes.php`

**Purpose:** Add custom meta boxes to property edit screen

**Meta Boxes:**
1. **Property Details**
   - Reference Code
   - Price (THB, USD, EUR)
   - Beds, Baths
   - Living Area, Land Area
   - Project Name

2. **Bitrix24 Integration**
   - Bitrix24 Deal ID (read-only)
   - Proppit Project Mapping (dropdown)
   - Featured Property (checkbox)
   - Last Synced (read-only)

3. **Location Coordinates**
   - Latitude, Longitude
   - Map preview (Google Maps)

4. **Rental Availability**
   - Available From (date picker)
   - Available Until (date picker)

5. **Gallery Management**
   - Original images
   - Watermarked images
   - Reorder gallery

### Public/Frontend Classes

#### 1. Public Class
**File:** `roof21-core/public/class-public.php`

**Purpose:** Handle frontend functionality

**Key Methods:**
- `add_body_classes()` - Add custom body classes
- `modify_property_query()` - Customize property queries
- `add_custom_query_vars()` - Register query vars for filters

#### 2. Shortcodes Class
**File:** `roof21-core/public/class-shortcodes.php`

**Purpose:** Register all plugin shortcodes

**Shortcodes:**
- `[roof21_search_form]` - Property search form
- `[roof21_properties]` - Property grid with filters
- `[roof21_featured_properties]` - Featured properties slider/grid
- `[roof21_property_details]` - Property details (for custom layouts)
- `[roof21_similar_properties]` - Similar properties
- `[roof21_contact_form]` - Contact form (Bitrix24 integrated)
- `[roof21_currency_switcher]` - Currency switcher dropdown
- `[roof21_language_switcher]` - Language switcher

#### 3. AJAX Class
**File:** `roof21-core/public/class-ajax.php`

**Purpose:** Handle all AJAX requests

**AJAX Actions:**
- `roof21_search_properties` - AJAX property search
- `roof21_load_more_properties` - Infinite scroll
- `roof21_switch_currency` - Currency switching
- `roof21_submit_contact_form` - Contact form submission
- `roof21_submit_property_inquiry` - Property inquiry form

### Assets

#### CSS Files
1. **Admin CSS:** `roof21-core/admin/css/admin.css`
   - Settings page styles
   - Meta box styles
   - Sync status dashboard styles

2. **Public CSS:** `roof21-core/public/css/public.css`
   - Property card styles
   - Search form styles
   - Currency/language switcher styles

#### JavaScript Files
1. **Admin JS:** `roof21-core/admin/js/admin.js`
   - Settings page interactions
   - Manual sync trigger
   - Batch watermark trigger
   - Connection test

2. **Search JS:** `roof21-core/public/js/search.js`
   - AJAX property search
   - Filter interactions
   - Infinite scroll
   - URL parameter handling

3. **Currency Switcher JS:** `roof21-core/public/js/currency-switcher.js`
   - Currency switching
   - Cookie management
   - Price updates

4. **Language Switcher JS:** `roof21-core/public/js/language-switcher.js`
   - Language switching
   - Integration with Polylang/WPML

### Admin Views

1. `roof21-core/admin/views/settings-main.php` - Main settings page
2. `roof21-core/admin/views/settings-bitrix24.php` - Bitrix24 settings tab
3. `roof21-core/admin/views/settings-feeds.php` - Feed settings tab
4. `roof21-core/admin/views/settings-watermark.php` - Watermark settings tab
5. `roof21-core/admin/views/sync-status.php` - Sync status dashboard

---

## THEME STRUCTURE

### Directory Layout
```
roof21-theme/
├── style.css
├── functions.php
├── screenshot.png
├── README.md
│
├── header.php
├── footer.php
├── index.php
├── 404.php
├── search.php
│
├── templates/
│   ├── front-page.php
│   ├── archive-roof21_property.php
│   ├── single-roof21_property.php
│   ├── archive-roof21_development.php
│   ├── single-roof21_development.php
│   ├── page-about.php
│   ├── page-contact.php
│   ├── page-international.php
│   └── page-guides.php
│
├── template-parts/
│   ├── header/
│   │   ├── top-bar.php
│   │   └── navigation.php
│   ├── footer/
│   │   ├── footer-main.php
│   │   └── footer-bottom.php
│   ├── property/
│   │   ├── card.php
│   │   ├── grid.php
│   │   ├── search-form.php
│   │   ├── filters.php
│   │   ├── gallery.php
│   │   ├── details.php
│   │   ├── features.php
│   │   ├── agent.php
│   │   └── similar.php
│   ├── development/
│   │   ├── card.php
│   │   └── units-list.php
│   ├── sections/
│   │   ├── hero.php
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
│   ├── theme-setup.php
│   ├── enqueue.php
│   ├── template-tags.php
│   ├── customizer.php
│   └── widgets.php
│
├── assets/
│   ├── css/
│   │   ├── tailwind.css
│   │   └── custom.css
│   ├── js/
│   │   ├── main.js
│   │   ├── header.js
│   │   ├── property-search.js
│   │   └── map.js
│   └── images/
│       ├── logo-white.svg
│       ├── logo-green.svg
│       └── placeholder.jpg
│
└── languages/
    └── roof21-theme.pot
```

### Key Theme Files

#### 1. style.css
```css
/*
Theme Name: ROOF21 Real Estate
Theme URI: https://roof21.co.th
Author: ROOF21 Development Team
Author URI: https://roof21.co.th
Description: Custom real estate theme for ROOF21 platform with Bitrix24 integration
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: roof21-theme
*/
```

#### 2. functions.php
Main theme functions file that loads all includes

#### 3. Template Files
All template files listed above in directory structure

---

## INSTALLATION & SETUP

### 1. Install WordPress
- WordPress 6.4+
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+

### 2. Install Plugin
1. Upload `roof21-core` folder to `/wp-content/plugins/`
2. Activate plugin from WordPress admin

### 3. Install Theme
1. Upload `roof21-theme` folder to `/wp-content/themes/`
2. Activate theme from WordPress admin

### 4. Configure Bitrix24
1. Go to ROOF21 Core > Settings > Bitrix24
2. Enter Webhook URL from your Bitrix24 account
3. Click "Test Connection"
4. Set sync interval (default: hourly)
5. Enable automatic sync

### 5. Configure Feeds
1. Go to ROOF21 Core > Settings > Feeds
2. Configure Proppit feed filters
3. Map projects to Proppit
4. Set up other custom feeds as needed

### 6. Configure Watermarking
1. Go to ROOF21 Core > Settings > Watermark
2. Upload watermark logo (PNG with transparency)
3. Set position and opacity
4. Enable watermarking

### 7. Initial Sync
1. Go to ROOF21 Core > Sync Status
2. Click "Manual Sync Now"
3. Wait for sync to complete
4. Review sync logs

### 8. Create Pages
Create WordPress pages for:
- Homepage (set as front page)
- About Us
- Contact Us
- Guides
- International Properties

### 9. Configure Menus
1. Go to Appearance > Menus
2. Create main navigation menu
3. Add menu items as per specification

### 10. Install Recommended Plugins
- **Polylang** or **WPML** for multilingual support
- **Yoast SEO** for SEO optimization
- **WP Rocket** for caching (optional)
- **Wordfence** for security (optional)

---

## FEED ENDPOINTS

After activation, the following XML feeds will be available:

- **Proppit Feed:** `https://yourdomain.com/feed/roof21-proppit/`
- **Featured Feed:** `https://yourdomain.com/feed/roof21-featured/`
- **Condos Feed:** `https://yourdomain.com/feed/roof21-condos/`

Add `?nocache=1` to bypass cache during testing.

---

## SHORTCODE USAGE EXAMPLES

### Property Search Form
```php
[roof21_search_form]
```

### Featured Properties Grid
```php
[roof21_featured_properties limit="6"]
```

### Property Grid with Filters
```php
[roof21_properties per_page="12" listing_type="for-sale"]
```

### Contact Form
```php
[roof21_contact_form]
```

### Currency Switcher
```php
[roof21_currency_switcher]
```

---

## CUSTOMIZATION

### Adding Custom Feed
```php
// In your child theme or custom plugin
add_filter( 'roof21_feed_query_args', function( $args, $feed_slug ) {
    if ( 'custom-feed' === $feed_slug ) {
        // Modify query args
        $args['meta_query'][] = array(
            'key' => '_roof21_custom_field',
            'value' => 'custom_value',
        );
    }
    return $args;
}, 10, 2 );
```

### Customizing Property Meta Mapping
```php
add_filter( 'roof21_property_meta_mapping', function( $mapped, $deal ) {
    // Add custom field mapping
    $mapped['_roof21_custom_field'] = $deal['UF_CRM_CUSTOM'] ?? '';
    return $mapped;
}, 10, 2 );
```

### Hooks Available
```php
// Before property sync
do_action( 'roof21_before_property_sync', $deal );

// After property created
do_action( 'roof21_property_created', $property_id, $deal );

// After property updated
do_action( 'roof21_property_updated', $property_id, $deal );

// After all properties synced
do_action( 'roof21_after_property_sync', $results );

// Feed generated
do_action( 'roof21_feed_generated', $feed_slug, $property_count );
```

---

## MAINTENANCE

### Regular Tasks
- Monitor sync logs daily
- Review failed syncs
- Update exchange rates (auto or manual)
- Check feed quality weekly
- Update WordPress, plugins, theme monthly

### Performance Optimization
- Enable object caching (Redis/Memcached)
- Use CDN for static assets
- Optimize images before upload to Bitrix24
- Set appropriate feed cache duration
- Limit properties per page for large datasets

### Backup Strategy
- Daily automated backups
- Include database AND uploads folder
- Test restore process monthly
- Keep at least 30 days of backups

---

## TROUBLESHOOTING

### Sync Issues
1. Check Bitrix24 webhook URL is correct
2. Test connection in settings
3. Review sync logs for errors
4. Verify Bitrix24 field mappings
5. Check PHP error logs

### Feed Issues
1. Visit feed URL directly
2. Check for PHP errors
3. Verify query filters in settings
4. Clear feed cache (?nocache=1)
5. Check XML validity

### Watermark Issues
1. Verify GD or Imagick extension installed
2. Check watermark logo file exists and is readable
3. Verify upload directory permissions (755)
4. Test on single image first
5. Check PHP memory limit (256MB+)

---

## SUPPORT & DOCUMENTATION

- Plugin Settings: WP Admin > ROOF21 Core
- Sync Logs: ROOF21 Core > Sync Status
- Architecture Doc: `/ROOF21-ARCHITECTURE.md`
- Implementation Guide: `/ROOF21-IMPLEMENTATION-SUMMARY.md`

---

This summary provides a complete overview of the implemented system and remaining work needed to complete the ROOF21 WordPress + Bitrix24 integration.
