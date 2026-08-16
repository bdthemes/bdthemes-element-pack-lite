(function (window, document) {
	'use strict';

	var HEADING_SELECTOR = '.elementor-widget-e-heading .e-heading-base, [data-widget_type^="e-heading"] .e-heading-base, .e-heading-base';
	var HEADING_WRAPPER_SELECTOR = '.elementor-widget-e-heading[data-id], [data-widget_type^="e-heading"][data-id], [data-id][data-atomic]';
	var initialized = false;
	var retryTimer = null;
	var editorBindRetryTimer = null;
	var editorChangeBound = false;
	var previewHooksBound = false;

	function getPreviewDocument() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		return iframe && iframe.contentDocument ? iframe.contentDocument : null;
	}

	function getPreviewWindow() {
		var previewDocument = getPreviewDocument();

		return previewDocument && previewDocument.defaultView ? previewDocument.defaultView : null;
	}

	function unwrapAtomicValue(value) {
		if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
			return unwrapAtomicValue(value.value);
		}

		return value;
	}

	function normalizeSettings(settings) {
		if (!settings) {
			return {};
		}

		if ('function' === typeof settings.toJSON) {
			return settings.toJSON();
		}

		if (settings.attributes && 'object' === typeof settings.attributes) {
			return settings.attributes;
		}

		return settings;
	}

	function getSettingValue(settings, key) {
		if (!settings) {
			return null;
		}

		if ('function' === typeof settings.get) {
			return settings.get(key);
		}

		settings = normalizeSettings(settings);

		return settings ? settings[key] : null;
	}

	function getElementSettings(elementId) {
		var container;
		var settings;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		if (!container || !container.model) {
			return null;
		}

		settings = container.model.get('settings') || {};

		return normalizeSettings(settings);
	}

	function getElementModel(elementId) {
		var container;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		return container && container.model ? container.model : null;
	}

	function getElementTypeFromModel(model) {
		return model ? model.get('widgetType') || model.get('elType') : '';
	}

	function isHeadingElementId(elementId) {
		var model = getElementModel(elementId);
		var elementType = getElementTypeFromModel(model);

		return 'e-heading' === elementType || 'e-heading.default' === elementType;
	}

	function isHeadingModel(model) {
		var elementType = getElementTypeFromModel(model);

		return 'e-heading' === elementType || 'e-heading.default' === elementType;
	}

	function escapeAttributeValue(value) {
		if (window.CSS && window.CSS.escape) {
			return window.CSS.escape(value);
		}

		return String(value).replace(/"/g, '\\"');
	}

	function getHeadingElement(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector(
			'[data-id="' + safeId + '"] .e-heading-base, ' +
			'[data-id="' + safeId + '"].elementor-widget-e-heading .e-heading-base, ' +
			'[data-id="' + safeId + '"][data-widget_type^="e-heading"] .e-heading-base'
		);
	}

	function getElementWrapper(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector('[data-id="' + safeId + '"]');
	}

	function getElementId(element) {
		var closestElement = element.closest('[data-id]');

		return closestElement ? closestElement.getAttribute('data-id') : null;
	}

	function normalizeToggle(value) {
		value = unwrapAtomicValue(value);

		if (true === value || 'yes' === value || 1 === value || '1' === value) {
			return 'yes';
		}

		return '';
	}

	function toNumber(value, fallback) {
		value = parseFloat(unwrapAtomicValue(value));

		return isNaN(value) ? fallback : value;
	}

	function toInteger(value, fallback) {
		value = parseInt(unwrapAtomicValue(value), 10);

		return isNaN(value) ? fallback : value;
	}

	function toString(value, fallback) {
		value = unwrapAtomicValue(value);

		return 'string' === typeof value && '' !== value ? value : fallback;
	}

	function normalizeThreedSettings(settings) {
		if (!settings || 'yes' !== normalizeToggle(getSettingValue(settings, 'ep_threed_text_active'))) {
			return null;
		}

		return {
			active: 'yes',
			depth: {
				size: toNumber(getSettingValue(settings, 'ep_threed_text_depth'), 30),
				unit: 'px'
			},
			layers: toInteger(getSettingValue(settings, 'ep_threed_text_layers'), 8),
			depth_color: toString(getSettingValue(settings, 'ep_threed_text_depth_color'), ''),
			perspective: {
				size: toNumber(getSettingValue(settings, 'ep_threed_text_perspective'), 500),
				unit: 'px'
			},
			fade: 'yes' === normalizeToggle(getSettingValue(settings, 'ep_threed_text_fade')) ? 'yes' : '',
			event: toString(getSettingValue(settings, 'ep_threed_text_event'), 'none'),
			event_rotation: {
				size: toNumber(getSettingValue(settings, 'ep_threed_text_event_rotation'), 35),
				unit: 'deg'
			},
			event_direction: toString(getSettingValue(settings, 'ep_threed_text_event_direction'), 'default')
		};
	}

	function cleanupHeading(heading) {
		var duplicateSource;

		if (!heading) {
			return;
		}

		if (heading.parentNode) {
			duplicateSource = heading.parentNode.querySelector('.ep-z-text-duplicate');
			if (duplicateSource) {
				heading.innerHTML = duplicateSource.innerHTML;
			}
		}

		heading.removeAttribute('data-ep-threed-text');
		heading.removeAttribute('data-ep-threed-original-html');
		heading.classList.remove('bdt-threed-text-yes');
		heading.style.minHeight = '';
		heading.style.minWidth = '';
		heading.style.display = '';
		heading.querySelectorAll('.z-text').forEach(function (node) {
			node.remove();
		});

		if (heading.parentNode) {
			heading.parentNode.querySelectorAll('.ep-z-text-duplicate').forEach(function (node) {
				node.remove();
			});
		}
	}

	function cleanupElementById(elementId) {
		var heading = getHeadingElement(elementId);
		var wrapper = getElementWrapper(elementId);

		cleanupHeading(heading);

		if (wrapper && wrapper !== heading) {
			wrapper.removeAttribute('data-ep-threed-text');
		}
	}

	function getStableDimension(heading, axis) {
		var rect;
		var computed;
		var value;

		if (!heading) {
			return 0;
		}

		rect = heading.getBoundingClientRect();
		if (rect && rect[axis] > 0) {
			return rect[axis];
		}

		computed = window.getComputedStyle(heading);
		if ('height' === axis) {
			value = parseFloat(computed.lineHeight);
			if (!isNaN(value) && value > 0) {
				return value;
			}

			value = parseFloat(computed.fontSize);
			if (!isNaN(value) && value > 0) {
				return value * 1.2;
			}

			return 0;
		}

		value = parseFloat(computed[axis]);
		if (!isNaN(value) && value > 0) {
			return value;
		}

		return 0;
	}

	function getSourceHtmlForThreed(heading, $) {
		var originalHtml = heading.getAttribute('data-ep-threed-original-html');
		var duplicateHtml;

		if (originalHtml) {
			return originalHtml;
		}

		if (heading.querySelector('.z-text') && heading.parentNode) {
			duplicateHtml = $(heading.parentNode).find('.ep-z-text-duplicate:first').html();
			if (duplicateHtml) {
				return duplicateHtml;
			}
		}

		return heading.innerHTML;
	}

	function applyThreedEffect(heading, normalizedSettings, elementId) {
		var previewWindow = getPreviewWindow();
		var $ = (previewWindow && previewWindow.jQuery) || window.jQuery;
		var Ztextify = (previewWindow && previewWindow.Ztextify) || window.Ztextify;
		var headingId;
		var selector;
		var options;
		var text;
		var stableHeight;
		var stableWidth;
		var originalHtml;

		if (!heading || !normalizedSettings || !$ || !Ztextify) {
			return;
		}

		stableHeight = getStableDimension(heading, 'height');
		stableWidth = getStableDimension(heading, 'width');
		originalHtml = getSourceHtmlForThreed(heading, $);

		cleanupHeading(heading);
		heading.setAttribute('data-ep-threed-original-html', originalHtml);
		heading.innerHTML = originalHtml;

		heading.setAttribute('data-ep-threed-text', JSON.stringify(normalizedSettings));
		heading.classList.add('bdt-threed-text-yes');

		headingId = 'ep-atomic-' + elementId;
		heading.setAttribute('id', headingId);
		selector = '#' + headingId;

		options = {
			depth: normalizedSettings.depth.size + (normalizedSettings.depth.unit || 'px'),
			layers: normalizedSettings.layers
		};

		if (normalizedSettings.perspective && normalizedSettings.perspective.size) {
			options.perspective = normalizedSettings.perspective.size + 'px';
		}

		if ('yes' === normalizedSettings.fade) {
			options.fade = true;
		}

		if (normalizedSettings.event) {
			options.event = normalizedSettings.event;
		}

		if ('none' !== normalizedSettings.event && normalizedSettings.event_rotation && normalizedSettings.event_rotation.size) {
			options.eventRotation = normalizedSettings.event_rotation.size + 'deg';
		}

		if ('none' !== normalizedSettings.event && normalizedSettings.event_direction) {
			options.eventDirection = normalizedSettings.event_direction;
		}

		text = originalHtml;
		$(selector).parent().find('.ep-z-text-duplicate').remove();
		$(selector).parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + text + '</div>');
		text = $(selector).parent().find('.ep-z-text-duplicate:first').html();

		$(selector).find('.z-text').remove();
		new Ztextify(selector, options, text);
		if (stableHeight > 0) {
			heading.style.minHeight = stableHeight + 'px';
		}
		if (stableWidth > 0) {
			heading.style.minWidth = stableWidth + 'px';
			heading.style.display = 'inline-block';
		}

		if (normalizedSettings.depth_color) {
			$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', normalizedSettings.depth_color);
		} else {
			$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', $(selector).css('color'));
		}
	}

	function updateHeading(elementId, settings) {
		var heading = getHeadingElement(elementId);
		var normalizedRawSettings = normalizeSettings(settings);
		var normalizedSettings = normalizeThreedSettings(normalizedRawSettings);

		if (!heading) {
			return;
		}

		if (!normalizedSettings) {
			cleanupElementById(elementId);
		} else {
			applyThreedEffect(heading, normalizedSettings, elementId);
		}
	}

	function scheduleElementUpdate(elementId, settings) {
		updateHeading(elementId, settings);

		window.setTimeout(function () {
			updateHeading(elementId, settings);
		}, 100);

		window.setTimeout(function () {
			updateHeading(elementId, settings);
		}, 500);
	}

	function syncExistingHeadings() {
		var previewDocument = getPreviewDocument();
		var elements;

		if (!previewDocument) {
			return;
		}

		elements = previewDocument.querySelectorAll(HEADING_WRAPPER_SELECTOR + ', ' + HEADING_SELECTOR);

		elements.forEach(function (element) {
			var elementId = getElementId(element);

			if (!isHeadingElementId(elementId)) {
				return;
			}

			updateHeading(elementId, getElementSettings(elementId));
		});
	}

	function scheduleInitialSync() {
		var previewDocument = getPreviewDocument();

		if (!previewDocument || !previewDocument.body) {
			retryTimer = window.setTimeout(function () {
				retryTimer = null;
				scheduleInitialSync();
			}, 500);
			return;
		}

		window.requestAnimationFrame(syncExistingHeadings);
	}

	function getChangedModel(view) {
		if (view && view.container && view.container.model) {
			return view.container.model;
		}

		if (view && view.model) {
			return view.model;
		}

		return null;
	}

	function bindEditorChange() {
		if (editorChangeBound) {
			return;
		}

		if (!window.elementor || !window.elementor.channels || !window.elementor.channels.editor) {
			if (!editorBindRetryTimer) {
				editorBindRetryTimer = window.setTimeout(function () {
					editorBindRetryTimer = null;
					bindEditorChange();
				}, 300);
			}
			return;
		}

		window.elementor.channels.editor.on('change', function (view) {
			var model = getChangedModel(view);
			var elementId;

			if (!isHeadingModel(model)) {
				return;
			}

			elementId = model.get('id');
			scheduleElementUpdate(elementId, normalizeSettings(model.get('settings') || {}));
		});

		editorChangeBound = true;
	}

	function bindPreviewFrontendHooks() {
		var previewDocument = getPreviewDocument();
		var previewWindow = previewDocument && previewDocument.defaultView;
		var frontend = previewWindow && previewWindow.elementorFrontend;

		if (previewHooksBound) {
			return;
		}

		if (!frontend || !frontend.hooks) {
			return;
		}

		frontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			var element = $scope && $scope[0];
			var elementId = element ? getElementId(element) : null;

			if (!elementId || !isHeadingElementId(elementId)) {
				return;
			}

			updateHeading(elementId, getElementSettings(elementId));
		});

		previewHooksBound = true;
	}

	function init() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		if (!initialized) {
			bindEditorChange();
			initialized = true;
		}

		if (iframe) {
			iframe.addEventListener('load', function () {
				previewHooksBound = false;
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		if (window.elementor) {
			window.elementor.on('preview:loaded', function () {
				previewHooksBound = false;
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		bindPreviewFrontendHooks();
		scheduleInitialSync();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}(window, document));
