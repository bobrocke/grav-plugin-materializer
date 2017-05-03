<?php
namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page\Page;

class MaterializerPlugin extends Plugin
{
	/**
	* @return array
	*/
	public static function getSubscribedEvents()
	{
		return [
				'onThemeInitialized' => ['onThemeInitialized', 0]
		];
	}

	/**
	* Initialize configuration
	*/
	public function onThemeInitialized()
	{
		if ($this->isAdmin()) {
			return;
		}

		$load_events = false;

		// if not always_load see if the theme expects to load the materializer plugin
		if (!$this->config->get('plugins.materializer.always_load')) {
				$theme = $this->grav['theme'];
				if (isset($theme->load_materializer_plugin) && $theme->load_materializer_plugin) {
					$load_events = true;
				}
		} else {
				$load_events = true;
		}

		if ($load_events) {
				$this->enable([
					'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
				]);
		}
	}


	/**
	* if enabled on this page, load the JS + CSS and set the selectors.
	*/
	public function onTwigSiteVariables()
	{
		$config = $this->config->get('plugins.materializer');
		$mode = $config['mode'] == 'production' ? '.min' : '';

		$materialize_bits = [];
		$currentVersion = '0.98.2';

		if ($config['use_cdn']) {
			if ($config['load_css']) {
					$materialize_bits[] = "https://cdnjs.cloudflare.com/ajax/libs/materialize/{$currentVersion}/css/materialize{$mode}.css";
					// Material Design Icons does not have a CDN
					$materialize_bits[] = "plugin://materializer/css/materialdesignicons{$mode}.css";
			}
			if ($config['load_js']) {
					$materialize_bits[] = "https://cdnjs.cloudflare.com/ajax/libs/materialize/{$currentVersion}/js/materialize{$mode}.js";
			}
		} else {
			if ($config['load_css']) {
					$materialize_bits[] = "plugin://materializer/css/materialize{$mode}.css";
					$materialize_bits[] = "plugin://materializer/css/materialdesignicons{$mode}.css";
			}
			if ($config['load_js']) {
					$materialize_bits[] = "plugin://materializer/js/materialize{$mode}.js";
			}
		}

		$assets = $this->grav['assets'];
		$assets->registerCollection('materialize', $materialize_bits);
		$assets->add('materialize', 100);
	}
}
