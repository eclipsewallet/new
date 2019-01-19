/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Mageplaza_StoreLocator/js/jquery.storelocator'
], function ($) {
    'use strict';

    $.widget('mageplaza.storelocator', {
        options: {
            dataConfig: {},
            mapStyles: {}
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this.initStoreLocator();
            this.loadDetailLocation();
            this._EventListener();
        },

        initStoreLocator: function () {
            var _this = this,
                checkHttps = true,
                isFilterRadius = false;

            if (window.location.protocol === 'http:') {
                checkHttps = false;
            }

            if (checkHttps && this.options.dataConfig.isFilterRadius) {
                isFilterRadius = true;
            }

            $('#bh-sl-map-container').storeLocator({
                'slideMap': false,
                'mapSettings': {
                    styles: this.options.mapStyles,
                    zoom: parseInt(this.options.dataConfig.zoom)
                },
                'markerImg': this.options.dataConfig.markerIcon,
                'markerDim': {height: 32, width: 25},
                'fullMapStart': true,
                'autoComplete': true,
                'inlineDirections': checkHttps,
                'dataLocation': this.options.dataConfig.dataLocations,
                'infowindowTemplatePath': this.options.dataConfig.infowindowTemplatePath,
                'listTemplatePath': this.options.dataConfig.listTemplatePath,
                'KMLinfowindowTemplatePath': this.options.dataConfig.KMLinfowindowTemplatePath,
                'KMLlistTemplatePath': this.options.dataConfig.KMLlistTemplatePath,
                'autoGeocode': isFilterRadius,
                'maxDistance': true,
                'defaultLoc': this.options.dataConfig.isDefaultStore,
                'defaultLat': this.options.dataConfig.defaultLat,
                'defaultLng': this.options.dataConfig.defaultLng,
                callbackListClick: function () {
                    var el = $(this),
                        locationId = $(this).attr('data-id');

                    $(this).find('.link-detail').on('click', function () {
                        var markerId = el.attr('data-markerid'),
                            urlKey = el.attr('data-url-key');
                        $('.mp-store-info').show();
                        $('.mp-detail-info-' + locationId).show();
                        $('.mp-storelocator-list-location').hide();

                        $('.loc-directions-' + locationId).attr('marker-id', markerId);

                        window.history.pushState('', '', urlKey + _this.options.dataConfig.urlSuffix);
                    });
                }
            });
        },

        /**
         * Show details location when load url_key
         */
        loadDetailLocation: function () {
            var i = 0,
                locationId = this.options.dataConfig.locationIdDetail;

            if (locationId) {
                var showMarker = setInterval(function () {
                    var storeViewEl = $('.store-view-' + locationId);
                    i++;

                    if (i > 60) {
                        clearInterval(showMarker);
                    }

                    if (storeViewEl.length) {
                        clearInterval(showMarker);

                        storeViewEl.trigger('click');
                    }
                }, 1000);

                $('.mp-store-info').show();
                $('.mp-detail-info-' + locationId).show();
                $('.mp-storelocator-list-location').hide();
            }
        },

        /**
         * Event listener
         * @private
         */
        _EventListener: function () {
            var _this = this;
            /** Event back button */
            $('.mp-back-results').on('click', function () {
                $('.mp-store-info').hide();
                $('.mp-detail-info').hide();
                $('.mp-storelocator-list-location').show();

                window.history.pushState('', '', _this.options.dataConfig.router + _this.options.dataConfig.urlSuffix);
            });

            /**
             * Dropdown all time of location
             */
            $('.mp-detail-info-text i').each(function () {
                var el = $(this);
                el.on('click', function () {
                    if (el.hasClass('fa-angle-double-down')) {
                        $('.mp-openday-list').slideDown('slow');
                        el.removeClass('fa-angle-double-down');
                        el.addClass('fa-angle-double-up');
                    } else {
                        $('.mp-openday-list').slideUp(10);
                        $('.mp-openday-list').promise().done(function () {
                            el.removeClass('fa-angle-double-up');
                            el.addClass('fa-angle-double-down');
                        });
                    }
                });
            });

            /**
             * Dropdown phone2, tax infors
             */
            $('.mp-detail-phone-text i').each(function () {
                var el = $(this);
                el.on('click', function () {
                    if (el.hasClass('fa-angle-double-down')) {
                        $('.mp-phone-list').slideDown('slow');
                        el.removeClass('fa-angle-double-down');
                        el.addClass('fa-angle-double-up');
                    } else {
                        $('.mp-phone-list').slideUp(10);
                        $('.mp-phone-list').promise().done(function () {
                            el.removeClass('fa-angle-double-up');
                            el.addClass('fa-angle-double-down');
                        });
                    }
                });
            });

            /**
             *  event click menu icon
             */
            $('.mp-menu-icon').on('click', function () {
                $('.mp-dialog-setting').toggle("slide");
            });

            /**
             *  event click close icon
             */
            $('.mp-btn-close').on('click', function () {
                $('.mp-dialog-setting').toggle("slide");
            });

            /** check https */
            if (window.location.protocol === 'http:') {
                $('#mp-location-icon').hide();
                $('.loc-directions').hide();
                $('#bh-sl-submit').css({'float': 'right', 'margin-right': '15px'});
            } else {
                $('#mp-location-icon').show();
                if (!$('#mp-location-icon').length) {
                    $('#bh-sl-submit').css({'float': 'right', 'margin-right': '15px'});
                }

            }

            if (!this.options.dataConfig.isFilter) {
                $('.mp-storelocator-list-location').css({'top': '3%', 'height': '99%'});
                $('.mp-store-info').css({'top': '3%', 'height': '95%'});
            }
        }

    });

    return $.mageplaza.storelocator;
});