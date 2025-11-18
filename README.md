# Simple Weather Plugin for Botble CMS

A simple and lightweight weather plugin for Botble CMS that displays current weather and forecasts using the free Open-Meteo API.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Botble CMS](https://img.shields.io/badge/Botble%20CMS-7.0+-green.svg)
![License](https://img.shields.io/badge/license-MIT-orange.svg)

## Features

- ✅ **Free Weather API** - Uses Open-Meteo API (no API key required)
- ✅ **Geocoding Support** - Automatically converts location names to coordinates
- ✅ **Multiple Display Styles** - Default horizontal layout and compact widget style
- ✅ **Night Mode** - Beautiful dark theme for night display
- ✅ **Customizable Forecast** - Show 1-7 days of weather forecast
- ✅ **Unit Support** - Imperial (°F) and Metric (°C) units
- ✅ **Responsive Design** - Works perfectly on all devices
- ✅ **Weather Icons** - Beautiful weather icon font included
- ✅ **Smart Caching** - Configurable cache duration for better performance
- ✅ **Error Handling** - User-friendly error messages

## Requirements

- Botble CMS 7.0 or higher
- PHP 8.1 or higher
- Laravel 10.x or higher

## Installation

1. Download the plugin and extract it to `platform/plugins/simple-weather`

2. Activate the plugin in **Admin Panel → Plugins**

3. Configure settings in **Admin Panel → Settings → Simple Weather**

## Configuration

### Plugin Settings

Navigate to **Admin Panel → Settings → Simple Weather** to configure:

- **Default Location** - Set default location (e.g., "London, GB")
- **Default Units** - Choose between Metric (°C) or Imperial (°F)
- **Cache Duration** - How long to cache weather data (in seconds)

#### Screenshot

![Simple Weather Plugin Settings](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/settings-1.png)
![Simple Weather Plugin Settings](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/settings-2.png)

### Shortcode Usage

Add weather widget to any page using the shortcode:

```
[simple-weather location="Karachi, PK" days="5" style="default" night="no"][/simple-weather]
```

### Shortcode Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `location` | string | "London, GB" | Location name (City, Country) |
| `latitude` | float | - | Optional: Manual latitude |
| `longitude` | float | - | Optional: Manual longitude |
| `days` | integer | 1 | Number of forecast days (1-7) |
| `units` | string | "imperial" | Units: "imperial" or "metric" |
| `show_units` | string | "yes" | Show unit symbols: "yes" or "no" |
| `show_date` | string | "yes" | Show dates: "yes" or "no" |
| `date` | string | "dddd" | Date format: "dddd" (day name) or "default" |
| `night` | string | "no" | Night mode: "yes" or "no" |
| `style` | string | "default" | Display style: "default" or "widget" |

## Usage Examples

### Basic Usage
```
[simple-weather location="New York, US"][/simple-weather]
```

### 5-Day Forecast with Night Mode
```
[simple-weather location="Tokyo, Japan" days="5" night="yes"][/simple-weather]
```

### Widget Style with Metric Units
```
[simple-weather location="Paris, France" style="widget" units="metric"][/simple-weather]
```

### Using Coordinates
```
[simple-weather latitude="51.5074" longitude="-0.1278" days="3"][/simple-weather]
```

## Display Styles

### Default Style
Horizontal card layout showing current weather and forecast days side by side.
#### Light
![Default Style](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/default-light.png)
#### Dark
![Default Style](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/default-dark.png)

### Widget Style
Vertical compact layout with detailed information (humidity, clouds, wind speed).
#### Light
![Widget Style](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/widget-light.png)
#### Dark
![Widget Style](https://raw.githubusercontent.com/rajaishtiaq6/simple-weather/main/art/widget-dark.png)

## Weather Icons

The plugin includes Erik Flowers' Weather Icons font library with support for:
- Clear sky, clouds, rain, snow, thunderstorm
- Day and night variations
- Wind, fog, and other weather conditions

## Caching

The plugin uses smart caching to improve performance:
- **Geocoding Cache**: 30 days (location to coordinates conversion)
- **Weather Cache**: Configurable in settings (default: 3600 seconds)

## API Information

This plugin uses the free [Open-Meteo API](https://open-meteo.com/):
- ✅ No API key required
- ✅ No registration needed
- ✅ Free for non-commercial and commercial use
- ✅ High rate limits

## Troubleshooting

### "Could not connect to weather service"
- Check your internet connection
- Verify the location name format (e.g., "City, Country")
- Try using coordinates instead of location name

### Weather not updating
- Clear cache: `php artisan cache:clear`
- Check cache duration in settings
- Verify API is accessible from your server

### Location not found
- Use simple location names: "London" instead of "London, United Kingdom"
- Try different location formats
- Use latitude/longitude for precise locations

## Support

For issues, questions, or feature requests:
- GitHub: [https://github.com/rajaishtiaq6](https://github.com/rajaishtiaq6)
- Email: Contact through GitHub profile

## Credits

- **Author**: Ishtiaq Ahmed
- **GitHub**: [https://github.com/rajaishtiaq6](https://github.com/rajaishtiaq6)
- **Weather API**: [Open-Meteo](https://open-meteo.com/)
- **Weather Icons**: [Erik Flowers](https://erikflowers.github.io/weather-icons/)
- **Framework**: [Botble CMS](https://botble.com/)

## License

MIT License - Free to use for personal and commercial projects.

## Changelog

### Version 1.0.0 (2025)
- ✅ Initial release
- ✅ Current weather and forecast display
- ✅ Multiple display styles (default, widget)
- ✅ Night mode support
- ✅ Multi-language support
- ✅ Geocoding integration
- ✅ Smart caching system
- ✅ Responsive design
- ✅ Error handling

---

**Made with ❤️ by Ishtiaq Ahmed for Botble CMS Community**

