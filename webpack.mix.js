const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .js(`${source}/resources/js/simple-weather.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/simple-weather.scss`, `${dist}/css`)

// Copy fonts from public to dist (fonts are already in public/fonts)
if (require('fs').existsSync(`${source}/public/fonts`)) {
    mix.copyDirectory(`${source}/public/fonts`, `${dist}/fonts`)
}

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/simple-weather.js`, `${source}/public/js`)
        .copy(`${dist}/css/simple-weather.css`, `${source}/public/css`)
}

