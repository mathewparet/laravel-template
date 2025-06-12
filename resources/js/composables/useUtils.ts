import { ResourceMetadataLink } from "@/types";
import { FieldDefinition } from "@/components/ui/form";
import { FieldType } from "@/components/ui/form";

export function getCurrentUrl<URL>(data: any) {
    let url: string|null = data?.meta?.links?.filter((link: ResourceMetadataLink) => link.active)[0]['url']
    
    if(url) {
        return new URL(url);
    }
}

export function getFormattedFieldName(field: FieldDefinition) {
    let name = field.label?.length ? field.label : field.name

    return name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

export function getFieldTypeName(field: FieldDefinition): string|undefined {
    switch(field.type)
    {
        case FieldType.Password: return 'password'
        case FieldType.Text: return 'text'
        case FieldType.Integer: return 'number'
        case FieldType.Float: return 'number'
        case FieldType.Date: return 'date'
        case FieldType.DateTime: return 'datetime-local'
        case FieldType.File: return 'file'
    }
}

export function getLocalDatetime() {
  const now = new Date();
  const offset = now.getTimezoneOffset();
  const localDate = new Date(now.getTime() - offset * 60000);
  return localDate.toISOString().slice(0, 16);
}

export function toDatetimeLocal(datetimeString: string) {
  const date = new Date(datetimeString);
  const offset = date.getTimezoneOffset();
  const localDate = new Date(date.getTime() - offset * 60000);
  return localDate.toISOString().slice(0, 16);
}

export function humanizePhpClass(phpNamespace: string) {
  const parts = phpNamespace.replace(/^['"]|['"]$/g, '').split('\\');

  const className = parts[parts.length - 1];

  const humanReadable = className.replace(/([a-z])([A-Z])/g, '$1 $2');

  return humanReadable;
}

export function getToday() {
  return new Date().toISOString().split('T')[0];
}