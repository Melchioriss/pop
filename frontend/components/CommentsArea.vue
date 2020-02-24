<template>
    <div class="comments">
        <comment-item
            v-for="comment in comments"
            :comment="comment"
            :pickUuid="pickedGames[comment.reviewedGame] ? pickedGames[comment.reviewedGame].pickUuid : ''"
        />

        <div
            v-if="canComment && isShowingReplyForm"
            class="comments__form-area"
        >
            <div class="form comments__form">
                <textarea
                    v-model="commentText"
                    class="input input--textarea input--space-bottom"
                    placeholder="Your thoughts..."
                    rows="7"
                ></textarea>
                <button
                    @click="addComment"
                    type="button"
                    class="button"
                >Reply</button>
            </div>
            <div class="comments__game-block">
                <label class="form__label">If you want this comment to be a game review, then select a game:</label>
                <select
                    v-model="selectedPickUuid"
                    class="input input--space-bottom"
                >
                    <option :value="''">-----</option>
                    <option
                        v-for="game in pickedGames"
                        :value="game.pickUuid"
                        :disabled="!!game.commentExists"
                    >{{game.name}}</option>
                </select>
                <div
                    v-if="selectedPickUuid"
                    class="comments__game"
                >
                    <a
                        :href="'https://store.steampowered.com/app/'+selectedGame.localId+'/'"
                        target="_blank"
                        class="comments__game-img-block"
                    >
                        <img
                            :src="'https://steamcdn-a.akamaihd.net/steam/apps/'+selectedGame.localId+'/capsule_184x69.jpg'"
                            class="comments__game-img"
                        />
                    </a>
                    <div class="comments__game-name">{{selectedGame.name}}</div>
                </div>
            </div>
        </div>
        <span
            v-if="canComment && !isShowingReplyForm"
            @click="showReplyForm"
            class="edit-link edit-link--comments-show"
        >Show Reply Form</span>
    </div>
</template>

<script>
    import CommentItem from "./CommentItem";
    export default {
        name: "CommentsArea",
        components: {CommentItem},
        props: {
            comments: {
                type: Array,
                default: []
            },
            canComment: {
                type: Boolean,
                default: false
            },
            pickedGames: {
                type: Object,
                default: () => ({})
            }
        },
        data() {
            return {
                isShowingReplyForm: false,
                commentText: '',
                selectedPickUuid: ''
            };
        },
        computed: {
            commentsCount: function () {
                return this.comments.length;
            },
            selectedGame: function () {
                let pick = this.$store.getters.getPick(this.selectedPickUuid);
                return pick ? this.$store.getters.getGame(pick.game): null;
            }
        },
        watch: {
            commentsCount: function () {
                this.isShowingReplyForm = false;
                this.commentText = '';
            }
        },
        methods: {
            showReplyForm () {
                this.isShowingReplyForm = true;
            },

            addComment () {
                this.$emit('add-comment', {
                    text: this.commentText,
                    reviewedPickUuid: this.selectedPickUuid,
                    reviewedGame: this.selectedGame ? this.selectedGame.id: ''
                });
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comments{
        margin-bottom: 10px;

        &__form-area{
            display: flex;
        }

        &__form{
            width: 600px;
            flex-shrink: 0;
            margin-right: 20px;
        }

        &__game-block{
            flex-grow: 1;
        }

        &__game{
            display: flex;
            align-items: center;
        }

        &__game-img-block{
            display: block;
            width: 184px;
            height: 69px;
            border: 1px solid @color-cobalt;
            flex-shrink: 0;
            margin-right: 10px;
        }

        &__game-img{
            display: block;
            width: 100%;
        }
    }

</style>