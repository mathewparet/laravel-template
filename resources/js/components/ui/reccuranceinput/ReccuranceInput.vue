<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import type { Choice } from '../form/index';
import { ReccuranceRuleFrequency, Month, Day, type ReccuranceInputProp, ReccuranceRule} from './index'
import { defineProps, defineEmits, ref, reactive, computed } from 'vue';
import { SelectInput } from '../selectinput';
import { Input } from '../input';
import InputError from '@/components/InputError.vue';


const props = defineProps<ReccuranceInputProp>();

const options: Array<Choice> = [
    {
        value: ReccuranceRuleFrequency.None,
        label: 'Never',
    },
    {
        value: ReccuranceRuleFrequency.Daily,
        label: 'Daily',
    },
    {
        value: ReccuranceRuleFrequency.Weekly,
        label: 'Weekly',
    },
    {
        value: ReccuranceRuleFrequency.Monthly,
        label: 'Monthly',
    },
    {
        value: ReccuranceRuleFrequency.Yearly,
        label: 'Yearly',
    },
];

const dayOptions: Array<Choice> = [
    {
        value: Day.Sunday,
        label: 'Sunday',
    },
    {
        value: Day.Monday,
        label: 'Monday',
    },
    {
        value: Day.Tuesday,
        label: 'Tuesday',
    },
    {
        value: Day.Wednesday,
        label: 'Wednesday',
    },
    {
        value: Day.Thursday,
        label: 'Thursday',
    },
    {
        value: Day.Friday,
        label: 'Friday',
    },
    {
        value: Day.Saturday,
        label: 'Saturday',
    },
];

const monhtOptions: Array<Choice> = [
    {
        value: Month.January,
        label: 'January',
    },
    {
        value: Month.February,
        label: 'February',
    },
    {
        value: Month.March,
        label: 'March',
    },
    {
        value: Month.April,
        label: 'April',
    },
    {
        value: Month.May,
        label: 'May',
    },
    {
        value: Month.June,
        label: 'June',
    },
    {
        value: Month.July,
        label: 'July',
    },
    {
        value: Month.August,
        label: 'August',
    },
    {
        value: Month.September,
        label: 'September',
    },
    {
        value: Month.October,
        label: 'October',
    },
    {
        value: Month.November,
        label: 'November',
    },
    {
        value: Month.December,
        label: 'December',
    },
];



const emits = defineEmits<{
    (e: 'update:modelValue', payload: ReccuranceRule): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const frequencyName = computed(() => {
    switch(props.modelValue.frequency)
    {
        case ReccuranceRuleFrequency.Daily: return 'day(s)';
        case ReccuranceRuleFrequency.Weekly: return 'week(s)';
        case ReccuranceRuleFrequency.Monthly: return 'month(s)';
        case ReccuranceRuleFrequency.Yearly: return 'year(s)';
    }
})

</script>

<template>
    <div class="grid gap-4 w-full pt-10 grid-cols-3">
        <div>
            <Label for="frequency">Frequency</Label>
            <SelectInput
                id="frequency"
                v-model="props.modelValue.frequency"
                class="mt-1 block w-full"
                required
                :readonly="props.readonly"
                :options="options"
                :disabled="props.disabled"
            />
        </div>
        <div v-if="props.modelValue.frequency != ReccuranceRuleFrequency.None">
            <Label for="interval">Every x {{ frequencyName }}</Label>
            <Input
                id="interval"
                v-model="props.modelValue.interval"
                placeholder="2 for every 2x"
                class="mt-1 block w-full"
                :readonly="props.readonly"
                :disabled="props.disabled"
                type="number"
                min="1"
            />
        </div>
        <div v-if="props.modelValue.frequency == ReccuranceRuleFrequency.Weekly">
            <Label for="byDay">Day</Label>
            <SelectInput
                id="byDay"
                v-model="props.modelValue.byDay"
                class="mt-1 block w-full"
                required
                :disabled="props.disabled"
                :readonly="props.readonly"
                optional
                :options="dayOptions"
            />
        </div>
        <div v-if="props.modelValue.frequency == ReccuranceRuleFrequency.Yearly">
            <Label for="byMonth">Month</Label>
            <SelectInput
                id="byMonth"
                v-model="props.modelValue.byMonth"
                class="mt-1 block w-full"
                :disabled="props.disabled"
                required
                optional
                :readonly="props.readonly"
                :options="monhtOptions"
            />
        </div>
        <div v-if="props.modelValue.frequency == ReccuranceRuleFrequency.Yearly">
            <Label for="byWeekNo">Week #</Label>
            <Input
                id="byWeekNo"
                v-model="props.modelValue.byWeekNo"
                :disabled="props.disabled"
                class="mt-1 block w-full"
                :readonly="props.readonly"
                placeholder="-3 for 3rd last week OR 3 for 3rd week"
                type="number"
                min="-52"
                max="52"
            />
        </div>
        <div v-if="props.modelValue.frequency == ReccuranceRuleFrequency.Yearly">
            <Label for="byYearDay">Day of year</Label>
            <Input
                id="byYearDay"
                v-model="props.modelValue.byYearDay"
                class="mt-1 block w-full"
                :readonly="props.readonly"
                :disabled="props.disabled"
                type="number"
                placeholder="-3 for 3rd last day OR 3 for 3rd day"
                min="-365"
                max="365"
            />
        </div>
        <div v-if="props.modelValue.frequency == ReccuranceRuleFrequency.Monthly || props.modelValue.frequency == ReccuranceRuleFrequency.Yearly">
            <Label for="byMonthDay">Day of month</Label>
            <Input
                id="byMonthDay"
                v-model="props.modelValue.byMonthDay"
                placeholder="-3 for 3rd last day OR 3 for 3rd day"
                class="mt-1 block w-full"
                :disabled="props.disabled"
                :readonly="props.readonly"
                type="number"
                min="-31"
                max="31"
            />
        </div>
        <div>
            <Label for="start_date">From / Draw Date</Label>
            <Input
                id="start_date"
                v-model="props.modelValue.start"
                class="mt-1 block w-full"
                :disabled="props.disabled"
                :readonly="props.readonly"
                type="date"
            />
        </div>
        <div v-if="props.modelValue.frequency !== ReccuranceRuleFrequency.None">
            <Label for="end_date">To</Label>
            <Input
                id="end_date"
                v-model="props.modelValue.end"
                :disabled="props.disabled"
                class="mt-1 block w-full"
                :readonly="props.readonly"
                type="datetime-local"
            />
        </div>
        <div v-if="props.modelValue.frequency  !== ReccuranceRuleFrequency.None">
            <Label for="count">Count</Label>
            <Input
                id="count"
                v-model="props.modelValue.count"
                placeholder="No. times to repeat"
                class="mt-1 block w-full"
                :disabled="props.disabled"
                :readonly="props.readonly"
                type="number"
                min="0"
            />
        </div>
    </div>
</template>
