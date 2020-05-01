<template>
    <div class="not-found">
        <h1 class="title">404 Not Found</h1>
        <div class="not-found__text">
            <i class="not-found__icon fa-icon far fa-fw fa-file"></i>
            <div>This is not the page you are looking for.</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "NotFound",
        props: {},
        data() {
            return {};
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .not-found{

        &__text{
            border-left: 2px solid @color-red;
            padding: 20px;
            font-style: italic;
            display: flex;
            align-items: center;
            
            .dark-mode &{
                border-left-color: @color-red-light;
            }
        }
        
        &__icon{
            font-size: 24px;
            color: @color-dark-orange;
            flex-shrink: 0;
            
            .dark-mode &{
                color: @color-red;
            }
        }
    }

</style>