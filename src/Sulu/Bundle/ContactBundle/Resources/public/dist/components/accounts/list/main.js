define(["services/sulucontact/account-manager","services/sulucontact/account-router","services/sulucontact/account-delete-dialog","widget-groups"],function(a,b,c,d){"use strict";var e={datagridInstanceName:"accounts"},f=function(){this.sandbox.on("sulu.toolbar.delete",function(){this.sandbox.emit("husky.datagrid."+e.datagridInstanceName+".items.get-selected",g.bind(this))},this),this.sandbox.on("sulu.contacts.account.deleted",function(a){this.sandbox.emit("husky.datagrid."+e.datagridInstanceName+".record.remove",a)},this),this.sandbox.on("sulu.toolbar.add",function(){b.toAdd()},this),this.sandbox.on("husky.datagrid."+e.datagridInstanceName+".number.selections",function(a){var b=a>0?"enable":"disable";this.sandbox.emit("sulu.header.toolbar.item."+b,"deleteSelected",!1)},this)},g=function(b){c.showDialog(b,function(c){a["delete"](b,c)}.bind(this))},h=function(a){this.sandbox.emit("sulu.sidebar.set-widget","/admin/widget-groups/account-info?account="+a)},i=function(a){b.toEdit(a)};return{view:!0,layout:{content:{width:"max"},sidebar:{width:"fixed",cssClasses:"sidebar-padding-50"}},header:function(){return{noBack:!0,toolbar:{buttons:{add:{},deleteSelected:{}}}}},templates:["/admin/contact/template/account/list"],initialize:function(){this.render(),f.call(this)},render:function(){this.sandbox.dom.html(this.$el,this.renderTemplate("/admin/contact/template/account/list")),this.sandbox.sulu.initListToolbarAndList.call(this,"accounts","/admin/api/accounts/fields",{el:this.$find("#list-toolbar-container"),instanceName:"accounts",template:"default"},{el:this.sandbox.dom.find("#companies-list",this.$el),url:"/admin/api/accounts?flat=true",resultKey:"accounts",searchInstanceName:"accounts",instanceName:e.datagridInstanceName,searchFields:["name"],clickCallback:d.exists("account-info")?h.bind(this):null,actionCallback:i.bind(this)},"accounts","#companies-list-info")}}});