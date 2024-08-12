## WHMCS

WHMCS Module for the [Easy Panel](https://github.com/easypanel-io).


## Configuration support

Please use the [EasyPanel Discord](https://discord.gg/SARw8nbpnZ) for configuration related support instead of GitHub issues.


## Installation


1. Download the latest easypanel.zip file from [here](https://github.com/easypanel-io/whmcs-plugin/releases/latest).
2. Upload the ``easypanel.zip`` into ``<path to whmcs>/modules/servers/``.
3. Extract the ``easypanel.zip``
4. Login to your Easypanel and Navigate to ``Settings`` > ``Users``
5. Click ``Generate API Key``
6. Copy the ``API key`` by clicking the Clipboard Icon
7. Navigate to ``Setup/System Settings`` > ``Products/Services``
8. Click ``Create a New Product``
9. Under the Module settings within the Product you created Select ``EasyPanel Provisioning Module``
10. Choose a Server Group or leave it as None
11. Enter your Easypanel Host name under Host. Be sure to include https:// within the URL Example: https://easypanelhost.example
12. Select your preferred template for this product I.E. Ghost, Wordpress
13. Set any RAM limits in Megabytes for instance 1024 for 1GB
14. Set any CPU limits if desired
15. Paste your ``API Key`` you copied earlier into the ``API Key`` field
16. Click Save Changes and You're good to go. Just Copy this product/service to easily create more products etc.

## Credits

[Andrei](https://github.com/deiucanta) and [Mateus](https://github.com/mateuslacorte) involved in development of the Easy Panel and the WHMCS Module.


# FAQ

## Overwriting values through configurable options

Overwriting values can be done through either Configurable Options or Custom Fields.

Their name should be exactly what you want to overwrite.
Valid options: ``id, service``

This also works for any name of environment variables

Useful trick: You can use the | seperator to change the display name of the variable like this:
service|Application => Will be displayed as "Application" but will work correctly.



## How to enable module debug log

1. In WHMCS 7 or below navigate to Utilities > Logs > Module Log. For WHMCS 8.x navigate to System Logs > Module Log in the left sidebar.
2. Click the Enable Debug Logging button.
3. Do the action that failed again and you will have required logs to debug the issue. All 404 errors can be ignored.
4. Remember to Disable Debug Logging if you are using this in production, as it's not recommended to have it enabled.
