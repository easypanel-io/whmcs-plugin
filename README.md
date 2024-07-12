## WHMCS

WHMCS Module for the [Easy Panel](https://github.com/easypanel-io).


## Configuration support

Please use the [EasyPanel Discord](https://discord.gg/SARw8nbpnZ) for configuration related support instead of GitHub issues.


## Installation


1. Download/Git clone this repository.
2. Move the ``easypanel/`` folder into ``<path to whmcs>/modules/servers/``.
3. Login to your Easypanel and Navigate to Settings > Users
4. Click ``Generate API Key``
5. Copy the API key by clicking the Clipboard Icon
4. In WHMCS 8+ navigate to System Settings → Servers. In WHMCS 7 or below navigate to Setup → Products/Services → Servers
5. Create new server, fill the name with anything you want, hostname as the url to the panel either as an IP or domain. For example: ``123.123.123.123`` or ``my.easypanel.com``
6. Change Server Type to EasyPanel, leave username empty, fill the password field with your generated API Key.
7. Tick the "Secure" option if your panel is using SSL.
8. Confirm that everything works by clicking the Test Connection button -> Save Changes.
9. Go back to the Servers screen and press Create New Group, name it anything you want and choose the created server and press the Add button, Save Changes.
10. Navigate to Setup > Products/Services > Products/Services
11. Create your desired product (and product group if you haven't already) with the type of Other and product name of anything -> Continue.
12. Click the Module Settings tab, choose for Module Name EasyPanel and for the Server Group the group you created in step 8.
13. Fill all non-optional fields, and you are good to go!

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
