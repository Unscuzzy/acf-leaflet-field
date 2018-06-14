/**
 * GULPFILE
 *
 * Tasks:
 * - pot    Build/update traduction zz-acf-leaflet-field.pot file
 */

let gulp = require('gulp'),
    wpPot = require('gulp-wp-pot'),
    zip = require('gulp-zip');

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

// Build WordPress zip
gulp.task('zip', () => {
    return gulp.src([
        './**/*',
        '!./.gitignore',
        '!./gulpfile.js',
        '!./package.json',
        '!./package-lock.json',
        '!./README.md',
        '!./{node_modules,node_modules/**/*}',
        '!./acf-leaflet-field.zip',
    ])
        .pipe(zip('acf-leaflet-field.zip'))
        .pipe(gulp.dest('./'));
});