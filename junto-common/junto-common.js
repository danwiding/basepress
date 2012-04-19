/**
 * Created with JetBrains PhpStorm.
 * User: dwiding
 * Date: 3/30/12
 * Time: 3:02 PM
 * To change this template use File | Settings | File Templates.
 */
var asyncJsLoader = (function () {
    var load = function (url,divElementId,callback) {
        var jsPlaceholder = document.createElement('script');

        jsPlaceholder.type = 'text/javascript';
        jsPlaceholder.async = true;
        jsPlaceholder.src = url;
        jsPlaceholder.addEventListener('load', callback);

        if (divElementId === undefined)
        {
            document.getElementsByTagName('head')[0].appendChild(jsPlaceholder);
        }
        else
        {
            document.getElementById(divElementId).appendChild(jsPlaceholder);
        }

    };

    return function (url,divElementId, callback) {
        setTimeout(function () { load(url,divElementId,callback); }, 1);
    };
})();