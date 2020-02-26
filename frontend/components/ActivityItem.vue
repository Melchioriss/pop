<template>
    <div class="activity-item">
        <div class="activity-item__row">
            <div class="activity-item__time">{{$getExactTime(activity.createdAt)}}</div>
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

                <span
                    v-if="user.steamId !== actor.steamId"
                    class="activity-item__fix"
                >
                    <i class="fa-icon fas fa-tools"></i>changed by
                    <a
                        :href="actor.profileUrl"
                    >{{actor.profileName}}</a>
                </span>

                <!--<a href="#">Ardiffaz</a> left a review for <a href="#">Pathfinder: Kingmaker - Enhanced Edition</a>:-->
                <!--<a href="#">insideone</a> joined the group-->
            </div>
        </div>
        <!--<div class="activity__review text">-->
            <!--<p>With the help of over 18,000 Kickstarter backers, Narrative Designer Chris Avellone and composer Inon Zur, Owlcat Games is proud to bring you the first isometric computer RPG set in the beloved Pathfinder tabletop universe. Enjoy a classic RPG experience inspired by games like Baldur's Gate, Fallout 1 and 2 and Arcanum. Explore and conquer the Stolen Lands and make them your kingdom!</p>-->
        <!--</div>-->
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
                'getPick'
            ]),

            user: function () {
                return this.getUser(this.activity.payload.participantUser);
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

            actor: function () {
                return this.getUser(this.activity.actor);
            }
        },
        methods: {


        }
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
        }

        &__pic-block{
            width: 34px;
            height: 34px;
            margin-right: 6px;
            border: 1px solid @color-cobalt;
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
        }

        &__stats{

        }

        &__stat-item{
            margin-left: 14px;
        }

        &__fix{
            margin-left: 20px;
            color: @color-cobalt;
        }
    }

</style>