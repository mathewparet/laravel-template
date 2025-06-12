export { default as ReccuranceInput } from './ReccuranceInput.vue';
import type { BaseFieldProps } from '../form/index';

export type ReccuranceInputProp = BaseFieldProps & ReccuranceInputDefinition

export interface ReccuranceInputDefinition
{
    cols: number,
    modelValue: ReccuranceRule,
    disabled?: boolean,
}

export enum ReccuranceRuleFrequency
{
    Yearly = "YEARLY",
    Monthly = "MONTHLY",
    Weekly = "WEEKLY",
    Daily = "DAILY",
    None = "NONE",
}

export enum Day
{
    Sunday = "SU",
    Monday = "MO",
    Tuesday = "TU",
    Wednesday = "WE",
    Thursday = "TH",
    Friday = "FR",
    Saturday = "SA",
}

export enum Month
{
    January = 1,
    February = 2,
    March = 3,
    April = 4,
    May = 5,
    June = 6,
    July = 7,
    August = 8,
    September = 9,
    October = 10,
    November = 11,
    December = 12,
}

export interface ReccuranceRule
{
    frequency: ReccuranceRuleFrequency,
    start: string|null,
    end: string|null,
    weekStart: Day|null,
    byDay: Day|null,
    byMonth: Month|null,
    byWeekNo: Number|null,
    byYearDay: Number|null,
    byMonthDay: Number|null,
    interval: Number|null,
    count: Number|null,
}