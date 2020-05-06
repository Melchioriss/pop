<template>
    <div class="activity-item">
        <div class="activity-item__row">
            <div class="activity-item__time">{{$getExactTime(activity.createdAt)}}</div>
            <div class="activity-item__link-block">
                <router-link
                    v-if="isChangedStatusType || isReviewAddedType"
                    :to="{name: 'event', params: {eventUuid: activity.payload.event}, hash: '#'+activity.payload.pick}"
                    class="activity-item__link"
                ><i class="fas fa-link"></i></router-link>
            </div>

            <div class="activity-item__pic-block">
                <img
                    :src="user.avatar"
                    :alt="user.profileName"
                    class="activity-item__img" />
            </div>
            <div class="activity-item__content">
                <a
                    :href="user.profileUrl"
                    target="_blank"
                >{{user.profileName}}</a>

                <span v-if="isChangedStatusType">
                    marked
                    <a
                        :href="'https://store.steampowered.com/app/'+game.localId"
                        target="_blank"
                    >{{game.name}}</a>
                    as
                    <span
                        :class="['activity-item__status', 'activity-item__'+pickStatusText.toLowerCase()]"
                    >{{pickStatusText}}</span>
                    <span class="activity-item__stats">
                        <span class="activity-item__stat-item">
                            <i
                                :class="['fa-icon', 'fas', 'fa-fw', 'fa-trophy', 'activity-item__'+pickStatusText.toLowerCase()]"
                            ></i>{{+pick.playingState.achievements}} / {{+game.achievements}}
                        </span>
                        <span class="activity-item__stat-item">
                            <i
                                :class="['fa-icon', 'far', 'fa-fw', 'fa-clock', 'activity-item__'+pickStatusText.toLowerCase()]"
                            ></i>{{playHours}} hrs
                        </span>
                    </span>
                </span>

                <span v-if="isReviewAddedType">
                    left a review for
                    <a
                        :href="'https://store.steampowered.com/app/'+game.localId"
                        target="_blank"
                    >{{game.name}}</a>:
                </span>

                <span v-if="isMemberAddedType">
                    joined the group
                </span>

                <span v-if="isMemberRemovedType">
                    left the group
                </span>

                <span
                    v-if="isChangedStatusType && user.steamId !== actor.steamId"
                    class="activity-item__fix"
                >
                    <i class="fa-icon fas fa-tools"></i>changed by
                    <a
                        :href="actor.profileUrl"
                    >{{actor.profileName}}</a>
                </span>
            </div>
        </div>
        <div
            v-if="isReviewAddedType"
            class="activity-item__review text"
            v-html="$getMarkedContent(comment.text)"
        ></div>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    export default {
        name: "ActivityItem",
        props: {
            activity: {
                type: Object,
                default: () => ({
                    uuid: '',
                    actor: '',
                    name: '',
                    payload: {},
                    createdAt: ''
                })
            }
        },
        data() {
            return {};
        },
        computed: {
            ...mapState([
                'ACTIVITY_TYPES'
            ]),

            ...mapGetters([
                'statusTexts',
                'getUser',
                'getGame',
                'getPick',
                'getComment'
            ]),

            user: function () {
                if (this.activity.payload.participantUser)
                    return this.getUser(this.activity.payload.participantUser);

                if (this.activity.payload.member)
                    return this.getUser(this.activity.payload.member);

                return this.getUser(this.activity.payload.user);
            },

            game: function () {
                return this.getGame(this.activity.payload.game);
            },

            pick: function () {
                return this.getPick(this.activity.payload.pick);
            },

            pickStatusText: function () {
                return this.statusTexts[ this.activity.payload.to ];
            },

            playHours: function () {
                return (this.pick.playingState.playtime / 60).toFixed(1);
            },

            isChangedStatusType: function () {
                return this.activity.name === this.ACTIVITY_TYPES.STATUS_CHANGE;
            },

            isReviewAddedType: function () {
                return this.activity.name === this.ACTIVITY_TYPES.REVIEW_ADDED;
            },

            isMemberAddedType: function () {
                return this.activity.name === this.ACTIVITY_TYPES.MEMBER_ADDED;
            },

            isMemberRemovedType: function () {
                return this.activity.name === this.ACTIVITY_TYPES.MEMBER_REMOVED;
            },

            actor: function () {
                return this.getUser(this.activity.actor);
            },

            comment: function () {
                return this.getComment(this.activity.payload.comment);
            }
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .activity-item{
        margin-bottom: 20px;

        &__row{
            display: flex;
            align-items: center;
        }

        &__time{
            color: @color-cobalt;
            font-size: 12px;
            margin-right: 10px;
            min-width: 60px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__link-block{
            display: block;
            width: 30px;
            margin-right: 10px;
        }

        &__link{
            display: block;
            text-align: center;
        }

        &__pic-block{
            width: 34px;
            height: 34px;
            margin-right: 6px;
            border: 1px solid @color-cobalt;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__img{
            display: block;
            width: 100%;
        }

        &__status{
            font-weight: bold;
        }

        &__beaten{color: @color-beaten;}
        &__completed{color: @color-completed;}
        &__unfinished{color: @color-unfinished;}
        &__abandoned{color: @color-abandoned;}

        &__review{
            padding-left: 10px;
            margin: 10px 0 10px 110px;
            border-left: 2px solid @color-cobalt;

            .dark-mode &{
                border-left-color: @color-cobalt-light;
            }
        }

        &__stat-item{
            margin-left: 14px;
        }

        &__fix{
            margin-left: 20px;
            color: @color-cobalt;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }
    }

</style>