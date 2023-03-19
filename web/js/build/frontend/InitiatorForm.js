define(["require","exports"],(function(t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.InitiatorForm=void 0;i.InitiatorForm=class{constructor(t){this.$widget=t,this.otherInitiator=!1,this.wasPerson=!1,this.$editforms=t.parents("form").first(),this.$supporterData=t.find(".supporterData"),this.$initiatorData=t.find(".initiatorData"),this.$initiatorAdderRow=this.$initiatorData.find(".adderRow"),this.$fullTextHolder=$("#fullTextHolder"),this.$supporterAdderRow=this.$supporterData.find(".adderRow"),this.userData=t.data("user-data"),this.settings=t.data("settings"),this.hasOrganisationList=!!t.data("organisation-list"),this.$otherInitiator=t.find("input[name=otherInitiator]"),this.$otherInitiator.on("change",this.onChangeOtherInitiator.bind(this)).trigger("change"),t.find("#personTypeNatural, #personTypeOrga").on("click change",this.onChangePersonType.bind(this)),this.onChangePersonType(),this.$initiatorAdderRow.find("a").on("click",this.initiatorAddRow.bind(this)),this.$initiatorData.on("click",".initiatorRow .rowDeleter",this.initiatorDelRow.bind(this)),this.$supporterAdderRow.find("a").on("click",this.supporterAddRow.bind(this)),this.$supporterData.on("click",".supporterRow .rowDeleter",this.supporterDelRow.bind(this)),this.$supporterData.on("keydown"," .supporterRow input[type=text]",this.onKeyOnTextfield.bind(this)),$(".fullTextAdder a").on("click",this.fullTextAdderOpen.bind(this)),$(".fullTextAdd").on("click",this.fullTextAdd.bind(this)),this.hasOrganisationList&&this.$initiatorData.find("#initiatorPrimaryOrgaName").on("change",(()=>{this.setOrgaNameFromSelect()})).trigger("change"),this.$supporterData.length>0&&this.$supporterData.data("min-supporters")>0&&this.initMinSupporters(),this.initAdminSetUser(),this.$editforms.on("submit",this.submit.bind(this))}onChangeOtherInitiator(){this.otherInitiator=1==this.$otherInitiator.val()||this.$otherInitiator.prop("checked"),this.onChangePersonType()}setOrgaNameFromSelect(){if(this.$initiatorData.find("#initiatorPrimaryOrgaName").hasClass("hidden"))return;const t=this.$initiatorData.find("#initiatorPrimaryOrgaName").val();this.$initiatorData.find("#initiatorPrimaryName").val(t)}onChangePersonType(){let t=!1;($("#personTypeHidden").length>0&&"1"==$("#personTypeHidden").val()||$("#personTypeOrga").prop("checked"))&&(t=!0),t?(this.setFieldsVisibilityOrganization(),this.setFieldsReadonlyOrganization(),this.wasPerson&&this.$initiatorData.find("#initiatorPrimaryName").val(""),this.hasOrganisationList&&(this.$initiatorData.find("#initiatorPrimaryName").addClass("hidden"),this.$initiatorData.find("#initiatorPrimaryOrgaName").removeClass("hidden"),this.setOrgaNameFromSelect()),this.wasPerson=!1):(this.setFieldsVisibilityPerson(),this.setFieldsReadonlyPerson(),this.hasOrganisationList&&(this.$initiatorData.find("#initiatorPrimaryName").removeClass("hidden"),this.$initiatorData.find("#initiatorPrimaryOrgaName").addClass("hidden")),this.wasPerson=!0)}setFieldsVisibilityOrganization(){this.$initiatorData.addClass("type-organization").removeClass("type-person"),this.$initiatorData.find(".organizationRow").addClass("hidden"),this.$initiatorData.find(".contactNameRow").removeClass("hidden"),this.$initiatorData.find(".resolutionRow").removeClass("hidden"),this.$initiatorData.find(".genderRow").addClass("hidden"),this.$initiatorData.find(".adderRow").addClass("hidden"),$(".supporterData, .supporterDataHead").addClass("hidden")}setFieldsReadonlyOrganization(){!this.userData.fixed_orga||this.otherInitiator?this.$initiatorData.find("#initiatorPrimaryName").prop("readonly",!1):this.$initiatorData.find("#initiatorPrimaryName").prop("readonly",!0).val(this.userData.person_organization),this.$initiatorData.find("#initiatorOrga").prop("readonly",!1)}setFieldsVisibilityPerson(){this.$initiatorData.removeClass("type-organization").addClass("type-person"),this.$initiatorData.find(".organizationRow").removeClass("hidden"),2==this.settings.contactName?(this.$initiatorData.find(".contactNameRow").removeClass("hidden"),this.$initiatorData.find(".contactNameRow input").prop("required",!0)):1==this.settings.contactName?(this.$initiatorData.find(".contactNameRow").removeClass("hidden"),this.$initiatorData.find(".contactNameRow input").prop("required",!1)):(this.$initiatorData.find(".contactNameRow").addClass("hidden"),this.$initiatorData.find(".contactNameRow input").prop("required",!1)),this.$initiatorData.find(".genderRow").removeClass("hidden"),this.$initiatorData.find(".resolutionRow").addClass("hidden"),this.$initiatorData.find(".adderRow").removeClass("hidden"),$(".supporterData, .supporterDataHead").removeClass("hidden")}setFieldsReadonlyPerson(){!this.userData.fixed_name||this.otherInitiator?this.$initiatorData.find("#initiatorPrimaryName").prop("readonly",!1):this.$initiatorData.find("#initiatorPrimaryName").prop("readonly",!0).val(this.userData.person_name)}initiatorAddRow(t){t.preventDefault();let i=$($("#newInitiatorTemplate").data("html"));this.$initiatorAdderRow.before(i)}initiatorDelRow(t){t.preventDefault(),$(t.target).parents(".initiatorRow").remove()}supporterAddRow(t){t.preventDefault();let i=$($("#newSupporterTemplate").data("html"));this.$supporterAdderRow.before(i)}supporterDelRow(t){t.preventDefault(),$(t.target).parents(".supporterRow").remove()}initMinSupporters(){this.$editforms.submit((t=>{if($("#personTypeOrga").prop("checked"))return;let i=0;this.$supporterData.find(".supporterRow").each(((t,e)=>{""!==$(e).find("input.name").val().trim()&&i++})),i<this.$supporterData.data("min-supporters")&&(t.preventDefault(),bootbox.alert(__t("std","min_x_supporter").replace(/%NUM%/,this.$supporterData.data("min-supporters"))))}))}fullTextAdderOpen(t){t.preventDefault(),$(t.target).parent().addClass("hidden"),$("#fullTextHolder").removeClass("hidden")}fullTextAdd(){let t=this.$fullTextHolder.find("textarea").val().split(";"),i=$("#newSupporterTemplate").data("html"),e=()=>{let t=this.$supporterData.find(".supporterRow");for(let i=0;i<t.length;i++){let e=t.eq(i);if(""==e.find(".name").val()&&""==e.find(".organization").val())return e}let e=$(i);return this.$supporterAdderRow.length>0?this.$supporterAdderRow.before(e):$(".fullTextAdder").before(e),e},a=null;for(let i=0;i<t.length;i++){if(""==t[i])continue;let r=e();if(null==a&&(a=r),r.find("input.organization").length>0){let e=t[i].split(",");r.find("input.name").val(e[0].trim()),e.length>1&&r.find("input.organization").val(e[1].trim())}else r.find("input.name").val(t[i])}this.$fullTextHolder.find("textarea").trigger("select").trigger("focus"),a.length&&a.scrollintoview()}onKeyOnTextfield(t){let i;if(13==t.keyCode)if(t.preventDefault(),t.stopPropagation(),i=$(t.target).parents(".supporterRow"),i.next().hasClass("adderRow")){let t=$($("#newSupporterTemplate").data("html"));this.$supporterAdderRow.before(t),t.find("input[type=text]").first().focus()}else i.next().find("input[type=text]").first().focus();else if(8==t.keyCode){if(i=$(t.target).parents(".supporterRow"),""!=i.find("input.name").val())return;if(""!=i.find("input.organization").val())return;i.remove(),this.$supporterAdderRow.prev().find("input.name, input.organization").last().trigger("focus")}}submit(t){$("#personTypeOrga").prop("checked")&&2===this.settings.hasResolutionDate&&""===$("#resolutionDate").val()&&(t.preventDefault(),bootbox.alert(__t("std","missing_resolution_date"))),$("#personTypeNatural").prop("checked")&&2===this.settings.contactGender&&""===$("#initiatorGender").val()&&(t.preventDefault(),bootbox.alert(__t("std","missing_gender")))}initAdminSetUser(){this.$widget.find(".initiatorCurrentUsername .btnEdit").on("click",(()=>{this.$widget.find("input[name=initiatorSet]").val("1"),this.$widget.find(".initiatorCurrentUsername").addClass("hidden"),this.$widget.find(".initiatorSetUsername").removeClass("hidden")}))}}}));
//# sourceMappingURL=InitiatorForm.js.map
