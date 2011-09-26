Octopus.Web.Scene = function(sceneDom,sceneId){
    this.dom = sceneDom;
    this.sceneId = sceneId;
    this.scene = {};
    this.widgets = {};
    this.tpl = null;
    this.bg = null;
}

Octopus.Web.Scene.prototype._load = function(callback){
    var self = this;
    $.get('/scene/get/scene_id/' + this.sceneId, function(data) {
        self.scene = data;
        if(callback){
            callback();
        }
    });
}

Octopus.Web.Scene.prototype.render = function(){
    var self = this;
    self._load(function(){
        self.tpl = Octopus.Web.loadTpl('Layout.tpl','Octopus.Web.Scene',function(){
            self._invalidateScene();
            self._loadWidgets();
            self.bg = $(self.dom.find("#bg"));
            if(self.scene.settings_data.background_image){
                self._updateBg();
                self.bg.load(function(){
                    self._updateBg();
                })
            }
        });
    })

}

Octopus.Web.Scene.prototype.update = function(){
    var self = this;
    self._load(function(){
        for (var id in self.scene.devices){
            self.widgets[id].update(self.scene.devices[id]);
        }
    })

}

Octopus.Web.Scene.prototype._loadWidgets = function(){
    for (var id in this.scene.devices){
        var device = this.scene.devices[id];
        $('#device-' + id).css(
            {
                'left': device.settings_data.left,
                'top': device.settings_data.top,
                'height': device.settings_data.height,
                'width': device.settings_data.width
            }
        );

        this._loadWidget(device,$('#device-' + id));
    }
}

Octopus.Web.Scene.prototype._loadWidget = function(device,dom){
    var self = this;
    var deviceId = device.device_id;
    //alert("var deviceObj = new " + device.widget_class + "(device,dom);");
    eval("var widget = new " + device.widget_class + "(device,dom);");
    self.widgets[deviceId] = widget;
    self.widgets[deviceId].render();
}

Octopus.Web.Scene.prototype._invalidateScene = function(){
    $(this.dom) .html(tmpl(this.tpl.html(), this.scene))
                .height(this.scene.settings_data.height)
                .width(this.scene.settings_data.width);
    this._updateBg();
}

Octopus.Web.Scene.prototype._updateBg = function(){
    if(this.scene.settings_data.background_image){
        var d = new Date();
        var bgUrl = this.scene.settings_data.background_image + "?ts=" + d.getTime();
        $(this.bg).attr("src",bgUrl);
    }
}

Octopus.Web.Scene.prototype._invalidateDevices = function(){
}

