/*
 * Copyright © Denis Kopylov (dba. Magenius.Team). All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/template'
], function ($, mageTemplate) {
    'use strict';

    let adminMessageUpdater = {
        adminMessageTemplate:
            '<% _.each(data, function(messages, type) { %>' +
            '<% _.each(messages, function(message) { %>' +
            '<div class="message message-' + '<%= type %>' + ' ' + '<%= type %>' + '">' +
            '<div data-ui-id="messages-message-' + '<%= type %>' + '">' +
            '<%= message %>' +
            '</div>' +
            '</div>' +
            '<% }); %>' +
            '<% }) %>',

        init: function () {
            $(document).on('ajaxComplete', $.proxy(this.addMessages, this));
        },

        addMessages: function (event, data) {
            try {
                let response = JSON.parse(data.responseText);

                if (response && response['controller_messages']) {
                    let tmpl = mageTemplate(this.adminMessageTemplate, {
                        data: response['controller_messages']
                    });

                    tmpl = $(tmpl);

                    $('#messages').find('.messages')
                        .prepend(tmpl)
                        .trigger('contentUpdated');
                }
            } catch (e) {
                console.error(e);
            }
        }
    }

    $('<div id="messages"><div class="messages"></div></div>').insertBefore('[id="page:main-container"]');

    return adminMessageUpdater;
});
