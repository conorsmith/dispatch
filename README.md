# Dispatch

This application backs up multiple databases on a server to a single remote location eg AWS S3, Dropbox, etc.

## TODO

* __Better Configuration__
    * Currently only one target storage location should be set in the `storage.php` config file
    * The output path should be configurable
    * Perhaps alternate config styles should be supported, such as YAML
* __Dynamic Providers__
    * Service providers for Flysystem and database types should be loaded dynamically based on the config file(s)
