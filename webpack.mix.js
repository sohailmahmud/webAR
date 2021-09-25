const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js([
    'resources/js/app.js', 
    'resources/js/sb-admin-2/jquery.easing.js',
    'resources/js/sb-admin-2/sb-admin-2.js',
    // colorpicker
    'resources/js/colorpicker/spectrum.min.js',
    'resources/js/main.js'], 
    'public/js'
    );


// ==================== AR ===================== //

// JS

// Header scripts

mix.js([
    'resources/js/ar/aframe-1.0.4.js',],
    'public/js/ar/aframe.js');

mix.js([
    'resources/js/ar/aframe-ar-components.js',
    'resources/js/ar/aframe-chromakey-material.min.js',
    'resources/js/ar/aframe-extras.loaders.min.js',
    'resources/js/ar/aframe-gif-shader.min.js',],
    'public/js/ar/aframe-components.js');

// Bottom scripts
mix.js([
    // Default packages: JQuery, Bootstrap, Popper, Axios
    'resources/js/ar/bootstrap.js', 
    // Custom Javascript
    'resources/js/ar/app.js'],
    'public/js/ar/app.js');

// CSS
mix.styles([
        // Bootstrap
        'resources/css/ar/bootstrap-4.5.3.min.css',
        // Custom CSS
        'resources/css/ar/custom.css'], 
        'public/css/ar/ar.css');
