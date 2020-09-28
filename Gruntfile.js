module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        version: {
            php: {
                options: {
                    pkg: {
                        version: function () {
                            var s = grunt.file.read('controller.php');
                            var re = /\$pkgVersion[\s*]=[\s*][\'|\"](.*)[\'|\"]/g
                            var m = re.exec(s);

                            if (m.length) {
                                return m[1];
                            }

                            return false;
                        }()
                    },
                    prefix: '@version\\s*'
                },
                src: [
                    'dist/*.php',
                    'dist/**/*.php',
                    'dist/**/**/*.php',
                    'dist/**/**/**/*.php',
                    'dist/**/**/**/**/*.php'
                ]
            }
        },
        composer: {
            options: {
                usePhp: true,
                composerLocation: './node_modules/getcomposer/composer.phar'
            },
            dev: {
                options: {
                    flags: ['ignore-platform-reqs']
                }
            },
            release: {
                options: {
                    flags: ['no-dev']
                }
            }
        },
        copy: {
            main: {
                files: [
                    {src: ['controller.php'], dest: "dist/", filter: 'isFile'},
                    {src: ['icon.png'], dest: "dist/", filter: 'isFile'},
                    {src: ['INSTALL.TXT'], dest: "dist/", filter: 'isFile'},
                    {src: ['LICENSE.TXT'], dest: "dist/", filter: 'isFile'},
                    {src: ['CHANGELOG'], dest: "dist/", filter: 'isFile'},
                    {src: ['blocks/**'], dest: "dist/"},
                    {src: ['controllers/**'], dest: "dist/"},
                    {src: ['elements/**'], dest: "dist/"},
                    {src: ['jobs/**'], dest: "dist/"},
                    {src: ['routes/**'], dest: "dist/"},
                    {src: ['single_pages/**'], dest: "dist/"},
                    {src: ['src/**'], dest: "dist/"},
                    {src: ['vendor/**'], dest: "dist/"}
                ]
            }
        },
        compress: {
            main: {
                options: {
                    archive: 'build/drop_box.zip'
                },
                files: [
                    {src: ['**'], dest: 'drop_box', expand: true, cwd: 'dist/'}
                ]
            }
        },
        clean: {
            dist: ['dist'],
            composer: ['vendor', 'composer.lock']
        },
        phpcsfixer: {
            app: {
                dir: 'dist'
            },
            options: {
                bin: './vendor/friendsofphp/php-cs-fixer/php-cs-fixer',
                usingCache: "no",
                quiet: true
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-php-cs-fixer');
    grunt.loadNpmTasks('grunt-version');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', [
        'clean:composer',
        'composer:release:install',
        'clean:dist',
        'copy',
        //'version',
        'clean:composer',
        'composer:dev:install',
        //'phpcsfixer',
        'compress:main',
        'clean:dist'
    ]);
};