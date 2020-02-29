<template>
    <div class="leaderboard-item">
        <div class="leaderboard-item__user">
            <div class="leaderboard-item__position">#{{number}}</div>
            <div class="leaderboard-item__user-name">{{user.profileName}}</div>
            <div
                v-if="participant.totalRewardValue >= POINTS_THRESHOLD"
                class="leaderboard-item__status"
            >
                <i class="fa-icon fas fa-fw fa-check-circle"></i>Completed
            </div>
            <div
                v-if="participant.totalRewardValue < POINTS_THRESHOLD"
                class="leaderboard-item__status leaderboard-item__status--failed"
            >
                <i class="fa-icon fas fa-fw fa-times-circle"></i>Not completed
            </div>
        </div>
        <div class="leaderboard-item__games">

            <template v-for="pickerType in [MAJOR, MINOR]">

                <leaderboard-game
                    v-for="pickType in pickTypes[pickerType]"
                    :key="'lb_game_'+pickerType+'_'+pickType"
                    :pick="getPick(participant.picks[pickerType][pickType])"
                    :rewards="participant.rewards[ participant.picks[pickerType][pickType] ]"
                    :user-id="participant.user"
                    class="leaderboard-item__game"
                />

            </template>

        </div>
        <div class="leaderboard-item__bonus">
            <div
                v-if="all7BeatenReward"
                :title="rewardHints[ rewardReasons.ALL_PICKS_BEATEN ]"
                class="medal"
            >{{all7BeatenReward.value}}</div>
        </div>
        <div class="leaderboard-item__blaeo">
            <div
                v-if="blaeoPoints > 0"
                :title="rewardHints[ rewardReasons.BLAEO_POINTS ]"
                class="medal medal--blaeo"
            >{{blaeoPoints}}</div>
        </div>
        <div class="leaderboard-item__total">
            <div class="medal medal--total" title="Total points achieved">{{participant.totalRewardValue}}</div>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    import LeaderboardGame from "./LeaderboardGame";
    export default {
        name: "LeaderboardItem",
        components: {LeaderboardGame},
        props: {
            participant: {
                type: Object,
                default: () => ({
                    uuid: '',
                    user: '',
                    active: true,
                    groupWins: '',
                    blaeoGames: '',
                    pickers: [],
                    totalRewardValue: 0
                })
            },
            number: {
                type: Number,
                default: 0
            }
        },
        data() {
            return {};
        },
        computed: {
            ...mapState([
                'MAJOR', 'MINOR',
                'SHORT', 'MEDIUM', 'LONG', 'VERY_LONG',
                'POINTS_THRESHOLD'
            ]),

            ...mapGetters([
                'getUser',
                'getPick',
                'pickTypes',
                'rewardReasons',
                'rewardHints'
            ]),
            user: function () {
                return this.getUser(this.participant.user);
            },
            blaeoPoints: function () {
                let reward = this.participant.rewards.global ? this.participant.rewards.global[ this.rewardReasons.BLAEO_POINTS ] : null;
                return reward ? reward.value : null;
            },
            all7BeatenReward: function () {
                return this.participant.rewards.global ? this.participant.rewards.global[ this.rewardReasons.ALL_PICKS_BEATEN ] : null;
            },
        },
        methods: {

        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/medal";
    @import "../assets/leaderboard-item";

</style>