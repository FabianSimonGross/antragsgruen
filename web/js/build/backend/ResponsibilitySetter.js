define(["require","exports"],function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=function(){function e(e){(this.$list=e).find(".respHolder .respUser").on("click",this.userSelected.bind(this)),e.find(".respHolder .respCommentRow button").on("click",this.saveComment.bind(this))}return e.prototype.userSelected=function(e){e.preventDefault();var t=$(e.currentTarget),r=t.parents(".respHolder").first(),s=t.data("user-id"),n=t.find(".name").text(),i=r.data("save-url");$.post(i,{_csrf:$("input[name=_csrf]").val(),user:s},function(e){e.success?(r.find(".respUser").removeClass("selected"),t.addClass("selected"),r.find(".responsibilityUser").text(n).data("user-id",s)):alert("An error occurred while saving")})},e.prototype.saveComment=function(e){e.preventDefault();var t=$(e.currentTarget).parents(".respHolder").first(),r=t.find(".respCommentRow input[type=text]").val(),s=t.data("save-url");$.post(s,{_csrf:$("input[name=_csrf]").val(),comment:r},function(e){e.success?t.find(".responsibilityComment").text(r):alert("An error occurred while saving")})},e}();t.ResponsibilitySetter=r});
//# sourceMappingURL=ResponsibilitySetter.js.map
