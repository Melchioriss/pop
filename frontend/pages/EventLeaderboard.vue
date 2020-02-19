<template>
    <div>
        <h1 class="title">PoP May-June-July</h1>

        <button type="button" class="button">Back to event</button>

        <div class="leaderboard">
            <div class="leaderboard__item leaderboard__item--head">
                <div class="leaderboard__user">Member</div>
                <div class="leaderboard__games">
                    <div class="leaderboard__games-head">Games</div>
                    <div class="leaderboard__game">Short</div>
                    <div class="leaderboard__game">Medium</div>
                    <div class="leaderboard__game">Long</div>
                    <div class="leaderboard__game">Very long</div>
                </div>
                <div class="leaderboard__bonus">All 7</div>
                <div class="leaderboard__blaeo">BLAEO points</div>
                <div class="leaderboard__total">Total</div>
            </div>
            <div class="leaderboard__item" v-for="i in 10">
                <div class="leaderboard__user">
                    <div class="leaderboard__position">#1</div>
                    <div>Ardiffaz Melchioriss</div>
                    <div class="leaderboard__status">
                        <i class="fa-icon fas fa-fw fa-check-circle"></i>Completed
                    </div>
                    <div class="leaderboard__status leaderboard__status--failed">
                        <i class="fa-icon fas fa-fw fa-times-circle"></i>Not completed
                    </div>
                </div>
                <div class="leaderboard__games">
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Stories: The Path of Destinies</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>7 / 7</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>3.7 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">2</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Dex</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>0 / 18</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>2.4 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">4</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Batman: Arkham Origins</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>35 / 35</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>25.8 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">6</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>

                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">DISTRAINT: Deluxe Edition</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>14 / 14</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>2.4 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">2</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Root Double -Before Crime * After Days</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>18 / 42</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>30.1 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">8</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Battle Chef Brigade</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>24 / 35</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>12.8 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">4</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Resident Evil 5</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>42 / 42</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>26.1 hrs</div>
                        </div>
                        <div class="leaderboard__game-points">
                            <div class="medal leaderboard__medal-inline">6</div>
                            <div class="medal medal--completed leaderboard__medal-inline">1</div>
                        </div>
                    </div>
                </div>
                <div class="leaderboard__bonus">
                    <div class="medal medal--completed">1</div>
                </div>
                <div class="leaderboard__blaeo">
                    <div class="medal medal--blaeo">18</div>
                </div>
                <div class="leaderboard__total">
                    <div class="medal medal--total">58</div>
                </div>
            </div>

            <div class="leaderboard__item">
                <div class="leaderboard__user">
                    Total per categories:
                </div>
                <div class="leaderboard__games">
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Short games</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>777</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>1258 hrs</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Medium games</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>777</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>1258 hrs</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Long games</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>777</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>1258 hrs</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Very long games</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>777</div>
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>1258 hrs</div>
                        </div>
                    </div>
                </div>
                <div class="leaderboard__bonus"></div>
                <div class="leaderboard__blaeo"></div>
                <div class="leaderboard__total"></div>
            </div>

            <div class="leaderboard__item">
                <div class="leaderboard__user">Total stats</div>
                <div class="leaderboard__games leaderboard__games--total">
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Total hours played</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon far fa-fw fa-clock"></i>5712 hrs</div>
                        </div>
                    </div>
                    <div class="leaderboard__game">
                        <div class="leaderboard__game-name">Total achievements unlocked</div>
                        <div class="leaderboard__game-stats">
                            <div class="leaderboard__game-stat"><i class="fa-icon fas fa-fw fa-trophy"></i>2584</div>
                        </div>
                    </div>
                </div>
                <div class="leaderboard__bonus"></div>
                <div class="leaderboard__blaeo"></div>
                <div class="leaderboard__total"></div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: "EventLeaderboard",
        props: {},
        data() {
            return {};
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/medal";

    .leaderboard{
        font-size: 14px;

        &__item{
            display: flex;
            align-items: flex-start;
            border-bottom: 1px solid @color-cobalt;
            padding: 20px 0;

            &--head{
                padding: 20px 0 0;
            }
        }

        &__user{width: 190px;}
        &__games{flex-grow: 1;}
        &__bonus{width: 60px;}
        &__blaeo{width: 60px;}
        &__total{width: 70px;}

        &__user, &__bonus, &__blaeo, &__total{
            flex-shrink: 0;
        }

        &__bonus, &__blaeo, &__total{
            text-align: center;
        }

        &__games-head{
            width: 100%;
            margin-bottom: 6px;
        }

        &__position{
            padding-bottom: 10px;
            color: @color-cobalt;
        }

        &__user{

        }

        &__status{
            font-size: 18px;
            color: @color-green;
            margin: 16px 0;

            &--failed{
                color: @color-red;
            }
        }

        &__games{
            display: flex;
            flex-wrap: wrap;
            margin-bottom: -20px;

            &--total{
                display: block;
            }
        }

        &__game{
            width: 25%;
            box-sizing: border-box;
            padding-right: 10px;
            margin-bottom: 30px;
        }

        &__game-name{
            color: @color-cobalt;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        &__game-stats{
            display: flex;
            margin-bottom: 4px;
        }

        &__game-stat{
            margin-right: 16px;
        }

        &__game-points{
            padding-left: 20px;
        }

        &__medal-inline{
            margin-right: 12px;
        }
    }

</style>