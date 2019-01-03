//credit: https://gist.github.com/MattSurabian/7868115
define(['jquery'],function ($) {    
    var thailand_loaded_def = $.Deferred();
    $(window).ready(function () {
        thailand_loaded_def.resolve();        
    });
return thailand_loaded_def.promise();

});