<template>
    <div class="pick">
        <template v-if="pick.uuid">
            <div
                v-if="pick.rejected"
                class="pick__request"
            >Repick requested!</div>
            <div class="pick__game">
                <div
                    :style="'background-image: url(https://steamcdn-a.akamaihd.net/steam/apps/'+game.localId+'/capsule_184x69.jpg);'"
                    class="pick__img"
                ></div>
                <a
                    :href="'https://store.steampowered.com/app/'+game.localId+'/'"
                    target="_blank"
                    class="pick__name"
                >
                    {{game.name}}
                </a>
                <div class="pick__stats">
                    <a
                        :href="'https://steamcommunity.com/profiles/'+userId+'/stats/'+game.localId+'/achievements/'"
                        target="_blank"
                        class="pick__stats-item"
                    >
                        <i class="fa-icon fas fa-fw fa-trophy"></i>{{+pick.playingState.achievements}} / {{+game.achievements}}
                    </a>
                    <div class="pick__stats-item">
                        <i class="fa-icon far fa-fw fa-clock"></i>{{playedHours}} hrs
                    </div>
                    <div class="pick__rewards">
                        <div
                            v-for="reward in rewards"
                            :class="['pick__reward', 'medal', {'medal--completed': reward.reason === reasonCompleted}]"
                            :title="rewardHints[reward.reason]"
                        >{{reward.value}}</div>

                        <div
                            v-if="potentialBeatenReward"
                            :title="rewardHints[ potentialBeatenReward.reason ]"
                            class="pick__reward medal medal--absent"
                        >{{potentialBeatenReward.value}}</div>

                        <div
                            v-if="potentialCompletedReward"
                            :title="rewardHints[ potentialCompletedReward.reason ]"
                            class="pick__reward medal medal--completed medal--absent"
                        >{{potentialCompletedReward.value}}</div>
                    </div>
                </div>
            </div>
            <div class="pick__links">
                <span
                    v-if="canChangePick && !isChangingPick"
                    @click="changePick"
                    class="edit-link"
                >change pick</span>
                <span
                    v-if="canChangePick && isChangingPick"
                    @click="cancelChangingPick"
                    class="edit-link"
                >Cancel changing pick</span>
            </div>
            <status-item
                :status="pick.playedStatus"
                :is-participant="isParticipant"
                class="pick__status"
                @change-status="changeStatus"
            />
        </template>
        <picking-game-form
            v-if="(isPicker && !pick.uuid) || isChangingPick"
            @select-game="selectGame"
            :initial-show-form="isChangingPick"
        />
        <div
            v-if="!pick.uuid && !isPicker"
            class="pick__placeholder"
        >Not picked yet.</div>
        <error-box v-if="pickError">{{pickError}}</error-box>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    import StatusItem from "./StatusItem";
    import PickingGameForm from "./PickingGameForm";
    import ErrorBox from "./ErrorBox";
    export default {
        name: "PickItem",
        components: {ErrorBox, PickingGameForm, StatusItem},
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
                    },
                    rejected: false
                })
            },
            userId: {
                type: String,
                default: ''
            },
            isPicker: {
                type: Boolean,
                default: false
            },
            isParticipant: {
                type: Boolean,
                default: false
            },
            rewards: {
                type: Object,
                default: () => ({})
            },
            potentialRewards: {
                type: Object,
                default: () => ({})
            },
            pickError: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                isChangingPick: false
            };
        },
        computed: {
            ...mapState(['BEATEN', 'COMPLETED']),
            ...mapGetters(['rewardReasons', 'rewardHints']),

            game: function () {
                return this.$store.getters.getGame(this.pick.game);
            },

            gameId: function () {
                return this.game ? this.game.id : '';
            },

            playedHours: function () {
                return (+this.pick.playingState.playtime / 60).toFixed(1);
            },

            canChangePick: function () {
                if (!this.isPicker)
                    return false;

                return !(
                    (this.pick.playedStatus === this.BEATEN)
                    ||
                    (this.pick.playedStatus === this.COMPLETED)
                );
            },

            reasonCompleted: function () {
                return this.rewardReasons.GAME_COMPLETED;
            },

            potentialCompletedReward: function () {
                if (this.pick.playedStatus === this.COMPLETED)
                    return null;

                return this.potentialRewards[this.reasonCompleted];
            },

            potentialBeatenReward: function () {
                if ((this.pick.playedStatus === this.COMPLETED) || (this.pick.playedStatus === this.BEATEN))
                    return null;

                return this.potentialRewards[ this.rewardReasons.GAME_BEATEN_UNI ];
            }
        },
        watch: {
            gameId: function () {
                this.isChangingPick = false;
            }
        },
        methods: {
            changePick: function () {
                this.isChangingPick = true;
            },

            cancelChangingPick: function () {
                this.isChangingPick = false;
            },

            selectGame: function ($event) {
                this.$emit('select-game', $event);
            },

            changeStatus: function ($event) {
                this.$emit('change-status', $event);
            }
        },
        created() {
            if (this.pick.rejected && this.isPicker)
                this.$store.dispatch('setRepickNotification', this.pick.uuid);
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .pick{
        display: flex;
        flex-direction: column;
        flex-grow: 1;

        &__game{
            padding: 0 10px;
        }

        &__img{
            background-position: top center;
            box-sizing: border-box;
            width: 184px;
            height: 69px;
            border: 1px solid @color-cobalt;
            margin: 0 auto 6px;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__name{
            display: block;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            line-height: 1.1;
            margin-bottom: 10px;
        }

        &__stats{
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        &__stats-item{
            margin: 0 5px;
            color: @color-text;
            white-space: nowrap;

            .dark-mode &{
                color: @color-light-text;
            }
        }

        &__rewards{
            white-space: nowrap;
        }

        &__reward{
            margin: 0 3px;
        }

        &__links{
            text-align: center;
            margin-bottom: 10px;

            &:empty{
                margin-bottom: 0;
            }
        }

        &__placeholder{
            text-align: center;
            padding: 10px;
            color: @color-cobalt;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__status{
            margin-top: auto;
        }

        &__request{
            text-align: center;
            padding: 4px 10px;
            color: @color-red;
            background: @color-gray;
            border-top: 1px solid @color-red;
            border-bottom: 1px solid @color-red;
            margin-bottom: 10px;

            .dark-mode &{
                color: @color-red-light;
                background-color: @color-gray-darker;
                font-weight: bold;
            }
        }
    }

</style>