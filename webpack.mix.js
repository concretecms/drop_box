// Javascript dependencies are compiled with Laravel Mix https://laravel.com/docs/5.5/mix
let mix = require('laravel-mix');

mix
    .sass('resources/drop_box.scss', 'blocks/drop_box/view.css')
    .js('resources/drop_box.js', 'blocks/drop_box/view.js')