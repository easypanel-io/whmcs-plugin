<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/sdk.php';

use Ramsey\Uuid\Uuid;
use WHMCS\Database\Capsule;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

/**
 * WHMCS SDK Sample Provisioning Module
 *
 * Provisioning Modules, also referred to as Product or Server Modules, allow
 * you to create modules that allow for the provisioning and management of
 * products and services in WHMCS.
 *
 * This sample file demonstrates how a provisioning module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Provisioning Modules are stored in the /modules/servers/ directory. The
 * module name you choose must be unique, and should be all lowercase,
 * containing only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "provisioningmodule" and therefore all
 * functions begin "provisioningmodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _ConfigOptions
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/provisioning-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license https://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function easypanel_MetaData()
{
    return array(
        'DisplayName' => 'EasyPanel Provisioning Module',
        'APIVersion' => '1.0', // Use API Version 1.1
        'RequiresServer' => false, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '80', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '443', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
 */
function easypanel_ConfigOptions()
{
    return array(
        // a text field type allows for single line text input
        'Host' => array(
            'Type' => 'text',
            'Size' => '1024',
            'Default' => '',
            'Description' => 'Enter the URL of your EasyPanel instance here',
        ),
        'API Key' => array(
            'Type' => 'text',
            'Size' => '1024',
            'Default' => '',
            'Description' => 'Enter the API Key of your EasyPanel instance here',
        ),
        "Template" => [
            "FriendlyName" => "Template Name",
            "Type" => "dropdown", # Dropdown Choice of Options
            //"Options" => "ackee,activepieces,actualbudget,adguardhome,adminer,alltube,ampache,anonupload,answer,appsmith,audiobookshelf,authorizer,barrage,baserow,bazarr,blender,bookstack,botpress,budibase,bytebase,cachet,calcom,calibre,changedetection,chatpad,chatwoot,chromium,clickhouse,cockpit,codex-docs,collabora-office,commento,commentoplusplus,coralproject,corteza,cryptpad,cubejs,cusdis,cyberchef,dashdot,dashy,deluge,directus,docker-registry,documize,dokuwiki,domainmod,dozzle,drone-runner,drone,duplicati,easyshortener,element,emby,espocrm,etherpad,evobot,excalidraw,fider,filebrowser,filerun,filezilla,firefox,fireshare,flame,flarum,flowise,focalboard,freescout,freshrss,ghost-file-sharing,ghost,gitea,github-desktop,glitchtip,gogs,gotenberg,gotify,gotosocial,grafana,grav,grayduckmail,hammond,hastebin,hastypaste,hasura,hedgedoc,heimdall,hiccup,homarr,homeassistant,homer,humhub,icecoder,imgproxy,isso,ittools,jellyfin,jellyseerr,jirafeau,joplin,kanboard,kavita,keila,keycloak,komga,kopia,kutt,langflow,languagetool,lavalink,lenpaste,libreoffice,libretranslate,limesurvey,linkding,linkstack,listmonk,logseq,lychee,lynx,mailcrab,mailhog,mastodon,matomo,matrix-synapse,mattermost,mcaptcha,mealie,meilisearch,memos,metabase,metube,minecraft-server,miniflux,mongo-express,monica,mstream,muse,mysql-backup,n8n,nango,navidrome,netbox,nextcloud,nocobase,nocodb,nodebb,nodered,ntfy,nzbget,octobox,octoprint,odoo,onedev,openblocks,openproject,openspeedtest,opera,outline,owncast,owncloud,oxigen,papercups,paperlessngx,passbolt,peertube,pgvector,pgweb,photoprism,photoview,phpmyadmin,pidgin,planka,plausible,plex,ploi-roadmap,pocketbase,portainer,postgres-backup,powerdns-admin,prowlarr,psitransfer,pterodactyl-panel,pufferpanel,qdrant,rabbitmq,radarr,radicale,rallly,remmina,restreamer,rocketchat,rotki,roundcube,rsshub,searxng,serpbear,servas,shaarli,shiori,shynet,silicon-notes,simpletorrent,soketi,sonarr,sshwifty,statping-ng,strapi,substreamer,suitecrm,supertokens-core,syncthing,tandoor,tautulli,thelounge,thumbor,tolgee,tooljet,trilium,typebot,typesense,umami,unleash,uptimekuma,vaultwarden,verdaccio,vscode-server,wakatime,weaviate,webcord,weblate,wekan,wg-easy,whiteboard,wikijs,windmill,wizarr,wordpress,wps-office,yao,zipline",
            "Options" => "ghost,wordpress",
            "Description" => "Select the template you want to provision.",
            "Default" => "ghost",
        ],
        'CPU limit' => array(
            'Type' => 'text',
            'Size' => '1024',
            'Default' => '1024',
            'Description' => 'Enter the CPU use limit number',
        ),
        'RAM limit (MB)' => array(
            'Type' => 'text',
            'Size' => '1024',
            'Default' => '1024',
            'Description' => 'Enter the RAM use limit in MB',
        ),
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function easypanel_CreateAccount(array $params)
{
    ini_set('display_errors', 0);
    try {
        Capsule::schema()->create(
            'tbleasypanel',
            function ($table) {
                /** @var \Illuminate\Database\Schema\Blueprint $table */
                $table->uuid('id')->primary();
                $table->string('service');
                $table->string('prefix');
                $table->boolean('primary');
                $table->integer('port')->unsigned();
                $table->uuid('client');
                $table->timestamps();
                $table->foreign('client')
                    ->references('uuid')
                    ->on('tblclients')
                    ->onDelete('cascade');
            }
        );
    } catch (\Exception $e) {
        //echo "Unable to create my_table: {$e->getMessage()}";
    }
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        $sdk->createProject($clientId);
        // TODO: Create Services from Git and Box
        $services = $sdk->createServiceFromSchema($clientId, $params['configoption3'], $params['serviceid'], $params['domain']);
        foreach ($services['services'] as $service) {
            Capsule::table('tbleasypanel')->insert([
                'id' => Uuid::uuid4()->toString(),
                'service' => $service,
                'prefix' => $services['prefix'],
                'primary' => preg_replace($services['prefix'], "", $service) == $params['configoption3'] ? true : false,
                'client' => $clientId,
            ]);
            $sdk->updateResources($clientId, $service, $params['configoption4'], $params['configoption5']);
        }
        

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function easypanel_SuspendAccount(array $params)
{
    try {
        // Call the service's suspend function, using the values provided by
        // WHMCS in `$params`.
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->disableService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function easypanel_UnsuspendAccount(array $params)
{
    try {
        // Call the service's unsuspend function, using the values provided by
        // WHMCS in `$params`.
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->enableService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function easypanel_TerminateAccount(array $params)
{
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->destroyService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function provisioningmodule_ChangePassword(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function provisioningmodule_ChangePackage(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Renew an instance of a product/service.
 *
 * Attempt to renew an existing instance of a given product/service. This is
 * called any time a product/service invoice has been paid.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function provisioningmodule_Renew(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function provisioningmodule_TestConnection(array $params)
{
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see provisioningmodule_buttonOneFunction()
 *
 * @return array
 */
/*function easypanel_AdminCustomButtonArray()
{
    return array(
        "Button 1 Display Value" => "buttonOneFunction",
        "Button 2 Display Value" => "buttonTwoFunction",
    );
}*/

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function easypanel_ClientAreaCustomButtonArray()
{
    return array(
        "Start service" => "startService",
        "Restart service" => "restartService",
        "Stop service" => "stopService",
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see provisioningmodule_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function easypanel_stopService(array $params)
{
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->disableService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see provisioningmodule_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function easypanel_restartService(array $params)
{
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->restartService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see provisioningmodule_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function easypanel_startService(array $params)
{
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $params['userid'])
            ->first(['uuid']);
        $clientId = $client->uuid;
        $services = Capsule::table('tbleasypanel')
            ->where('client', $clientId)
            ->where('prefix', $params['serviceid']."_")
            ->get();
        $sdk = new EasyPanelSDK($params['configoption1'], $params['configoption2']);
        foreach ($services as $service) {
            $sdk->enableService($clientId, $service->service);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see provisioningmodule_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function provisioningmodule_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();

        // Return an array based on the function's response.
        return array(
            'Number of Apples' => (int) $response['numApples'],
            'Number of Oranges' => (int) $response['numOranges'],
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'Something Editable' => '<input type="hidden" name="provisioningmodule_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="provisioningmodule_uniquefieldname"'
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see provisioningmodule_AdminServicesTabFields()
 */
function provisioningmodule_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['provisioningmodule_original_uniquefieldname'])
        ? $_REQUEST['provisioningmodule_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['provisioningmodule_uniquefieldname'])
        ? $_REQUEST['provisioningmodule_uniquefieldname']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'provisioningmodule',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function provisioningmodule_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function provisioningmodule_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function provisioningmodule_ClientArea(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        $extraVariable1 = 'abc';
        $extraVariable2 = '123';

        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'extraVariable1' => $extraVariable1,
                'extraVariable2' => $extraVariable2,
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}