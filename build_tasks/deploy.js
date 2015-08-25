module.exports = function (grunt) {
  'use strict';

  var config = grunt.config;

  /** -----------------------------------------
   * Deploy
   *
   * Run all tasks
   -------------------------------------------*/

  grunt.registerTask('deploy', ['browserify', 'uglify', 'sass', 'cmq', 'postcss']);

};
