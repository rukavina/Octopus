Function.prototype.inherits = function(parent)
{
    this.prototype = new parent();
    this.prototype.constructor = this;
}

var Octopus = {
    Web: {
        // creates an empty namespace
        createNamespace: function (className) {
            //prepare namespace
            var ns = window;
            if (className != '') {
                var parts = className.split('.');
                for (var i = 0; i < (parts.length - 1); i++) {
                    if (!ns[parts[i]]) {
                        ns[parts[i]] = {};
                    }
                    ns = ns[parts[i]];
                }
            }
        },
        // loads class and creates ns
        loadClass: function (className,callback) {
            Octopus.Web.createNamespace(className);
            //get via ajax
            var url = '/js/' + className.replace(/\./gi, '/') + '.js';
            $.getScript(url, callback);
        },
        loadTpl: function(tpl,className,callback){
            var script = document.createElement('script');
            $(script).attr('type', 'text/html');
            document.body.appendChild(script);

            var url = '/js/' + className.replace(/\./gi, '/') + '/' + tpl;
            $.get(url, function(data){
                $(script).html(data);
                if(callback){
                    callback(data);
                }
            });

            return $(script);
        }
    }
}


