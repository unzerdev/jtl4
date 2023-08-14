module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                options: {
                    style: 'compressed',
                },
                files: {
                    //'frontend/css/heidelpay.min.css': 'frontend/scss/heidelpay.scss',
                    'adminmenu/css/admin.css': 'adminmenu/scss/admin.scss'
                }
            }
        },
        autoprefixer:{
            dist: {
                files:{
                    //'frontend/css/heidelpay.min.css': 'frontend/css/heidelpay.min.css',
                    'adminmenu/css/admin.css': 'adminmenu/css/admin.css'
                }
            }
        },
        uglify: {
            dist: {
                files: {
                    'frontend/js/heidelpay.min.js': ['frontend/js/heidelpay.js']
                }
            }
        },
        watch: {
            sass: {
                files: ['frontend/scss/**/*.scss', 'adminmenu/scss/**/*.scss'],
                tasks: ['sass','autoprefixer'],
                options: {
                    atBegin: true
                }
            },
            js: {
                files: ['frontend/js/*.js', '!frontend/js/*.min.js'],
                tasks: ['uglify'],
                options: {
                    atBegin: true
                }
            }
        }
    });

    /* SCSS compilation */
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-autoprefixer');

    /* JS minify */
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task(s).
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default', ['watch']);

};
