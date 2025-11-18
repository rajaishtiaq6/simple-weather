class SimpleWeatherApp {
    constructor(elementId, atts, preloadedFeed = null) {
        this.elementId = elementId;
        this.atts = atts;
        this.weatherFeed = preloadedFeed;
        this.currentWeather = null;
        this.error = null;
        this.loading = true;
        this.element = document.getElementById(`simple-weather--${this.elementId}`);
        this.i18n = window.SimpleWeather?.i18n || {
            loading: 'Loading weather...',
            errorLoadData: 'Could not load weather data',
            errorConnect: 'Could not connect to weather service',
            current: 'Current',
            humidity: 'Humidity',
            clouds: 'Clouds',
            wind: 'Wind'
        };

        this.init();
    }

    init() {
        if (!this.element) return;

        // If we don't have preloaded data, fetch it
        if (!this.weatherFeed) {
            this.fetchWeather();
        } else {
            this.processWeatherData(this.weatherFeed);
            this.render();
        }
    }

    async fetchWeather() {
        this.loading = true;
        this.render();

        try {
            // Get CSRF token from meta tag or cookie
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || this.getCookie('XSRF-TOKEN');

            const response = await fetch(window.SimpleWeather.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(this.atts)
            });

            const data = await response.json();

            // Check for errors at multiple levels
            if (data.error && data.data && data.data.error) {
                // Nested error: data.error = false, but data.data.error = true
                this.error = data.data.message || this.i18n.errorLoadData;
            } else if (data.error) {
                // Top level error
                this.error = data.message || this.i18n.errorLoadData;
            } else if (data.data && data.data.error) {
                // Data level error
                this.error = data.data.message || this.i18n.errorLoadData;
            } else {
                // Success - process weather data
                this.processWeatherData(data.data || data);
            }
        } catch (error) {
            this.error = this.i18n.errorConnect;
        } finally {
            this.loading = false;
            this.render();
        }
    }

    processWeatherData(data) {
        if (data.current) {
            this.currentWeather = data.current;
        }
        if (data.daily) {
            this.weatherFeed = data.daily;
        }
    }

    render() {
        if (!this.element) return;

        if (this.loading) {
            this.element.innerHTML = `<div class="simple-weather__loading">${this.i18n.loading}</div>`;
            return;
        }

        if (this.error) {
            this.element.innerHTML = `<div class="simple-weather__error">${this.error}</div>`;
            return;
        }

        const style = this.atts.style || 'default';
        const units = this.atts.units === 'metric' ? 'C' : 'F';
        const unitsWind = this.atts.units === 'metric' ? 'km/h' : 'mph';

        if (style === 'widget') {
            this.element.innerHTML = this.renderWidget(units, unitsWind);
        } else {
            this.element.innerHTML = this.renderDefault(units);
        }
    }

    renderDefault(units) {
        let html = '';

        // Location heading
        if (this.atts.location) {
            html += `<div class="simple-weather__location">${this.atts.location}</div>`;
        }

        // Current weather
        if (this.currentWeather && this.currentWeather.dt) {
            const showUnits = this.atts.show_units === 'yes';

            html += '<span class="simple-weather__day simple-weather__day--current">';
            html += `<span class="simple-weather__date">${this.i18n.current}</span>`;
            html += `<i class="sw ${this.getWeatherIcon(this.currentWeather)}"></i>`;
            html += `<em class="simple-weather__temp">${this.formatTemp(this.currentWeather.temp)} &deg;${showUnits ? units : ''}</em>`;
            html += '</span>';
        }

        // Forecast
        if (this.weatherFeed && Array.isArray(this.weatherFeed)) {
            const days = parseInt(this.atts.days || 1);
            const showDate = this.atts.show_date === 'yes';
            const showUnits = this.atts.show_units === 'yes';

            this.weatherFeed.forEach((day, index) => {
                if (index < days && day.dt) {
                    html += '<span class="simple-weather__day">';
                    if (showDate) {
                        html += `<span class="simple-weather__date">${this.formatDate(day.dt, this.atts.date)}</span>`;
                    }
                    html += `<i class="sw ${this.getWeatherIcon(day)}"></i>`;
                    html += `<em class="simple-weather__temp">${this.formatTemp(day.temp.max)} &deg;`;
                    if (day.temp.min) {
                        html += `<em class="simple-weather__temp-min">${this.formatTemp(day.temp.min)} &deg;</em>`;
                    }
                    html += `${showUnits ? units : ''}</em>`;
                    html += '</span>';
                }
            });
        }

        return html;
    }

    renderWidget(units, unitsWind) {
        let html = '<div class="simple-weather-widget">';

        // Location heading
        if (this.atts.location) {
            html += `<h4 class="widget_title">${this.atts.location}</h4>`;
        }

        // Current temperature and details
        if (this.currentWeather) {
            html += '<div class="temp">';
            if (this.currentWeather.temp) {
                html += `<span class="degrees">${this.formatTemp(this.currentWeather.temp)} &deg;</span>`;
            }
            html += '<span class="details">';
            if (this.currentWeather.humidity) {
                html += `${this.i18n.humidity}: <em class="float-right">${this.currentWeather.humidity}%</em><br>`;
            }
            if (this.currentWeather.clouds) {
                html += `${this.i18n.clouds}: <em class="float-right">${this.currentWeather.clouds}%</em><br>`;
            }
            if (this.currentWeather.wind_speed) {
                html += `${this.i18n.wind}: <em class="float-right">${this.currentWeather.wind_speed}<small>${unitsWind}</small></em>`;
            }
            html += '</span></div>';

            // Weather description
            if (this.currentWeather.weather && this.currentWeather.weather[0]) {
                html += `<div class="summary">${this.currentWeather.weather[0].description}</div>`;
            }
        }

        // Forecast table
        if (this.weatherFeed && Array.isArray(this.weatherFeed)) {
            const days = parseInt(this.atts.days || 1);
            html += '<div class="simple-weather-table">';
            this.weatherFeed.forEach((day, index) => {
                if (index < days && day.dt) {
                    html += '<div class="simple-weather-table__row">';
                    html += `<div class="simple-weather-table__date">${this.formatDate(day.dt, this.atts.date)}</div>`;
                    html += `<div class="simple-weather-table__icon"><i class="sw ${this.getWeatherIcon(day)}"></i></div>`;
                    html += `<div class="simple-weather-table__temp">${this.formatTemp(day.temp.max)}&deg;`;
                    html += `<span class="simple-weather-table__temp-min">${this.formatTemp(day.temp.min)} &deg;</span>`;
                    html += '</div></div>';
                }
            });
            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    getWeatherIcon(weatherData) {
        if (!weatherData || !weatherData.weather || !weatherData.weather[0]) {
            return 'wi-day-sunny';
        }
        const code = weatherData.weather[0].id;
        return this.mapWeatherCodeToIcon(code);
    }

    mapWeatherCodeToIcon(code) {
        if (code >= 200 && code < 300) return 'wi-thunderstorm';
        if (code >= 300 && code < 400) return 'wi-sprinkle';
        if (code >= 500 && code < 600) return 'wi-rain';
        if (code >= 600 && code < 700) return 'wi-snow';
        if (code >= 700 && code < 800) return 'wi-fog';
        if (code === 800) return 'wi-day-sunny';
        if (code > 800) return 'wi-cloudy';
        return 'wi-day-sunny';
    }

    formatTemp(temp) {
        return Math.round(temp);
    }

    formatDate(timestamp, format) {
        const date = new Date(timestamp * 1000);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        if (format === 'dddd') {
            return days[date.getDay()];
        }

        return date.toLocaleDateString();
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
        }
        return null;
    }
}

// Initialize all weather widgets
function initSimpleWeather() {
    if (typeof window.SimpleWeatherAtts !== 'undefined') {
        Object.keys(window.SimpleWeatherAtts).forEach(function(id) {
            const atts = window.SimpleWeatherAtts[id];
            const preloadedFeed = window.SimpleWeatherFeeds ? window.SimpleWeatherFeeds[id] : null;
            new SimpleWeatherApp(id, atts, preloadedFeed);
        });
    }
}

// Try to initialize on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSimpleWeather);
} else {
    // DOM already loaded, initialize immediately
    initSimpleWeather();
}

// Also expose globally for manual initialization
window.initSimpleWeather = initSimpleWeather;


