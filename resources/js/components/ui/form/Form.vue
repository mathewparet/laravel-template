<script setup lang="ts">
    import { defineProps, computed, onMounted } from 'vue';
    import { FieldDecoration, type FieldDefinition, FieldType, FormDefinitions, FormEmitions, FormProps } from './index';
    import { defineEmits } from 'vue'
    import { Link } from '@inertiajs/vue3'
    import { type InertiaForm, InertiaFormProps } from "@inertiajs/vue3"
    import { useForm } from '@inertiajs/vue3'
    import HeadingSmall from '@/components/HeadingSmall.vue';
    import { Input } from '@/components/ui/input';
    import { AutoComplete } from '@/components/ui/autocomplete';
    import { TextArea } from '@/components/ui/textarea';
    import { SelectInput } from '@/components/ui/selectinput';
    import { Radio } from '@/components/ui/radio';
    import InputError from '@/components/InputError.vue';
    import InputHelp from '@/components/InputHelp.vue';
    import { Label } from '@/components/ui/label';
    import { Button } from '@/components/ui/button';
    import { LoaderCircle } from 'lucide-vue-next';
    import { Pencil, Archive, Save } from 'lucide-vue-next';
    import { ReccuranceInput } from '../reccuranceinput';
    import { getFieldTypeName, getFormattedFieldName } from '@/composables/useUtils';
    import { Tooltip, TooltipTrigger, TooltipContent } from '../tooltip';
    import { TooltipArrow } from 'radix-vue';

    const emit = defineEmits<FormEmitions>();

    const props = defineProps<FormProps>();

    const save = () => {
        if(!props.readonly && form.isDirty) {
            emit('save', form)
        }
    };

    const form = props.form || useForm(props.formFields);

    const getFieldStep = (field: FieldDefinition): string|number => {
        if(field.inputOptions?.type == FieldType.Integer) {
            return 1;
        }
        else if(field.inputOptions?.type == FieldType.Float) {
            return '0.01';
        }
        else {
            return '';
        }
    }

    const fieldChanged = (field: FieldDefinition, e = null) => {
        let file = '';
        if(field.type == FieldType.File) {
            file = e.target.files[0]
        }

        let data = {
            name: field.name,
            value: field.type == FieldType.File ? file : form[field.name],
            form: form
        };

        emit('change', data)
    }
    
    const fieldUpdating = (field: FieldDefinition, e = null) => {
        let file = '';
        if(field.type == FieldType.File) {
            file = e.target.files[0]
        }

        let data = {
            name: field.name,
            value: field.type == FieldType.File ? file : form[field.name],
            form: form
        };

        emit('updating', data)
    }

    const isReadonly = computed(() => props.readonly === true)

    const whenConditionPasses = (field: FieldDefinition) => field?.when === undefined || form[field.when.field] === field.when.value
    
    const generatePrefix = (prefix: string|undefined|null): string|undefined|null => prefix?.replace(/:(\w+):/g, (_, key) => form[key] ?? '');

    const deleteModel = () => {
        if(confirm('Are you sure you want to archive this item?')) {
            form.delete(props.deleteRoute, {
                preserveScroll: true,
            })
        }
    }

    defineExpose({
        setField: (name: string, value: any) => form[name] = value
    })
</script>
<template>
    <div class="flex-1  px-10">
        <form @submit.prevent="save">
            <div class="flex justify-between">
                <HeadingSmall :title="props.title" :description="props.description" />
                <div class="space-x-2 flex flex-wrap items-center" v-if="isReadonly && (props.canEdit || props.canDelete)">
                    <Link v-if="props.canEdit" class="text-blue-500 hover:drop-shadow" :href="props.editRoute">
                        <Pencil class="size-5"/>
                    </Link>
                    <Link as="button" v-if="props.canDelete" class="text-red-500 hover:drop-shadow" @click.prevent="deleteModel">
                        <Archive class="size-5"/>
                    </Link>
                </div>
                <div class="space-x-2 flex flex-wrap items-center" v-if="!isReadonly">
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600 bg-blue-600 text-white rounded-lg px-2 py-1">Saved.</p>
                    </Transition>
                    <div v-if="form.isDirty" class="flex items-center text-sm text-orange-500 animate-pulse">
                        <span class="text-xl leading-none">•</span>
                    </div>
                    <div>
                        <Button :disabled="form.processing || !form.isDirty" @click.prevent="save">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            <Save v-else class="h-4 w-4" />
                            Save
                        </Button>
                    </div>
                </div>
            </div>
            <div class="grid gap-4 w-full pt-10" :class="{'grid-cols-1 lg:grid-cols-2': props.cols == 2 || props.cols == undefined, 'grid-cols-1 lg:grid-cols-1': props.cols == 1, 'grid-cols-1 lg:grid-cols-3': props.cols == 3}">
                <div 
                    class="grid gap-2" 
                    :class="{
                        'lg:col-span-2': field.colSpan == 2,
                        'lg:col-span-3': field.colSpan == 3,
                    }" 
                    v-for="(field, index) in props.formDefinition" 
                    :key="field.name"
                >
                    <span v-if="whenConditionPasses(field)">
                        <Label :for="field.name">{{ getFormattedFieldName(field) }} <span v-if="!isReadonly"><span class="text-red-500" v-if="field.required">*</span><span class="text-sm italic" v-if="!field.required">(optional)</span></span></Label>
                        <Input
                            v-if="[FieldType.File, FieldType.Text, FieldType.Password, FieldType.Integer, FieldType.Float, FieldType.Date, FieldType.DateTime].includes(field.type)"
                            :id="field.name"
                            v-model="form[field.name]"
                            :type="getFieldTypeName(field)"
                            class="mt-1 block w-full"
                            v-bind="field.inputOptions"
                            :min="field.inputOptions?.min !== undefined ? field.inputOptions?.min : null"
                            :max="field.inputOptions?.max !== undefined ? field.inputOptions?.max : null"
                            :auto-focus="index==0"
                            :required="field.required"
                            :prefix="generatePrefix(field.inputOptions?.prefix)"
                            :readonly="isReadonly || field.readonly === true"
                            :accept="field.inputOptions?.accept"
                            :class="{
                                'uppercase': field.decoration == FieldDecoration.Uppercase,
                                'lowercase': field.decoration == FieldDecoration.Lowercase,
                                'capitalize': field.decoration == FieldDecoration.Capitalize,
                                'normal-case': field.decoration == FieldDecoration.Normal,
                                'bg-red-100 text-red-600 border-red-600 dark:bg-red-200 dark:text-red-800 dark:border-red-800': form.errors[field.name],
                            }"
                            :step="getFieldStep(field)"
                            @change="(e) =>  fieldChanged(field, e)"
                            @keyup="(e) =>  fieldUpdating(field, e)"
                            :disabled="form.processing"
                            :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                        />
                        <AutoComplete
                            v-if="field.type == FieldType.Autocomplete"
                            :id="field.name"
                            :form="props.form"
                            v-model="form[field.name]"
                            :type="getFieldTypeName(field)"
                            class="mt-1 block w-full"
                            v-bind="field.inputOptions"
                            :dataKey="field.autoCompleteOptions?.dataKey"
                            :auto-focus="index==0"
                            :search="field.autoCompleteOptions?.search"
                            :value-field="field.autoCompleteOptions?.valueField"
                            :format="field.autoCompleteOptions?.format"
                            @change="(e) =>  fieldChanged(field, e)"
                            :disabled="form.processing"
                            :required="field.required"
                            :prefix="generatePrefix(field.inputOptions?.prefix)"
                            :readonly="isReadonly || field.readonly === true"
                            :class="{
                                'uppercase': field.decoration == FieldDecoration.Uppercase,
                                'lowercase': field.decoration == FieldDecoration.Lowercase,
                                'capitalize': field.decoration == FieldDecoration.Capitalize,
                                'normal-case': field.decoration == FieldDecoration.Normal,
                                'bg-red-100 text-red-600 border-red-600 dark:bg-red-200 dark:text-red-800 dark:border-red-800': form.errors[field.name],
                            }"
                            :step="getFieldStep(field)"
                            :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                        />
                        <TextArea
                            v-if="field.type == FieldType.TextArea"
                            :id="field.name"
                            v-model="form[field.name]"
                            class="mt-1 block w-full"
                            :required="field.required"
                            :rows="field.textAreaOptions?.rows"
                            :readonly="isReadonly || field.readonly === true"
                            :disabled="form.processing"
                            @keyup.ctrl.enter="save"
                            :class="{
                                'uppercase': field.decoration == FieldDecoration.Uppercase,
                                'lowercase': field.decoration == FieldDecoration.Lowercase,
                                'capitalize': field.decoration == FieldDecoration.Capitalize,
                                'normal-case': field.decoration == FieldDecoration.Normal,
                                'bg-red-100 text-red-600 border-red-600 dark:bg-red-200 dark:text-red-800 dark:border-red-800': form.errors[field.name],
                            }"
                            @keyup="(e) =>  fieldUpdating(field, e)"
                            @change="fieldChanged(field)"
                            :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                        />
                        <SelectInput
                            v-if="field.type == FieldType.Select"
                            :id="field.name"
                            v-model="form[field.name]"
                            class="mt-1 block w-full"
                            :required="field.required"
                            :disabled="form.processing"
                            :readonly="isReadonly || field.readonly === true"
                            :class="{
                                'uppercase': field.decoration == FieldDecoration.Uppercase,
                                'lowercase': field.decoration == FieldDecoration.Lowercase,
                                'capitalize': field.decoration == FieldDecoration.Capitalize,
                                'normal-case': field.decoration == FieldDecoration.Normal,
                                'bg-red-100 text-red-600 border-red-600 dark:bg-red-200 dark:text-red-800 dark:border-red-800': form.errors[field.name],
                            }"
                            @keyup="(e) =>  fieldUpdating(field, e)"
                            @change="fieldChanged(field)"
                            :options="[]"
                            v-bind="field.selectOptions"
                            :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                        />
                        <Radio
                            v-if="field.type == FieldType.Radio"
                            :id="field.name"
                            v-model="form[field.name]"
                            class="mt-1 block w-full"
                            :required="field.required"
                            :disabled="form.processing"
                            :readonly="isReadonly || field.readonly === true"
                            :class="{
                                'uppercase': field.decoration == FieldDecoration.Uppercase,
                                'lowercase': field.decoration == FieldDecoration.Lowercase,
                                'capitalize': field.decoration == FieldDecoration.Capitalize,
                                'normal-case': field.decoration == FieldDecoration.Normal,
                                'bg-red-100 text-red-600 border-red-600 dark:bg-red-200 dark:text-red-800 dark:border-red-800': form.errors[field.name],
                            }"
                            @keyup="(e) =>  fieldUpdating(field, e)"
                            @change="fieldChanged(field)"
                            :options="[]"
                            v-bind="field.selectOptions"
                            :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                        >
                            <template v-for="(_, name) in $slots" #[name]="slotProps">
                                <slot :name="name" v-bind="slotProps" />
                            </template>
                        </Radio>
                        <ReccuranceInput
                            v-if="field.type == FieldType.ReccuranceInput"
                            :id="field.name"
                            v-model="form[field.name]"
                            :disabled="form.processing"
                            class="mt-1 block w-full"
                            :required="field.required"
                            :readonly="isReadonly || field.readonly === true"
                            @keyup="(e) =>  fieldUpdating(field, e)"
                            @change="fieldChanged(field)"
                            :options="[]"
                        />
                        <InputError :message="form.errors[field.name]" />
                        <InputHelp :message="field.help" />
                    </span>
                </div>
            </div>
        </form>
        <div class="mt-4">
            <slot name="footer"/>
        </div>
    </div>
</template>