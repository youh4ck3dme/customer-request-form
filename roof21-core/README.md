# ROOF21 Core Plugin

Complete WordPress plugin for ROOF21 real estate platform with Bitrix24 CRM integration, XML feed generation, and automatic image watermarking.

## Features

- **Bitrix24 Integration**: Full bi-directional sync with Bitrix24 CRM
- **Custom Post Types**: Properties, Developments, Area Guides, Team Members
- **Taxonomies**: Locations, Property Types, Ownership Types, Features, Listing Types, Countries
- **XML Feeds**: Multiple configurable feeds (Proppit, Featured, Condos, Custom)
- **Image Watermarking**: Automatic watermark application with dual-version storage
- **Multi-Currency**: Support for THB, USD, EUR with automatic exchange rates
- **Multi-Language**: Compatible with Polylang and WPML
- **Property Search**: Advanced AJAX-powered property search
- **Shortcodes**: Ready-to-use shortcodes for properties, search, contact forms

## Requirements

- WordPress 6.4+
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- GD or Imagick extension for watermarking
- Bitrix24 account with webhook access

## Installation

1. Upload the `roof21-core` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to ROOF21 Core > Settings to configure

## Configuration

### Bitrix24 Setup

1. Log in to your Bitrix24 account
2. Create an incoming webhook
3. Copy the webhook URL
4. In WordPress admin, go to ROOF21 Core > Settings > Bitrix24
5. Paste the webhook URL
6. Click "Test Connection" to verify
7. Set sync interval (hourly recommended)
8. Enable automatic sync

### Watermark Setup

1. Prepare a PNG logo with transparency (recommended: 300x100px)
2. Go to ROOF21 Core > Settings > Watermark
3. Upload your watermark logo
4. Choose position (bottom-right recommended)
5. Set opacity (70% recommended)
6. Enable watermarking

### Feed Setup

1. Go to ROOF21 Core > Settings > Feeds
2. Configure Proppit feed filters
3. Map WordPress projects to Proppit projects
4. Set up custom feeds as needed
5. Test feeds at `/feed/roof21-proppit/`

## Usage

### Shortcodes

**Property Search Form:**
```
[roof21_search_form]
```

**Featured Properties:**
```
[roof21_featured_properties limit="6"]
```

**Property Grid:**
```
[roof21_properties per_page="12" listing_type="for-sale"]
```

**Contact Form:**
```
[roof21_contact_form]
```

### Feed URLs

- Proppit: `https://yoursite.com/feed/roof21-proppit/`
- Featured: `https://yoursite.com/feed/roof21-featured/`
- Condos: `https://yoursite.com/feed/roof21-condos/`

Add `?nocache=1` to bypass cache.

### Helper Functions

```php
// Get property price in current currency
$price = roof21_get_property_price( $property_id );

// Format price with currency symbol
echo roof21_format_price( $price );

// Get property reference code
$ref = roof21_get_property_reference( $property_id );

// Check if property is featured
if ( roof21_is_property_featured( $property_id ) ) {
    // ...
}

// Get similar properties
$similar = roof21_get_similar_properties( $property_id, 3 );
```

## Hooks & Filters

### Actions

```php
// Before property sync
do_action( 'roof21_before_property_sync', $deal );

// After property created
do_action( 'roof21_property_created', $property_id, $deal );

// After property updated
do_action( 'roof21_property_updated', $property_id, $deal );
```

### Filters

```php
// Customize property meta mapping
add_filter( 'roof21_property_meta_mapping', function( $mapped, $deal ) {
    $mapped['_custom_field'] = $deal['UF_CUSTOM'];
    return $mapped;
}, 10, 2 );

// Modify feed query
add_filter( 'roof21_feed_query_args', function( $args, $feed_slug ) {
    // Customize query
    return $args;
}, 10, 2 );
```

## Troubleshooting

### Sync not working
- Check Bitrix24 webhook URL
- Verify WP-Cron is enabled
- Review sync logs in ROOF21 Core > Sync Logs

### Watermarks not applying
- Verify GD/Imagick extension is installed
- Check upload directory permissions
- Review PHP error logs

### Feeds showing errors
- Test feed URL directly in browser
- Check for PHP errors
- Clear feed cache with `?nocache=1`

## Support

For documentation and support:
- Architecture: See `/ROOF21-ARCHITECTURE.md`
- Implementation Guide: See `/ROOF21-IMPLEMENTATION-SUMMARY.md`

## License

GPL-2.0+

## Version

1.0.0
