Octopus.Web.Device.X10.Switch = function(deviceObject, domObject){
    if(deviceObject){
        this.id = deviceObject.device_id;
        this.className = deviceObject.widget_class;
        this.tplName = this.className;
        this.name = deviceObject.name;
    }
    else{
        this.id = null;
        this.className = null;
        this.tplName = null;
        this.name = null;
    }
    
    this.dom = domObject;    
    this.device = deviceObject;
    this.tpl = null;
    this.button = null;
}

Octopus.Web.Device.X10.Switch.prototype.internalRender = function(){
    var self = this;
    self.button.click(function(){
        self.execute();
    });
}

Octopus.Web.Device.X10.Switch.prototype.render = function(){
    var self = this;
    this.tpl = this.loadTpl('Widget.tpl',function(tpl){                
        $(self.dom).html(tmpl(self.tpl.html(), self.device));
        self.button = $($(self.dom).find('.device_button'));
        //hook
        self.internalRender();

        self.invalidate();
        
    });
}

Octopus.Web.Device.X10.Switch.prototype.execute = function(){
    var command = "";
    if(this.button.hasClass('device_on')){
        command = "off";
    }
    else{
        command = "on";
    }

    $.ajax({
        url: "/device/command/",
        data: {
            device_id: this.id,
            command_name: command
        },
        success: function(data){
            
        }
    });
}

Octopus.Web.Device.X10.Switch.prototype.update = function(deviceObject){
    this.device = deviceObject;

    this.invalidate();
}

Octopus.Web.Device.X10.Switch.prototype.invalidate = function(){
    if(this.device.status_data && this.device.status_data.on == 1){
        this.button.removeClass('device_off').addClass('device_on');
    }
    else{
        this.button.removeClass('device_on').addClass('device_off');
    }
    
}

Octopus.Web.Device.X10.Switch.prototype.loadTpl = function(tpl,callback){
    return Octopus.Web.loadTpl(tpl,this.tplName,callback);
}



