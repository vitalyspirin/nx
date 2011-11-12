# NX

## URL layouts

NX supports the following URL constructions:

    foobar.com/
    foobar.com/controller[?args]
    foobar.com/controller/id[?args]
    foobar.com/controller/action[?args]
    foobar.com/controller/action/id[?args]

## Server Configurations

### nginx

(Note that you will need to change the root directory according to your filesystem.  In this example, /srv/http/nx/ is the project root.)

	server {
	     server_name     nx;
	     root            /srv/http/nx/app/public;
	     index           index.php index.html;

	     access_log      /var/log/nginx/nx/access.log;
	     error_log       /var/log/nginx/nx/error.log;

	     location / {
            try_files $uri /index.php?url=$uri;
	     }

	     location ~ \.php$ {
            fastcgi_pass    unix:/var/run/php-fpm/php-fpm.sock;
            fastcgi_index   index.php;
            fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include         fastcgi_params;
	     }
	 }

### lighttpd

    TODO

### apache

    TODO
