<template>
    <div
        :class="['loading-indicator', {'loading-indicator--nomargin': noMargin}]"
    >
        <i class="fa-icon fas fa-spin fa-spinner"></i>
        <slot name="default">
            loading...
        </slot>
    </div>
</template>

<script>
    export default {
        name: "LoadingIndicator",
        props: {
            noMargin: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {};
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .loading-indicator{
        border-left: 2px solid @color-cobalt;
        background: @color-gray;
        padding: 8px 10px;
        margin-bottom: 20px;

        &--nomargin{
            margin-bottom: 0;
        }

        .dark-mode &{
            background: @color-gray-darker;
            border-left-color: @color-cobalt-light;
        }
    }

</style>