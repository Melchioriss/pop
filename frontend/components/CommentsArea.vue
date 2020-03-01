<template>
    <div class="comments">
        <comment-item
            v-for="commentUuid in comments"
            :key="'c_'+commentUuid"
            :comment="getComment(commentUuid)"
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
                    rows="9"
                ></textarea>
                <button
                    @click="addComment"
                    type="button"
                    class="button"
                >Reply</button>
            </div>
            <div
                v-if="canSelectGames"
                class="comments__game-block"
            >
                <input
                    v-model="isReview"
                    type="checkbox"
                    class="checkbox"
                    :id="'review_'+uniqueKey"
                />
                <label
                    :for="'review_'+uniqueKey"
                    class=""
                >It's a review</label>
                <div
                    v-show="isReview"
                    class="comments__select-block"
                >
                    <label
                        :for="'select_'+uniqueKey"
                    >Select a game you want to write a review for:</label>
                    <select
                        :id="'select_'+uniqueKey"
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
        </div>
        <span
            v-if="canComment && !isShowingReplyForm"
            @click="showReplyForm"
            class="edit-link edit-link--comments-show"
        >Show Reply Form</span>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import uuid from 'uuid';
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
            isParticipant: {
                type: Boolean,
                default: false
            },
            pickedGames: {
                type: Object,
                default: () => ({})
            },
            uniqueKey: {
                type: String,
                default: 'comments_'+Math.random()
            }
        },
        data() {
            return {
                isShowingReplyForm: false,
                commentText: '',
                selectedPickUuid: '',
                isReview: false
            };
        },
        computed: {
            ...mapGetters([
                'getComment'
            ]),

            commentsCount: function () {
                return this.comments.length;
            },
            selectedGame: function () {
                let pick = this.$store.getters.getPick(this.selectedPickUuid);
                return pick ? this.$store.getters.getGame(pick.game): null;
            },
            canSelectGames: function () {
                if (!this.isParticipant)
                    return false;

                return Object.values(this.pickedGames).length > 0;
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
                    uuid: uuid.v4(),
                    text: this.commentText,
                    reviewedPickUuid: this.isReview ? this.selectedPickUuid : '',
                    reviewedGame: (this.isReview && this.selectedGame) ? this.selectedGame.id: ''
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

        &__select-block{
            margin: 10px 0 6px;
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