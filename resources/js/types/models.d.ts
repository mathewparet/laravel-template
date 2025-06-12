import { ReccuranceRule } from "@/components/ui/reccuranceinput";
import { Resource, User, type ResourcePermissions } from ".";

export interface HashedResource
{
    hash_id: string,
    can: ResourcePermissions,
    updated_at: string,
    created_at: string,
    deleted_at?: string,
}
