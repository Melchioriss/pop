<template>
    <div
        :class="['comment', {'comment--new': isNew}]"
        :id="'comment_'+comment.uuid"
    >
        <div class="comment__user-img-block">
            <img
                :src="user.avatar"
                :alt="user.profileName"
                class="comment__user-img"
            />
        </div>
        <div class="comment__body">
            <div class="comment__head-line">
                <div class="comment__user-name">{{user.profileName}}</div>
                <div class="comment__date">
                    <span
                        :title="$getExactDatetime(comment.createdAt)"
                    >{{$getRelativeDate(comment.createdAt)}}</span>
                    <span
                        v-if="comment.updatedAt"
                        :title="$getExactDatetime(comment.updatedAt)"
                    >(edited {{$getRelativeDate(comment.updatedAt)}})</span>
                </div>
                <span
                    v-if="isAuthor"
                    @click="startEditing"
                    class="edit-link comment__edit-link"
                >edit</span>
            </div>
            <div
                v-if="isReview"
                :class="['comment__game-line', 'comment__game-line--'+playedStatusLowerCase]"
            >
                <div class="comment__game-title">{{game.name}}</div>
            </div>
            <div
                v-if="isRepick"
                class="comment__repick-request"
            >
                {{user.profileName}} is asking to repick
                <a
                    :href="'https://store.steampowered.com/app/'+game.localId+'/'"
                >{{game.name}}</a>
            </div>
            <div v-if="isEditing">
                <textarea
                    v-model="newText"
                    class="input input--textarea input--space-bottom"
                ></textarea>
                <button
                    @click="saveEditedComment"
                    type="button"
                    class="button button--space-right"
                >save</button>
                <button
                    @click="endEditing"
                    type="button"
                    class="button button--space-right"
                >cancel</button>
            </div>
            <div
                v-else
                v-html="$getMarkedContent(comment.text)"
                class="text"
            ></div>
        </div>
        <a
            v-if="isReview"
            :href="'https://store.steampowered.com/app/'+game.localId+'/'"
            target="_blank"
            class="comment__game"
        >
            <img
                :src="'https://steamcdn-a.akamaihd.net/steam/apps/'+game.localId+'/capsule_184x69.jpg'"
                class="comment__game-img"
            />
        </a>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    export default {
        name: "CommentItem",
        props: {
            comment: {
                type: Object,
                default: () => ({
                    uuid: '',
                    user: '',
                    createdAt: '',
                    text: '',
                    gameReferenceType: null,
                    referencedGame: null,
                    referencedPick: null
                })
            },
            isRelevantToMe: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isEditing: false,
                newText: '',
                isNotificationSet: false
            };
        },
        computed: {
            ...mapState({
                commentTypes: 'GAME_REFERENCE_TYPE'
            }),

            ...mapGetters([
                'getUser',
                'getPick',
                'getGame',
                'statusTexts',
                'loggedUserSteamId'
            ]),

            user: function () {
                return this.getUser(this.comment.user);
            },

            pick: function () {
                return this.getPick(this.comment.referencedPick);
            },

            game: function () {
                return this.comment.referencedGame ? this.getGame(this.comment.referencedGame) : null;
            },

            playedStatusLowerCase: function () {
                return this.pick ? this.statusTexts[this.pick.playedStatus].toLowerCase() : '';
            },

            isAuthor: function () {
                return this.comment.user === this.loggedUserSteamId;
            },

            isReview: function () {
                return this.comment.gameReferenceType === this.commentTypes.REVIEW;
            },

            isRepick: function () {
                return this.comment.gameReferenceType === this.commentTypes.REPICK;
            },

            isNew: function () {
                let isNew = false;
                let lastVisit = new Date(this.$store.state.lastVisit).getTime();

                if (this.isAuthor || !lastVisit)
                    isNew = false;
                else
                {
                    let commentDate = new Date(this.comment.createdAt).getTime();
                    let updatedDate = new Date(this.comment.updatedAt).getTime();

                    isNew = ((lastVisit <= commentDate) || (lastVisit <= updatedDate));
                }

                if (isNew && !this.isAuthor && !this.isNotificationSet && this.isRelevantToMe)
                {
                    this.$store.dispatch('setCommentNotification', this.comment.uuid);
                }

                return isNew;
            }
        },
        methods: {
            startEditing: function () {
                this.newText = this.comment.text;
                this.isEditing = true;
            },

            endEditing: function () {
                this.isEditing = false;
            },

            saveEditedComment: function () {
                this.$store.dispatch('updateComment', {...this.comment, text: this.newText})
                    .finally(() => this.endEditing());
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comment{
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid @color-dark-orange;

        .dark-mode &{
            border-bottom-color: @color-dark-orange-hover;
        }

        &--new{
            background: @color-gray;
            position: relative;

            &:after{
                content: 'New';
                display: block;
                font-size: 12px;
                color: @color-dark-orange;
                padding: 2px 4px;
                position: absolute;
                top: -1px;
                right: 0;
            }

            .dark-mode &{
                background: fade(@color-gray-darkest, 80%);
                
                &:after{
                    color: @color-light-orange;
                }
            }
        }

        &__user-img-block{
            width: 40px;
            flex-shrink: 0;
            margin-right: 10px;
        }

        &__user-img{
            width: 100%;
            display: block;
            border: 1px solid @color-cobalt;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__body{
            flex-grow: 1;
            min-width: 0;
        }

        &__head-line{
            display: flex;
            align-items: baseline;
            margin-bottom: 6px;
        }

        &__user-name{
            font-weight: bold;
            font-size: 14px;
            margin-right: 20px;
        }

        &__date{
            color: @color-cobalt;
            font-size: 13px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__edit-link{
            margin-left: 20px;
        }

        &__game-line{
            margin-bottom: 4px;
            position: relative;

            &:after{
                content: '';
                display: block;
                height: 8px;
                width: 100%;
                position: absolute;
                top: 50%;
                left: 0;
                margin-top: -6px;
                z-index: 1;
            }

            &--unfinished:after{background: @color-unfinished;}
            &--beaten:after{background: @color-beaten;}
            &--completed:after{background: @color-completed;}
            &--abandoned:after{background: @color-abandoned;}
        }

        &__game-title{
            font-size: 16px;
            border: 1px solid @color-cobalt;
            padding: 2px 6px;
            display: inline-block;
            position: relative;
            z-index: 2;
            background: @color-gray-dark;
            color: @color-bg;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            max-width: 90%;

            .dark-mode &{
                border-color: @color-cobalt-light;
                background: @color-gray-darkest;
                color: @color-light-text;
            }
        }

        &__game{
            width: 184px;
            height: 69px;
            display: block;
            flex-shrink: 0;
            border: 1px solid @color-cobalt;
            margin-top: 23px;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__game-img{
            display: block;
            width: 100%;
        }

        &__body{
            padding-right: 10px;
        }

        &__repick-request{
            font-size: 14px;
            font-style: italic;
            background: fade(@color-gray-dark, 60%);
            padding: 4px 8px;
            margin: 6px 0;

            .dark-mode &{
                background: fade(@color-gray-darkest, 80%);
            }
        }
    }

</style>