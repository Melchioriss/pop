<template>
    <div class="error-box">
        <i class="error-box__icon fas fa-fw fa-exclamation-triangle"></i>
        <div class="error-box__text">
            <slot name="default"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ErrorBox",
        props: {},
        data() {
            return {};
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .error-box{
        color: @color-red;
        font-weight: bold;
        font-size: 14px;
        margin: 10px 0;
        display: flex;
        align-items: start;
        
        .dark-mode &{
            color: @color-red-light;
        }

        &__icon{
            font-size: 24px;
            margin-right: 8px;
            margin-top: 5px;
        }

        &__text{
            white-space: pre-wrap;
        }
    }

</style>