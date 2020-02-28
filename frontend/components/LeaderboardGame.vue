<template>
    <div class="leaderboard-game">
        <template v-if="pick.game">
            <a
                :href="'https://store.steampowered.com/app/'+game.localId+'/'"
                target="_blank"
                class="leaderboard-game__name"
            >{{game.name}}</a>
            <div class="leaderboard-game__stats">
                <a
                    :href="'https://steamcommunity.com/profiles/'+userId+'/stats/'+game.localId+'/achievements/'"
                    target="_blank"
                    class="leaderboard-game__stat"
                >
                    <i class="fa-icon fas fa-fw fa-trophy"></i>{{+pick.playingState.achievements}} / {{+game.achievements}}
                </a>
                <div class="leaderboard-game__stat">
                    <i class="fa-icon far fa-fw fa-clock"></i>{{playedHours}} hrs
                </div>
            </div>
            <div
                :class="['leaderboard-game__status', 'leaderboard-game__status--'+statusLowerCase]"
                :title="this.statusTexts[this.pick.playedStatus]"
            ></div>
            <div class="leaderboard-game__points">
                <div
                    v-for="reward in rewards"
                    :class="['leaderboard-game__inline-medal', 'medal', {'medal--completed': reward.reason === reasonCompleted}]"
                    :title="rewardHints[reward.reason]"
                >{{reward.value}}</div>
            </div>
        </template>
        <div v-else class="leaderboard-game__no-pick">Not picked yet.</div>

    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    export default {
        name: "LeaderboardGame",
        props: {
            pick: {
                type: Object,
                default: () => ({
                    uuid: '',
                    type: 10,
                    game: '',
                    playedStatus: 0,
                    playingState: {
                        playtime: null,
                        achievements: null
                    }
                })
            },
            rewards: {
                type: Object,
                default: () => ({})
            },
            userId: {
                type: String,
                default: ''
            },
        },
        data() {
            return {};
        },
        computed: {
            ...mapState([
                'NOT_PLAYED',
                'UNFINISHED',
                'BEATEN',
                'COMPLETED',
                'ABANDONED'
            ]),

            ...mapGetters([
                'getGame',
                'rewardReasons',
                'rewardHints',
                'statusTexts'
            ]),

            game: function () {
                return this.getGame(this.pick.game);
            },

            playedHours: function () {
                return (+this.pick.playingState.playtime / 60).toFixed(1);
            },

            reasonCompleted: function () {
                return this.rewardReasons['GAME_COMPLETED'];
            },

            statusLowerCase: function () {
                return this.statusTexts[this.pick.playedStatus].toLowerCase();
            }
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/leaderboard-game";

</style>