/*
 * Copyright © Denis Kopylov (dba. Magenius.Team) https://github.com/magenius-team
 * See LICENSE distributed with the module for license details.
 */

define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/alert'
], function ($, mageTemplate, alert) {
    'use strict';

    let ajaxMessageUpdater = {
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

        /**
         * Add  messages to page.
         * @param messages
         */
        addMessages: function (messages) {
            let tmpl = mageTemplate(this.adminMessageTemplate, {
                data: messages
            });

            tmpl = $(tmpl);

            $('#messages').find('.messages')
                .prepend(tmpl)
                .trigger('contentUpdated');
        },

        /**
         * Reloads message container
         */
        reload: function () {
            var data = {'form_key': window.FORM_KEY},
                that = this;

            $.ajax({
                type: 'POST',
                url: window.adminMessagesUrl,
                showLoader: true,
                data: data,

                success: function (response) {
                    var result = JSON.parse(response);

                    if (typeof result.error !== "undefined" || typeof result.success !== "undefined") {
                        that.addMessages(result);
                    }
                },
                error: function (jqXHR, status, error) {
                    alert({
                        content: $.mage.__('Sorry, something went wrong while reloading messages. Please try again later.')
                    });
                    window.console && console.log(status + ': ' + error + '\nResponse text:\n' + jqXHR.responseText);
                },
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        }
    };

    $('<div id="messages"><div class="messages"></div></div>').insertBefore('[id="page:main-container"]');

    return ajaxMessageUpdater;
});
