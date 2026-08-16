jQuery(document).ready(function ($) {
    // Delegate to capture dynamically injected biggopti as well
    $(document).on('click', '.element-pack-biggopti.is-dismissible .bdt-biggopti-dismiss', function () {
        $this = $(this).parents('.element-pack-biggopti');
        var $id = $this.attr('id') || '';
        var $time = $this.attr('dismissible-time') || '';
        var $meta = $this.attr('dismissible-meta') || '';
        $.ajax({
            url: (window.ElementPackBiggoptiConfig && ElementPackBiggoptiConfig.ajaxurl) ? ElementPackBiggoptiConfig.ajaxurl : (typeof ajaxurl !== 'undefined' ? ajaxurl : ''),
            type: 'POST',
            data: {
                action: 'element_pack_biggopti',
                id: $id,
                meta: $meta,
                time: $time,
                _wpnonce: ElementPackBiggoptiConfig.nonce
            }
        });
        $this.fadeTo(100, 0, function () {
            $(this).slideUp(100, function () {
                $(this).remove();
            });
        });
    });
});