<?php

namespace ImageFocus;

/**
 * The class responsible for loading WordPress functionality and other classes
 *
 * Class ImageFocus
 * @package ImageFocus
 */
class ImageFocus
{
    public function __construct()
    {
        $this->addHooks();
        $this->loadEarlyClasses();
    }

    /**
     * Make sure all hooks are being executed.
     */
    private function addHooks()
    {
        add_action('admin_init', [$this, 'loadTextDomain']);
        add_action('admin_init', [$this, 'loadAdminClasses']);
    }

    /**
     * Load the gettext plugin textdomain located in our language directory.
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain(IMAGEFOCUS_TEXTDOMAIN, false, IMAGEFOCUS_LANGUAGES);
    }
    
    /**
     * Load classes that ensure focus points are always respected and to prevent WordPress from
     * mistakenly resizing images back to the default focus point.
     *
     * We want to run it as early as possible to prevent other plugins or themes from generating
     * thumbnails with the wrong focus point. A typical case of that can be image regeneration
     * during a REST API request. Those will be executed under `rest_api_init` hook, which happens
     * in the non-admin context, so we cannot use `admin_init` to load the service.
     * We could use `init` or even earlier `plugins_loaded`, but since we don't rely on any
     * WordPress functionality that is loaded later, we can just instantiate the service directly.
     *
     * @since 0.10.2
     */
    public function loadEarlyClasses()
    {
        new ResizeService();
    }
    
    /**
     * Load all necessary classes for the Admin UI.
     */
    public function loadAdminClasses()
    {
        if (current_user_can('upload_files') === false) {
            return false;
        }

        new FocusPoint();
    }
}
