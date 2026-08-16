/**
 * Start chart widget script
 */

(() => {
    'use strict';

    const addCommas = (nStr, separatorSymbol, kFormatter) => {
        nStr = String(nStr);

        if (kFormatter) {
            const num = parseFloat(nStr);
            if (num >= 1_000_000_000) return (num / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'G';
            if (num >= 1_000_000)     return (num / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (num >= 1_000)         return (num / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
            return nStr;
        }

        const [integer, decimal = ''] = nStr.split('.');
        const formatted = integer.replace(/(\d+)(\d{3})/, '$1' + separatorSymbol + '$2');
        return decimal ? `${formatted}.${decimal}` : formatted;
    };

    const buildTickCallback = (prefix, suffix, thouSeparator, separatorSymbol, kFormatter, axesSeparator) => {
        return (value) => {
            if (thouSeparator === 'yes' && axesSeparator === 'yes') {
                return prefix + addCommas(value, separatorSymbol, kFormatter) + suffix;
            }
            return prefix + value + suffix;
        };
    };

    const updateChartSetting = (chart, suffixprefix, thouSeparator, separatorSymbol, kFormatter, s_p_status, xAxesSeparator, yAxesSeparator) => {
        const prefix = s_p_status === 'yes' ? (suffixprefix.y_custom_prefix ?? '') : '';
        const suffix = s_p_status === 'yes' ? (suffixprefix.y_custom_suffix ?? '') : '';

        const tickCallback = buildTickCallback(prefix, suffix, thouSeparator, separatorSymbol, kFormatter, yAxesSeparator);

        if (suffixprefix.type === 'horizontalBar') {
            chart.options.scales.x.ticks = { callback: tickCallback };
        } else if (['bar', 'line', 'bubble'].includes(suffixprefix.type)) {
            chart.options.scales.y.ticks = { callback: tickCallback };
        }

        chart.update();
    };

    const widgetChart = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const chartEl = scopeElement.querySelector('.bdt-chart');
        if (!chartEl) return;

        const settingsData    = chartEl.dataset.settings;
        const suffixprefixData = chartEl.dataset.suffixprefix;
        if (!settingsData) return;

        let settings, suffixprefix;
        try {
            settings     = typeof settingsData    === 'string' ? JSON.parse(settingsData)    : settingsData;
            suffixprefix = typeof suffixprefixData === 'string' ? JSON.parse(suffixprefixData) : (suffixprefixData ?? {});
        } catch (e) {
            console.error('Failed to parse chart settings:', e);
            return;
        }

        epObserveTarget(scopeElement, () => {
            const canvas = chartEl.querySelector(':scope > canvas');
            if (!canvas) return;

            const ctx     = canvas.getContext('2d');
            const myChart = new Chart(ctx, settings);

            if (settings.type === 'pie' || settings.type === 'doughnut') return;

            const thouSeparator   = settings.valueSeparator   ?? 'no';
            const separatorSymbol = settings.separatorSymbol  ?? ',';
            const xAxesSeparator  = settings.xAxesSeparator   ?? 'no';
            const yAxesSeparator  = settings.yAxesSeparator   ?? 'no';
            const kFormatter      = settings.kFormatter === 'yes';

            const s_p_status = suffixprefix.suffix_prefix_status ?? 'no';

            const needsUpdate = s_p_status === 'yes' || thouSeparator === 'yes';
            if (needsUpdate) {
                updateChartSetting(myChart, suffixprefix, thouSeparator, separatorSymbol, kFormatter, s_p_status, xAxesSeparator, yAxesSeparator);
            }

        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.8
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-chart.default', widgetChart);
        }
    });

})();

/**
 * End chart widget script
 */
