; (function ($, elementor) {
	'use strict';

	$(window).on('elementor/frontend/init', function () {
		var ModuleHandler = elementorModules.frontend.handlers.Base,
			ThreedText;

		function parseJSON(value) {
			if (!value || 'string' !== typeof value) {
				return null;
			}

			try {
				return JSON.parse(value);
			} catch (error) {
				return null;
			}
		}

		function unwrapAtomicValue(value) {
			if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
				return unwrapAtomicValue(value.value);
			}

			return value;
		}

		function normalizeAtomicSettings(settings) {
			if (!settings) {
				return null;
			}

			if (settings.active || settings.depth || settings.layers) {
				return settings;
			}

			if (unwrapAtomicValue(settings.ep_threed_text_active) !== true && unwrapAtomicValue(settings.ep_threed_text_active) !== 'yes') {
				return null;
			}

			return {
				active: 'yes',
				depth: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_depth) || 30), unit: 'px' },
				layers: parseInt(unwrapAtomicValue(settings.ep_threed_text_layers) || 8, 10),
				depth_color: unwrapAtomicValue(settings.ep_threed_text_depth_color) || '',
				perspective: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_perspective) || 500), unit: 'px' },
				fade: unwrapAtomicValue(settings.ep_threed_text_fade) ? 'yes' : '',
				event: unwrapAtomicValue(settings.ep_threed_text_event) || 'none',
				event_rotation: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_event_rotation) || 35), unit: 'deg' },
				event_direction: unwrapAtomicValue(settings.ep_threed_text_event_direction) || 'default'
			};
		}

		function applyThreeDText($heading, settings, forcedId) {
			if (!$heading.length || !settings || settings.active !== 'yes') {
				return;
			}

			var node = $heading.get(0);
			var headingId = forcedId || $heading.attr('id') || ('ep-atomic-' + Date.now() + '-' + Math.floor(Math.random() * 10000));
			var selector = '#' + headingId;
			var options = {
				depth: '30px',
				layers: 8
			};

			$heading.attr('id', headingId);

			if (settings.depth && settings.depth.size) {
				options.depth = settings.depth.size + (settings.depth.unit || 'px');
			}
			if (settings.layers) {
				options.layers = settings.layers;
			}
			if (settings.perspective && settings.perspective.size) {
				options.perspective = settings.perspective.size + 'px';
			}
			if (settings.fade) {
				options.fade = settings.fade === 'yes' || settings.fade === true;
			}
			if (settings.event) {
				options.event = settings.event;
			}
			if (settings.event_rotation && settings.event !== 'none') {
				options.eventRotation = settings.event_rotation.size + 'deg';
			}
			if (settings.event_direction && settings.event !== 'none') {
				options.eventDirection = settings.event_direction;
			}

			var text = $heading.html();
			$heading.parent().find('.ep-z-text-duplicate').remove();
			$heading.parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + text + '</div>');
			text = $heading.parent().find('.ep-z-text-duplicate:first').html();

			$heading.find('.z-text').remove();
			new Ztextify(selector, options, text);

			if (settings.depth_color) {
				$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', settings.depth_color);
			} else if (node) {
				var computedColor = window.getComputedStyle(node).color;
				$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', computedColor);
			}
		}

		function runAtomicThreedText() {
			jQuery('.elementor-widget-e-heading .e-heading-base, [data-widget_type^="e-heading"] .e-heading-base, .e-heading-base[data-ep-threed-text]').each(function () {
				var heading = this;
				var wrapper = heading.closest('[data-id]');
				var settings = parseJSON(heading.getAttribute('data-ep-threed-text'));

				if (!settings && wrapper) {
					settings = parseJSON(wrapper.getAttribute('data-ep-threed-text'));
				}

				if (!settings && wrapper) {
					settings = jQuery(wrapper).data('settings') || parseJSON(wrapper.getAttribute('data-settings'));
				}

				settings = normalizeAtomicSettings(settings);

				if (!settings) {
					return;
				}

				applyThreeDText(jQuery(heading), settings, 'ep-atomic-' + (wrapper ? wrapper.getAttribute('data-id') : Date.now()));
			});
		}

		ThreedText = ModuleHandler.extend({

			bindEvents: function () {
				this.run();
			},

			getDefaultSettings: function () {
				return {
					depth: '30px',
					layers: 8,
				};
			},

			onElementChange: debounce(function (prop) {
				if (prop.indexOf('ep_threed_text_') !== -1) {
					this.run();
				}
			}, 400),

			settings: function (key) {
				return this.getElementSettings('ep_threed_text_' + key);
			},

			run: function () {
				var options = this.getDefaultSettings(),
					$element = this.findElement('.elementor-heading-title, .bdt-main-heading-inner'),
					$widgetId = 'ep-' + this.getID(),
					$widgetIdSelect = '#' + $widgetId;

				jQuery($element).attr('id', $widgetId);

				if (this.settings('depth.size')) {
					options.depth = this.settings('depth.size') + this.settings('depth.unit') || '30px';
				}
				if (this.settings('layers')) {
					options.layers = this.settings('layers') || 8;
				}
				if (this.settings('perspective.size')) {
					options.perspective = this.settings('perspective.size') + 'px' || '500px';
				}
				if (this.settings('fade')) {
					options.fade = !!this.settings('fade');
				}
				if (this.settings('event')) {
					options.event = this.settings('event') || 'pointer';
				}
				if (this.settings('event_rotation') && this.settings('event') != 'none') {
					options.eventRotation = this.settings('event_rotation.size') + 'deg' || '35deg';
				}
				if (this.settings('event_direction') && this.settings('event') != 'none') {
					options.eventDirection = this.settings('event_direction') || 'default';
				}

				if (this.settings('active') == 'yes') {

					var $text = $($widgetIdSelect).html();
					$($widgetIdSelect).parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + $text + '</div>');

					$text = $($widgetIdSelect).parent().find('.ep-z-text-duplicate:first').html();

					$($widgetIdSelect).find('.z-text').remove();

					new Ztextify($widgetIdSelect, options, $text);
				}

				if (this.settings('depth_color')) {
					var depthColor = this.settings('depth_color') || '#fafafa';
					$($widgetIdSelect).find('.z-layers .z-layer:not(:first-child)').css('color', depthColor);
				}
			}
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			elementorFrontend.elementsHandler.addHandler(ThreedText, {
				$element: $scope
			});
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', function () {
			runAtomicThreedText();
		});

		runAtomicThreedText();
		jQuery(window).on('load', runAtomicThreedText);
	});
})(jQuery, window.elementorFrontend);
