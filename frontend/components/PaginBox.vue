<template>
    <div class="pagin">
        <a
            v-if="currentPageNumber > 1"
            :title="1"
            class="pagin__link"
            @click.prevent="changePageNumber(1)"
        >
            <i class="fas fa-fw fa-angle-double-left"></i>
        </a>

        <component
            v-for="i in range(minPageToShow, maxPageToShow)"
            :is="(i === currentPageNumber) ? 'span' : 'a'"
            :key="'page_'+i"
            :class="(i === currentPageNumber) ? 'pagin__cur' : 'pagin__link'"
            @click.prevent="(i !== currentPageNumber) ? changePageNumber(i) : null"
        >{{i}}</component>

        <a
            v-if="currentPageNumber < maxPageNumber"
            :title="maxPageNumber"
            class="pagin__link"
            @click.prevent="changePageNumber(maxPageNumber)"
        >
            <i class="fas fa-fw fa-angle-double-right"></i>
        </a>

    </div>
</template>

<script>
    export default {
        name: "PaginBox",
        props: {
            currentPageNumber: {
                type: Number,
                default: 1
            },
            maxPageNumber: {
                type: Number,
                default: 1
            }
        },
        data() {
            return {};
        },
        computed: {
            minPageToShow: function() {
                let minPage = this.currentPageNumber - 3;

                if (minPage < 1)
                    minPage = 1;

                return minPage;
            },
            maxPageToShow: function() {
                let maxPage = this.currentPageNumber + 3;

                if (maxPage > this.maxPageNumber)
                    maxPage = this.maxPageNumber;

                return maxPage;
            }
        },
        methods: {
            range(start, end){
                return Array(end - start + 1).fill(0).map((val, i) => i + start);
            },

            changePageNumber(pageNum){
                this.$emit('change-page-number', pageNum);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .pagin{
        padding: 6px 0;
        display: flex;
        justify-content: flex-end;

        &__link{
            cursor: pointer;
        }

        &__link, &__cur{
            padding: 0 5px;
        }
    }

</style>