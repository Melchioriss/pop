<template>
    <div class="game-options">

        <div
            v-for="(game, key) in games"
            :key="'game_'+game.id"
            class="game-options__item"
            @click="selectGame(game)"
        >
            <div
                :style="'background-image: url(https://steamcdn-a.akamaihd.net/steam/apps/'+game.localId+'/capsule_184x69.jpg);'"
                class="game-options__item-img"
            ></div>
            <div class="game-options__item-base">
                <a
                    :href="'https://store.steampowered.com/app/'+game.localId+'/'"
                    target="_blank"
                >{{game.name}}</a>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "GameOptions",
        props: {
            games: {
                type: Array,
                default: []
            }
        },
        data() {
            return {};
        },
        methods: {
            selectGame (game) {
                this.$emit('select-game', game);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .game-options{

        &__item{
            border-bottom: 1px solid @color-dark-orange;
            padding: 5px 0;
            font-size: 13px;
            display: flex;
            background: transparent;
            transition: background-color 0.3s;

            &:hover{
                background: @color-gray;
            }

            .dark-mode &{
                border-bottom-color: @color-dark-orange-hover;

                &:hover{
                    background: fade(@color-gray-darkest, 70%);
                }
            }
        }

        &__item-img{
            background-position: top center;
            background-repeat: no-repeat;
            background-size: contain;
            width: 92px;
            height: 35px;
            margin-right: 6px;
            flex-shrink: 0;
            border: 1px solid @color-cobalt;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__item-base{
            display: block;
            flex-grow: 1;
        }

        &__pagin{
            margin-top: 5px;
        }
    }

</style>