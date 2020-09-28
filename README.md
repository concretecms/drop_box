# Drop Box

This add-on enables big file uploads and combines the power of the concrete5 CMS together with the TUS-standard. With the included Drop Box block type you can easily integrate a draggable file upload area into your concrete5 site.

The included settings page allows you to configure the TUS server and even to define a separate S3 storage location. In any way the files are stored within the concrete5 file manager.

For end users all uploaded files are accessible through a public URL.

Admins can set up permissions and manage who is able upload files with the included permission type and of course all uploaded files can be managed with an nice looking dashboard page. 

## Contributing

If you have Vagrant installed on your development environment the easiest way to contribute this add-on is to run the vagrant script by executing the following command.

```
vagrant up
``` 

This command will perform the installation and create a virtual machine with Apache, PHP, phpMyAdmin, concrete5 CMS and this package. The package has a symlink to the current working directory on the host system. Means that any changes automatically applied in the virtual machine

## Installation

If you don't have vagrant installed on your development environment you need to run the following command. 

```
npm i
```

This will install the node packages and run a webpack build + composer install automatically on post install.

## Deployment

If you want to deploy this package run the following command.
                                                         
```
npm run deploy
```

This will create a zip file located at `build/drop_box.zip`. The zip archive contains the package which is cleaned up with PHP CS Fixer and don't contain any unnecessary files that are used in development. 

## TODO

- General
    - Test TUS server library
    - Implement S3 Storage
    - Implement Settings page
    - Implement Public URL Controller + Routing + Add "Get Public URL" menu item to context menu
    - Implement permissions check