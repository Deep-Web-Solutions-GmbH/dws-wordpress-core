/**
 * Useful helper to get parameter values from a URL.
 *
 * @since       1.0.0
 * @version     1.0.0
 *
 * @param       name    Name of the parameter.
 * @param       url     The URL to parse for the value.
 *
 * @returns     {*}     The value of the parameter with that name, or null if it doesn't exist
 */
function dws_get_param_by_name(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/**
 * Useful helper to replace the value of a certain parameter in a URL.
 *
 * @since       1.0.0
 * @version     1.0.0
 *
 * @param       url             The URL to parse and replace the value.
 * @param       paramName       Name of the parameter.
 * @param       paramValue      The new value of the parameter.
 *
 * @returns     {*}             The URL containing the proper parameter.
 */
function dws_replace_url_param(url, paramName, paramValue) {
    if (paramValue === null) {
        paramValue = '';
    }

    var pattern = new RegExp('\\b(' + paramName + '=).*?(&|$)');
    if (url.search(pattern) >= 0) {
        return url.replace(pattern, '$1' + paramValue + '$2');
    }

    return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue;
}

/**
 * @deprecated since version 1.5.0
 */
function get_param_by_name(name, url) {
    console.log("DEPRECATED USE OF THE get_param_by_name FUNCTION. PLEASE USE THE NEW dws_get_param_by_name FUNCTION");
    return dws_get_param_by_name(name, url);
}
/**
 * @deprecated since version 1.5.0
 */
function replace_url_param(url, paramName, paramValue) {
    console.log("DEPRECATED USE OF THE replace_url_param FUNCTION. PLEASE USE THE NEW dws_replace_url_param FUNCTION");
    return dws_replace_url_param(url, paramName, paramValue);
}