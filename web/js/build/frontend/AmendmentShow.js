define(["require","exports","../shared/MotionInitiatorShow"],function(t,e){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),new(function(){function t(){new MotionInitiatorShow,$("form.delLink").submit(this.delSubmit.bind(this)),$(".share_buttons a").click(this.shareLinkClicked.bind(this));var t=location.hash.split("#comm");2==t.length&&$("#comment"+t[1]).scrollintoview({top_offset:-100}),this.initPrivateComments(),this.initCmdEnterSubmit()}return t.prototype.delSubmit=function(t){t.preventDefault();var e=t.target;bootbox.confirm(__t("std","del_confirm"),function(t){t&&e.submit()})},t.prototype.shareLinkClicked=function(t){var e=$(t.currentTarget).attr("href");window.open(e,"_blank","width=600,height=460")&&t.preventDefault()},t.prototype.initCmdEnterSubmit=function(){$(document).on("keypress","form textarea",function(t){t.originalEvent.metaKey&&13===t.originalEvent.keyCode&&$(t.currentTarget).parents("form").first().find("button[type=submit]").trigger("click")})},t.prototype.initPrivateComments=function(){$(".privateNoteOpener").click(function(){$(".privateNoteOpener").remove(),$(".motionData .privateNotes").removeClass("hidden"),$(".motionData .privateNotes textarea").focus()}),$(".privateNotes blockquote").click(function(){$(".privateNotes blockquote").addClass("hidden"),$(".privateNotes form").removeClass("hidden"),$(".privateNotes textarea").focus()})},t}())});
//# sourceMappingURL=AmendmentShow.js.map
