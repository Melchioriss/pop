<template>
    <div
        :class="[
            'status',
            className,
            {'status--editable': canEdit}
        ]"
        @click.prevent="showSelection"
    >
        <select
            v-if="isSelecting"
            v-model="newStatus"
            @change="changeStatus"
            class="input input--narrow"
            title="Choose a played status"
        >
            <option
                v-for="(statusText, statusCode) in statusTexts"
                :value="statusCode"
            >{{statusText}}</option>
        </select>

        <span
            v-else
            :class="['status__text', {'status__text--editable': canEdit}]"
        >{{statusTexts[status]}}</span>

    </div>
</template>

<script>
    import {mapState, mapGetters} from 'vuex';
    export default {
        name: "StatusItem",
        props: {
            status: {
                type: Number,
                default: 0
            },
            isParticipant: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                newStatus: '',
                isSelecting: false
            };
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
                'statusTexts'
            ]),

            canEdit: function () {
                return this.isParticipant || this.$store.getters.loggedUserIsAdmin;
            },

            className: function () {
                return 'status--' +  this.statusTexts[this.status].toLowerCase().replace(' ', '-');
            }
        },
        watch: {
            status: function () {
                this.isSelecting = false;
            }
        },
        methods: {
            showSelection() {
                if (!this.canEdit || this.isSelecting)
                    return false;

                this.isSelecting = true;
                this.newStatus = this.status;
            },

            changeStatus() {
                this.$emit('change-status', parseInt(this.newStatus));
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .status{
        text-align: center;
        font-size: 14px;
        padding: 4px 5px;

        &--editable{
            cursor: pointer;
        }
        
        .dark-mode &{
            color: @color-text;
            
            &--not-played{
                color: @color-light-text;
            }
        }

        &--beaten{background: @color-beaten;}
        &--completed{background: @color-completed;}
        &--unfinished{background: @color-unfinished;}
        &--abandoned{background: @color-abandoned;}

        &__text{
            display: inline-block;
            height: 19px;
            padding: 4px 0;

            &--editable:after{
                content: '\f303';
                display: inline;
                //noinspection CssNoGenericFontName
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                color: @color-text;
                margin-left: 5px;
            }
        }

        .dark-mode &--not-played &__text--editable:after{
            color: @color-light-text;
        }

    }

</style>