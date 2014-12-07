module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      css: {
        files: [
          '**/*.sass',
          '**/*.scss'
        ],
        tasks: ['compass']
      }
    },
    compass: {
      dist: {
        options: {
          sassDir: 'www/app/stylesheets/sass',
          cssDir: 'www/app/stylesheets/css',
          outputStyle: 'compressed'
        }
      }
    }
  });

  // Load the Grunt plugins.
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Register the default tasks.
  grunt.registerTask('default', ['watch']);
};