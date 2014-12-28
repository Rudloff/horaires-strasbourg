/*jslint node: true */
module.exports = function (grunt) {
    'use strict';
    grunt.initConfig({
        cssmin: {
            combine: {
                files: {
                    'dist/main.css': ['style.css']
                }
            }
        },
        watch: {
            styles: {
                files: ['*.css'],
                tasks: ['cssmin']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['cssmin']);
};
