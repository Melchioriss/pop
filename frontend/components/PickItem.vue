<template>
    <div class="pick">
        <template v-if="pick.uuid">
            <div class="pick__game">
                <div
                    :style="'background-image: url(https://steamcdn-a.akamaihd.net/steam/apps/'+pick.game.id+'/capsule_184x69.jpg);'"
                    class="pick__img"
                ></div>
                <a
                    :href="'https://store.steampowered.com/app/'+pick.game.id+'/'"
                    target="_blank"
                    class="pick__name"
                >
                    {{pick.game.name}}
                </a>
                <div class="pick__stats">
                    <a
                        :href="'https://steamcommunity.com/profiles/'+userId+'/stats/'+pick.game.id+'/achievements/'"
                        class="pick__stats-item"
                    >
                        <i class="fa-icon fas fa-fw fa-trophy"></i>{{+pick.playingState.achievements}} / {{+pick.game.achievements}}
                    </a>
                    <div class="pick__stats-item">
                        <i class="fa-icon far fa-fw fa-clock"></i>{{playedHours}} hrs
                    </div>
                </div>
            </div>
            <div
                v-if="!isChangingPick"
                class="pick__links"
            >
                <span
                    v-if="isPicker"
                    @click="changePick"
                    class="edit-link"
                >change pick</span>
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
    </div>
</template>

<script>
    import StatusItem from "./StatusItem";
    import PickingGameForm from "./PickingGameForm";
    export default {
        name: "PickItem",
        components: {PickingGameForm, StatusItem},
        props: {
            pick: {
                type: Object,
                default: () => ({
                    uuid: '',
                    type: 10,
                    game: {
                        id: 0,
                        name: '',
                        achievements: null,
                    },
                    playedStatus: 0,
                    playingState: {
                        playtime: null,
                        achievements: null
                    }
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
            }
        },
        data() {
            return {
                isChangingPick: false
            };
        },
        computed: {
            gameId: function () {
                return this.pick.game.id;
            },

            playedHours: function () {
                return (+this.pick.playingState.playtime / 60).toFixed(1);
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

            selectGame: function ($event) {
                this.$emit('select-game', $event);
            },

            changeStatus: function ($event) {
                this.$emit('change-status', $event);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .pick{

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
            align-items: baseline;
            justify-content: center;
            margin-bottom: 10px;
        }

        &__stats-item{
            margin: 0 5px;
            color: @color-text;

            a&:hover{
                color: fade(@color-text, 60%);
            }
        }

        &__links{
            text-align: center;
            margin-bottom: 10px;
        }

        &__placeholder{
            text-align: center;
            padding: 10px;
            color: @color-cobalt;
        }
    }

</style>