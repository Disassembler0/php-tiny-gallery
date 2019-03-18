# PHP Tiny Gallery

Have you ever needed to quickly share a bunch of photos via your own web server? Have you just dumped them into a directory and settled for Apache's *mod_autoindex* directory listing? Have you thought that there has to be some open-source project which makes an actual browsable gallery with thumbnails out of these, preferably without any elaborate installation and configuration process? Well, if there hasn't been any until now, now there is.

## Description

`php-tiny-gallery` is a single simple stupid PHP script, which you drop along with the photos into the same directory, and it will turn your directory into an actual gallery. On the client side, the script uses [*jQuery 3*](https://jquery.com/) and [*Fancybox 3*](https://fancyapps.com/fancybox/3/) presentation library, fetched from *CloudFlare CDN*. When you first visit the gallery, the script will check if the thumbnails have been created yet and if not, it will create them via series of AJAX calls. On the server side, the script will create a thumbnail subdirectory, generate the thumbnails using *Imagick* or *GD2* image processing PHP extensions, and store them for future use.

## Requirements

- PHP 7.0 or newer
- `php-imagick` OR `php-gd2` image processing extension

## Usage

Just upload `index.php` to a directory from which you want to create a gallery.

## Configuration

Well... none. Which is kinda the whole point. :)

But if you really want to, on first 3 lines of the script you can set
- `THUMB_SIZE` for thumbnail size in pixels. The default is 200 px.
- `THUMB_DIR` for name of the thumbnail subdirectory. The default is `thumbs`.
- `ALLOWED_EXT` for file extensions which will be displayed and for which the thumbnails will be generated. The defaults are `['bmp', 'gif', 'jpe', 'jpeg', 'jpg', 'png']`.

## FAQ

**Q:** Can the script create thumbnails for videos (MP4, WEBM, ...) or documents (PDF, DOC, XLS, ...)?  
**A:** It can't, because it would need other modules for it. But you can hack it a little bit. The script expects the thumbnails in the `THUMB_DIR` with the same base name as the original file and `jpg` extension. First you need to manually create the thumbnails using whichever tool you like and upload them to the thumbnails directory. Then add the file extension to supported extensions. This will cause the script to display your thumbnail and link your file without attempting to create a new thumbnail, because it has been already created manually.

**Q:** I have added / removed / changed some files in the gallery. Do I need to do anything to reflect the changes?  
**A:** New files will be picked up and the thumbnails will be generated for them automatically. As for the update or removal - the old thumbnails won't be removed automatically, so you need to remove them manually. The same goes also for change of `THUMB_SIZE`.
