define(["require","exports"],(function(t,e){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.VotingBlock=void 0;e.VotingBlock=class{constructor(t){this.$element=t;const e=this.$element.find(".currentVoting"),o=t.data("voting"),n=t.data("url-poll"),i=t.data("url-vote");this.widget=new Vue({el:e[0],template:'\n                <div class="currentVotings">\n                <voting-block-widget v-for="voting in votings" :voting="voting" @vote="vote"></voting-block-widget>\n                </div>',data:()=>({votings:o,pollingId:null}),methods:{vote:function(t,e,o,n,s){const c={_csrf:$("head").find("meta[name=csrf-token]").attr("content"),votes:[{itemGroupSameVote:e,itemType:o,itemId:n,vote:s}]},a=this,l=i.replace(/VOTINGBLOCKID/,t);$.post(l,c,(function(t){void 0===t.success||t.success?a.votings=t:alert(t.message)})).catch((function(t){alert(t.responseText)}))},reloadData:function(){const t=this;$.get(n,(function(e){t.votings=e})).catch((function(t){console.error("Could not load voting data from backend",t)}))},startPolling:function(){const t=this;this.pollingId=window.setInterval((function(){t.reloadData()}),3e3)}},beforeDestroy(){window.clearInterval(this.pollingId)},created(){this.startPolling()}})}}}));
//# sourceMappingURL=VotingBlock.js.map