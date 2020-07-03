<template>
    <div
        :class="['app', {'dark-mode': isDarkMode}]"
    >
        <header class="app__header">
            <div class="app__wrapper app__wrapper--flex-row">
                <router-link :to="{name: 'index'}" class="app__logo">
                    <img
                        :src="group.logoUrl"
                        class="app__logo-img"
                        alt="PoP logo"
                    />
                    <span class="app__logo-text">Play or Pay</span>
                </router-link>
                <nav class="app__main-nav nav">
                    <router-link :to="{name: 'events'}" class="nav__link">Events</router-link>
                    <router-link :to="{name: 'members'}" class="nav__link">Members</router-link>
                    <router-link :to="{name: 'activity_feed'}" class="nav__link">Activity Feed</router-link>
                    <router-link
                        v-if="loggedUserIsAdmin"
                        :to="{name: 'admin_tools'}"
                        class="nav__link"
                    >Admin Tools</router-link>
                </nav>
                <div class="app__theme">
                    <span
                        @click="setDarkMode(false)"
                        :class="['app__theme-choice', {'app__theme-choice--current': !isDarkMode}]"
                        title="Light Mode"
                    ><i class="fas fa-fw fa-sun"></i></span>
                    <span
                        @click="setDarkMode(true)"
                        :class="['app__theme-choice', {'app__theme-choice--current': isDarkMode}]"
                        title="Dark Mode"
                    ><i class="fas fa-fw fa-moon"></i></span>
                </div>
                <steam-login />
            </div>
        </header>
        <main class="app__main">
            <div class="app__wrapper app__main-area">
                <router-view :key="$route.fullPath" />
            </div>
        </main>
        <footer class="app__footer">
            <div class="app__wrapper app__wrapper--flex-row">
                <div class="app__authors">
                    {{year}}, Powered by <a href="https://store.steampowered.com/" target="_blank" class="app__authors-link">Steam</a>.
                    Created by
                    <a href="https://github.com/Ardiffaz" target="_blank" class="app__authors-link">Ardiffaz</a>
                    &
                    <a href="https://github.com/insideone" target="_blank" class="app__authors-link">insideone</a>
                </div>

                <nav class="app__bottom-nav nav">
                    <router-link :to="{name: 'text_formatting'}" class="nav__link">Text Formatting</router-link>
                    <a
                        :href="'https://steamcommunity.com/groups/'+group.code"
                        target="_blank"
                        class="nav__link"
                    >Steam Group</a>
                    <a href="https://github.com/Ardiffaz/pop" target="_blank" class="nav__link">
                        <i class="fa-fw fab fa-github"></i>&nbsp;&nbsp;Source Code
                    </a>
                    <a href="https://github.com/Ardiffaz/pop/issues" target="_blank" class="nav__link">Bugs & Suggestions</a>
                </nav>
            </div>
        </footer>
    </div>
</template>

<script>

    import {mapGetters} from 'vuex';
    import SteamLogin from "./components/SteamLogin";

    export default {
        name: "App",
        components: {
            SteamLogin
        },
        data: function () {
            return {
                group: {
                    id: '',
                    code: '',
                    name: '',
                    logoUrl: '',
                },
                isDarkMode: false
            };
        },
        computed: {
            ...mapGetters({
                mainGroup: 'getMainGroup',
                loggedUserIsAdmin: 'loggedUserIsAdmin'
            }),

            year: () => (new Date).getFullYear()
        },
        watch: {
            mainGroup: function () {
                this.group = {...this.mainGroup};
            }
        },
        methods: {
            setDarkMode(isDarkMode) {
                this.$cookie.set('dark_mode', isDarkMode ? 'y' : 'n', { expires: '3M', sameSite: 'Lax' });
                this.isDarkMode = isDarkMode;
            }
        },
        created() {
            this.isDarkMode = (this.$cookie.get('dark_mode') === 'y');
        }
    }
</script>

<style lang="less">
    @import './assets/_colors';
    @import '~normalize.css';

    @import '~@fortawesome/fontawesome-free/less/fontawesome';
    @import '~@fortawesome/fontawesome-free/less/regular';
    @import '~@fortawesome/fontawesome-free/less/solid';
    @import '~@fortawesome/fontawesome-free/less/brands';


    @import "./assets/text";
    @import "./assets/fa-icon";
    @import "./assets/button";
    @import "./assets/nav";
    @import "./assets/title";
    @import "./assets/input";
    @import "./assets/checkbox";
    @import "./assets/form";
    @import "./assets/edit-link";

    html, body{
        height: 100%;
    }

    a{
        color: @color-dark-orange;
        text-decoration: underline;
        transition: color 0.3s;

        &:hover{
            color: @color-dark-orange-hover;
        }

        .dark-mode &{
            color: @color-light-orange;

            &:hover{
                color: @color-light-orange-hover;
            }
        }
    }

    input, textarea{
        font-family: sans-serif;
    }
    
    .color-dark-orange{color: @color-dark-orange;}

    .app{
        min-height: 100%;
        display: flex;
        flex-direction: column;
        background: @color-bg;
        color: @color-text;
        font-size: 15px;
        font-family: sans-serif;
        line-height: 1.3;

        &.dark-mode{
            background: @color-dark-bg;
            color: @color-light-text;
        }

        &__header, &__footer{
            flex-shrink: 0;
        }

        &__main{
            flex-grow: 1;
        }

        &__wrapper{
            min-width: 1250px;
            max-width: 1500px;
            margin: 0 auto;
            box-sizing: border-box;
            padding: 0 16px;

            &--flex-row{
                display: flex;
                justify-content: flex-start;
                align-items: stretch;
            }
        }

        &__header{
            background: @color-cobalt;
            border-top: 2px solid @color-light-orange;

            .dark-mode &{
                background: @color-cobalt-dark;
            }
        }

        &__logo{
            display: flex;
            align-items: center;
            box-sizing: border-box;
            height: 50px;
            padding: 0 20px 0 0;
            transition: background-color 0.3s;
            text-decoration: none;

            &.router-link-exact-active{
                background: @color-cobalt-hover;

                .dark-mode &{
                    background: @color-cobalt-dark-hover;
                }
            }
        }

        &__logo-img{
            height: 100%;
            margin-right: 18px;
            font-size: 12px;
        }

        &__logo-text{
            color: @color-bg;
            font-weight: bold;
            font-size: 20px;
        }

        &__main-nav{
            margin-right: auto;
        }

        &__bottom-nav{
            margin-left: auto;
            font-size: 14px;
        }

        &__footer{
            background: @color-cobalt;
            border-bottom: 2px solid @color-light-orange;
            font-size: 12px;

            .dark-mode &{
                background: @color-cobalt-dark;
            }
        }

        &__authors{
            align-self: center;
            padding: 5px 0;
            font-size: 12px;
            font-style: italic;
            color: @color-bg;
        }
        
        &__authors-link, .dark-mode &__authors-link{
            color: @color-bg;
            &:hover{
                color: @color-gray;
            }
        }

        &__main-area{
            padding-top: 20px;
            padding-bottom: 20px;
        }

        &__theme{
            font-size: 18px;
            display: flex;
            align-items: center;
            margin: 0 20px;
        }

        &__theme-choice{
            color: @color-bg;
            margin: 0 6px;
            padding: 3px 2px 2px;
            border: 2px solid rgba(0, 0, 0, 0);
            cursor: pointer;

            &--current{
                border-color: @color-light-orange;
                color: @color-light-orange;
            }
        }
    }
</style>