<template>
    <div class="comments">

        <div
            v-if="comments.length"
            class="comments__items"
        >
            <comment-item
                v-for="commentUuid in comments"
                :key="'c_'+commentUuid"
                :comment="getComment(commentUuid)"
                :is-relevant-to-me="canComment"
            />
        </div>

        <div
            v-else
            class="comments__empty"
        >No comments yet.</div>

        <div
            v-if="canComment && isShowingReplyForm"
            class="comments__form-area"
        >
            <div class="form comments__form">
                <textarea
                    v-model="commentText"
                    class="input input--textarea input--space-bottom"
                    placeholder="Your thoughts..."
                    rows="6"
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
                <div>
                    <input
                        v-model="isReview"
                        type="checkbox"
                        class="checkbox"
                        :id="'review_'+uniqueKey"
                    />
                    <label
                        :for="'review_'+uniqueKey"
                    >It's a review</label>
                </div>

                <div>
                    <input
                        v-model="isRepick"
                        type="checkbox"
                        class="checkbox"
                        :id="'repick_'+uniqueKey"
                    />
                    <label
                        :for="'repick_'+uniqueKey"
                    >I want to get a pick changed</label>
                </div>

                <div
                    v-show="isReview || isRepick"
                    class="comments__select-block"
                >
                    <label
                        v-if="isReview"
                        :for="'select_'+uniqueKey"
                    >Select a game you want to write a review for:</label>
                    <label
                        v-if="isRepick"
                        :for="'select_'+uniqueKey"
                    >Select a game you want to get changed:</label>
                    <select
                        :id="'select_'+uniqueKey"
                        v-model="selectedPickUuid"
                        class="input input--space-bottom"
                    >
                        <option :value="''">-----</option>
                        <option
                            v-for="game in pickedGames"
                            :value="game.pickUuid"
                            :disabled="gameOptionIsDisabled(game)"
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
    import {mapGetters, mapState} from 'vuex';
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
                isReview: false,
                isRepick: false
            };
        },
        computed: {
            ...mapState(['NOT_PLAYED', 'UNFINISHED']),

            ...mapState({
                commentTypes: 'GAME_REFERENCE_TYPE'
            }),

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
            },
            isRepick: function (newValue) {
                if (newValue)
                    this.isReview = false;
            },
            isReview: function (newValue) {
                if (newValue)
                    this.isRepick = false;

                let selectedGame = Object.values(this.pickedGames).filter(game => game.pickUuid === this.selectedPickUuid).shift();

                if (selectedGame && selectedGame.reviewExists)
                    this.selectedPickUuid = '';
            }
        },
        methods: {
            showReplyForm () {
                this.isShowingReplyForm = true;
            },

            gameOptionIsDisabled (game) {
                if (this.isReview && !!game.reviewExists)
                    return true;

                return this.isRepick && game.playedStatus !== this.NOT_PLAYED && game.playedStatus !== this.UNFINISHED;
            },

            addComment () {

                let referenceType = this.isReview ? this.commentTypes.REVIEW : this.isRepick ? this.commentTypes.REPICK : null;

                this.$emit('add-comment', {
                    uuid: uuid.v4(),
                    text: this.commentText,
                    referencedPick: referenceType ? this.selectedPickUuid : '',
                    referencedGame: (referenceType && this.selectedGame) ? this.selectedGame.id: '',
                    gameReferenceType: referenceType
                });
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comments{

        &__items{
            margin-bottom: 10px;
        }

        &__empty{
            color: @color-gray-dark;
            font-style: italic;
            font-size: 13px;
            padding: 0 0 6px 30px;
            
            .dark-mode &{
                color: @color-gray;
            }
        }

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

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__game-img{
            display: block;
            width: 100%;
        }
    }

</style>