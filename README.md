## What is DocSys
DocSys is an account document upload/archive system.
## Is it done?
Noe.
## Colors
 (#EABE6C, #891652, #d4a258, #FFEDD8).

## To-do
pdfam pievienot lapu: 


## Dependencies
sudo apt install php-imagick
windows: imagick dll's on C:/php/ext

https://mlocati.github.io/articles/php-windows-imagick.htmlnpm

On Linux (deploy to Ubuntu/Debian)
bash
# apt-get update
# apt-get install -y imagemagick php-imagick
# systemctl restart apache2   # or php-fpm, etc.
Then confirm imagick is loaded with php -m | grep imagick.

bash
# apt-get update
# apt-get install -y libreoffice
shell_exec('libreoffice --headless --convert-to pdf --outdir ' . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($originalPath));
…and remove the Windows path references.

## Ko man vjg no oto
cname uz abrams
gcloud admin perms