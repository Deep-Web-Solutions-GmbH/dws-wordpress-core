jQuery(document).ready(function ($) {
    function getDeviceType(innerWidth) {
        let deviceType = $.DEVICE_TYPE.LARGE;

        if (window.matchMedia("(max-width: 767px)").matches) {
            deviceType = $.DEVICE_TYPE.EXTRASMALL;
        } else if (window.matchMedia("(min-width: 768px) and (max-width: 959px)").matches) {
            deviceType = $.DEVICE_TYPE.SMALL;
        } else if (window.matchMedia("(min-width: 960px) and (max-width: 1199px)").matches) {
            deviceType = $.DEVICE_TYPE.MEDIUM;
        }

        $(document).trigger('dws_device_type_updated');
        return deviceType;
    }

    $.DEVICE_TYPE = {EXTRASMALL: 0, SMALL: 1, MEDIUM: 2, LARGE: 3};
    $.deviceType = getDeviceType($(window).innerWidth());
    $(document).trigger('dws_device_type_initialized');

    $(window).resize(function () {
        setTimeout(function () {
            let currentDeviceType = getDeviceType($(window).innerWidth());
            if (currentDeviceType !== $.deviceType) {
                $.deviceType = currentDeviceType;
            }
        }, 50);
    });

    $.fn.removeHeights = function (action) {
        if (action === "auto" || action === undefined) {
            return $("div", this).each(function () {
                $(this).height("auto");
            });
        } else if (action === "100%") {
            return $("div", this).each(function () {
                $(this).height("100%");
            });
        } else if (action === "removeAttr") {
            return $("div", this).each(function () {
                $(this).height("");
            });
        }

        return this;
    };
    $.fn.equalizeHeights = function (action) {
        this.removeHeights(action);

        let maxHeight = 0;
        $("div", this).each(function () {

            let height = $(this).height();

            if (height === 0) {
                $(this).parent().css("display", "inline-block");
                height = $(this).height();
                $(this).parent().css("display", "");
            }

            if (height > maxHeight) {
                maxHeight = height;
            }
        });

        $("div", this).each(function () {
            $(this).height(maxHeight);
        });

        return this;
    };

    $('body').trigger('dws_can_equalize_heights');
});