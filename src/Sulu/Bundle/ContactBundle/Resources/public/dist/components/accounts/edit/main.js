define(["services/sulucontact/account-manager","services/sulucontact/account-router","services/sulucontact/account-delete-dialog"],function(a,b,c){"use strict";return{header:function(){return{tabs:{url:"/admin/content-navigations?alias=account"},toolbar:{buttons:{save:{parent:"saveWithOptions"},"delete":{}}}}},initialize:function(){this.bindCustomEvents(),this.afterSaveAction=""},bindCustomEvents:function(){this.sandbox.on("sulu.header.back",b.toList),this.sandbox.on("sulu.tab.dirty",this.enableSave.bind(this)),this.sandbox.on("sulu.toolbar.save",this.save.bind(this)),this.sandbox.on("sulu.tab.saving",this.loadingSave.bind(this)),this.sandbox.on("sulu.tab.saved",this.afterSave.bind(this)),this.sandbox.on("sulu.toolbar.delete",this.deleteAccount.bind(this))},deleteAccount:function(){c.showDialog([this.options.id],function(c){a["delete"](this.options.id,c).then(function(){b.toList()}.bind(this))}.bind(this))},save:function(a){this.afterSaveAction=a,this.sandbox.emit("sulu.tab.save")},enableSave:function(){this.sandbox.emit("sulu.header.toolbar.item.enable","save",!1)},loadingSave:function(){this.sandbox.emit("sulu.header.toolbar.item.loading","save")},afterSave:function(a){this.sandbox.emit("sulu.header.toolbar.item.disable","save",!0),"back"===this.afterSaveAction?b.toList():"new"===this.afterSaveAction?b.toAdd():this.options.id||b.toEdit(a.id)}}});