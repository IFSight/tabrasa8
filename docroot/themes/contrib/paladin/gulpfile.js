/*
We keep things pretty simple here:
  - Require Gulp
  - Require Gulp Load Plugins and load all dependencies (gulp-rename and gulp-sourcemaps)
  - Require gulp-sass, the greatest gift.
*/
var gulp = require('gulp'),
  $      = require('gulp-load-plugins')(),
  sass   = require('gulp-sass');

/* 
The main styles compliation function:
  1. Set source to be all .scss files in the sass directory.
  2. Create source maps.
  3. Compile the sass.
  4. Remove the directories so all CSS files are output into the specified dir.
  5. Write the sourcemaps to the specified dir.
  6. Specify the dir as css.
*/
gulp.task('styles', function(){
  return gulp.src('sass/**/*.scss')
    .pipe($.sourcemaps.init())
    .pipe(sass())
    .pipe($.rename({dirname: ''}))
    .pipe($.sourcemaps.write('./'))
    .pipe(gulp.dest('css'))
});

/*
The main watch function:
  1. Look in the SASS dir for changes to .scss files.
  2. Run the 'styles' task on anything that changes.
*/

gulp.task('watch', function(){
  gulp.watch('sass/**/*.scss', gulp.series('styles'));
});