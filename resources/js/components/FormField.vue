<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <select
                :id="field.attribute"
                v-model="value"
                class="w-full form-control form-select"
                :class="errorClasses"
                :disabled="disabled"
            >
                <option value selected>{{ __('Choose an option') }}</option>
                <option
                    :key="option.value"
                    :value="option.value"
                    v-for="option in options"
                >{{ option.label }}</option>
            </select>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    data() {
        return {
            parentValue: null,
            loaded: false,
            options: []
        };
    },

    mounted() {
        if (this.field.recursive)
            this.watchComponentsRecursively(this.$root);
        else
            this.watchParentComponents();
    },

    computed: {

        disabled() {
            return this.options.length == 0;
        }
    },

    methods: {
        setInitialValue() {
            this.value = "";
        },

        fill(formData) {
            formData.append(this.field.attribute, this.value || "");
        },

        updateOptions() {
            this.value = "";
            this.loaded = false;
            this.options = [];

            if (this.parentValue != null && this.parentValue != "") {
                Nova.request()
                    .get(
                        "/nova-vendor/child-select/options?resource=" +
                            this.resourceName +
                            "&attribute=" +
                            this.field.attribute +
                            "&parent=" +
                            this.parentValue +
                            "&recursive=" +
                            this.field.recursive
                    )
                    .then(response => {
                        this.loaded = true;
                        this.options = response.data;
                        let optionValueExists = false;
                        this.options.forEach(option => {
                            if (option.value == this.field.value) {
                                optionValueExists = true;
                                this.value = option.value;
                            }
                        });
                    });
            }
        },

        isWatchingComponent(component) {
            return (
                component.field !== undefined &&
                component.field.attribute == this.field.parentAttribute
            );
        },
        watchParentComponents(){
            this.$parent.$children.forEach(component => {
                this.watchIfParent(component)
            })
        }
        ,
        watchComponentsRecursively(root) {
            const _this = this;
            root.$children.forEach(component => {
                this.watchIfParent(component)
                this.watchComponentsRecursively(component)
            })
        },
        watchIfParent(component) {
            let attribute = "value";

            if (this.isWatchingComponent(component)) {
                if (component.field.component === "belongs-to-field") {
                    attribute = "selectedResource";
                }
                component.$watch(
                    attribute,
                    value => {
                        this.parentValue =
                            value && attribute == "selectedResource"
                                ? value.value
                                : value;

                        this.updateOptions();
                    },
                    {immediate: true}
                );
            }

        }
    }
};
</script>
