<template>
    <div class="comment">
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
                <div
                    :title="$getExactDatetime(comment.createdAt)"
                    class="comment__date"
                >{{$getRelativeDate(comment.createdAt)}}</div>
            </div>
            <div
                v-if="comment.reviewedGame"
                :class="['comment__game-line', 'comment__game-line--'+playedStatusLowerCase]"
            >
                <div class="comment__game-title">{{game.name}}</div>
            </div>
            <div>{{comment.text}}</div>
        </div>
        <a
            v-if="comment.reviewedGame"
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
    import {mapGetters} from 'vuex';
    export default {
        name: "CommentItem",
        props: {
            comment: {
                type: Object,
                default: () => ({
                    user: '',
                    createdAt: '',
                    text: '',
                    reviewedGame: null
                })
            },
            pickUuid: {
                type: String,
                default: ''
            }
        },
        data() {
            return {};
        },
        computed: {
            ...mapGetters([
                'getUser',
                'getPick',
                'getGame',
                'statusTexts'
            ]),

            user: function () {
                return this.getUser(this.comment.user);
            },

            pick: function () {
                return this.getPick(this.pickUuid);
            },

            game: function () {
                return this.comment.reviewedGame ? this.getGame(this.comment.reviewedGame) : null;
            },

            playedStatusLowerCase: function () {
                return this.pick ? this.statusTexts[this.pick.playedStatus].toLowerCase() : '';
            }
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comment{
        display: flex;
        padding-bottom: 10px;
        border-bottom: 1px solid @color-dark-orange;
        margin-bottom: 10px;

        &__user-img-block{
            width: 40px;
            flex-shrink: 0;
            margin-right: 10px;
        }

        &__user-img{
            width: 100%;
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
            background: @color-gray;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            max-width: 90%;
        }

        &__game{
            width: 184px;
            height: 69px;
            display: block;
            flex-shrink: 0;
            border: 1px solid @color-cobalt;
            margin-top: 23px;
        }

        &__game-img{
            display: block;
            width: 100%;
        }

        &__body{
            padding-right: 10px;
        }
    }

</style>