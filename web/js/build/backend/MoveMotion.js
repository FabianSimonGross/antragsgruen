var __awaiter=this&&this.__awaiter||function(i,n,e,t){return new(e||(e=Promise))((function(o,a){function d(i){try{r(t.next(i))}catch(i){a(i)}}function s(i){try{r(t.throw(i))}catch(i){a(i)}}function r(i){var n;i.done?o(i.value):(n=i.value,n instanceof e?n:new e((function(i){i(n)}))).then(d,s)}r((t=t.apply(i,n||[])).next())}))};define(["require","exports"],(function(i,n){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.MoveMotion=void 0;n.MoveMotion=class{constructor(i){this.$form=i,this.checkBackend=i.data("check-backend"),this.initCopyMove(),this.initTarget(),this.initConsultation(),this.initButtonEnabled()}initCopyMove(){this.$form.find("input[name=operation]").on("change",(()=>{"copynoref"===this.$form.find("input[name=operation]:checked").val()?(this.$form.find(".labelTargetMove").addClass("hidden"),this.$form.find(".labelTargetCopy").removeClass("hidden"),this.$form.find(".targetSame").removeClass("hidden")):(this.$form.find(".labelTargetMove").removeClass("hidden"),this.$form.find(".labelTargetCopy").addClass("hidden"),this.$form.find(".targetSame").addClass("hidden"))}))}initTarget(){const i=this.$form.find("input[name=target]");i.on("change",(()=>{const n=i.filter(":checked").val();"agenda"===n?this.$form.find(".moveToAgendaItem").removeClass("hidden"):this.$form.find(".moveToAgendaItem").addClass("hidden"),"consultation"===n?this.$form.find(".moveToConsultationItem").removeClass("hidden"):this.$form.find(".moveToConsultationItem").addClass("hidden"),this.rebuildMotionTypes()})).trigger("change")}initConsultation(){$("#consultationId").on("change",this.rebuildMotionTypes.bind(this))}rebuildMotionTypes(){const i=$("#consultationId").val();$(".moveToMotionTypeId").addClass("hidden"),"consultation"===this.$form.find("input[name=target]:checked").val()&&$(".moveToMotionTypeId"+i).removeClass("hidden")}isPrefixAvailable(i,n,e){return new Promise(((t,o)=>$.get(this.checkBackend,{checkType:"prefix",operation:n,newMotionPrefix:i,newConsultationId:e}).then((i=>{t(i.success)}))))}rebuildButtonEnabled(){return __awaiter(this,void 0,void 0,(function*(){let i,n=!0;i="consultation"===this.$form.find("input[name=target]:checked").val()&&this.$form.find("[name=consultation]").length>0?parseInt(this.$form.find("[name=consultation]").val()):null;(yield this.isPrefixAvailable(this.$form.find("#motionTitlePrefix").val(),this.$form.find("input[name=operation]:checked").val(),i))?this.$form.find(".prefixAlreadyTaken").addClass("hidden"):(this.$form.find(".prefixAlreadyTaken").removeClass("hidden"),n=!1),this.$form.find("input[name=operation]:checked").val()||(n=!1),this.$form.find("input[name=target]:checked").val()||(n=!1),this.$form.find("button[type=submit]").prop("disabled",!n)}))}initButtonEnabled(){this.$form.find("#motionTitlePrefix").on("change keyup",this.rebuildButtonEnabled.bind(this)),this.$form.find("input[name=operation]").on("change",this.rebuildButtonEnabled.bind(this)),this.$form.find("input[name=target]").on("change",this.rebuildButtonEnabled.bind(this)),$("#consultationId").on("change",this.rebuildButtonEnabled.bind(this)),this.rebuildButtonEnabled()}}}));
//# sourceMappingURL=MoveMotion.js.map
