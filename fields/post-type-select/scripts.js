"use strict";

(function(window, document, $) {
    window.AO = window.AO || {};

    $(document).ready(() => {
        const $ptsSelects = $('.jsAlchemyPostTypeSelect');

        if( $ptsSelects[0] ) {
            $ptsSelects.each((i, select) => {
                const $select = $(select);
                const fieldData = $select.data('alchemy');
                const ptsData = AlchemyPTSData['search'];
                const currentUrl = new URL(window.location.href);
                const pageID = currentUrl.searchParams.get('page');

                $select.select2({
                    language: fieldData.locale,
                    ajax: {
                        url: ptsData.url,
                        type: 'get',
                        dataType: 'json',
                        delay: 250,
                        data: params => {
                            return {
                                'searchedFor': params.term,
                                '_wpnonce': ptsData.nonce,
                                'post-type': fieldData['post-type'],
                                'page-id': pageID
                            };
                        },
                        processResults: data => {
                            return {
                                results: data.data,
                            };
                        },
                        cache: false
                    },
                    minimumInputLength: 2
                }).on('select2:select', e => {
                    const $target = $(e.target);
                    const option = $target.children(`[value=${e.params.data.id}]`);

                    option.detach();
                    $target.append(option).change();
                });

                $select.siblings('.jsAlchemyPostTypeSelectClear').on('click', () => {
                    $select.val("").change();
                });
            });
        }
    });

    AO.get_post_type_select_value = id => {
        return Promise.resolve( {
            'type': 'post_type_select',
            'id': id,
            'value': $(`#${id}`).val()
        } );
    };
})(window, document, jQuery);