# Dynamic Weather Fetcher

**Dynamic Weather Fetcher** is a WordPress plugin that allows users to fetch weather data from the [OpenWeatherMap](https://openweathermap.org/) API based on city input and display it on the site.

## Features

- Fetches and displays current weather data from OpenWeatherMap API.
- Allows users to input a city name and see the weather details.
- Caches weather data for 30 minutes to avoid redundant API calls.
- Option to set your own OpenWeatherMap API key in the plugin settings.
- Shortcode support to display the weather anywhere on your site.
- Admin page to manage plugin settings.

## Installation

1. Download the plugin as a `.zip` file.
2. In your WordPress dashboard, go to **Plugins** > **Add New** > **Upload Plugin**.
3. Choose the downloaded `.zip` file and click **Install Now**.
4. After installation, click **Activate** to enable the plugin.

## Configuration

1. After activating the plugin, navigate to **Weather Fetcher** in the WordPress admin sidebar.
2. Go to **Settings** under the **Weather Fetcher** menu.
3. Enter your OpenWeatherMap API key and save the settings.

## Usage

### Fetch Weather via Admin Page

1. Go to **Weather Fetcher** in the admin sidebar.
2. Enter a city name in the input field and click **Fetch Weather**.
3. The current weather data for the city will be displayed including temperature, weather description, and an icon.

### Display Weather on Frontend

You can display the weather anywhere on your WordPress site using a shortcode. Simply use the following shortcode in a post, page, or widget:

## Changelog

### Version 1.2
- Added caching mechanism for weather data (30 minutes).
- Improved API error handling and user messages.
- Added settings page to input and save OpenWeatherMap API key.
  
### Version 1.1
- Initial release with basic weather fetching from OpenWeatherMap API.

## Author

**Haseeb Mushtaq**  
[Your Website](https://github.com/AlamBinary01)

## License

This plugin is licensed under the GPLv2 (or later) License.

The license text can be found in the `LICENSE` file, or you can read it here:

### GPLv2 License

This program is free software; you can redistribute it and/or modify it under the terms of the **GNU General Public License** as published by the **Free Software Foundation**, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the **GNU General Public License** for more details.

You should have received a copy of the **GNU General Public License** along with this program; if not, write to the **Free Software Foundation, Inc.**, **51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA**.
