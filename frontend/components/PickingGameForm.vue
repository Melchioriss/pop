<template>
    <div class="picking-game">
        <div
            v-if="isPickingFormShown"
        >
            <input
                v-model="searchString"
                type="text"
                class="input"
                placeholder="Type a game title or its ID..."
            />
            <div
                v-if="isGamesNotFound"
                class="picking-game__not-found"
            >No games match your request.</div>
            <game-options
                :games="foundGames"
                @select-game="selectGame"
            />
            <pagin-box
                v-if="pages > 1 && foundGames.length > 0"
                class="game-options__pagin"
                :currentPageNumber="currentPage"
                :maxPageNumber="pages"
                @change-page-number="changePageNumber"
            />
        </div>
        <div
            v-else
            class="picking-game__not-picked"
        >
            <button
                @click="showPickingForm"
                type="button"
                class="button"
            >Pick a game</button>
        </div>
    </div>
</template>

<script>
    import GameOptions from "./GameOptions";
    import PaginBox from "./PaginBox";
    export default {
        name: "PickingGameForm",
        components: {PaginBox, GameOptions},
        props: {
            initialShowForm: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isPickingFormShown: false,
                searchString: '',
                foundGames: [],
                currentPage: 1,
                pages: 1,
                isGamesNotFound: false
            };
        },
        watch: {
            searchString: function (newValue, oldValue) {
                this.handleSearchStringChange();
            }
        },
        methods: {
            showPickingForm () {
                this.isPickingFormShown = true;
            },

            handleSearchStringChange () {

                if (this.searchString === '')
                {
                    this.foundGames = [];
                    return;
                }

                this.$debounce(() => {
                    this.searchGames();
                }, 500)();
            },

            searchGames (pageNumber) {
                pageNumber = pageNumber || 1;
                this.isGamesNotFound = false;

                this.$store.dispatch('findGames', {query: this.searchString, page: pageNumber})
                    .then(({games, pagin}) => {
                        this.foundGames = games;
                        this.currentPage = pagin.page;
                        this.pages = pagin.pages;
                    })
                    .catch(errorStatus => {
                        if (errorStatus === 404)
                        {
                            this.isGamesNotFound = true;
                            this.foundGames = [];
                        }
                    });
            },

            changePageNumber (newPageNumber) {
                this.searchGames(newPageNumber);
            },

            selectGame (game) {
                this.foundGames = [];
                this.searchString = '';
                this.$emit('select-game', game);
            }
        },
        created() {
            this.isPickingFormShown = this.initialShowForm;
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .picking-game{
        padding: 10px;

        &__not-picked{
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60px;
        }

        &__not-found{
            color: @color-cobalt;
            font-style: italic;
            font-size: 13px;
            padding: 5px 12px;
            border-bottom: 1px solid @color-cobalt;
        }
    }

</style>