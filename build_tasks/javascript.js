module.exports = function (grunt) {
  'use strict';

  var config = grunt.config;

  /** =========================================
   * Javascript
   ===========================================*/

  /** -----------------------------------------
   * Browserify
   * ----------------------------------------*/

  config.set('browserify.all', {
    files: {
      '<%= directories.silverstripeSiteBuilder %>/javascript/main.js': ['<%= directories.silverstripeSiteBuilder %>/javascript/src/init.js']
    }
  });

  /** -----------------------------------------
   * Uglify
   * ----------------------------------------*/

  config.set('uglify.all', {
    options: {
      preserveComments: 'some'
    },
    src: '<%= directories.silverstripeSiteBuilder %>/javascript/main.js',
    dest: '<%= directories.silverstripeSiteBuilder %>/javascript/main.min.js'
  });

  /** =========================================
   * Watch
   ===========================================*/

  config.set('watch.javascript', {
    files: ['<%= directories.silverstripeSiteBuilder %>/javascript/src/*.js', '<%= directories.silverstripeSiteBuilder %>/javascript/src/**/*.js'],
    tasks: ['browserify'],
    options: {
      interrupt: true,
      spawn: false
    }
  });

};
