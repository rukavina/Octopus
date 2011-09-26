Octopus.Web.Device.X10.Lamp = function(deviceObject, domObject){
    Octopus.Web.Device.X10.Switch.apply(this,arguments);
    this.dialogDom = null;
    this.sliderDom = null;
}

Octopus.Web.Device.X10.Lamp.inherits(Octopus.Web.Device.X10.Switch);

Octopus.Web.Device.X10.Lamp.prototype.invalidate = function(){
    var intesity = Math.round(8 * (this.getIntesity() / 100));
    var bgUrl = 'url(\'/images/devices/x10/brightness_' + intesity  +'.png\')';
    this.button.css('background-image',bgUrl);
}

Octopus.Web.Device.X10.Lamp.prototype.getIntesity = function(){
    var intesity = 0;
    if(this.device.status_data){
        if(this.device.status_data.on == 1){
            intesity = 100;
            if(this.device.status_data.intesity){
                intesity = this.device.status_data.intesity;
            }
        }
        else{
            intesity = 0;
        }
    }
    return intesity;
}

Octopus.Web.Device.X10.Lamp.prototype.internalRender = function(){
    this.dialogDom = $(this.dom.find(".lamp_dialog"));
    this.sliderDom = $(this.dom.find(".lamp_dialog .slider"));

    var self = this;
    
    this.sliderDom.slider({
        range: "min",
        min: 0,
        max: 100,
        animate: true,
        stop: function(event,ui) {
            self.setBrightness(ui.value);
        }
    });

    this.dialogDom.dialog({
        height: 300,
        width: 650,
        modal: true,
        autoOpen: false,
        buttons: [
            {
                text: "Off",
                click: function() {
                    self.sliderDom.slider("value",0);
                    self.setBrightness(self.sliderDom.slider("value"));
                }
            },
            {
                text: "+",
                click: function() {
                    self.sliderDom.slider("value",self.sliderDom.slider("value") + 8);
                    self.setBrightness(self.sliderDom.slider("value"));
                }
            },
            {
                text: "-",
                click: function() {
                    self.sliderDom.slider("value",self.sliderDom.slider("value") - 8);
                    self.setBrightness(self.sliderDom.slider("value"));
                }
            },
            {
                text: "Close",
                click: function() { $(this).dialog("close"); }
            }
        ]
    });

    
    this.button.click(function(){
        self.sliderDom.slider("value",self.getIntesity());
        self.dialogDom.dialog("open");
    });
}

Octopus.Web.Device.X10.Switch.prototype.setBrightness = function(intesity){
    $.ajax({
        url: "/device/command/",
        data: {
            'device_id': this.id,
            'command_name': "xdim",
            'intesity': intesity
        },
        success: function(data){

        }
    });
}