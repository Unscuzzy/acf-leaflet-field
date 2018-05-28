/**
 * GULPFILE
 *
 * Tasks:
 * - pot    Build/update traduction zz-acf-leaflet-field.pot file
 */

let gulp = require('gulp'),
    wpPot = require('gulp-wp-pot');

gulp.task('default');

// Generates pot files for this WordPress plugin or theme.
gulp.task('pot', function () {
    return gulp.src('**/*.php')
        .pipe(wpPot({
            domain: 'zz',
            package: 'ACF Leaflet Field'
        }))
        .pipe(gulp.dest('lang/zz.pot'));
});