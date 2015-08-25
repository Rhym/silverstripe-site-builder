module.exports = function (grunt) {
  'use strict';

  var config = grunt.config;

  /** =========================================
   * CSS
   ===========================================*/

  /** -----------------------------------------
   * Sass
   * ----------------------------------------*/

  config.set('sass.all', {
    files: [{
      '<%= directories.silverstripeSiteBuilder %>/css/main.css': '<%= directories.silverstripeSiteBuilder %>/scss/main.scss'
    }]
  });

  /** -----------------------------------------
   * Combine Media Queries
   * ----------------------------------------*/

  config.set('cmq.all', {
    options: {
      log: false
    },
    files: [{
      '<%= directories.silverstripeSiteBuilder %>/css/': ['<%= directories.silverstripeSiteBuilder %>/css/main.css']
    }]
  });

  /** -----------------------------------------
   * PostCSS
   * ----------------------------------------*/

  config.set('postcss.all', {
    options: {
      map: true,
      processors: [
        require('pixrem')(),
        require('autoprefixer-core')({
          browsers: 'last 3 versions'
        }),
        require('cssnano')()
      ]
    },
    dist: {
      src: '<%= directories.silverstripeSiteBuilder %>/css/*.css'
    }
  });

  /** =========================================
   * Watch
   ===========================================*/

  config.set('watch', {
    files: ['<%= directories.silverstripeSiteBuilder %>/scss/**/*.scss'],
    tasks: ['sass', 'cmq', 'postcss'],
    options: {
      spawn: false
    }
  });

};
